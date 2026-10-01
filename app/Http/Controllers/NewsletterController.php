<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ], [
            'email.required' => 'Alamat email wajib diisi.',
            'email.email'    => 'Format email tidak valid. Contoh: nama@email.com',
        ]);

        $email = strtolower(trim($request->email));

        // Cek apakah sudah terdaftar
        $sudahAda = NewsletterSubscriber::where('email', $email)->exists();

        if ($sudahAda) {
            return back()
                ->withInput()
                ->with('newsletter_info', 'Email ini sudah terdaftar sebagai pelanggan newsletter.');
        }

        NewsletterSubscriber::create(['email' => $email]);

        return back()->with('newsletter_success', 'Berhasil berlangganan! Terima kasih telah berlangganan newsletter kami.');
    }
}
