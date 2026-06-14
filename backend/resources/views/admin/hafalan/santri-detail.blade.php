@extends('layouts.app')

@push('styles')
<style>
    .nav-tabs .nav-link { color: #212529 !important; }
    .nav-tabs .nav-link.active { color: #fff !important; background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; }
</style>
@endpush

@section('title', 'Detail Hafalan ' . $santri->nama . ' - SIMHAFAL')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.hafalan') }}" class="btn btn-sm btn-secondary mb-2">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h2 class="mb-1">
            {{ $santri->nama }}
        </h2>
        <small class="text-muted">{{ $santri->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.hafalan.santri.pdf', $santri->id) }}" class="btn btn-danger" target="_blank">
            <i class="bi bi-file-pdf"></i> Ekspor PDF
        </a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="row g-0">
                <div class="col-md-4 bg-light d-flex flex-column align-items-center justify-content-center p-4">
                    @if($santri->foto_url)
                        <img src="{{ $santri->foto_url }}" alt="{{ $santri->nama }}" class="img-fluid rounded mb-3" style="width: 200px; height: 200px; object-fit: cover; border: 2px solid #1a472a;">
                    @else
                        <div class="bg-white rounded shadow-sm d-flex align-items-center justify-content-center mb-3" style="width: 200px; height: 200px; border: 2px solid #1a472a;">
                            <i class="bi bi-person" style="font-size: 4rem; color: #6c757d;"></i>
                        </div>
                    @endif
                    <span class="badge bg-primary text-white px-3 py-2">
                        {{ $santri->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </span>
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title text-primary mb-3">Profil Santri</h5>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">NIS</div>
                            <div class="col-sm-8"><strong>{{ $santri->nis ?? '-' }}</strong></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Nama</div>
                            <div class="col-sm-8"><strong>{{ $santri->nama }}</strong></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">TTL</div>
                            <div class="col-sm-8">{{ $santri->tempat_lahir ?? '-' }} / {{ $santri->tanggal_lahir ? $santri->tanggal_lahir->format('d/m/Y') : '-' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Alamat</div>
                            <div class="col-sm-8">{{ $santri->alamat ?? '-' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">No. HP</div>
                            <div class="col-sm-8">{{ $santri->no_hp ?? '-' }}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Total Setoran</div>
                            <div class="col-sm-8"><strong>{{ $totalSetoran }} hafalan</strong></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Juz Selesai</div>
                            <div class="col-sm-8"><strong>{{ $santri->total_juz }}</strong></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4 text-muted">Progres</div>
                            <div class="col-sm-8">
                                <div class="progress" style="height: 18px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $santri->progres_hafalan }}%;">
                                        {{ number_format($santri->progres_hafalan, 1) }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat" type="button" role="tab">
                    <i class="bi bi-list-ul"></i> Riwayat Hafalan
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="riwayat" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Juz</th>
                                <th>Surat</th>
                                <th>Ayat</th>
                                <th>Status</th>
                                <th>Ustadz</th>
                                <th>Tanggal Setoran</th>
                                <th>Audio</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hafalans as $h)
                                <tr>
                                    <td>{{ $loop->iteration + ($hafalans->currentPage() - 1) * $hafalans->perPage() }}</td>
                                    <td>{{ $h->juz ?? '-' }}</td>
                                    <td>{{ $h->surat ?? '-' }}</td>
                                    <td>{{ $h->ayat_dari && $h->ayat_sampai ? $h->ayat_dari . '-' . $h->ayat_sampai : '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $h->status == 'selesai' ? 'success' : ($h->status == 'sedang' ? 'warning' : ($h->status == 'mengulang' ? 'danger' : 'secondary')) }}">
                                            {{ ucfirst($h->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $h->ustadz ? $h->ustadz->name : '-' }}</td>
                                    <td>{{ $h->tanggal_setoran ? $h->tanggal_setoran->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        @if($h->audio_path)
                                            <a href="{{ route('parent.hafalan.listen', $h->id) }}" class="btn btn-sm btn-success" title="Dengarkan Audio">
                                                <i class="bi bi-play-circle-fill"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-muted">Belum ada riwayat hafalan</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($hafalans->hasPages())
                    <div class="mt-3">
                        {{ $hafalans->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
