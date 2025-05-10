@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Jenis Cuti</h2>
        <a href="{{ route('jenis-cutis.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('jenis-cutis.update', $jenisCuti->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="nama_jenis" class="form-label">Nama Jenis Cuti</label>
                    <input type="text" class="form-control @error('nama_jenis') is-invalid @enderror" id="nama_jenis" name="nama_jenis" value="{{ old('nama_jenis', $jenisCuti->nama_jenis) }}" required>
                    @error('nama_jenis')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="jatah_hari" class="form-label">Jatah Hari</label>
                    <input type="number" class="form-control @error('jatah_hari') is-invalid @enderror" id="jatah_hari" name="jatah_hari" value="{{ old('jatah_hari', $jenisCuti->jatah_hari) }}" min="1" required>
                    @error('jatah_hari')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Jenis POH (Point of Hire)</label>
                    <div class="d-flex">
                        <div class="form-check me-4">
                            <input class="form-check-input" type="radio" name="jenis_poh" id="poh-lokal" value="lokal" {{ old('jenis_poh', $jenisCuti->jenis_poh ?? 'lokal') == 'lokal' ? 'checked' : '' }}>
                            <label class="form-check-label" for="poh-lokal">
                                Lokal
                            </label>
                        </div>
                        <div class="form-check me-4">
                            <input class="form-check-input" type="radio" name="jenis_poh" id="poh-luar" value="luar" {{ old('jenis_poh', $jenisCuti->jenis_poh ?? '') == 'luar' ? 'checked' : '' }}>
                            <label class="form-check-label" for="poh-luar">
                                Luar
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="jenis_poh" id="poh-lokal-luar" value="lokal_luar" {{ old('jenis_poh', $jenisCuti->jenis_poh ?? '') == 'lokal_luar' ? 'checked' : '' }}>
                            <label class="form-check-label" for="poh-lokal-luar">
                                Lokal & Luar
                            </label>
                        </div>
                    </div>
                    @error('jenis_poh')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>



                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $jenisCuti->keterangan) }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection