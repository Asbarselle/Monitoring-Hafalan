@extends('layouts.app')

@section('title', 'Daftar Santri - SIMHAFAL')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people"></i> Daftar Santri</h2>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIS</th>
                        <th>Nama</th>
                        <th>Total Hafalan</th>
                        <th>Hafalan Selesai</th>
                        <th>Progres</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($santri as $s)
                        <tr>
                            <td>{{ $loop->iteration + ($santri->currentPage() - 1) * $santri->perPage() }}</td>
                            <td>{{ $s->nis }}</td>
                            <td>{{ $s->nama }}</td>
                            <td>{{ $s->hafalan->count() }}</td>
                            <td>{{ $s->hafalan->where('status', 'selesai')->count() }}</td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ $s->progres_hafalan }}%">
                                        {{ number_format($s->progres_hafalan, 1) }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('ustadz.santri.show', $s->id) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $santri->links() }}
        </div>
    </div>
</div>
@endsection
