@extends('layouts.app')

@section('title', 'Kelola Santri - SIMHAFAL')

@section('content')
@push('styles')
<style>
    /* Make santri tab labels visible on light card background */
    #santriTabs .nav-link { color: #212529 !important; }
    #santriTabs .nav-link.active { color: #fff !important; background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; }
</style>
@endpush

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-person-badge"></i> Kelola Santri</h2>
    <a href="{{ route('admin.santri.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Santri
    </a>
</div>

<div class="card">
    <div class="card-body">
        <ul class="nav nav-tabs mb-3" id="santriTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="laki-tab" data-bs-toggle="tab" data-bs-target="#laki" type="button" role="tab" aria-controls="laki" aria-selected="true">Laki-laki ({{ $laki->total() }})</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="perempuan-tab" data-bs-toggle="tab" data-bs-target="#perempuan" type="button" role="tab" aria-controls="perempuan" aria-selected="false">Perempuan ({{ $perempuan->total() }})</button>
            </li>
        </ul>

        <div class="tab-content" id="santriTabsContent">
            <div class="tab-pane fade show active" id="laki" role="tabpanel" aria-labelledby="laki-tab">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Foto</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Orang Tua</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($laki as $s)
                                <tr>
                                    <td>{{ $loop->iteration + ($laki->currentPage() - 1) * $laki->perPage() }}</td>
                                    <td>
                                        @if($s->foto)
                                            <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}" class="rounded-circle" width="40" height="40">
                                        @else
                                            <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-person text-white"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $s->nis }}</td>
                                    <td>{{ $s->nama }}</td>
                                    <td>{{ $s->orangTua ? $s->orangTua->name : '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.santri.edit', $s->id) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                        <form action="{{ route('admin.santri.destroy', $s->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $laki->appends(request()->except('l_page'))->links() }}
                </div>
            </div>

            <div class="tab-pane fade" id="perempuan" role="tabpanel" aria-labelledby="perempuan-tab">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Foto</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Orang Tua</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($perempuan as $s)
                                <tr>
                                    <td>{{ $loop->iteration + ($perempuan->currentPage() - 1) * $perempuan->perPage() }}</td>
                                    <td>
                                        @if($s->foto)
                                            <img src="{{ $s->foto_url }}" alt="{{ $s->nama }}" class="rounded-circle" width="40" height="40">
                                        @else
                                            <div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-person text-white"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $s->nis }}</td>
                                    <td>{{ $s->nama }}</td>
                                    <td>{{ $s->orangTua ? $s->orangTua->name : '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.santri.edit', $s->id) }}" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                        <form action="{{ route('admin.santri.destroy', $s->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $perempuan->appends(request()->except('p_page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
