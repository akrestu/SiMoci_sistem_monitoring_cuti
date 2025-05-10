@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Tambah Detail Transportasi</h2>
        <a href="{{ route('cutis.show', $cuti->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Detail Pengajuan Cuti</h5>
        </div>
        <div class="card-body">
            <table class="table table-borderless">
                <tr>
                    <th width="200">Nama Karyawan</th>
                    <td>{{ $cuti->karyawan->nama }}</td>
                </tr>
                <tr>
                    <th>Jenis Cuti</th>
                    <td>
                        @if($cuti->cutiDetails->count() > 0)
                            @foreach($cuti->cutiDetails as $detail)
                                <div class="badge bg-primary mb-1">
                                    {{ $detail->jenisCuti->nama_jenis }} ({{ $detail->jumlah_hari }} hari)
                                </div>
                                @if(!$loop->last) <br> @endif
                            @endforeach
                        @else
                            {{ $cuti->jenisCuti->nama_jenis }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Tanggal Cuti</th>
                    <td>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <th>Lama Cuti</th>
                    <td>{{ $cuti->lama_hari }} hari</td>
                </tr>
                <tr>
                    <th>Alasan</th>
                    <td>{{ $cuti->alasan }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Tabs for Pergi/Kembali -->
    <div class="card mt-4">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="transportasiTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pergi-tab" data-bs-toggle="tab" data-bs-target="#pergi-content" type="button" role="tab" aria-controls="pergi-content" aria-selected="true">
                        <i class="fas fa-plane-departure me-1"></i> Tiket Pergi (Berangkat)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="kembali-tab" data-bs-toggle="tab" data-bs-target="#kembali-content" type="button" role="tab" aria-controls="kembali-content" aria-selected="false">
                        <i class="fas fa-plane-arrival me-1"></i> Tiket Pulang (Kembali)
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="transportasiTabContent">
                <!-- Tab Pergi -->
                <div class="tab-pane fade show active" id="pergi-content" role="tabpanel" aria-labelledby="pergi-tab">
                    <form action="{{ url('/cutis/' . $cuti->id . '/transportasi-details') }}" method="POST" id="form-pergi">
                        @csrf
                        <input type="hidden" name="jenis_perjalanan" value="pergi">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="transportasi_id_pergi" class="form-label">Jenis Transportasi</label>
                                <select name="transportasi_id" id="transportasi_id_pergi" class="form-select @error('transportasi_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jenis Transportasi --</option>
                                    @foreach($transportasis as $transportasi)
                                        <option value="{{ $transportasi->id }}">{{ $transportasi->jenis }}</option>
                                    @endforeach
                                </select>
                                @error('transportasi_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="provider_pergi" class="form-label">Provider/Maskapai</label>
                                <input type="text" name="provider" id="provider_pergi" class="form-control @error('provider') is-invalid @enderror" placeholder="Misalnya: Garuda, Lion, KAI, dll.">
                                @error('provider')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="rute_asal_pergi" class="form-label">Rute Asal</label>
                                <input type="text" name="rute_asal" id="rute_asal_pergi" class="form-control @error('rute_asal') is-invalid @enderror input-rute" required>
                                @error('rute_asal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="rute_tujuan_pergi" class="form-label">Rute Tujuan</label>
                                <input type="text" name="rute_tujuan" id="rute_tujuan_pergi" class="form-control @error('rute_tujuan') is-invalid @enderror input-rute" required>
                                @error('rute_tujuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="waktu_berangkat_pergi" class="form-label">Waktu Berangkat</label>
                                <input type="datetime-local" name="waktu_berangkat" id="waktu_berangkat_pergi" class="form-control @error('waktu_berangkat') is-invalid @enderror">
                                @error('waktu_berangkat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="biaya_aktual_pergi" class="form-label">Estimasi Biaya (Rp)</label>
                                <input type="number" name="biaya_aktual" id="biaya_aktual_pergi" class="form-control @error('biaya_aktual') is-invalid @enderror" step="1000">
                                @error('biaya_aktual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="perlu_hotel_pergi" name="perlu_hotel">
                                <label class="form-check-label" for="perlu_hotel_pergi">
                                    Memerlukan Akomodasi Hotel
                                </label>
                            </div>
                        </div>
                        
                        <div id="hotel_details_pergi" style="display: none;">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="hotel_nama_pergi" class="form-label">Nama Hotel</label>
                                    <input type="text" name="hotel_nama" id="hotel_nama_pergi" class="form-control @error('hotel_nama') is-invalid @enderror">
                                    @error('hotel_nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="hotel_biaya_pergi" class="form-label">Estimasi Biaya Hotel (Rp)</label>
                                    <input type="number" name="hotel_biaya" id="hotel_biaya_pergi" class="form-control @error('hotel_biaya') is-invalid @enderror" step="1000" value="0">
                                    @error('hotel_biaya')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="catatan_pergi" class="form-label">Catatan</label>
                            <textarea name="catatan" id="catatan_pergi" class="form-control @error('catatan') is-invalid @enderror" rows="3"></textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Simpan Tiket Pergi</button>
                        </div>
                    </form>
                </div>
                
                <!-- Tab Kembali -->
                <div class="tab-pane fade" id="kembali-content" role="tabpanel" aria-labelledby="kembali-tab">
                    <form action="{{ url('/cutis/' . $cuti->id . '/transportasi-details') }}" method="POST" id="form-kembali">
                        @csrf
                        <input type="hidden" name="jenis_perjalanan" value="kembali">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="transportasi_id_kembali" class="form-label">Jenis Transportasi</label>
                                <select name="transportasi_id" id="transportasi_id_kembali" class="form-select @error('transportasi_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jenis Transportasi --</option>
                                    @foreach($transportasis as $transportasi)
                                        <option value="{{ $transportasi->id }}">{{ $transportasi->jenis }}</option>
                                    @endforeach
                                </select>
                                @error('transportasi_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="provider_kembali" class="form-label">Provider/Maskapai</label>
                                <input type="text" name="provider" id="provider_kembali" class="form-control @error('provider') is-invalid @enderror" placeholder="Misalnya: Garuda, Lion, KAI, dll.">
                                @error('provider')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="rute_asal_kembali" class="form-label">Rute Asal</label>
                                <input type="text" name="rute_asal" id="rute_asal_kembali" class="form-control @error('rute_asal') is-invalid @enderror input-rute" required>
                                @error('rute_asal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="rute_tujuan_kembali" class="form-label">Rute Tujuan</label>
                                <input type="text" name="rute_tujuan" id="rute_tujuan_kembali" class="form-control @error('rute_tujuan') is-invalid @enderror input-rute" required>
                                @error('rute_tujuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="waktu_berangkat_kembali" class="form-label">Waktu Berangkat</label>
                                <input type="datetime-local" name="waktu_berangkat" id="waktu_berangkat_kembali" class="form-control @error('waktu_berangkat') is-invalid @enderror">
                                @error('waktu_berangkat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="biaya_aktual_kembali" class="form-label">Estimasi Biaya (Rp)</label>
                                <input type="number" name="biaya_aktual" id="biaya_aktual_kembali" class="form-control @error('biaya_aktual') is-invalid @enderror" step="1000">
                                @error('biaya_aktual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="perlu_hotel_kembali" name="perlu_hotel">
                                <label class="form-check-label" for="perlu_hotel_kembali">
                                    Memerlukan Akomodasi Hotel
                                </label>
                            </div>
                        </div>
                        
                        <div id="hotel_details_kembali" style="display: none;">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="hotel_nama_kembali" class="form-label">Nama Hotel</label>
                                    <input type="text" name="hotel_nama" id="hotel_nama_kembali" class="form-control @error('hotel_nama') is-invalid @enderror">
                                    @error('hotel_nama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="hotel_biaya_kembali" class="form-label">Estimasi Biaya Hotel (Rp)</label>
                                    <input type="number" name="hotel_biaya" id="hotel_biaya_kembali" class="form-control @error('hotel_biaya') is-invalid @enderror" step="1000" value="0">
                                    @error('hotel_biaya')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="catatan_kembali" class="form-label">Catatan</label>
                            <textarea name="catatan" id="catatan_kembali" class="form-control @error('catatan') is-invalid @enderror" rows="3"></textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Simpan Tiket Pulang</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/city-dropdown.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hotel visibility toggle for Pergi
        const perluHotelPergi = document.getElementById('perlu_hotel_pergi');
        const hotelDetailsPergi = document.getElementById('hotel_details_pergi');
        const hotelNamaPergi = document.getElementById('hotel_nama_pergi');
        const hotelBiayaPergi = document.getElementById('hotel_biaya_pergi');
        
        perluHotelPergi.addEventListener('change', function() {
            if (this.checked) {
                hotelDetailsPergi.style.display = 'block';
                hotelBiayaPergi.value = '';
            } else {
                hotelDetailsPergi.style.display = 'none';
                hotelBiayaPergi.value = '0';
                hotelNamaPergi.value = '';
            }
        });
        
        // Hotel visibility toggle for Kembali
        const perluHotelKembali = document.getElementById('perlu_hotel_kembali');
        const hotelDetailsKembali = document.getElementById('hotel_details_kembali');
        const hotelNamaKembali = document.getElementById('hotel_nama_kembali');
        const hotelBiayaKembali = document.getElementById('hotel_biaya_kembali');
        
        perluHotelKembali.addEventListener('change', function() {
            if (this.checked) {
                hotelDetailsKembali.style.display = 'block';
                hotelBiayaKembali.value = '';
            } else {
                hotelDetailsKembali.style.display = 'none';
                hotelBiayaKembali.value = '0';
                hotelNamaKembali.value = '';
            }
        });
        
        // Copy data from Pergi to Kembali tab when clicking on Kembali tab
        document.getElementById('kembali-tab').addEventListener('click', function() {
            // Get values from pergi fields
            const transportasiIdPergi = document.getElementById('transportasi_id_pergi').value;
            const providerPergi = document.getElementById('provider_pergi').value;
            const ruteAsalPergi = document.getElementById('rute_asal_pergi').value;
            const ruteTujuanPergi = document.getElementById('rute_tujuan_pergi').value;
            
            // Only set values if they exist on the Pergi form
            if (transportasiIdPergi) {
                document.getElementById('transportasi_id_kembali').value = transportasiIdPergi;
            }
            
            if (providerPergi) {
                document.getElementById('provider_kembali').value = providerPergi;
            }
            
            // Swap the routes for return trip
            if (ruteAsalPergi && ruteTujuanPergi) {
                document.getElementById('rute_asal_kembali').value = ruteTujuanPergi;
                document.getElementById('rute_tujuan_kembali').value = ruteAsalPergi;
            }
        });
    });
</script>
@endpush