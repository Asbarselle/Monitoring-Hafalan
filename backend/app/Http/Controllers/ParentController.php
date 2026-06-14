<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Hafalan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ParentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:orang_tua');
    }

    /**
     * Dashboard Orang Tua - Menampilkan perkembangan hafalan anak
     */
    public function dashboard()
    {
        $user = Auth::user();
        $santri = Santri::where('orang_tua_id', $user->id)->get();
        $santri->each(fn($s) => $this->authorize('view', $s));

        if ($santri->isEmpty()) {
            return view('parent.dashboard', [
                'santri' => collect(),
                'statistik' => [],
                'hafalanTerbaru' => collect(),
            ])->with('info', 'Belum ada data santri yang terhubung dengan akun Anda.');
        }

        // Ambil semua hafalan dari santri yang dimiliki
        $santriIds = $santri->pluck('id');
        $hafalanTerbaru = Hafalan::whereIn('santri_id', $santriIds)
            ->with(['santri', 'ustadz'])
            ->latest()
            ->take(10)
            ->get();

        // Statistik per santri
        $statistik = [];
        foreach ($santri as $s) {
            $totalHafalan = $s->hafalan()->count();
            $hafalanSelesai = $s->hafalan()->where('status', 'selesai')->count();
            $totalJuz = $s->hafalan()->where('status', 'selesai')->distinct('juz')->count('juz');
            $progres = $s->progres_hafalan;

            $statistik[$s->id] = [
                'nama' => $s->nama,
                'total_hafalan' => $totalHafalan,
                'hafalan_selesai' => $hafalanSelesai,
                'total_juz' => $totalJuz,
                'progres' => $progres,
            ];
        }

        return view('parent.dashboard', compact('santri', 'statistik', 'hafalanTerbaru'));
    }

    /**
     * Detail hafalan santri tertentu
     */
    public function showSantri($id)
    {
        $user = Auth::user();
        $santri = Santri::where('orang_tua_id', $user->id)->findOrFail($id);
        $this->authorize('view', $santri);

        $hafalan = Hafalan::where('santri_id', $santri->id)
            ->with('ustadz')
            ->latest()
            ->paginate(10);

        // Statistik hafalan per juz
        $statistikJuz = Hafalan::where('santri_id', $santri->id)
            ->selectRaw('juz, status, COUNT(*) as total')
            ->groupBy('juz', 'status')
            ->get()
            ->groupBy('juz');

        return view('parent.santri.show', compact('santri', 'hafalan', 'statistikJuz'));
    }

    /**
     * Dengarkan rekaman hafalan
     */
    public function listenAudio($hafalanId)
    {
        $hafalan = Hafalan::findOrFail($hafalanId);
        
        // Authorization: parent can only listen to their own children's recordings
        $user = Auth::user();
        $santri = Santri::where('orang_tua_id', $user->id)->where('id', $hafalan->santri_id)->firstOrFail();
        
        if (!$hafalan->audio_path || !Storage::disk('public')->exists($hafalan->audio_path)) {
            abort(404, 'Rekaman audio tidak ditemukan');
        }

        return response()->file(Storage::disk('public')->path($hafalan->audio_path));
    }

    /**
     * Download rekaman hafalan
     */
    public function downloadAudio($hafalanId)
    {
        $hafalan = Hafalan::findOrFail($hafalanId);
        
        // Authorization: parent can only download their own children's recordings
        $user = Auth::user();
        $santri = Santri::where('orang_tua_id', $user->id)->where('id', $hafalan->santri_id)->firstOrFail();
        
        if (!$hafalan->audio_path || !Storage::disk('public')->exists($hafalan->audio_path)) {
            abort(404, 'Rekaman audio tidak ditemukan');
        }

        $filename = $hafalan->audio_filename ?? 'hafalan-' . $hafalan->id . '.webm';
        
        return Storage::disk('public')->download(
            $hafalan->audio_path,
            $hafalan->santri->nama . '-' . $hafalan->surat . '-' . date('d-m-Y', strtotime($hafalan->tanggal_setoran)) . '.webm'
        );
    }

    /**
     * Tampilkan notifikasi orang tua
     */
    public function notifications()
    {
        $user = Auth::user();
        $santriIds = Santri::where('orang_tua_id', $user->id)->pluck('id');

        $notifications = \App\Models\HafalanNotification::where(function ($query) use ($user, $santriIds) {
                $query->where('parent_id', $user->id)
                    ->orWhereIn('santri_id', $santriIds);
            })
            ->with(['hafalan', 'santri', 'ustadz'])
            ->latest()
            ->paginate(20);

        return view('parent.notifications', compact('notifications'));
    }

    /**
     * Tandai notifikasi sebagai sudah dibaca
     */
    public function markNotificationAsRead($notificationId)
    {
        $notification = \App\Models\HafalanNotification::findOrFail($notificationId);
        
        // Authorization check
        $user = Auth::user();
        if ($notification->parent_id !== $user->id) {
            abort(403, 'Anda tidak berhak mengakses notifikasi ini');
        }

        $notification->update(['status' => 'read']);

        return response()->json(['success' => true]);
    }
}
