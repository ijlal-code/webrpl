<?php

namespace App\Http\Controllers;


use App\Models\Arsip;
use App\Models\KategoriArsip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;



class ArsipController extends Controller
{
    public function index(Request $request)
{
    $user = auth()->user();

    // Mulai query arsip
        $arsipQuery = Arsip::with('kategori', 'user');
    

    // Filter berdasarkan pencarian
    if ($request->filled('search')) {
        $arsipQuery->where('judul_arsip', 'like', '%' . $request->search . '%');
    }

    $arsip = $arsipQuery->get();

    // Tentukan view berdasarkan role
    $view = match ($user->role) {
        'admin' => 'admin.arsip.index',
        'sekretaris' => 'sekretaris.arsip.index',
        'kepala' => 'kepala.arsip.index',
        default => abort(403),
    };

    return view($view, compact('arsip'));
}

    

    public function create() {
         $kategori = KategoriArsip::all();

    if ($kategori->isEmpty()) {
        return redirect()->route('sekretaris.kategori.create')->with('warning', 'Silakan buat kategori terlebih dahulu sebelum menambah arsip.');
    }

    return view('arsip.create', compact('kategori'));
    }

    public function store(Request $request) {
        $request->validate([
            'judul_arsip' => 'required|unique:arsips,judul_arsip',
            'file_arsip' => 'required|file|mimes:pdf,xlsx,docx|max:2048',
            'tanggal_upload' => 'required|date',
            'kategori_id' => 'required|exists:kategori_arsips,id'
        ]);
    
        DB::beginTransaction();
        try {
            $data = $request->all();
            $data['user_id'] = Auth::id();
            $data['tanggal_upload'];
            $data['file_arsip'] = $request->file('file_arsip')->store('arsip', 'public');
    
            $arsip = Arsip::create($data);


            $arsip->aksesUsers()->attach(Auth::id());
    
            DB::commit();
            return redirect()->route('sekretaris.arsip.index')->with('success', 'Arsip berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();

             // Hapus file yang sempat diunggah
        if (isset($data['file_arsip'])) {
            Storage::disk('public')->delete($data['file_arsip']);
        }
        
            \Log::error('Gagal menyimpan arsip: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menyimpan arsip: ' . $e->getMessage()]);
        }
    }

    public function show($id) {
        $arsip = Arsip::with('aksesUsers')->findOrFail($id);
        return view('arsip.show', compact('arsip'));
    }

    public function edit($id) {
        $arsip = Arsip::findOrFail($id);
        $kategori = KategoriArsip::all();
        return view('arsip.edit', compact('arsip', 'kategori'));
    }

    public function update(Request $request, $id) {

        // Validasi input
        $request->validate([
            'judul_arsip' => 'required|unique:arsips,judul_arsip,' . $id,
            'file_arsip' => 'nullable|file|mimes:pdf,xlsx,docx|max:2048',
            'kategori_id' => 'required|exists:kategori_arsips,id',
        ]);
    
        $arsip = Arsip::findOrFail($id);
    
        DB::beginTransaction();
        try {
            $data = $request->all();
    
            if ($request->hasFile('file_arsip')) {
                if ($arsip->file_arsip && Storage::disk('public')->exists($arsip->file_arsip)) {
                    Storage::disk('public')->delete($arsip->file_arsip);
                }
                $data['file_arsip'] = $request->file('file_arsip')->store('arsip', 'public');
            }
    
            $arsip->update($data);
    
            DB::commit();
            return redirect()->route($this->getIndexRouteByRole())->with('success', 'Arsip berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal memperbarui arsip: ' . $e->getMessage("s"));
            return back()->withErrors(['error' => 'Gagal memperbarui arsip: ' . $e->getMessage()]);
        }
    }

    public function destroy($id) {
        $arsip = Arsip::findOrFail($id);    
    
        DB::beginTransaction();
        try {
            if ($arsip->file_arsip && Storage::disk('public')->exists($arsip->file_arsip)) {
                Storage::disk('public')->delete($arsip->file_arsip);
            }
    
            $arsip->delete();
    
            DB::commit();
            return redirect()->route($this->getIndexRouteByRole())->with('success', 'Arsip berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal menghapus arsip: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menghapus arsip: ' . $e->getMessage()]);
        }
    }

    public function download($id) {
        $arsip = Arsip::findOrFail($id);
        
        $path = storage_path('app/public/' . $arsip->file_arsip);
    
        if (!file_exists($path)) {
            abort(404);
        }
    
        return response()->download($path);
    }

    public function view($role, $id)
{
    $arsip = Arsip::findOrFail($id);
    $path = storage_path('app/public/' . $arsip->file_arsip);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
}



private function getIndexRouteByRole()
{
    return match (auth()->user()->role) {
        'admin' => 'admin.arsip.index',
        'sekretaris' => 'sekretaris.arsip.index',
        'kepala' => 'kepala.arsip.index',
        default => abort(403),
    };
}
}
