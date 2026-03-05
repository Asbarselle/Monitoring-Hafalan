<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\Hafalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
