@extends('layouts.app')

@section('title', 'Notifikasi Hafalan - SIMHAFAL')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-bell"></i> Notifikasi Setoran Hafalan</h2>
            <a href="{{ route('parent.dashboard') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        @if($notifications->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="mt-3 text-muted">Belum ada notifikasi</p>
                    <small class="text-muted">Notifikasi akan muncul di sini ketika ustadz menambahkan hafalan baru</small>
                </div>
            </div>
        @else
            <div class="notification-list">
                @foreach($notifications as $notification)
                    <div class="card mb-3 notification-card @if($notification->status !== 'read') border-primary @endif" 
                         data-notification-id="{{ $notification->id }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <!-- Notification Icon & Type -->
                                    <div class="mb-2">
                                        @if($notification->notification_channel === 'whatsapp')
                                            <span class="badge bg-success"><i class="bi bi-whatsapp"></i> WhatsApp</span>
                                        @elseif($notification->notification_channel === 'email')
                                            <span class="badge bg-info"><i class="bi bi-envelope"></i> Email</span>
                                        @else
                                            <span class="badge bg-primary"><i class="bi bi-app-indicator"></i> In-App</span>
                                        @endif
                                        
                                        @if($notification->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($notification->status === 'failed')
                                            <span class="badge bg-danger">Gagal</span>
                                        @elseif($notification->status !== 'read')
                                            <span class="badge bg-primary">Baru</span>
                                        @endif
                                    </div>

                                    <!-- Notification Content -->
                                    <h6 class="card-title mb-2">
                                        @if($notification->hafalan)
                                            {{ $notification->santri->nama }} - 
                                            {{ $notification->hafalan->surat ?? ('Juz ' . $notification->hafalan->juz) }}
                                            @if($notification->hafalan->ayat_dari)
                                                ({{ $notification->hafalan->ayat_dari }}-{{ $notification->hafalan->ayat_sampai }})
                                            @endif
                                        @else
                                            Notifikasi Hafalan
                                        @endif
                                    </h6>

                                    <p class="card-text text-muted mb-2" style="font-size: 0.9rem;">
                                        {{ Str::limit($notification->message, 150) }}
                                    </p>

                                    <!-- Hafalan Details -->
                                    @if($notification->hafalan)
                                        <div class="small mb-2">
                                            <div class="row">
                                                <div class="col-6">
                                                    <strong>Status:</strong>
                                                    <span class="badge bg-{{ $notification->hafalan->status === 'selesai' ? 'success' : ($notification->hafalan->status === 'sedang' ? 'warning' : 'secondary') }}">
                                                        {{ ucfirst($notification->hafalan->status) }}
                                                    </span>
                                                </div>
                                                <div class="col-6">
                                                    <strong>Nilai:</strong>
                                                    <span class="badge bg-info">
                                                        {{ $notification->hafalan->nilai ?? '-' }}/100
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <strong>Tanggal Setoran:</strong>
                                                {{ optional($notification->hafalan->tanggal_setoran)->format('d M Y') ?? '-' }}
                                            </div>
                                        </div>

                                        <!-- Audio Player (if available) -->
                                        @if($notification->hafalan->audio_path)
                                            <div class="mt-3 p-2 bg-light rounded">
                                                <small><strong>🎧 Dengarkan Hafalan:</strong></small>
                                                <audio controls style="width: 100%; margin-top: 8px; max-height: 30px;">
                                                    <source src="{{ route('parent.hafalan.listen', $notification->hafalan->id) }}" type="audio/webm">
                                                    Browser Anda tidak mendukung audio player.
                                                </audio>
                                                <div class="mt-2">
                                                    <a href="{{ route('parent.hafalan.download', $notification->hafalan->id) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-download"></i> Download
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                                    <!-- Timestamp -->
                                    <small class="text-muted d-block mt-3">
                                        <i class="bi bi-clock"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </small>
                                </div>

                                <!-- Action Buttons -->
                                @if($notification->status !== 'read')
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-primary mark-read-btn" 
                                                data-notification-id="{{ $notification->id }}"
                                                title="Tandai sebagai sudah dibaca">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

    <!-- Sidebar: Notification Preferences -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-gear"></i> Preferensi Notifikasi</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Kelola cara Anda menerima notifikasi hafalan</p>

                <a href="{{ route('parent.dashboard') }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="bi bi-pencil"></i> Edit Preferensi
                </a>

                <div class="mt-4">
                    <h6 class="mb-3">Status Kanal Notifikasi:</h6>
                    
                    @php
                        $user = Auth::user();
                    @endphp

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" id="check_email" 
                               {{ $user->notify_email ? 'checked' : '' }} disabled>
                        <label class="form-check-label" for="check_email">
                            <i class="bi bi-envelope" style="color: #0D6EFD;"></i>
                            Email
                            <small class="d-block text-muted ms-4">
                                @if($user->notify_email)
                                    <i class="bi bi-check-circle text-success"></i> Aktif
                                @else
                                    <i class="bi bi-x-circle text-danger"></i> Non-aktif
                                @endif
                            </small>
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="check_app" 
                               {{ $user->notify_app ? 'checked' : '' }} disabled>
                        <label class="form-check-label" for="check_app">
                            <i class="bi bi-app-indicator" style="color: #6C757D;"></i>
                            In-App Notification
                            <small class="d-block text-muted ms-4">
                                @if($user->notify_app)
                                    <i class="bi bi-check-circle text-success"></i> Aktif
                                @else
                                    <i class="bi bi-x-circle text-danger"></i> Non-aktif
                                @endif
                            </small>
                        </label>
                    </div>
                </div>

                <hr>

                <div class="alert alert-info alert-sm">
                    <i class="bi bi-info-circle"></i>
                    <small>
                        <strong>Informasi:</strong> Setiap kali ustadz menambahkan hafalan baru untuk anak Anda,
                        Anda akan menerima notifikasi sesuai preferensi yang telah disetel.
                    </small>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card mt-3">
            <div class="card-body">
                <h6 class="mb-2"><i class="bi bi-lightbulb"></i> Tips</h6>
                <ul class="small mb-0">
                    <li>Email berguna untuk arsip hafalan</li>
                    <li>In-App notification untuk monitoring real-time</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const markReadBtns = document.querySelectorAll('.mark-read-btn');

    markReadBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const notificationId = this.dataset.notificationId;
            const card = this.closest('.notification-card');

            fetch(`/parent/notification/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    card.classList.remove('border-primary');
                    this.remove();
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>

<style>
    .notification-card {
        border-left: 4px solid transparent;
        transition: all 0.3s ease;
    }

    .notification-card.border-primary {
        background-color: #f0f8ff;
    }

    .alert-sm {
        padding: 0.75rem;
        font-size: 0.875rem;
    }

    .mark-read-btn:hover {
        transform: scale(1.1);
    }
</style>
@endsection
