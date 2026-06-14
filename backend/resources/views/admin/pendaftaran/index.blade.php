@extends('layouts.app')

@section('title', 'Manajemen Pendaftaran Santri - SIMHAFAL')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Manajemen Pendaftaran Santri</h5>
            </div>
            <div class="card-body">
                <style>
                    .nav-tabs .nav-link {
                        color: #000 !important;
                    }
                    .nav-tabs .nav-link.active {
                        background-color: #198754 !important;
                        color: #fff !important;
                        border-color: #198754 #198754 #fff !important;
                    }
                </style>
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="menunggu-tab" data-bs-toggle="tab" data-bs-target="#menunggu" type="button" role="tab">Menunggu</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="disetujui-tab" data-bs-toggle="tab" data-bs-target="#disetujui" type="button" role="tab">Disetujui</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="ditolak-tab" data-bs-toggle="tab" data-bs-target="#ditolak" type="button" role="tab">Ditolak</button>
                    </li>
                </ul>
                <div class="tab-content mt-4">
                    <div class="tab-pane fade show active" id="menunggu" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Nama Wali</th>
                                        <th>Email Wali</th>
                                        <th>Tanggal Daftar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pending as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->nama }}
                                            <td>{{ $item->jenis_kelamin }}</td>
                                            <td>{{ $item->nama_wali }}</td>
                                            <td>{{ $item->email_wali }}</td>
                                            <td>{{ $item->created_at->format('Y-m-d') }}</td>
                                            <td class="d-flex gap-2">
                                                <form action="{{ route('admin.pendaftaran.approve', $item->id) }}" method="POST" class="d-flex gap-2">
                                                    @csrf
                                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                                    <input type="text" name="catatan_admin" class="form-control form-control-sm" placeholder="Catatan admin">
                                                    <button type="submit" class="btn btn-success btn-sm">Setujui</button>
                                                </form>
                                                <form action="{{ route('admin.pendaftaran.reject', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">Tolak</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Belum ada pendaftaran menunggu.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="disetujui" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Nama Wali</th>
                                        <th>Email Wali</th>
                                        <th>Tanggal Daftar</th>
                                        <th>Catatan Admin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($approved as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->nama }}</td>
                                            <td>{{ $item->jenis_kelamin }}</td>
                                            <td>{{ $item->nama_wali }}</td>
                                            <td>{{ $item->email_wali }}</td>
                                            <td>{{ $item->created_at->format('Y-m-d') }}</td>
                                            <td>{{ $item->catatan_admin }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">Belum ada pendaftaran disetujui.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="ditolak" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Nama Wali</th>
                                        <th>Email Wali</th>
                                        <th>Tanggal Daftar</th>
                                        <th>Catatan Admin</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rejected as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->nama }}</td>
                                            <td>{{ $item->jenis_kelamin }}</td>
                                            <td>{{ $item->nama_wali }}</td>
                                            <td>{{ $item->email_wali }}</td>
                                            <td>{{ $item->created_at->format('Y-m-d') }}</td>
                                            <td>{{ $item->catatan_admin }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Belum ada pendaftaran ditolak.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
