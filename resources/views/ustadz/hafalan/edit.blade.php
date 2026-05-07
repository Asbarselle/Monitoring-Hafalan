@extends('layouts.app')

@section('title', 'Edit Hafalan - SIMHAFAL')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-pencil"></i> Edit Hafalan</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('ustadz.hafalan.update', $hafalan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="juz" class="form-label">Juz</label>
                            <input type="number" class="form-control @error('juz') is-invalid @enderror" 
                                   id="juz" name="juz" value="{{ old('juz', $hafalan->juz) }}" min="1" max="30">
                            @error('juz')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="surat" class="form-label">Surat</label>
                            <input type="text" class="form-control @error('surat') is-invalid @enderror" 
                                   id="surat" name="surat" value="{{ old('surat', $hafalan->surat) }}">
                            @error('surat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ayat_dari" class="form-label">Ayat Dari</label>
                            <input type="text" class="form-control @error('ayat_dari') is-invalid @enderror" 
                                   id="ayat_dari" name="ayat_dari" value="{{ old('ayat_dari', $hafalan->ayat_dari) }}">
                            @error('ayat_dari')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ayat_sampai" class="form-label">Ayat Sampai</label>
                            <input type="text" class="form-control @error('ayat_sampai') is-invalid @enderror" 
                                   id="ayat_sampai" name="ayat_sampai" value="{{ old('ayat_sampai', $hafalan->ayat_sampai) }}">
                            @error('ayat_sampai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="belum" {{ old('status', $hafalan->status) == 'belum' ? 'selected' : '' }}>Belum</option>
                                <option value="sedang" {{ old('status', $hafalan->status) == 'sedang' ? 'selected' : '' }}>Sedang</option>
                                <option value="selesai" {{ old('status', $hafalan->status) == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="mengulang" {{ old('status', $hafalan->status) == 'mengulang' ? 'selected' : '' }}>Mengulang</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_setoran" class="form-label">Tanggal Setoran</label>
                            <input type="date" class="form-control @error('tanggal_setoran') is-invalid @enderror" 
                                   id="tanggal_setoran" name="tanggal_setoran" value="{{ old('tanggal_setoran', $hafalan->tanggal_setoran?->format('Y-m-d')) }}">
                            @error('tanggal_setoran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nilai" class="form-label">Nilai (0-100)</label>
                        <input type="number" class="form-control @error('nilai') is-invalid @enderror" 
                               id="nilai" name="nilai" value="{{ old('nilai', $hafalan->nilai) }}" min="0" max="100">
                        @error('nilai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                  id="catatan" name="catatan" rows="3">{{ old('catatan', $hafalan->catatan) }}</textarea>
                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Audio Section -->
                    <hr class="my-4">
                    <h5 class="mb-3">
                        <i class="bi bi-mic"></i> Rekaman Audio Hafalan
                    </h5>

                    <div class="card bg-light mb-3">
                        <div class="card-body">

                            {{-- Audio Lama --}}
                            @if($hafalan->audio_path)
                            <div id="existingAudio" class="mb-3">
                                <label class="form-label fw-bold">🎵 Audio Saat Ini:</label>
                                <audio controls style="width:100%;" class="mb-2">
                                    <source src="{{ Storage::url($hafalan->audio_path) }}" type="audio/webm">
                                </audio>
                                <div class="d-flex gap-2">
                                    <button type="button" id="btnGantiAudio" class="btn btn-warning btn-sm">
                                        <i class="bi bi-arrow-repeat"></i> Ganti Audio
                                    </button>
                                    <button type="button" id="btnHapusAudio" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i> Hapus Audio
                                    </button>
                                </div>
                                <input type="hidden" name="hapus_audio" id="hapusAudioInput" value="0">
                            </div>
                            @endif

                            {{-- Form Rekam Audio Baru --}}
                            <div id="recordingSection" style="{{ $hafalan->audio_path ? 'display:none;' : '' }}">
                                <label class="form-label fw-bold">
                                    {{ $hafalan->audio_path ? '🔄 Rekam Audio Baru:' : '🎤 Rekam Audio:' }}
                                </label>

                                <div class="d-flex gap-2 mb-3">
                                    <button type="button" id="startRecord" class="btn btn-success btn-sm">
                                        <i class="bi bi-record-circle"></i> Mulai Rekam
                                    </button>
                                    <button type="button" id="stopRecord" class="btn btn-danger btn-sm" style="display:none;">
                                        <i class="bi bi-stop-circle"></i> Hentikan Rekam
                                    </button>
                                    <div id="recordingTime" style="display:none; font-weight:bold;">
                                        <span id="timer">00:00</span>
                                    </div>
                                </div>

                                {{-- Preview Rekaman Baru --}}
                                <div id="recordingPreview" style="display:none;" class="mb-3">
                                    <label class="form-label">Preview Rekaman Baru:</label>
                                    <audio id="audioPreview" controls style="width:100%;" class="mb-2"></audio>
                                    <div class="d-flex gap-2">
                                        <button type="button" id="clearRecording" class="btn btn-secondary btn-sm">
                                            <i class="bi bi-trash"></i> Bersihkan
                                        </button>
                                        <button type="button" id="uploadRecording" class="btn btn-primary btn-sm">
                                            <i class="bi bi-cloud-upload"></i> Gunakan Rekaman Ini
                                        </button>
                                    </div>
                                </div>

                                {{-- Status Audio Baru --}}
                                <div id="uploadStatus" style="display:none;">
                                    <div class="alert alert-success mb-0">
                                        <i class="bi bi-check-circle"></i>
                                        Rekaman baru siap diupload
                                        <br><small id="audioFileName"></small>
                                    </div>
                                </div>

                                {{-- Batalkan Ganti Audio --}}
                                @if($hafalan->audio_path)
                                <div class="mt-2">
                                    <button type="button" id="btnBatalGanti" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-x"></i> Batal Ganti Audio
                                    </button>
                                </div>
                                @endif
                            </div>

                            <input type="file" name="audio" id="audioInput" style="display:none;" accept="audio/*">

                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('ustadz.santri.show', $hafalan->santri_id) }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let mediaRecorder;
    let audioChunks = [];
    let recordedAudioBlob = null;
    let timerInterval;

    const startButton = document.getElementById('startRecord');
    const stopButton = document.getElementById('stopRecord');
    const audioPreview = document.getElementById('audioPreview');
    const recordingPreview = document.getElementById('recordingPreview');
    const uploadStatus = document.getElementById('uploadStatus');
    const clearButton = document.getElementById('clearRecording');
    const uploadButton = document.getElementById('uploadRecording');
    const recordingTime = document.getElementById('recordingTime');
    const timer = document.getElementById('timer');
    const audioFileName = document.getElementById('audioFileName');
    const audioInput = document.getElementById('audioInput');
    const recordingSection = document.getElementById('recordingSection');

    // Tombol Ganti Audio
    const btnGantiAudio = document.getElementById('btnGantiAudio');
    if (btnGantiAudio) {
        btnGantiAudio.addEventListener('click', function() {
            document.getElementById('existingAudio').style.display = 'none';
            recordingSection.style.display = 'block';
        });
    }

    // Tombol Batal Ganti
    const btnBatalGanti = document.getElementById('btnBatalGanti');
    if (btnBatalGanti) {
        btnBatalGanti.addEventListener('click', function() {
            recordingSection.style.display = 'none';
            document.getElementById('existingAudio').style.display = 'block';
            recordedAudioBlob = null;
            audioInput.value = '';
            uploadStatus.style.display = 'none';
            recordingPreview.style.display = 'none';
        });
    }

    // Tombol Hapus Audio
    const btnHapusAudio = document.getElementById('btnHapusAudio');
    if (btnHapusAudio) {
        btnHapusAudio.addEventListener('click', function() {
            if (confirm('Yakin ingin menghapus audio ini?')) {
                document.getElementById('hapusAudioInput').value = '1';
                document.getElementById('existingAudio').style.display = 'none';
                recordingSection.style.display = 'block';
            }
        });
    }

    // Mulai Rekam
    if (startButton) {
        startButton.addEventListener('click', async function() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                mediaRecorder = new MediaRecorder(stream);
                audioChunks = [];

                recordingTime.style.display = 'block';
                let seconds = 0;
                timerInterval = setInterval(() => {
                    seconds++;
                    const m = Math.floor(seconds / 60);
                    const s = seconds % 60;
                    timer.textContent = `${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
                }, 1000);

                mediaRecorder.addEventListener('dataavailable', (e) => {
                    audioChunks.push(e.data);
                });

                mediaRecorder.addEventListener('stop', () => {
                    clearInterval(timerInterval);
                    recordingTime.style.display = 'none';
                    recordedAudioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                    audioPreview.src = URL.createObjectURL(recordedAudioBlob);
                    recordingPreview.style.display = 'block';
                });

                mediaRecorder.start();
                startButton.style.display = 'none';
                stopButton.style.display = 'inline-block';

            } catch (error) {
                alert('Tidak dapat mengakses microphone: ' + error.message);
            }
        });
    }

    // Hentikan Rekam
    if (stopButton) {
        stopButton.addEventListener('click', function() {
            mediaRecorder.stop();
            mediaRecorder.stream.getTracks().forEach(t => t.stop());
            stopButton.style.display = 'none';
            startButton.style.display = 'inline-block';
        });
    }

    // Bersihkan Rekaman
    if (clearButton) {
        clearButton.addEventListener('click', function() {
            recordedAudioBlob = null;
            audioPreview.src = '';
            audioInput.value = '';
            recordingPreview.style.display = 'none';
            uploadStatus.style.display = 'none';
        });
    }

    // Gunakan Rekaman Ini
    if (uploadButton) {
        uploadButton.addEventListener('click', function() {
            if (!recordedAudioBlob) return;

            const filename = 'hafalan-' + Date.now() + '.webm';
            const audioFile = new File([recordedAudioBlob], filename, { type: 'audio/webm' });

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(audioFile);
            audioInput.files = dataTransfer.files;

            audioFileName.textContent = `File: ${filename} | Ukuran: ${(recordedAudioBlob.size/1024/1024).toFixed(2)} MB`;
            recordingPreview.style.display = 'none';
            uploadStatus.style.display = 'block';
            startButton.style.display = 'inline-block';
        });
    }
});
</script>

<style>
    #timer { font-size: 1.25rem; color: #dc3545; }
</style>
@endsection