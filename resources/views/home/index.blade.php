@extends('layouts.app')

@section('title', 'Beranda - SIMHAFAL')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center p-5">
                <p class="lead mb-2">Selamat Datang di</p>
                <h1 class="display-4 mb-3">Pondok Pesantren Hilyatul Irsyad</h1>
                <p class="lead">Sistem Informasi Monitoring Hafalan Al-Qur'an</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-info-circle"></i> Tentang Pondok</h5>
                <p class="card-text" style="text-align: justify;">
                    Pondok Pesantren Hilyatul Irsyad adalah lembaga pendidikan Islam yang berkomitmen 
                    untuk membina generasi yang hafal Al-Qur'an dan berakhlak mulia. Dengan sistem 
                    monitoring yang terintegrasi, kami memastikan setiap santri mendapatkan 
                    pendampingan optimal dalam menghafal Al-Qur'an.
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-book"></i> Program Hafalan</h5>
                <p class="card-text" style="text-align: justify;">
                    Program hafalan Al-Qur'an di Pondok Pesantren Hilyatul Irsyad dirancang secara 
                    sistematis dengan pendampingan ustadz yang berkualitas dan berpengalaman. Setiap perkembangan 
                    hafalan santri dapat dipantau secara real-time melalui sistem monitoring ini.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-images"></i> Galeri Pondok</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <img src="{{ asset('images/pondok.jpg') }}" alt="Foto Pondok" class="img-fluid rounded shadow" style="width: 100%; height: 200px; object-fit: cover;">
                        <p class="mt-2 text-center">Peletakan Batu Pertama</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <img src="{{ asset('images/aktivitas_santri.jpg') }}" alt="Aktivitas Santri" class="img-fluid rounded shadow" style="width: 100%; height: 200px; object-fit: cover;">
                        <p class="mt-2 text-center">Hari Santri Nasional</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <img src="{{ asset('images/kegiatan_hafalan.jpg') }}" alt="Kegiatan Hafalan" class="img-fluid rounded shadow" style="width: 100%; height: 200px; object-fit: cover;">
                        <p class="mt-2 text-center">Pembelajaran Bahasa Arab</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <img src="{{ asset('images/mumtaz.jpg') }}" alt="mumtaz" class="img-fluid rounded shadow" style="width: 100%; height: 200px; object-fit: cover;">
                        <p class="mt-2 text-center">Mumtaz</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <img src="{{ asset('images/pelepasan.jpg') }}" alt="pelepasan" class="img-fluid rounded shadow" style="width: 100%; height: 200px; object-fit: cover;">
                        <p class="mt-2 text-center">Pelepasan Ke Al Azhar Mesir</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <img src="{{ asset('images/qari.jpg') }}" alt="qari" class="img-fluid rounded shadow" style="width: 100%; height: 200px; object-fit: cover;">
                        <p class="mt-2 text-center">Qari' Internasional</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <img src="{{ asset('images/hilya.jpg') }}" alt="hilya" class="img-fluid rounded shadow" style="width: 100%; height: 200px; object-fit: cover;">
                        <p class="mt-2 text-center">Belajar Tilawah Dengan Syekh Dan Qari' Internasional</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <img src="{{ asset('images/marawis.jpg') }}" alt="marawis" class="img-fluid rounded shadow" style="width: 100%; height: 200px; object-fit: cover;">
                        <p class="mt-2 text-center">Tim Hadroh/Marawis</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <img src="{{ asset('images/imam.jpg') }}" alt="imam" class="img-fluid rounded shadow" style="width: 100%; height: 200px; object-fit: cover;">
                        <p class="mt-2 text-center">Belajar Ke Imam Ashim Makassar</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <img src="{{ asset('images/isra.jpg') }}" alt="isra" class="img-fluid rounded shadow" style="width: 100%; height: 200px; object-fit: cover;">
                        <p class="mt-2 text-center">Isra' Mi'raj</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <img src="{{ asset('images/wisuda.jpg') }}" alt="wisuda" class="img-fluid rounded shadow" style="width: 100%; height: 200px; object-fit: cover;">
                        <p class="mt-2 text-center">Promosi Dr </p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <img src="{{ asset('images/malam.jpg') }}" alt="malam" class="img-fluid rounded shadow" style="width: 100%; height: 200px; object-fit: cover;">
                        <p class="mt-2 text-center">Malam Kreativitas</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <img src="{{ asset('images/bukber.jpg') }}" alt="bukber" class="img-fluid rounded shadow" style="width: 100%; height: 200px; object-fit: cover;">
                        <p class="mt-2 text-center">Buka Bersama</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <img src="{{ asset('images/hsn.jpg') }}" alt="hsn" class="img-fluid rounded shadow" style="width: 100%; height: 200px; object-fit: cover;">
                        <p class="mt-2 text-center">HSN 2025</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <img src="{{ asset('images/image.jpg') }}" alt="image" class="img-fluid rounded shadow" style="width: 100%; height: 200px; object-fit: cover;">
                        <p class="mt-2 text-center">Upacara</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
