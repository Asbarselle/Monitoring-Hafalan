@extends('layouts.app')

@section('title', 'Dashboard Ustadz - SIMHAFAL')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer2"></i> Dashboard Ustadz</h2>
</div>

<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Santri</h6>
                        <h3 class="mb-0">{{ $totalSantri }}</h3>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-people" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Hafalan</h6>
                        <h3 class="mb-0">{{ $totalHafalan }}</h3>
                    </div>
                    <div class="text-info">
                        <i class="bi bi-book" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Hafalan Selesai</h6>
                        <h3 class="mb-0">{{ $hafalanSelesai }}</h3>
                    </div>
                    <div class="text-success">
                        <i class="bi bi-check-circle" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Menu Utama</h5>
            </div>
            <div class="card-body">
                <div class="list-group">
                    <a href="{{ route('ustadz.santri.index') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-people"></i> Daftar Santri
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Hafalan Terbaru</h5>
            </div>
            <div class="card-body">
                @forelse($hafalanTerbaru as $h)
                    <div class="border-bottom pb-2 mb-2">
                        <strong>{{ $h->santri->nama }}</strong><br>
                        <small class="text-muted">
                            Juz {{ $h->juz ?? '-' }} | 
                            {{ $h->surat ?? '-' }} | 
                            Status: <span class="badge bg-{{ $h->status == 'selesai' ? 'success' : 'warning' }}">
                                {{ ucfirst($h->status) }}
                            </span>
                        </small>
                    </div>
                @empty
                    <p class="text-muted mb-0">Belum ada hafalan</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
