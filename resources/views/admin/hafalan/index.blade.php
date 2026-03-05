@extends('layouts.app')

@section('title', 'Data Hafalan - SIMHAFAL')

@section('content')
@push('styles')
<style>
    /* Ensure hafalan tab labels contrast on light background */
    #hafalanTabs .nav-link { color: #212529 !important; }
    #hafalanTabs .nav-link.active { color: #fff !important; background-color: var(--primary-color) !important; border-color: var(--primary-color) !important; }
</style>
@endpush

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-book"></i> Data Hafalan</h2>
</div>

<div class="card">
    <div class="card-body">
        <ul class="nav nav-tabs mb-3" id="hafalanTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="hl-tab" data-bs-toggle="tab" data-bs-target="#hl" type="button" role="tab" aria-controls="hl" aria-selected="true">Laki-laki ({{ $l_hafalan->total() }})</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="hp-tab" data-bs-toggle="tab" data-bs-target="#hp" type="button" role="tab" aria-controls="hp" aria-selected="false">Perempuan ({{ $p_hafalan->total() }})</button>
            </li>
        </ul>

        <div class="tab-content" id="hafalanTabsContent">
            <div class="tab-pane fade show active" id="hl" role="tabpanel" aria-labelledby="hl-tab">
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
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($l_hafalan as $h)
                                <tr>
                                    <td>{{ $loop->iteration + ($l_hafalan->currentPage() - 1) * $l_hafalan->perPage() }}</td>
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
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $l_hafalan->appends(request()->except('hl_page'))->links() }}
                </div>
            </div>

            <div class="tab-pane fade" id="hp" role="tabpanel" aria-labelledby="hp-tab">
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
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($p_hafalan as $h)
                                <tr>
                                    <td>{{ $loop->iteration + ($p_hafalan->currentPage() - 1) * $p_hafalan->perPage() }}</td>
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
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center">Tidak ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $p_hafalan->appends(request()->except('hp_page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
