@extends('layouts.app')

@section('title', 'Detail Hafalan - SIMHAFAL')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="bi bi-book"></i> Detail Hafalan: {{ $santri->nama }}</h2>
    <a href="{{ route('parent.dashboard') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Informasi Santri</h5>
            </div>
            <div class="card-body">
                <p><strong>NIS:</strong> {{ $santri->nis }}</p>
                <p><strong>Nama:</strong> {{ $santri->nama }}</p>
                <p><strong>Jenis Kelamin:</strong> {{ $santri->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Statistik Hafalan</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <h3 class="mb-0">{{ $santri->total_juz }}</h3>
                        <small class="text-muted">Juz Selesai</small>
                    </div>
                    <div class="col-4">
                        <h3 class="mb-0">{{ $santri->hafalan->count() }}</h3>
                        <small class="text-muted">Total Hafalan</small>
                    </div>
                    <div class="col-4">
                        <h3 class="mb-0">{{ number_format($santri->progres_hafalan, 1) }}%</h3>
                        <small class="text-muted">Progres</small>
                    </div>
                </div>
                <div class="progress mt-3">
                    <div class="progress-bar" role="progressbar" 
                         style="width: {{ $santri->progres_hafalan }}%">
                        {{ number_format($santri->progres_hafalan, 1) }}%
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Data Hafalan</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Juz</th>
                        <th>Surat</th>
                        <th>Ayat Dari</th>
                        <th>Ayat Sampai</th>
                        <th>Status</th>
                        <th>Tanggal Setoran</th>
                        <th>Nilai</th>
                        <th>Rekaman Audio</th>
                        <th>Catatan</th>
                        <th>Ustadz</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hafalan as $h)
                        <tr>
                            <td>{{ $loop->iteration + ($hafalan->currentPage() - 1) * $hafalan->perPage() }}</td>
                            <td>{{ $h->juz ?? '-' }}</td>
                            <td>{{ $h->surat ?? '-' }}</td>
                            <td>{{ $h->ayat_dari ?? '-' }}</td>
                            <td>{{ $h->ayat_sampai ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $h->status == 'selesai' ? 'success' : ($h->status == 'sedang' ? 'warning' : ($h->status == 'mengulang' ? 'danger' : 'secondary')) }}">
                                    {{ ucfirst($h->status) }}
                                </span>
                            </td>
                            <td>{{ $h->tanggal_setoran ? $h->tanggal_setoran->format('d/m/Y') : '-' }}</td>
                            <td>{{ $h->nilai ?? '-' }}</td>
                            <td>
                                @if($h->audio_path)
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary audio-player-btn" 
                                                data-hafalan-id="{{ $h->id }}" 
                                                title="Dengarkan Rekaman">
                                            <i class="bi bi-play-circle"></i>
                                        </button>
                                        <a href="{{ route('parent.hafalan.download', $h->id) }}" 
                                           class="btn btn-outline-secondary" title="Download Rekaman">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    </div>
                                @else
                                    <span class="badge bg-secondary">Belum ada</span>
                                @endif
                            </td>
                            <td>{{ $h->catatan ?? '-' }}</td>
                            <td>{{ $h->ustadz ? $h->ustadz->name : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">Belum ada data hafalan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
            {{ $hafalan->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

@if(isset($statistikJuz) && $statistikJuz->isNotEmpty())
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Statistik Per Juz</h5>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($statistikJuz as $juz => $data)
                <div class="col-md-3 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6>Juz {{ $juz }}</h6>
                            @foreach($data as $stat)
                                <small class="d-block">
                                    {{ ucfirst($stat->status) }}: {{ $stat->total }}
                                </small>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Audio Player Modal -->
<div class="modal fade" id="audioPlayerModal" tabindex="-1" aria-labelledby="audioPlayerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="audioPlayerLabel">
                    <i class="bi bi-headphones"></i> Dengarkan Hafalan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="audioPlayerContainer">
                    <div class="mb-3">
                        <small class="text-muted">Santri: <strong id="audioPlayerSantri"></strong></small>
                        <br>
                        <small class="text-muted">Surah/Juz: <strong id="audioPlayerSurat"></strong></small>
                    </div>
                    <audio id="audioPlayer" controls style="width: 100%; border-radius: 5px;">
                        Browser Anda tidak mendukung audio player.
                    </audio>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> 
                            Dengarkan bacaan hafalan Anda dengan seksama
                        </small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="audioDownloadLink" class="btn btn-primary" download>
                    <i class="bi bi-download"></i> Download Audio
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const audioPlayerBtns = document.querySelectorAll('.audio-player-btn');
    const audioPlayer = document.getElementById('audioPlayer');
    const audioPlayerModal = new bootstrap.Modal(document.getElementById('audioPlayerModal'));
    const audioPlayerSantri = document.getElementById('audioPlayerSantri');
    const audioPlayerSurat = document.getElementById('audioPlayerSurat');
    const audioDownloadLink = document.getElementById('audioDownloadLink');
    
    const santriNama = '{{ $santri->nama }}';

    audioPlayerBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const hafalanId = this.dataset.hafalanId;
            const row = this.closest('tr');
            const juz = row.cells[1].textContent.trim();
            const surat = row.cells[2].textContent.trim();
            
            // Construct the audio URL
            const audioUrl = `/parent/hafalan/${hafalanId}/listen`;
            const downloadUrl = `/parent/hafalan/${hafalanId}/download`;
            
            // Set audio source
            audioPlayer.src = audioUrl;
            audioPlayerSantri.textContent = santriNama;
            audioPlayerSurat.textContent = (surat !== '-' ? surat : 'Juz ' + juz);
            audioDownloadLink.href = downloadUrl;
            
            // Show modal
            audioPlayerModal.show();
            
            // Auto play
            setTimeout(() => {
                audioPlayer.play().catch(e => console.log('Autoplay prevented:', e));
            }, 500);
        });
    });
});
</script>

<style>
    .audio-player-btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    #audioPlayer {
        max-width: 100%;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
    }
</style>
@endsection
