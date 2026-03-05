@extends('layouts.app')

@section('title', 'Detail Hafalan - SIMHAFAL')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-book"></i> Detail Hafalan: {{ $santri->nama }}</h2>
    <a href="{{ route('parent.dashboard') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informasi Santri</h5>
            </div>
            <div class="card-body">
                <p><strong>NIS:</strong> {{ $santri->nis }}</p>
                <p><strong>Nama:</strong> {{ $santri->nama }}</p>
                <p><strong>Jenis Kelamin:</strong> {{ $santri->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Statistik Hafalan</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <h3 class="mb-0">{{ $santri->total_juz }}</h3>
                        <small class="text-muted">Juz Selesai</small>
                    </div>
                    <div class="col-4">
                        <h3 class="mb-0">{{ $santri->hafalan->count() }}</h3>
                        <small class="text-muted">Total Hafalan</small>
                    </div>
                    <div class="col-4">
                        <h3 class="mb-0">{{ number_format($santri->progres_hafalan, 1) }}%</h3>
                        <small class="text-muted">Progres</small>
                    </div>
                </div>
                <div class="progress mt-3">
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
    <div class="card-header">
        <h5 class="mb-0">Data Hafalan</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Juz</th>
                        <th>Surat</th>
                        <th>Ayat Dari</th>
                        <th>Ayat Sampai</th>
                        <th>Status</th>
                        <th>Tanggal Setoran</th>
                        <th>Nilai</th>
                        <th>Catatan</th>
                        <th>Ustadz</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hafalan as $h)
                        <tr>
                            <td>{{ $loop->iteration + ($hafalan->currentPage() - 1) * $hafalan->perPage() }}</td>
                            <td>{{ $h->juz ?? '-' }}</td>
                            <td>{{ $h->surat ?? '-' }}</td>
                            <td>{{ $h->ayat_dari ?? '-' }}</td>
                            <td>{{ $h->ayat_sampai ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $h->status == 'selesai' ? 'success' : ($h->status == 'sedang' ? 'warning' : ($h->status == 'mengulang' ? 'danger' : 'secondary')) }}">
                                    {{ ucfirst($h->status) }}
                                </span>
                            </td>
                            <td>{{ $h->tanggal_setoran ? $h->tanggal_setoran->format('d/m/Y') : '-' }}</td>
                            <td>{{ $h->nilai ?? '-' }}</td>
                            <td>{{ $h->catatan ?? '-' }}</td>
                            <td>{{ $h->ustadz ? $h->ustadz->name : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Belum ada data hafalan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $hafalan->links() }}
        </div>
    </div>
</div>

@if(isset($statistikJuz) && $statistikJuz->isNotEmpty())
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Statistik Per Juz</h5>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($statistikJuz as $juz => $data)
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6>Juz {{ $juz }}</h6>
                            @foreach($data as $stat)
                                <small class="d-block">
                                    {{ ucfirst($stat->status) }}: {{ $stat->total }}
                                </small>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
