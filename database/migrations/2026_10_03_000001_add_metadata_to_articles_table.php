<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration aman: hanya MENAMBAH kolom baru ke tabel articles.
 * Tidak mengubah, menghapus, atau memodifikasi kolom yang sudah ada.
 * Semua kolom baru nullable sehingga 122 artikel lama tetap aman.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            // Sumber berita (untuk artikel dari RSS/API)
            $table->string('source_name', 150)->nullable()->after('image')
                ->comment('Nama media sumber, misal: CNN Indonesia');

            $table->string('source_url', 600)->nullable()->after('source_name')
                ->comment('URL artikel asli di media sumber — digunakan sebagai unique key anti-duplikat');

            // Ringkasan pendek (excerpt) — maks 500 karakter
            $table->text('excerpt')->nullable()->after('source_url')
                ->comment('Ringkasan 1-3 kalimat, digunakan di kartu berita homepage');

            // Estimasi waktu baca dalam menit (dihitung otomatis dari panjang content)
            $table->unsignedTinyInteger('read_time')->nullable()->after('excerpt')
                ->comment('Estimasi menit baca, dihitung @200 kata/menit, min 1');

            // Tanggal publikasi asli dari sumber berita
            $table->timestamp('published_at')->nullable()->after('read_time')
                ->comment('Tanggal publikasi asli dari sumber — berbeda dari created_at (waktu diimpor)');
        });

        // Index untuk mempercepat pencarian duplikat berdasarkan source_url
        Schema::table('articles', function (Blueprint $table) {
            $table->index('source_url', 'idx_articles_source_url');
            $table->index('published_at', 'idx_articles_published_at');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndexIfExists('idx_articles_source_url');
            $table->dropIndexIfExists('idx_articles_published_at');
            $table->dropColumn([
                'source_name',
                'source_url',
                'excerpt',
                'read_time',
                'published_at',
            ]);
        });
    }
};
