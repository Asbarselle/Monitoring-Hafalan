@extends('layouts.app')

@section('title', 'Dashboard Admin - SIMHAFAL')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer2"></i> Dashboard Admin</h2>
</div>

<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total User</h6>
                        <h3 class="mb-0">{{ $totalUsers }}</h3>
                    </div>
                    <div class="text-primary">
                        <i class="bi bi-people" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Santri</h6>
                        <h3 class="mb-0">{{ $totalSantri }}</h3>
                    </div>
                    <div class="text-success">
                        <i class="bi bi-person-badge" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
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
    <div class="col-md-3 mb-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Hafalan Selesai</h6>
                        <h3 class="mb-0">{{ $hafalanSelesai }}</h3>
                    </div>
                    <div class="text-warning">
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
                    <a href="{{ route('admin.users') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-people"></i> Kelola User
                    </a>
                    <a href="{{ route('admin.santri') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-person-badge"></i> Kelola Santri
                    </a>
                    <a href="{{ route('admin.hafalan') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-book"></i> Data Hafalan
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Statistik Hafalan</h5>
            </div>
            <div class="card-body">
                @foreach($statistikHafalan as $stat)
                    <div class="mb-2">
                        <div class="d-flex justify-content-between mb-1">
                            <span>{{ ucfirst($stat->status) }}</span>
                            <span>{{ $stat->total }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: {{ ($stat->total / $totalHafalan) * 100 }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Foto Santri Terbaru</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($santri as $s)
                        <div class="col-md-3 col-sm-4 col-6 mb-3">
                            <div class="text-center">
                                @if($s->foto)
                                    <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}" 
                                         class="img-thumbnail rounded-circle" 
                                         style="width: 120px; height: 120px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" 
                                         style="width: 120px; height: 120px;">
                                        <i class="bi bi-person text-white" style="font-size: 2rem;"></i>
                                    </div>
                                @endif
                                <p class="mt-2 mb-0 small font-weight-bold">{{ $s->nama }}</p>
                                <small class="text-muted">{{ $s->nis }}</small>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-center text-muted">Belum ada santri terdaftar</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
