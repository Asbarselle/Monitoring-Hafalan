<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Hafalan;
use App\Events\HafalanCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        $data = [
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
        ];

        // Handle audio file upload
        if ($request->hasFile('audio')) {
            $audioFile = $request->file('audio');

            // Store audio file in local storage.
            $audioPath = $audioFile->store('hafalan-audio', 'public');
            $data['audio_path'] = $audioPath;
            $data['audio_filename'] = $audioFile->getClientOriginalName();
        } else {
            $data['audio_path'] = null;
            $data['audio_filename'] = null;
        }

        // Create hafalan
        $hafalan = Hafalan::create($data);

        // Fire event for notifications
        HafalanCreated::dispatch($hafalan);

        return redirect()->route('ustadz.santri.show', $request->santri_id)
            ->with('success', 'Hafalan berhasil ditambahkan dan notifikasi dikirim ke orang tua.');
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
        
        $data = [
            'juz' => $request->juz,
            'surat' => $request->surat,
            'ayat_dari' => $request->ayat_dari,
            'ayat_sampai' => $request->ayat_sampai,
            'status' => $request->status,
            'catatan' => $request->catatan,
            'tanggal_setoran' => $request->tanggal_setoran,
            'nilai' => $request->nilai,
        ];

        // Hapus audio jika diminta
        if ($request->hapus_audio == '1') {
            if ($hafalan->audio_path && Storage::disk('public')->exists($hafalan->audio_path)) {
                Storage::disk('public')->delete($hafalan->audio_path);
            }
            $data['audio_path'] = null;
            $data['audio_filename'] = null;

        }

        // Handle audio file upload untuk update
        if ($request->hasFile('audio')) {
            $audioFile = $request->file('audio');

            // Hapus audio lama jika ada
            if ($hafalan->audio_path && Storage::disk('public')->exists($hafalan->audio_path)) {
                Storage::disk('public')->delete($hafalan->audio_path);
            }
            
            // Simpan audio baru
            $audioPath = $audioFile->store('hafalan-audio', 'public');
            $data['audio_path'] = $audioPath;
            $data['audio_filename'] = $audioFile->getClientOriginalName();
        }

        $hafalan->update($data);

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
        
        // Hapus file audio jika ada
        if ($hafalan->audio_path && Storage::disk('public')->exists($hafalan->audio_path)) {
            Storage::disk('public')->delete($hafalan->audio_path);
        }
        
        $hafalan->delete();

        return redirect()->route('ustadz.santri.show', $santriId)
            ->with('success', 'Hafalan berhasil dihapus.');
    }
}