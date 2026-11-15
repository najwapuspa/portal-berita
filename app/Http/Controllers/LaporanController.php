<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    // Helper: bangun query dasar dengan filter
    // ─────────────────────────────────────────────────────────────

    private function parseFilters(Request $request): array
    {
        $dari   = $request->filled('dari')
            ? Carbon::parse($request->dari)->startOfDay()
            : null;
        $sampai = $request->filled('sampai')
            ? Carbon::parse($request->sampai)->endOfDay()
            : null;

        // Pastikan sampai >= dari
        if ($dari && $sampai && $sampai->lt($dari)) {
            $sampai = $dari->copy()->endOfDay();
        }

        $kategoriId = $request->filled('kategori_id') ? (int) $request->kategori_id : null;
        $status     = in_array($request->status, ['published', 'draft'], true) ? $request->status : null;

        return compact('dari', 'sampai', 'kategoriId', 'status');
    }

    private function applyFilters($query, array $f)
    {
        if ($f['dari'])       $query->where(function ($q) use ($f) {
            $q->where('published_at', '>=', $f['dari'])
              ->orWhere(function ($q2) use ($f) {
                  $q2->whereNull('published_at')->where('created_at', '>=', $f['dari']);
              });
        });
        if ($f['sampai'])     $query->where(function ($q) use ($f) {
            $q->where('published_at', '<=', $f['sampai'])
              ->orWhere(function ($q2) use ($f) {
                  $q2->whereNull('published_at')->where('created_at', '<=', $f['sampai']);
              });
        });
        if ($f['kategoriId']) $query->where('category_id', $f['kategoriId']);
        if ($f['status'])     $query->where('status', $f['status']);

        return $query;
    }

    // ─────────────────────────────────────────────────────────────
    // Kumpulkan semua data laporan (dipakai oleh index & PDF)
    // ─────────────────────────────────────────────────────────────

    private function collectData(array $f): array
    {
        // ── Kartu statistik ──────────────────────────────────────
        $baseQ = fn () => $this->applyFilters(Article::query(), $f);

        $totalArtikel   = (clone $baseQ())->count();
        $totalPublished = (clone $baseQ())->where('status', 'published')->count();
        $totalDraft     = (clone $baseQ())->where('status', 'draft')->count();
        $totalViews     = (int) (clone $baseQ())->sum('views');
        $totalKategori  = Category::count();
        $totalPenulis   = (clone $baseQ())->distinct('user_id')->count('user_id');

        // ── Statistik per kategori ───────────────────────────────
        $statsKategori = Category::withCount([
            'articles as total_artikel' => fn ($q) => $this->applyFilters($q, $f),
            'articles as total_published' => fn ($q) => $this->applyFilters($q->where('status', 'published'), $f),
            'articles as total_draft'     => fn ($q) => $this->applyFilters($q->where('status', 'draft'), $f),
        ])
        ->orderByDesc('total_artikel')
        ->get();

        // ── Statistik per penulis ────────────────────────────────
        $statsPenulis = User::select('users.id', 'users.name')
            ->join('articles', 'articles.user_id', '=', 'users.id')
            ->selectRaw('COUNT(articles.id) as total_artikel')
            ->selectRaw('SUM(CASE WHEN articles.status = ? THEN 1 ELSE 0 END) as total_published', ['published'])
            ->selectRaw('SUM(CASE WHEN articles.status = ? THEN 1 ELSE 0 END) as total_draft', ['draft'])
            ->selectRaw('SUM(articles.views) as total_views')
            ->when($f['dari'], function ($q) use ($f) {
                $q->where(function ($q2) use ($f) {
                    $q2->where('articles.published_at', '>=', $f['dari'])
                       ->orWhere(function ($q3) use ($f) {
                           $q3->whereNull('articles.published_at')->where('articles.created_at', '>=', $f['dari']);
                       });
                });
            })
            ->when($f['sampai'], function ($q) use ($f) {
                $q->where(function ($q2) use ($f) {
                    $q2->where('articles.published_at', '<=', $f['sampai'])
                       ->orWhere(function ($q3) use ($f) {
                           $q3->whereNull('articles.published_at')->where('articles.created_at', '<=', $f['sampai']);
                       });
                });
            })
            ->when($f['kategoriId'], fn ($q) => $q->where('articles.category_id', $f['kategoriId']))
            ->when($f['status'],     fn ($q) => $q->where('articles.status', $f['status']))
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_artikel')
            ->get();

        // ── Data artikel detail (untuk Excel sheet 4) ───────────
        $artikelDetail = Article::with(['category', 'user'])
            ->select('id', 'title', 'category_id', 'user_id', 'status', 'published_at', 'created_at', 'views')
            ->when(true, fn ($q) => $this->applyFilters($q, $f))
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->get();

        return compact(
            'totalArtikel', 'totalPublished', 'totalDraft',
            'totalViews', 'totalKategori', 'totalPenulis',
            'statsKategori', 'statsPenulis', 'artikelDetail'
        );
    }

    // ─────────────────────────────────────────────────────────────
    // Halaman utama laporan
    // ─────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $f    = $this->parseFilters($request);
        $data = $this->collectData($f);

        $semuaKategori = Category::orderBy('name')->get();

        $periodeLabel = $this->periodeLabel($f);

        return view('admin.laporan.index', array_merge($data, [
            'filters'       => $f,
            'request'       => $request,
            'semuaKategori' => $semuaKategori,
            'periodeLabel'  => $periodeLabel,
            'dibuatPada'    => now()->setTimezone('Asia/Jakarta'),
        ]));
    }

    // ─────────────────────────────────────────────────────────────
    // Ekspor PDF
    // ─────────────────────────────────────────────────────────────

    public function exportPdf(Request $request)
    {
        $f    = $this->parseFilters($request);
        $data = $this->collectData($f);

        $periodeLabel = $this->periodeLabel($f);

        $pdf = Pdf::loadView('admin.laporan.pdf', array_merge($data, [
            'periodeLabel' => $periodeLabel,
            'dibuatPada'   => now()->setTimezone('Asia/Jakarta'),
        ]));

        $pdf->setPaper('a4', 'portrait');
        $pdf->set_option('defaultFont', 'sans-serif');
        $pdf->set_option('isHtml5ParserEnabled', true);
        $pdf->set_option('isRemoteEnabled', false);
        $pdf->set_option('chroot', public_path());

        $filename = 'laporan-statistik-berita-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    // ─────────────────────────────────────────────────────────────
    // Ekspor Excel (4 sheet)
    // ─────────────────────────────────────────────────────────────

    public function exportExcel(Request $request)
    {
        $f    = $this->parseFilters($request);
        $data = $this->collectData($f);

        $periodeLabel = $this->periodeLabel($f);
        $dibuatPada   = now()->setTimezone('Asia/Jakarta')->format('d/m/Y H:i');

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Laporan Statistik Berita – NEWS X PAPER')
            ->setCreator('NEWS X PAPER')
            ->setDescription('Laporan statistik berita periode ' . $periodeLabel);

        // ── Gaya header kolom ──────────────────────────────────
        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1b8bd8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
        ];
        $titleStyle = [
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '111111']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ];
        $subStyle = [
            'font'      => ['size' => 11, 'color' => ['rgb' => '666666']],
        ];
        $cellStyle = [
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E3E3E3']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ];
        $numStyle = array_merge($cellStyle, [
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // ════════════════════════════════════════════════════════
        // SHEET 1: Ringkasan
        // ════════════════════════════════════════════════════════
        $sh1 = $spreadsheet->getActiveSheet();
        $sh1->setTitle('Ringkasan');

        $sh1->getColumnDimension('A')->setWidth(30);
        $sh1->getColumnDimension('B')->setWidth(20);

        $sh1->mergeCells('A1:B1');
        $sh1->setCellValue('A1', 'NEWS X PAPER – Laporan Statistik Berita');
        $sh1->getStyle('A1')->applyFromArray($titleStyle);
        $sh1->getRowDimension(1)->setRowHeight(24);

        $sh1->mergeCells('A2:B2');
        $sh1->setCellValue('A2', 'Periode: ' . $periodeLabel);
        $sh1->getStyle('A2')->applyFromArray($subStyle);

        $sh1->mergeCells('A3:B3');
        $sh1->setCellValue('A3', 'Dibuat: ' . $dibuatPada . ' WIB');
        $sh1->getStyle('A3')->applyFromArray($subStyle);

        $sh1->getRowDimension(4)->setRowHeight(10);

        $sh1->setCellValue('A5', 'METRIK')->setCellValue('B5', 'NILAI');
        $sh1->getStyle('A5:B5')->applyFromArray($headerStyle);
        $sh1->getRowDimension(5)->setRowHeight(22);

        $rows1 = [
            ['Total Artikel',      $data['totalArtikel']],
            ['Berita Diterbitkan', $data['totalPublished']],
            ['Berita Draft',       $data['totalDraft']],
            ['Total Kategori',     $data['totalKategori']],
            ['Total Views',        $data['totalViews']],
            ['Total Penulis',      $data['totalPenulis']],
        ];
        $r = 6;
        foreach ($rows1 as [$label, $val]) {
            $sh1->setCellValue("A{$r}", $label)->setCellValue("B{$r}", $val);
            $sh1->getStyle("A{$r}:B{$r}")->applyFromArray($cellStyle);
            $sh1->getStyle("B{$r}")->applyFromArray($numStyle);
            $sh1->getRowDimension($r)->setRowHeight(18);
            $r++;
        }

        // ════════════════════════════════════════════════════════
        // SHEET 2: Statistik Kategori
        // ════════════════════════════════════════════════════════
        $sh2 = $spreadsheet->createSheet();
        $sh2->setTitle('Kategori');

        foreach (['A' => 5, 'B' => 30, 'C' => 18, 'D' => 18, 'E' => 18] as $col => $w) {
            $sh2->getColumnDimension($col)->setWidth($w);
        }

        $sh2->mergeCells('A1:E1');
        $sh2->setCellValue('A1', 'NEWS X PAPER – Statistik Kategori Berita');
        $sh2->getStyle('A1')->applyFromArray($titleStyle);
        $sh2->getRowDimension(1)->setRowHeight(24);

        $sh2->mergeCells('A2:E2');
        $sh2->setCellValue('A2', 'Periode: ' . $periodeLabel);
        $sh2->getStyle('A2')->applyFromArray($subStyle);

        $sh2->getRowDimension(3)->setRowHeight(10);

        $sh2->setCellValue('A4', 'NO.')->setCellValue('B4', 'NAMA KATEGORI')
            ->setCellValue('C4', 'TOTAL ARTIKEL')->setCellValue('D4', 'PUBLISHED')
            ->setCellValue('E4', 'DRAFT');
        $sh2->getStyle('A4:E4')->applyFromArray($headerStyle);
        $sh2->getRowDimension(4)->setRowHeight(22);

        $r = 5;
        foreach ($data['statsKategori'] as $i => $kat) {
            $sh2->setCellValue("A{$r}", $i + 1)
                ->setCellValue("B{$r}", $kat->name)
                ->setCellValue("C{$r}", $kat->total_artikel ?? 0)
                ->setCellValue("D{$r}", $kat->total_published ?? 0)
                ->setCellValue("E{$r}", $kat->total_draft ?? 0);
            $sh2->getStyle("A{$r}:E{$r}")->applyFromArray($cellStyle);
            foreach (['C', 'D', 'E'] as $col) $sh2->getStyle("{$col}{$r}")->applyFromArray($numStyle);
            $sh2->getRowDimension($r)->setRowHeight(18);
            $r++;
        }

        // Total row
        $sh2->setCellValue("A{$r}", '')->setCellValue("B{$r}", 'TOTAL')
            ->setCellValue("C{$r}", $data['totalArtikel'])
            ->setCellValue("D{$r}", $data['totalPublished'])
            ->setCellValue("E{$r}", $data['totalDraft']);
        $totalRowStyle = array_merge($cellStyle, ['font' => ['bold' => true]]);
        $sh2->getStyle("A{$r}:E{$r}")->applyFromArray($totalRowStyle);
        foreach (['C', 'D', 'E'] as $col) $sh2->getStyle("{$col}{$r}")->applyFromArray($numStyle);

        // ════════════════════════════════════════════════════════
        // SHEET 3: Statistik Penulis
        // ════════════════════════════════════════════════════════
        $sh3 = $spreadsheet->createSheet();
        $sh3->setTitle('Penulis');

        foreach (['A' => 5, 'B' => 30, 'C' => 18, 'D' => 18, 'E' => 18, 'F' => 18] as $col => $w) {
            $sh3->getColumnDimension($col)->setWidth($w);
        }

        $sh3->mergeCells('A1:F1');
        $sh3->setCellValue('A1', 'NEWS X PAPER – Statistik Penulis');
        $sh3->getStyle('A1')->applyFromArray($titleStyle);
        $sh3->getRowDimension(1)->setRowHeight(24);

        $sh3->mergeCells('A2:F2');
        $sh3->setCellValue('A2', 'Periode: ' . $periodeLabel);
        $sh3->getStyle('A2')->applyFromArray($subStyle);

        $sh3->getRowDimension(3)->setRowHeight(10);

        $sh3->setCellValue('A4', 'NO.')->setCellValue('B4', 'NAMA PENULIS')
            ->setCellValue('C4', 'TOTAL ARTIKEL')->setCellValue('D4', 'PUBLISHED')
            ->setCellValue('E4', 'DRAFT')->setCellValue('F4', 'TOTAL VIEWS');
        $sh3->getStyle('A4:F4')->applyFromArray($headerStyle);
        $sh3->getRowDimension(4)->setRowHeight(22);

        $r = 5;
        foreach ($data['statsPenulis'] as $i => $p) {
            $sh3->setCellValue("A{$r}", $i + 1)
                ->setCellValue("B{$r}", $p->name)
                ->setCellValue("C{$r}", $p->total_artikel ?? 0)
                ->setCellValue("D{$r}", $p->total_published ?? 0)
                ->setCellValue("E{$r}", $p->total_draft ?? 0)
                ->setCellValue("F{$r}", $p->total_views ?? 0);
            $sh3->getStyle("A{$r}:F{$r}")->applyFromArray($cellStyle);
            foreach (['C', 'D', 'E', 'F'] as $col) $sh3->getStyle("{$col}{$r}")->applyFromArray($numStyle);
            $sh3->getRowDimension($r)->setRowHeight(18);
            $r++;
        }

        // ════════════════════════════════════════════════════════
        // SHEET 4: Data Artikel
        // ════════════════════════════════════════════════════════
        $sh4 = $spreadsheet->createSheet();
        $sh4->setTitle('Data Artikel');

        foreach (['A' => 5, 'B' => 60, 'C' => 22, 'D' => 25, 'E' => 15, 'F' => 22, 'G' => 15] as $col => $w) {
            $sh4->getColumnDimension($col)->setWidth($w);
        }

        $sh4->mergeCells('A1:G1');
        $sh4->setCellValue('A1', 'NEWS X PAPER – Data Artikel');
        $sh4->getStyle('A1')->applyFromArray($titleStyle);
        $sh4->getRowDimension(1)->setRowHeight(24);

        $sh4->mergeCells('A2:G2');
        $sh4->setCellValue('A2', 'Periode: ' . $periodeLabel);
        $sh4->getStyle('A2')->applyFromArray($subStyle);

        $sh4->getRowDimension(3)->setRowHeight(10);

        $sh4->setCellValue('A4', 'NO.')->setCellValue('B4', 'JUDUL ARTIKEL')
            ->setCellValue('C4', 'KATEGORI')->setCellValue('D4', 'PENULIS/SUMBER')
            ->setCellValue('E4', 'STATUS')->setCellValue('F4', 'TANGGAL PUBLIKASI')
            ->setCellValue('G4', 'VIEWS');
        $sh4->getStyle('A4:G4')->applyFromArray($headerStyle);
        $sh4->getRowDimension(4)->setRowHeight(22);

        $r = 5;
        foreach ($data['artikelDetail'] as $i => $art) {
            $pubDate = $art->published_at
                ? Carbon::parse($art->published_at)->setTimezone('Asia/Jakarta')->format('d/m/Y H:i')
                : Carbon::parse($art->created_at)->setTimezone('Asia/Jakarta')->format('d/m/Y H:i');

            $sh4->setCellValue("A{$r}", $i + 1)
                ->setCellValue("B{$r}", $art->title)
                ->setCellValue("C{$r}", optional($art->category)->name ?? '-')
                ->setCellValue("D{$r}", optional($art->user)->name ?? ($art->source_name ?? '-'))
                ->setCellValue("E{$r}", $art->status === 'published' ? 'Diterbitkan' : 'Draft')
                ->setCellValue("F{$r}", $pubDate)
                ->setCellValue("G{$r}", $art->views ?? 0);

            $sh4->getStyle("A{$r}:G{$r}")->applyFromArray($cellStyle);
            $sh4->getStyle("G{$r}")->applyFromArray($numStyle);
            $sh4->getStyle("B{$r}")->getAlignment()->setWrapText(true);
            $sh4->getRowDimension($r)->setRowHeight(20);
            $r++;
        }

        // ── Output file ────────────────────────────────────────
        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'laporan-statistik-berita-' . now()->format('Y-m') . '.xlsx';

        $writer = new Xlsx($spreadsheet);

        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    // ─────────────────────────────────────────────────────────────
    // Helper: label periode
    // ─────────────────────────────────────────────────────────────

    private function periodeLabel(array $f): string
    {
        if ($f['dari'] && $f['sampai']) {
            return $f['dari']->format('d/m/Y') . ' – ' . $f['sampai']->format('d/m/Y');
        }
        if ($f['dari'])   return 'Mulai ' . $f['dari']->format('d/m/Y');
        if ($f['sampai']) return 'Sampai ' . $f['sampai']->format('d/m/Y');
        return 'Semua Periode';
    }
}
