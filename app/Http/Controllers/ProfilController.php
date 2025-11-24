<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function create()
    {
        return view('profil.create');
    }

    public function simpanProfil(Request $request)
    {
        $request->validate([
            'alamat' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:20',
            'jabatan' => 'nullable|string|max:50',
        ]);

        $user = auth()->user();

        // Jika user sudah punya profil, update. Kalau belum, buat baru.
        if ($user->profil) {
            $user->profil()->update($request->only(['alamat', 'nomor_hp', 'jabatan']));
        } else {
            $user->profil()->create($request->only(['alamat', 'nomor_hp', 'jabatan']));
        }

        return redirect()->route('profil.show')->with('success', 'Profil berhasil disimpan.');
    }
    
    // Menampilkan halaman profil user
    public function show()
    {
        $user = auth()->user();

        if (!$user->profil) {
            return redirect()->route('profil.create')->with('info', 'Silakan lengkapi profil Anda terlebih dahulu.');
        }

        return view('profil.show', compact('user'));
    }

    // Menampilkan form edit profil
    public function edit()
    {
        $user = auth()->user();

        if (!$user->profil) {
            return redirect()->route('profil.create')->with('info', 'Silakan lengkapi profil Anda terlebih dahulu.');
        }

        return view('profil.edit', compact('user'));
    }

    // Menyimpan perubahan profil
    public function update(Request $request)
    {
        $request->validate([
            'alamat' => 'required|string|max:255',
            'nomor_hp' => 'required|string|max:20',
        ]);

        $user = auth()->user();

        $user->profil->update([
            'alamat' => $request->alamat,
            'nomor_hp' => $request->nomor_hp,
        ]);

        return redirect()->route('profil.show')->with('success', 'Profil berhasil diperbarui.');
    }
}
