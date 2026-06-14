<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Santri;
use App\Models\Hafalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Dashboard Admin
     */
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalSantri = Santri::count();
        $totalHafalan = Hafalan::count();
        $hafalanSelesai = Hafalan::where('status', 'selesai')->count();
        
        // Get latest santri with photos (ordered by creation date, not update date)
        $santri = Santri::whereNotNull('foto')
            ->where('foto', '!=', '')
            ->latest('created_at')
            ->limit(8)
            ->get();
        
        $statistikHafalan = Hafalan::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        return view('admin.dashboard', compact('totalUsers', 'totalSantri', 'totalHafalan', 'hafalanSelesai', 'statistikHafalan', 'santri'));
    }

    /**
     * Kelola User
     */
    public function users()
    {
        // determine which tab should be active (default 'admin')
        $activeTab = request('role', 'admin');

        // separate users by role so we can display them in different tables/tabs
        $admins = User::with('santri')
            ->where('role', 'admin')
            ->latest()
            ->paginate(10, ['*'], 'admin_page');

        $ustadz = User::with('santri')
            ->where('role', 'ustadz')
            ->latest()
            ->paginate(10, ['*'], 'ustadz_page');

        $orangtua = User::with('santri')
            ->where('role', 'orang_tua')
            ->latest()
            ->paginate(10, ['*'], 'orangtua_page');

        return view('admin.users.index', compact('admins', 'ustadz', 'orangtua', 'activeTab'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(\App\Http\Requests\Admin\StoreUserRequest $request)
    {
        // validation already handled by StoreUserRequest
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users', ['role' => $request->role])->with('success', 'User berhasil ditambahkan.');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(\App\Http\Requests\Admin\UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users', ['role' => $request->role])->with('success', 'User berhasil diperbarui.');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        // after deletion we don't know role, so just go to admin tab
        return redirect()->route('admin.users', ['role' => 'admin'])->with('success', 'User berhasil dihapus.');
    }

    /**
     * Kelola Santri
     */
    public function santri()
    {
        $laki = Santri::where('jenis_kelamin', 'L')->with('orangTua')->latest()->paginate(10, ['*'], 'l_page');
        $perempuan = Santri::where('jenis_kelamin', 'P')->with('orangTua')->latest()->paginate(10, ['*'], 'p_page');

        return view('admin.santri.index', compact('laki', 'perempuan'));
    }

    public function createSantri()
    {
        $this->authorize('create', Santri::class);
        $orangTua = User::where('role', \App\Models\User::ROLE_ORANG_TUA)->get();
        return view('admin.santri.create', compact('orangTua'));
    }

    public function storeSantri(\App\Http\Requests\Admin\StoreSantriRequest $request)
    {
        $this->authorize('create', Santri::class);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            $filename = time().'_'.$request->file('foto')->getClientOriginalName();
            $path = $request->file('foto')->storeAs('santri', $filename, 'public');
            $data['foto'] = $path;
        }

        Santri::create($data);

        return redirect()
            ->route('admin.santri')
            ->with('success', 'Santri berhasil ditambahkan.');
    }

    public function editSantri($id)
    {
        $santri = Santri::findOrFail($id);
        $this->authorize('update', $santri);
        $orangTua = User::where('role', \App\Models\User::ROLE_ORANG_TUA)->get();
        return view('admin.santri.edit', compact('santri', 'orangTua'));
    }

    public function updateSantri(\App\Http\Requests\Admin\UpdateSantriRequest $request, $id)
    {
        $santri = Santri::findOrFail($id);

        $this->authorize('update', $santri);

        $data = $request->except('foto');

        if ($request->hasFile('foto')) {
            if ($santri->foto && Storage::disk('public')->exists($santri->foto)) {
                Storage::disk('public')->delete($santri->foto);
            }

            $filename = time().'_'.$request->file('foto')->getClientOriginalName();
            $path = $request->file('foto')->storeAs('santri', $filename, 'public');
            $data['foto'] = $path;
        }

        $santri->update($data);

        return redirect()
            ->route('admin.santri')
            ->with('success', 'Santri berhasil diperbarui.');
    }

    public function destroySantri($id)
{
    $santri = Santri::findOrFail($id);

    $this->authorize('delete', $santri);

    if ($santri->foto && Storage::disk('public')->exists($santri->foto)) {
        Storage::disk('public')->delete($santri->foto);
    }

    $santri->delete();

    return redirect()
        ->route('admin.santri')
        ->with('success', 'Santri berhasil dihapus.');
}
    /**
     * Kelola Hafalan — menampilkan daftar santri
     */
    public function hafalan()
    {
        $santriData = Santri::with(['hafalan' => function($q) {
            $q->latest('tanggal_setoran');
        }])->get()->map(function($santri) {
            $lastHafalan = $santri->hafalan->first();
            return [
                'id'              => $santri->id,
                'nama'            => $santri->nama,
                'jenis_kelamin'   => $santri->jenis_kelamin,
                'total_setoran'   => $santri->hafalan->count(),
                'juz_terakhir'    => $lastHafalan?->juz ?? '-',
                'surat_terakhir'  => $lastHafalan?->surat ?? '-',
                'status'          => $this->getStatusBadgeClass('at_risk'),
            ];
        });

        $lakiLaki  = $santriData->where('jenis_kelamin', 'L')->values();
        $perempuan = $santriData->where('jenis_kelamin', 'P')->values();
        $totalSetoran = Hafalan::whereDate('tanggal_setoran', today())->count();
        $santriAktifMingguIni = Hafalan::whereDate('tanggal_setoran', '>=', now()->subWeek())->distinct('santri_id')->count('santri_id');

        return view('admin.hafalan.index', compact('lakiLaki', 'perempuan', 'totalSetoran', 'santriAktifMingguIni'));
    }

    /**
     * Helper: get status badge class
     */
    private function getStatusBadgeClass(string $status): array
    {
        return match($status) {
            'at_risk' => ['label' => 'Berisiko', 'class' => 'bg-danger text-white', 'icon' => 'exclamation-circle'],
            'on_track' => ['label' => 'On Track', 'class' => 'bg-success text-white', 'icon' => 'check-circle'],
            'excellent' => ['label' => 'Excellent', 'class' => 'bg-success text-white', 'icon' => 'star-fill'],
            default => ['label' => 'Unknown', 'class' => 'bg-secondary text-white', 'icon' => 'question-circle'],
        };
    }

    /**
     * Ekspor Data Hafalan ke PDF
     */
    public function exportHafalanPdf()
    {
        $hafalanData = Hafalan::with(['santri', 'ustadz'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalHafalan = $hafalanData->count();
        $hafalanSelesai = $hafalanData->where('status', 'selesai')->count();
        $statistikByStatus = $hafalanData->groupBy('status')->map(function($items) {
            return $items->count();
        });

        $pdf = Pdf::loadView('admin.hafalan.pdf-export', compact('hafalanData', 'totalHafalan', 'hafalanSelesai', 'statistikByStatus'));
        
        $filename = 'Backup_Hafalan_' . date('Y-m-d_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Halaman Detail Hafalan Santri dengan Tab Riwayat & Analytics
     */
    public function hafalanSantriShow(int $santriId)
    {
        $santri = Santri::findOrFail($santriId);

        $hafalans = Hafalan::where('santri_id', $santriId)
                        ->with('ustadz')
                        ->latest('tanggal_setoran')
                        ->paginate(15);

        $totalSetoran = Hafalan::where('santri_id', $santriId)->count();

        return view('admin.hafalan.santri-detail', compact(
            'santri', 'hafalans', 'totalSetoran'
        ));
    }

    /**
     * Export Hafalan Per Santri ke PDF
     */
    public function exportHafalanPdfPerSantri(int $santriId)
    {
        $santri = Santri::findOrFail($santriId);
        
        $hafalans = Hafalan::where('santri_id', $santriId)
                    ->with('ustadz')
                    ->latest('tanggal_setoran')
                    ->get();

        $totalSetoran = $hafalans->count();

        $pdf = Pdf::loadView(
            'admin.hafalan.pdf-export-santri',
            compact(
                'santri', 'hafalans', 'totalSetoran'
            )
        );
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'Hafalan_' . str_replace(' ', '_', $santri->nama) . '.pdf';
        return $pdf->download($filename);
    }
}