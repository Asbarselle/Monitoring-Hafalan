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

        // Debug logging
        \Log::info('HafalanStore - Request received', [
            'has_audio_file' => $request->hasFile('audio'),
            'audio_key_exists' => $request->has('audio'),
            'request_keys' => array_keys($request->all()),
            'content_type' => $request->header('Content-Type'),
            'request_method' => $request->method(),
        ]);

        // Handle audio file upload
        if ($request->hasFile('audio')) {
            $audioFile = $request->file('audio');
            
            \Log::info('Audio file received', [
                'name' => $audioFile->getClientOriginalName(),
                'size' => $audioFile->getSize(),
                'mime_type' => $audioFile->getMimeType(),
                'client_mime_type' => $audioFile->getClientMimeType(),
            ]);
            
            // Validate audio file
            if ($audioFile->getClientMimeType() === 'audio/webm' || 
                str_starts_with($audioFile->getClientMimeType(), 'audio/')) {
                
                // Store audio file di local storage terlebih dahulu
                $audioPath = $audioFile->store('hafalan-audio', 'public');
                $data['audio_path'] = $audioPath;
                $data['audio_filename'] = $audioFile->getClientOriginalName();
                
                \Log::info('Audio file stored successfully in local storage', [
                    'path' => $audioPath,
                    'filename' => $audioFile->getClientOriginalName(),
                ]);
            } else {
                \Log::warning('Audio file MIME type not valid', [
                    'mime_type' => $audioFile->getClientMimeType(),
                ]);
            }
        } else {
            \Log::info('No audio file in request');
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

            \Log::info('Audio deleted for hafalan id: ' . $hafalan->id);
        }

        // Handle audio file upload untuk update
        if ($request->hasFile('audio')) {
            $audioFile = $request->file('audio');

            \Log::info('Audio update received', [
                'name' => $audioFile->getClientOriginalName(),
                'size' => $audioFile->getSize(),
                'mime_type' => $audioFile->getMimeType(),
            ]);

            // Hapus audio lama jika ada
            if ($hafalan->audio_path && Storage::disk('public')->exists($hafalan->audio_path)) {
                Storage::disk('public')->delete($hafalan->audio_path);
            }
            
            // Simpan audio baru
            if ($audioFile->getClientMimeType() === 'audio/webm' || 
                str_starts_with($audioFile->getClientMimeType(), 'audio/')) {
                
                $audioPath = $audioFile->store('hafalan-audio', 'public');
                $data['audio_path'] = $audioPath;
                $data['audio_filename'] = $audioFile->getClientOriginalName();

                \Log::info('Audio updated successfully', [
                    'path' => $audioPath,
                ]);
            }
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