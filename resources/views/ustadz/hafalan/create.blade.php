@extends('layouts.app')

@section('title', 'Tambah Hafalan - SIMHAFAL')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-book"></i> Tambah Hafalan - {{ $santri->nama }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('ustadz.hafalan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="santri_id" value="{{ $santri->id }}">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="juz" class="form-label">Juz</label>
                            <input type="number" class="form-control @error('juz') is-invalid @enderror" 
                                   id="juz" name="juz" value="{{ old('juz') }}" min="1" max="30">
                            @error('juz')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="surat" class="form-label">Surat</label>
                            <input type="text" class="form-control @error('surat') is-invalid @enderror" 
                                   id="surat" name="surat" value="{{ old('surat') }}">
                            @error('surat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ayat_dari" class="form-label">Ayat Dari</label>
                            <input type="text" class="form-control @error('ayat_dari') is-invalid @enderror" 
                                   id="ayat_dari" name="ayat_dari" value="{{ old('ayat_dari') }}">
                            @error('ayat_dari')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ayat_sampai" class="form-label">Ayat Sampai</label>
                            <input type="text" class="form-control @error('ayat_sampai') is-invalid @enderror" 
                                   id="ayat_sampai" name="ayat_sampai" value="{{ old('ayat_sampai') }}">
                            @error('ayat_sampai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="belum" {{ old('status') == 'belum' ? 'selected' : '' }}>Belum</option>
                                <option value="sedang" {{ old('status') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                                <option value="selesai" {{ old('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                <option value="mengulang" {{ old('status') == 'mengulang' ? 'selected' : '' }}>Mengulang</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_setoran" class="form-label">Tanggal Setoran</label>
                            <input type="date" class="form-control @error('tanggal_setoran') is-invalid @enderror" 
                                   id="tanggal_setoran" name="tanggal_setoran" value="{{ old('tanggal_setoran') }}">
                            @error('tanggal_setoran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nilai" class="form-label">Nilai (0-100)</label>
                        <input type="number" class="form-control @error('nilai') is-invalid @enderror" 
                               id="nilai" name="nilai" value="{{ old('nilai') }}" min="0" max="100">
                        @error('nilai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea class="form-control @error('catatan') is-invalid @enderror" 
                                  id="catatan" name="catatan" rows="3">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Audio Recording Component -->
                    <hr class="my-4">
                    <h5 class="mb-3">
                        <i class="bi bi-mic"></i> Rekaman Audio Hafalan
                        <small class="text-muted d-block mb-2">Rekam bacaan hafalan santri untuk validasi</small>
                    </h5>

                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <div id="recordingControls" class="mb-3">
                                <div class="d-flex gap-2 mb-3">
                                    <button type="button" id="startRecord" class="btn btn-success btn-sm">
                                        <i class="bi bi-record-circle"></i> Mulai Rekam
                                    </button>
                                    <button type="button" id="stopRecord" class="btn btn-danger btn-sm" style="display:none;">
                                        <i class="bi bi-stop-circle"></i> Hentikan Rekam
                                    </button>
                                    <div id="recordingTime" class="ms-2" style="display:none; font-weight: bold;">
                                        <span id="timer">00:00</span>
                                    </div>
                                </div>

                                @error('audio')
                                    <div class="alert alert-danger alert-sm">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Recording Preview -->
                            <div id="recordingPreview" style="display:none;" class="mb-3">
                                <label class="form-label">Preview Rekaman:</label>
                                <audio id="audioPreview" controls style="width: 100%; max-width: 100%;" class="mb-2"></audio>
                                <div class="d-flex gap-2">
                                    <button type="button" id="clearRecording" class="btn btn-secondary btn-sm">
                                        <i class="bi bi-trash"></i> Bersihkan
                                    </button>
                                    <button type="button" id="uploadRecording" class="btn btn-primary btn-sm">
                                        <i class="bi bi-cloud-upload"></i> Gunakan Rekaman Ini
                                    </button>
                                </div>
                            </div>

                            <!-- Audio Upload Status -->
                            <div id="uploadStatus" style="display:none;">
                                <div class="alert alert-success mb-0">
                                    <i class="bi bi-check-circle"></i>
                                    Rekaman audio telah dipilih dan siap diupload
                                    <br>
                                    <small id="audioFileName"></small>
                                </div>
                            </div>

                            <!-- Audio Info -->
                            <div class="alert alert-info alert-sm">
                                <i class="bi bi-info-circle"></i>
                                <small>
                                    <strong>Tips:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Baca dengan jelas dan penuh perhatian</li>
                                        <li>Rekam dalam lingkungan yang tenang</li>
                                        <li>Browser harus memberi izin akses ke microphone</li>
                                        <li>durasi maksimal 50MB</li>
                                        <li>setelah merekam, klik tombol Gunakan Rekaman Ini</li>
                                    </ul>
                                </small>
                            </div>
                        </div>
                    </div>

                    <<input type="file" name="audio" id="audioInput" style="display:none;" accept="audio/*">

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('ustadz.santri.show', $santri->id) }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Audio Recording JavaScript -->
 
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

    console.log('✅ Script loaded successfully');

    // Start Recording
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
                console.log('🎤 Recording done, blob size:', recordedAudioBlob.size);
            });

            mediaRecorder.start();
            startButton.style.display = 'none';
            stopButton.style.display = 'inline-block';
            console.log('🔴 Recording started');

        } catch (error) {
            console.error('❌ Microphone error:', error);
            alert('Tidak dapat mengakses microphone: ' + error.message);
        }
    });

    // Stop Recording
    stopButton.addEventListener('click', function() {
        mediaRecorder.stop();
        mediaRecorder.stream.getTracks().forEach(t => t.stop());
        stopButton.style.display = 'none';
        startButton.style.display = 'inline-block';
    });

    // Clear Recording
    clearButton.addEventListener('click', function() {
        recordedAudioBlob = null;
        audioPreview.src = '';
        audioInput.value = '';
        recordingPreview.style.display = 'none';
        uploadStatus.style.display = 'none';
        startButton.style.display = 'inline-block';
    });

    // Use Recording - convert blob to file input
    uploadButton.addEventListener('click', function() {
        if (!recordedAudioBlob) return;

        // Convert blob to File and assign to file input
        const filename = 'hafalan-' + Date.now() + '.webm';
        const audioFile = new File([recordedAudioBlob], filename, { type: 'audio/webm' });

        // Assign to file input via DataTransfer
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(audioFile);
        audioInput.files = dataTransfer.files;

        audioFileName.textContent = `File: ${filename} | Ukuran: ${(recordedAudioBlob.size/1024/1024).toFixed(2)} MB`;
        recordingPreview.style.display = 'none';
        uploadStatus.style.display = 'block';
        startButton.style.display = 'inline-block';

        console.log('✅ Audio file set to input:', audioInput.files[0]);
    });
});
</script>

<style>
    #recordingControls {
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 5px;
    }

    #audioPreview {
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: white;
    }

    .alert-sm {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    #timer {
        font-size: 1.25rem;
        color: #dc3545;
    }
</style>
@endsection
