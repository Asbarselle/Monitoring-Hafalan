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
                <form action="{{ route('ustadz.hafalan.store') }}" method="POST">
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

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('ustadz.santri.show', $santri->id) }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
