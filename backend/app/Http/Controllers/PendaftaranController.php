<?php

namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin'])->only(['index', 'approve', 'reject']);
    }

    public function create()
    {
        return view('pendaftaran.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string',
            'nama_wali' => 'required|string|max:255',
            'no_hp_wali' => 'required|string|max:20',
            'email_wali' => 'required|email|max:255',
        ]);

        Pendaftaran::create($validated);

        return redirect()->route('pendaftaran.create')
            ->with('success', 'Pendaftaran berhasil dikirim! Admin akan menghubungi Anda.');
    }

    public function index()
    {
        $pending = Pendaftaran::where('status', 'menunggu')->latest()->get();
        $approved = Pendaftaran::where('status', 'disetujui')->latest()->get();
        $rejected = Pendaftaran::where('status', 'ditolak')->latest()->get();

        return view('admin.pendaftaran.index', compact('pending', 'approved', 'rejected'));
    }

    public function approve(Request $request, $id)
    {
        $p = Pendaftaran::findOrFail($id);
        $p->update([
            'status' => 'disetujui',
            'catatan_admin' => $request->catatan_admin,
        ]);

        return back()->with('success', 'Pendaftaran disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $p = Pendaftaran::findOrFail($id);
        $p->update([
            'status' => 'ditolak',
            'catatan_admin' => $request->catatan_admin,
        ]);

        return back()->with('success', 'Pendaftaran ditolak.');
    }
}
