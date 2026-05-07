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
        
        // Get latest santri with photos
        $santri = Santri::where('foto', '!=', null)
            ->latest()
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
            $data['foto'] = $request->file('foto')->store('santri', 'public');
        }

        Santri::create($data);

        return redirect()->route('admin.santri')->with('success', 'Santri berhasil ditambahkan.');
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
            if ($santri->foto) {
                Storage::disk('public')->delete($santri->foto);
            }
            $data['foto'] = $request->file('foto')->store('santri', 'public');
        }

        $santri->update($data);

        return redirect()->route('admin.santri')->with('success', 'Santri berhasil diperbarui.');
    }

    public function destroySantri($id)
    {
        $santri = Santri::findOrFail($id);
        $this->authorize('delete', $santri);
        if ($santri->foto) {
            Storage::disk('public')->delete($santri->foto);
        }
        $santri->delete();

        return redirect()->route('admin.santri')->with('success', 'Santri berhasil dihapus.');
    }

    /**
     * Kelola Hafalan
     */
    public function hafalan()
    {
        $l_hafalan = Hafalan::whereHas('santri', function($q) {
            $q->where('jenis_kelamin', 'L');
        })->with(['santri', 'ustadz'])->latest()->paginate(10, ['*'], 'hl_page');

        $p_hafalan = Hafalan::whereHas('santri', function($q) {
            $q->where('jenis_kelamin', 'P');
        })->with(['santri', 'ustadz'])->latest()->paginate(10, ['*'], 'hp_page');

        return view('admin.hafalan.index', compact('l_hafalan', 'p_hafalan'));
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
}
