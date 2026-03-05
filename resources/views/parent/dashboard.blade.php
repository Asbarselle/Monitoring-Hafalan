@extends('layouts.app')

@section('title', 'Dashboard Orang Tua - SIMHAFAL')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-speedometer2"></i> Dashboard Monitoring Hafalan</h2>
</div>

@if($santri->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Belum ada data santri yang terhubung dengan akun Anda. 
        Silakan hubungi administrator untuk menghubungkan akun Anda dengan data santri.
    </div>
@else
    <div class="row mb-4">
        @foreach($santri as $s)
            <div class="col-md-6 mb-3">
                <div class="card stat-card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-person-badge"></i> {{ $s->nama }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>NIS:</strong> {{ $s->nis }}<br>
                            <strong>Total Juz:</strong> {{ $statistik[$s->id]['total_juz'] ?? 0 }} / 30
                        </div>
                        <div class="mb-2">
                            <strong>Progres Hafalan:</strong>
                            <div class="progress mt-2">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: {{ $statistik[$s->id]['progres'] ?? 0 }}%">
                                    {{ number_format($statistik[$s->id]['progres'] ?? 0, 1) }}%
                                </div>
                            </div>
                        </div>
                        <div class="row text-center mt-3">
                            <div class="col-6">
                                <div class="border-end">
                                    <h4 class="mb-0">{{ $statistik[$s->id]['total_hafalan'] ?? 0 }}</h4>
                                    <small class="text-muted">Total Hafalan</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <h4 class="mb-0">{{ $statistik[$s->id]['hafalan_selesai'] ?? 0 }}</h4>
                                <small class="text-muted">Selesai</small>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('parent.santri.show', $s->id) }}" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-eye"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-clock-history"></i> Hafalan Terbaru</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Santri</th>
                            <th>Juz</th>
                            <th>Surat</th>
                            <th>Ayat</th>
                            <th>Status</th>
                            <th>Ustadz</th>
                            <th>Tanggal Setoran</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hafalanTerbaru as $h)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $h->santri->nama }}</td>
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
                                <td>{{ \Illuminate\Support\Str::limit($h->catatan ?? '-', 30) }}</td>
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
@endif
@endsection
