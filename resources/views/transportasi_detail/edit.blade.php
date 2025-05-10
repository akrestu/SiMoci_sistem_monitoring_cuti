@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Detail Transportasi</h2>
        <a href="javascript:history.back()" class="btn btn-secondary">
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
                    <td>{{ $transportasiDetail->cuti->karyawan->nama }}</td>
                </tr>
                <tr>
                    <th>Jenis Cuti</th>
                    <td>{{ $transportasiDetail->cuti->jenisCuti->nama_jenis }}</td>
                </tr>
                <tr>
                    <th>Tanggal Cuti</th>
                    <td>{{ \Carbon\Carbon::parse($transportasiDetail->cuti->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($transportasiDetail->cuti->tanggal_selesai)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <th>Lama Cuti</th>
                    <td>{{ $transportasiDetail->cuti->lama_hari }} hari</td>
                </tr>
                <tr>
                    <th>Alasan</th>
                    <td>{{ $transportasiDetail->cuti->alasan }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="mb-0">Form Detail Transportasi</h5>
        </div>
        <div class="card-body">
            <form action="{{ url('/transportasi-details/' . $transportasiDetail->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="transportasi_id" class="form-label">Jenis Transportasi</label>
                        <select name="transportasi_id" id="transportasi_id" class="form-select @error('transportasi_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis Transportasi --</option>
                            @foreach($transportasis as $transportasi)
                                <option value="{{ $transportasi->id }}" {{ $transportasiDetail->transportasi_id == $transportasi->id ? 'selected' : '' }}>{{ $transportasi->jenis }}</option>
                            @endforeach
                        </select>
                        @error('transportasi_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="provider" class="form-label">Provider/Maskapai</label>
                        <select name="provider" id="provider" class="form-select @error('provider') is-invalid @enderror" required>
                            <option value="">-- Pilih Provider/Maskapai --</option>
                            <!-- Maskapai Penerbangan -->
                            <optgroup label="Maskapai Penerbangan">
                                <option value="Garuda Indonesia" {{ $transportasiDetail->provider == 'Garuda Indonesia' ? 'selected' : '' }}>Garuda Indonesia</option>
                                <option value="Lion Air" {{ $transportasiDetail->provider == 'Lion Air' ? 'selected' : '' }}>Lion Air</option>
                                <option value="Batik Air" {{ $transportasiDetail->provider == 'Batik Air' ? 'selected' : '' }}>Batik Air</option>
                                <option value="Citilink" {{ $transportasiDetail->provider == 'Citilink' ? 'selected' : '' }}>Citilink</option>
                                <option value="Sriwijaya Air" {{ $transportasiDetail->provider == 'Sriwijaya Air' ? 'selected' : '' }}>Sriwijaya Air</option>
                                <option value="Wings Air" {{ $transportasiDetail->provider == 'Wings Air' ? 'selected' : '' }}>Wings Air</option>
                                <option value="AirAsia" {{ $transportasiDetail->provider == 'AirAsia' ? 'selected' : '' }}>AirAsia</option>
                                <option value="Super Air Jet" {{ $transportasiDetail->provider == 'Super Air Jet' ? 'selected' : '' }}>Super Air Jet</option>
                                <option value="TransNusa" {{ $transportasiDetail->provider == 'TransNusa' ? 'selected' : '' }}>TransNusa</option>
                                <option value="Nam Air" {{ $transportasiDetail->provider == 'Nam Air' ? 'selected' : '' }}>Nam Air</option>
                            </optgroup>
                            <!-- Kereta Api -->
                            <optgroup label="Kereta Api">
                                <option value="KAI" {{ $transportasiDetail->provider == 'KAI' ? 'selected' : '' }}>KAI (Kereta Api Indonesia)</option>
                            </optgroup>
                            <!-- Lainnya -->
                            <optgroup label="Lainnya">
                                <option value="Lainnya" {{ !in_array($transportasiDetail->provider, ['Garuda Indonesia', 'Lion Air', 'Batik Air', 'Citilink', 'Sriwijaya Air', 'Wings Air', 'AirAsia', 'Super Air Jet', 'TransNusa', 'Nam Air', 'KAI']) && $transportasiDetail->provider ? 'selected' : '' }}>Lainnya</option>
                            </optgroup>
                        </select>
                        @error('provider')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Jenis Perjalanan</label>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>{{ $transportasiDetail->jenis_perjalanan == 'pergi' ? 'Tiket Pergi (Berangkat)' : 'Tiket Kembali (Pulang)' }}</strong>
                            <input type="hidden" name="jenis_perjalanan" value="{{ $transportasiDetail->jenis_perjalanan }}">
                        </div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="rute_asal" class="form-label">Rute Asal</label>
                        <input type="text" name="rute_asal" id="rute_asal" value="{{ $transportasiDetail->rute_asal }}" 
                            class="form-control @error('rute_asal') is-invalid @enderror input-rute {{ $transportasiDetail->transportasi->jenis == 'Pesawat' ? 'input-pesawat' : ($transportasiDetail->transportasi->jenis == 'Kereta Api' ? 'input-kereta' : '') }}" 
                            data-jenis="{{ $transportasiDetail->transportasi->jenis }}"
                            required>
                        @error('rute_asal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="rute_tujuan" class="form-label">Rute Tujuan</label>
                        <input type="text" name="rute_tujuan" id="rute_tujuan" value="{{ $transportasiDetail->rute_tujuan }}" 
                            class="form-control @error('rute_tujuan') is-invalid @enderror input-rute {{ $transportasiDetail->transportasi->jenis == 'Pesawat' ? 'input-pesawat' : ($transportasiDetail->transportasi->jenis == 'Kereta Api' ? 'input-kereta' : '') }}" 
                            data-jenis="{{ $transportasiDetail->transportasi->jenis }}"
                            required>
                        @error('rute_tujuan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="waktu_berangkat" class="form-label">Waktu Keberangkatan</label>
                        <input type="datetime-local" name="waktu_berangkat" id="waktu_berangkat" value="{{ \Carbon\Carbon::parse($transportasiDetail->waktu_berangkat)->format('Y-m-d\TH:i') }}" class="form-control @error('waktu_berangkat') is-invalid @enderror" required>
                        @error('waktu_berangkat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text text-danger">
                            @php
                                $deadline = $transportasiDetail->jenis_perjalanan == 'pergi' 
                                    ? Carbon\Carbon::parse($transportasiDetail->cuti->tanggal_mulai) 
                                    : Carbon\Carbon::parse($transportasiDetail->cuti->tanggal_selesai);
                                $daysUntilDeadline = (int)now()->diffInDays($deadline, false);
                            @endphp
                            @if($daysUntilDeadline < 7 && $daysUntilDeadline >= 0)
                                <i class="fas fa-exclamation-triangle"></i> <strong>Segera dipesan!</strong> {{ $daysUntilDeadline }} hari menuju tanggal keberangkatan.
                            @elseif($daysUntilDeadline < 0)
                                <i class="fas fa-exclamation-circle"></i> <strong>Peringatan!</strong> Tanggal keberangkatan sudah lewat.
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="nomor_tiket" class="form-label">Nomor Tiket</label>
                        <input type="text" name="nomor_tiket" id="nomor_tiket" value="{{ $transportasiDetail->nomor_tiket }}" class="form-control @error('nomor_tiket') is-invalid @enderror">
                        @error('nomor_tiket')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Hidden input for waktu_kembali to maintain compatibility -->
                <input type="hidden" name="waktu_kembali" value="{{ \Carbon\Carbon::parse($transportasiDetail->waktu_berangkat)->addHours(1)->format('Y-m-d\TH:i') }}">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="biaya_aktual" class="form-label">Biaya Aktual (Rp)</label>
                        <input type="text" name="biaya_aktual" id="biaya_aktual" value="{{ number_format($transportasiDetail->biaya_aktual, 0, ',', '.') }}" class="form-control currency-input @error('biaya_aktual') is-invalid @enderror">
                        <input type="hidden" name="biaya_aktual_hidden" id="biaya_aktual_hidden" value="{{ $transportasiDetail->biaya_aktual }}">
                        @error('biaya_aktual')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="status_pemesanan" class="form-label">Status Pemesanan</label>
                        <select name="status_pemesanan" id="status_pemesanan" class="form-select @error('status_pemesanan') is-invalid @enderror" required>
                            <option value="belum_dipesan" {{ $transportasiDetail->status_pemesanan == 'belum_dipesan' ? 'selected' : '' }}>Belum Dipesan</option>
                            <option value="proses_pemesanan" {{ $transportasiDetail->status_pemesanan == 'proses_pemesanan' ? 'selected' : '' }}>Proses Pemesanan</option>
                            <option value="tiket_terbit" {{ $transportasiDetail->status_pemesanan == 'tiket_terbit' ? 'selected' : '' }}>Tiket Terbit</option>
                            <option value="dibatalkan" {{ $transportasiDetail->status_pemesanan == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                        @error('status_pemesanan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="perlu_hotel" name="perlu_hotel" {{ $transportasiDetail->perlu_hotel ? 'checked' : '' }}>
                        <label class="form-check-label" for="perlu_hotel">
                            Memerlukan Akomodasi Hotel
                        </label>
                    </div>
                </div>
                
                <div id="hotel_details" style="display: {{ $transportasiDetail->perlu_hotel ? 'block' : 'none' }};">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="hotel_nama" class="form-label">Nama Hotel</label>
                            <input type="text" name="hotel_nama" id="hotel_nama" value="{{ $transportasiDetail->hotel_nama }}" class="form-control @error('hotel_nama') is-invalid @enderror">
                            @error('hotel_nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="hotel_biaya" class="form-label">Biaya Hotel (Rp)</label>
                            <input type="text" name="hotel_biaya" id="hotel_biaya" value="{{ number_format($transportasiDetail->hotel_biaya, 0, ',', '.') }}" class="form-control currency-input @error('hotel_biaya') is-invalid @enderror">
                            <input type="hidden" name="hotel_biaya_hidden" id="hotel_biaya_hidden" value="{{ $transportasiDetail->hotel_biaya }}">
                            @error('hotel_biaya')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="catatan" class="form-label">Catatan</label>
                    <textarea name="catatan" id="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="3">{{ $transportasiDetail->catatan }}</textarea>
                    @error('catatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Perbarui Detail Transportasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/city-dropdown.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Direct implementation of currency input with thousands separator
        function setupCurrencyInput(inputId, hiddenInputId) {
            const inputElement = document.getElementById(inputId);
            const hiddenInputElement = document.getElementById(hiddenInputId);
            
            if (!inputElement || !hiddenInputElement) return;
            
            // Format a number with thousand separators (Indonesian format)
            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }
            
            // Remove formatting
            function unformatNumber(str) {
                return str.replace(/\./g, "");
            }
            
            // Set initial values
            const initialValue = unformatNumber(inputElement.value || "0");
            hiddenInputElement.value = initialValue;
            inputElement.value = formatNumber(initialValue);
            
            // Handle user input
            inputElement.addEventListener('input', function() {
                // Store current cursor position
                const cursorPos = this.selectionStart;
                const previousLength = this.value.length;
                
                // Get clean number from input
                let value = unformatNumber(this.value);
                
                // Update both displayed and hidden values
                this.value = formatNumber(value);
                hiddenInputElement.value = value;
                
                // Adjust cursor position based on length change
                const newLength = this.value.length;
                const newPosition = cursorPos + (newLength - previousLength);
                this.setSelectionRange(newPosition, newPosition);
            });
            
            // Set proper value on form submission
            inputElement.form.addEventListener('submit', function() {
                hiddenInputElement.name = inputElement.name;
                inputElement.name = inputElement.name + '_formatted';
            });
        }
        
        // Setup currency inputs
        setupCurrencyInput('biaya_aktual', 'biaya_aktual_hidden');
        setupCurrencyInput('hotel_biaya', 'hotel_biaya_hidden');
        
        // Hotel toggle handling
        const perluHotel = document.getElementById('perlu_hotel');
        const hotelDetails = document.getElementById('hotel_details');
        const hotelNama = document.getElementById('hotel_nama');
        const hotelBiaya = document.getElementById('hotel_biaya');
        const hotelBiayaHidden = document.getElementById('hotel_biaya_hidden');
        
        perluHotel.addEventListener('change', function() {
            if (this.checked) {
                hotelDetails.style.display = 'block';
            } else {
                hotelDetails.style.display = 'none';
                // Reset hotel fields when unchecked
                hotelNama.value = '';
                hotelBiaya.value = '0';
                hotelBiayaHidden.value = '0';
            }
        });
        
        // Auto-swap rute asal and tujuan based on jenis perjalanan
        const jenisPergi = document.getElementById('jenis_pergi');
        const jenisKembali = document.getElementById('jenis_kembali');
        const ruteAsal = document.getElementById('rute_asal');
        const ruteTujuan = document.getElementById('rute_tujuan');
        
        if (jenisPergi && jenisKembali && ruteAsal && ruteTujuan) {
            let originalRuteAsal = ruteAsal.value;
            let originalRuteTujuan = ruteTujuan.value;
            
            jenisPergi?.addEventListener('change', function() {
                if (this.checked) {
                    ruteAsal.value = originalRuteAsal;
                    ruteTujuan.value = originalRuteTujuan;
                }
            });
            
            jenisKembali?.addEventListener('change', function() {
                if (this.checked) {
                    ruteAsal.value = originalRuteTujuan;
                    ruteTujuan.value = originalRuteAsal;
                }
            });
        }
    });
</script>
@endsection