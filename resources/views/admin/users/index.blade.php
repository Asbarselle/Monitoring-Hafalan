@extends('layouts.app')

@section('title', 'Kelola User - SIMHAFAL')

@section('content')

{{-- force dark text on tabs to override theme colors and add green background for active tab --}}
<style>
    .nav-tabs .nav-link,
    .nav-tabs .nav-link.active {
        color: #000 !important;
    }
    .nav-tabs .nav-link.active {
        background-color: #198754 !important; /* bootstrap success green */
        border-color: #198754 #198754 #ffffff !important;
        color: #fff !important; /* white text on green */
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-people"></i> Kelola User</h2>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah User
    </a>
</div>

<div class="card">
    <div class="card-body">
        {{-- nav tabs for roles --}}
        <ul class="nav nav-tabs" id="userTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link text-dark {{ $activeTab === 'admin' ? 'active' : '' }}" id="admins-tab" data-bs-toggle="tab" data-bs-target="#admins" type="button" role="tab" aria-controls="admins" aria-selected="{{ $activeTab === 'admin' ? 'true' : 'false' }}">Admin</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-dark {{ $activeTab === 'ustadz' ? 'active' : '' }}" id="ustadz-tab" data-bs-toggle="tab" data-bs-target="#ustadz" type="button" role="tab" aria-controls="ustadz" aria-selected="{{ $activeTab === 'ustadz' ? 'true' : 'false' }}">Ustadz</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-dark {{ $activeTab === 'orang_tua' ? 'active' : '' }}" id="orangtua-tab" data-bs-toggle="tab" data-bs-target="#orangtua" type="button" role="tab" aria-controls="orangtua" aria-selected="{{ $activeTab === 'orang_tua' ? 'true' : 'false' }}">Orang Tua</button>
            </li>
        </ul>
        <div class="tab-content mt-3" id="userTabsContent">
            {{-- Admins table --}}
            <div class="tab-pane fade {{ $activeTab === 'admin' ? 'show active' : '' }}" id="admins" role="tabpanel" aria-labelledby="admins-tab">
                @include('admin.users._table', ['users' => $admins])
                <div class="mt-2">
                    {{ $admins->appends(['role'=>'admin'])->links() }}
                </div>
            </div>
            {{-- Ustadz table --}}
            <div class="tab-pane fade {{ $activeTab === 'ustadz' ? 'show active' : '' }}" id="ustadz" role="tabpanel" aria-labelledby="ustadz-tab">
                @include('admin.users._table', ['users' => $ustadz])
                <div class="mt-2">
                    {{ $ustadz->appends(['role'=>'ustadz'])->links() }}
                </div>
            </div>
            {{-- Orang Tua table --}}
            <div class="tab-pane fade {{ $activeTab === 'orang_tua' ? 'show active' : '' }}" id="orangtua" role="tabpanel" aria-labelledby="orangtua-tab">
                @include('admin.users._table', ['users' => $orangtua])
                <div class="mt-2">
                    {{ $orangtua->appends(['role'=>'orang_tua'])->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
