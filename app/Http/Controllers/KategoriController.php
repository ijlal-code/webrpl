<?php

namespace App\Http\Controllers;

use App\Models\KategoriArsip;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
   public function index(Request $request) {
    // Mulai query kategori
    $kategoriQuery = KategoriArsip::query();

    // Jika ada input pencarian, filter berdasarkan nama kategori
    if ($request->filled('search')) {
        $kategoriQuery->where('nama_kategori', 'like', '%' . $request->search . '%');
    }

    // Ambil data kategori
    $kategori = $kategoriQuery->get();

    // Kirim ke view
    return view('sekretaris.kategori.index', compact('kategori'));
}


    public function create() {
        return view('sekretaris.kategori.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'nama_kategori' => 'required|unique:kategori_arsips,nama_kategori',
    ], [
        'nama_kategori.unique' => 'Nama kategori sudah digunakan.',
    ]);

    KategoriArsip::create($request->all());
    return redirect()->route('sekretaris.kategori.index')->with('success', 'Kategori berhasil dibuat.');
}

    public function edit($id) {
        $kategori = KategoriArsip::findOrFail($id);
        return view('sekretaris.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'nama_kategori' => 'required|unique:kategori_arsips,nama_kategori,' . $id,
    ], [
        'nama_kategori.unique' => 'Nama kategori sudah digunakan.',
    ]);

    KategoriArsip::findOrFail($id)->update($request->all());
    return redirect()->route('sekretaris.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
}

    public function destroy($id) {
        KategoriArsip::destroy($id);
        return back();
    }
}
