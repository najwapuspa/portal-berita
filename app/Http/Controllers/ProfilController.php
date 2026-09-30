<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfilController extends Controller
{
    public function nama(Request $request)
    {
        $data = $request->validateWithBag('nama', [
            'name' => ['required', 'string', 'max:255'],
        ]);

        $request->user()->update($data);

        return back()->with('ok_nama', 'Nama berhasil diubah.');
    }

    public function password(Request $request)
    {
        $request->validateWithBag('password', [
            'password_lama' => ['required', 'current_password'],
            'password'      => ['required', 'confirmed', Password::min(8)],
        ], [
            'password_lama.current_password' => 'Kata sandi lama tidak cocok.',
            'password.confirmed'             => 'Konfirmasi kata sandi tidak sama.',
        ]);

        $request->user()->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return back()->with('ok_password', 'Kata sandi berhasil diganti.');
    }
}