@extends('layouts.app')

@section('title', 'Tambah Santri - SIMHAFAL')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-person-plus"></i> Tambah Santri</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.santri.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                               id="nama" name="nama" value="{{ old('nama') }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nis" class="form-label">NIS</label>
                        <input type="text" class="form-control @error('nis') is-invalid @enderror" 
                               id="nis" name="nis" value="{{ old('nis') }}" required>
                        @error('nis')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                            <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" 
                                   value="{{ old('tempat_lahir') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" 
                                   value="{{ old('tanggal_lahir') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                        <select class="form-select" id="jenis_kelamin" name="jenis_kelamin">
                            <option value="">Pilih</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3">{{ old('alamat') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="no_hp" class="form-label">No. HP</label>
                        <input type="text" class="form-control" id="no_hp" name="no_hp" value="{{ old('no_hp') }}">
                    </div>

                    <div class="mb-3">
                        <label for="orang_tua_id" class="form-label">Orang Tua</label>
                        <select class="form-select" id="orang_tua_id" name="orang_tua_id">
                            <option value="">Pilih Orang Tua</option>
                            @foreach($orangTua as $ot)
                                <option value="{{ $ot->id }}" {{ old('orang_tua_id') == $ot->id ? 'selected' : '' }}>
                                    {{ $ot->name }} ({{ $ot->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto</label>
                        <div class="mb-2">
                            <div class="bg-secondary rounded d-inline-flex align-items-center justify-content-center" 
                                 style="width: 150px; height: 150px;" id="previewContainer">
                                <i class="bi bi-image text-white" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                        <input type="file" class="form-control @error('foto') is-invalid @enderror" 
                               id="foto" name="foto" accept="image/*">
                        <small class="text-muted d-block mt-1">Tipe file: JPG, PNG, GIF. Ukuran maksimal: 2MB</small>
                        @error('foto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.santri') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('foto').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const container = document.getElementById('previewContainer');
                if (container) {
                    container.outerHTML = '<img src="' + event.target.result + '" alt="Preview" class="img-thumbnail" width="150">';
                }
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
@endsection
