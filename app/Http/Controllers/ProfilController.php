<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return redirect()->route('dashboard');
        }

        $user->profil()->firstOrCreate([
            'user_id' => $user->id,
        ], [
            'alamat' => '-',
            'nomor_hp' => '-',
            'jabatan' => $user->role === 'sopir' ? 'Sopir' : 'Penumpang',
        ]);

        return view('profil.show', compact('user'));
    }

    // Menampilkan form edit profil
    public function edit()
    {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return redirect()->route('dashboard');
        }

        $user->profil()->firstOrCreate([
            'user_id' => $user->id,
        ], [
            'alamat' => '-',
            'nomor_hp' => '-',
            'jabatan' => $user->role === 'sopir' ? 'Sopir' : 'Penumpang',
        ]);

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

    /**
     * Tampilkan profil publik pengguna lain agar sopir dan penumpang bisa saling mengenal.
     */
    public function showUser(User $user)
    {
        $user->load('profil');

        return view('profil.public', [
            'profilUser' => $user,
        ]);
    }
}
