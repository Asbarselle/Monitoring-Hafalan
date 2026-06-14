@extends('layouts.app')

@section('title', 'Info - SIMHAFAL')

@section('content')
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Informasi Pendaftaran</h5>
            </div>
            <div class="card-body">
                <p><strong>Waktu Pendaftaran:</strong> Terbuka setiap tahun ajaran baru</p>
                <p><strong>Syarat Pendaftaran:</strong></p>
                <ul>
                    <li>Mengisi formulir pendaftaran</li>
                    <li>Membawa Kartu KIP dan KIS (jika ada)</li>
                    <li>Membawa foto copy ijazah terakhir</li>
                    <li>Membawa foto copy kartu keluarga</li>
                    <li>Membawa foto copy akta kelahiran</li>
                    <li>Membawa foto copy KTP orang tua</li>
                    <li>Minimal 2 lembar setiap dokumen</li>
                </ul>
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #e9ecef;">
                    <p class="mb-3"><strong>📝 Daftar Sekarang:</strong></p>
                <a href="{{ route('pendaftaran.create') }}" class="btn btn-primary" style="background-color: #2c5530; border-color: #2c5530;">
                    <i class="bi bi-person-plus"></i> Buka Form Pendaftaran
                </a>
            </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Program</h5>
            </div>
            <div class="card-body">
                <p><strong>Program Kegiatan:</strong></p>
                <ul>
                    <li>Program Tahfidzul Qur'an</li>
                    <li>Pengajian Tafsir Al-Qur'an</li>
                    <li>Pengajian AT-TIBYAN</li>
                    <li>Pengajian Taisirul Kholaq</li>
                    <li>Pengajian Ta'lim Al-Muta'allim</li>
                    <li>Pengajian Riyadhus Shalihin</li>
                    <li>Pengajian Fiqhi Al-Imta'</li>
                    <li>Pembinaan Barazanji</li>
                    <li>Pembinaan Bahasa Arab</li>
                    <li>Pembinaan Muhadoroh</li>
                    <li>Pembinaan Hadroh/Marawis</li>
                </ul>
                <p><strong>Metode Pembelajaran:</strong></p>
                <ul>
                    <li>Setoran harian kepada badal/ustadz</li>
                    <li>Diajar oleh ustadz/kiay yang berkualitas</li>
                    <li>Monitoring melalui sistem digital secara real-time</li>
                    <li>Laporan berkala kepada orang tua</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-question-circle"></i> FAQ</h5>
            </div>
            <div class="card-body">
                <h6>Bagaimana cara mengakses monitoring hafalan?</h6>
                <p>Anda dapat mengakses menu Monitoring setelah melakukan login dengan akun yang telah diberikan.</p>

                <h6 class="mt-3">Siapa saja yang bisa mengakses sistem?</h6>
                <p>Sistem dapat diakses oleh Admin, Ustadz, dan Orang Tua Santri dengan hak akses yang berbeda sesuai peran masing-masing.</p>

                <h6 class="mt-3">Bagaimana cara melihat perkembangan hafalan anak?</h6>
                <p>Setelah login sebagai Orang Tua, Anda akan langsung melihat dashboard yang menampilkan statistik dan perkembangan hafalan anak Anda.</p>
            </div>
        </div>
    </div>
</div>
@endsection
