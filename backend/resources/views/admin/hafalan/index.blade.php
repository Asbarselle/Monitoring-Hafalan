@extends('layouts.app')

@section('title', 'Data Hafalan Santri - SIMHAFAL')

@push('styles')
<style>
    .nav-tabs .nav-link { color: #000 !important; }
    .nav-tabs .nav-link.active { color: #fff !important; background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-book"></i> Data Hafalan Santri</h2>
    <a href="{{ route('admin.hafalan.export-pdf') }}" class="btn btn-danger" target="_blank" title="Ekspor semua data hafalan dalam format PDF">
        <i class="bi bi-file-pdf"></i> Ekspor Semua PDF
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-4 col-6">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="text-muted">Total Santri</h6>
                <h3>{{ $lakiLaki->count() + $perempuan->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="text-muted">Total Setoran Hari Ini</h6>
                <h3>{{ $totalSetoran }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-6">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="text-muted">Santri Aktif Minggu Ini</h6>
                <h3>{{ $santriAktifMingguIni }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="laki-tab" data-bs-toggle="tab" data-bs-target="#laki" type="button" role="tab">
                    <i class="bi bi-person"></i> Laki-laki ({{ $lakiLaki->count() }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="perempuan-tab" data-bs-toggle="tab" data-bs-target="#perempuan" type="button" role="tab">
                    <i class="bi bi-person"></i> Perempuan ({{ $perempuan->count() }})
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="laki" role="tabpanel">
                @if($lakiLaki->isNotEmpty())
                    <div class="row row-cols-1 row-cols-md-3 g-3">
                        @foreach($lakiLaki as $santri)
                            <div class="col">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title mb-2">{{ $santri['nama'] }}</h5>
                                        <div class="mb-2">
                                            <span class="badge bg-primary">Laki-laki</span>
                                        </div>
                                        <p class="card-text small">
                                            <strong>Total Setoran:</strong> {{ $santri['total_setoran'] }} hafalan<br>
                                            <strong>Juz Terakhir:</strong> {{ $santri['juz_terakhir'] }} | {{ $santri['surat_terakhir'] }}
                                        </p>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.hafalan.santri.show', $santri['id']) }}" class="btn btn-sm btn-success flex-grow-1">
                                                <i class="bi bi-eye"></i> Lihat Detail
                                            </a>
                                            <a href="{{ route('admin.hafalan.santri.pdf', $santri['id']) }}" class="btn btn-sm btn-outline-danger" target="_blank" title="Download PDF">
                                                <i class="bi bi-file-pdf"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">Belum ada santri laki-laki terdaftar.</div>
                @endif
            </div>

            <div class="tab-pane fade" id="perempuan" role="tabpanel">
                @if($perempuan->isNotEmpty())
                    <div class="row row-cols-1 row-cols-md-3 g-3">
                        @foreach($perempuan as $santri)
                            <div class="col">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <h5 class="card-title mb-2">{{ $santri['nama'] }}</h5>
                                        <div class="mb-2">
                                            <span class="badge bg-info">Perempuan</span>
                                        </div>
                                        <p class="card-text small">
                                            <strong>Total Setoran:</strong> {{ $santri['total_setoran'] }} hafalan<br>
                                            <strong>Juz Terakhir:</strong> {{ $santri['juz_terakhir'] }} | {{ $santri['surat_terakhir'] }}
                                        </p>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.hafalan.santri.show', $santri['id']) }}" class="btn btn-sm btn-success flex-grow-1">
                                                <i class="bi bi-eye"></i> Lihat Detail
                                            </a>
                                            <a href="{{ route('admin.hafalan.santri.pdf', $santri['id']) }}" class="btn btn-sm btn-outline-danger" target="_blank" title="Download PDF">
                                                <i class="bi bi-file-pdf"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">Belum ada santri perempuan terdaftar.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
