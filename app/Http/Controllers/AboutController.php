<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    /**
     * Tampilkan halaman profil Tentang Kami NEWS X PAPER.
     */
    public function index()
    {
        // Ambil kategori dari database dengan jumlah artikel terbit
        $categories = Category::withCount([
            'articles' => fn ($q) => $q->where('status', 'published'),
        ])->orderBy('name')->get();

        return view('about', compact('categories'));
    }
}

