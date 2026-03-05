@extends('layouts.app')

@section('title', 'Detail Santri - SIMHAFAL')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-person-badge"></i> Detail Santri: {{ $santri->nama }}</h2>
    <a href="{{ route('ustadz.santri.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informasi Santri</h5>
            </div>
            <div class="card-body">
                <p><strong>NIS:</strong> {{ $santri->nis }}</p>
                <p><strong>Nama:</strong> {{ $santri->nama }}</p>
                <p><strong>Jenis Kelamin:</strong> {{ $santri->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                <p><strong>Alamat:</strong> {{ $santri->alamat ?? '-' }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Statistik Hafalan</h5>
            </div>
            <div class="card-body">
                <p><strong>Total Juz:</strong> {{ $santri->total_juz }} / 30</p>
                <p><strong>Progres:</strong></p>
                <div class="progress mb-2">
                    <div class="progress-bar" role="progressbar" 
                         style="width: {{ $santri->progres_hafalan }}%">
                        {{ number_format($santri->progres_hafalan, 1) }}%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Data Hafalan</h5>
        <a href="{{ route('ustadz.hafalan.create', $santri->id) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Hafalan
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Juz</th>
                        <th>Surat</th>
                        <th>Ayat</th>
                        <th>Status</th>
                        <th>Tanggal Setoran</th>
                        <th>Nilai</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($santri->hafalan as $h)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $h->juz ?? '-' }}</td>
                            <td>{{ $h->surat ?? '-' }}</td>
                            <td>{{ $h->ayat_dari && $h->ayat_sampai ? $h->ayat_dari . '-' . $h->ayat_sampai : '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $h->status == 'selesai' ? 'success' : ($h->status == 'sedang' ? 'warning' : ($h->status == 'mengulang' ? 'danger' : 'secondary')) }}">
                                    {{ ucfirst($h->status) }}
                                </span>
                            </td>
                            <td>{{ $h->tanggal_setoran ? $h->tanggal_setoran->format('d/m/Y') : '-' }}</td>
                            <td>{{ $h->nilai ?? '-' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($h->catatan ?? '-', 30) }}</td>
                            <td>
                                <a href="{{ route('ustadz.hafalan.edit', $h->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('ustadz.hafalan.destroy', $h->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                            onclick="return confirm('Yakin ingin menghapus?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Belum ada data hafalan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
