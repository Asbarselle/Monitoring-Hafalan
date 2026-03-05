<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Hafalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UstadzController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:ustadz');
    }

    /**
     * Dashboard Ustadz
     */
    public function dashboard()
    {
        $ustadzId = Auth::id();
        $totalSantri = Santri::count();
        $totalHafalan = Hafalan::where('ustadz_id', $ustadzId)->count();
        $hafalanSelesai = Hafalan::where('ustadz_id', $ustadzId)->where('status', 'selesai')->count();
        
        $hafalanTerbaru = Hafalan::where('ustadz_id', $ustadzId)
            ->with('santri')
            ->latest()
            ->take(5)
            ->get();

        return view('ustadz.dashboard', compact('totalSantri', 'totalHafalan', 'hafalanSelesai', 'hafalanTerbaru'));
    }

    /**
     * Daftar semua santri
     */
    public function index()
    {
        $this->authorize('viewAny', Santri::class);
        $santri = Santri::with(['hafalan' => function($query) {
            $query->where('ustadz_id', Auth::id())->latest();
        }])->latest()->paginate(10);

        return view('ustadz.santri.index', compact('santri'));
    }

    /**
     * Tampilkan hafalan santri tertentu
     */
    public function show($id)
    {
        $santri = Santri::with(['hafalan' => function($query) {
            $query->where('ustadz_id', Auth::id())->latest();
        }])->findOrFail($id);

        $this->authorize('view', $santri);

        return view('ustadz.santri.show', compact('santri'));
    }

    /**
     * Form tambah hafalan
     */
    public function create($santriId)
    {
        $santri = Santri::findOrFail($santriId);
        $this->authorize('create', \App\Models\Hafalan::class);
        return view('ustadz.hafalan.create', compact('santri'));
    }

    /**
     * Simpan hafalan baru
     */
    public function store(\App\Http\Requests\Ustadz\StoreHafalanRequest $request)
    {
        Hafalan::create([
            'santri_id' => $request->santri_id,
            'ustadz_id' => Auth::id(),
            'juz' => $request->juz,
            'surat' => $request->surat,
            'ayat_dari' => $request->ayat_dari,
            'ayat_sampai' => $request->ayat_sampai,
            'status' => $request->status,
            'catatan' => $request->catatan,
            'tanggal_setoran' => $request->tanggal_setoran,
            'nilai' => $request->nilai,
        ]);

        return redirect()->route('ustadz.santri.show', $request->santri_id)
            ->with('success', 'Hafalan berhasil ditambahkan.');
    }

    /**
     * Form edit hafalan
     */
    public function edit($id)
    {
        $hafalan = Hafalan::where('ustadz_id', Auth::id())->findOrFail($id);
        $this->authorize('update', $hafalan);
        return view('ustadz.hafalan.edit', compact('hafalan'));
    }

    /**
     * Update hafalan
     */
    public function update(\App\Http\Requests\Ustadz\UpdateHafalanRequest $request, $id)
    {
        $hafalan = Hafalan::where('ustadz_id', Auth::id())->findOrFail($id);
        
        $hafalan->update($request->all());

        return redirect()->route('ustadz.santri.show', $hafalan->santri_id)
            ->with('success', 'Hafalan berhasil diperbarui.');
    }

    /**
     * Hapus hafalan
     */
    public function destroy($id)
    {
        $hafalan = Hafalan::where('ustadz_id', Auth::id())->findOrFail($id);
        $this->authorize('delete', $hafalan);
        $santriId = $hafalan->santri_id;
        $hafalan->delete();

        return redirect()->route('ustadz.santri.show', $santriId)
            ->with('success', 'Hafalan berhasil dihapus.');
    }
}
