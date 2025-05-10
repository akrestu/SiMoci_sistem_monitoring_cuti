@extends('layouts.app')

@section('title', 'Edit Pengajuan Cuti')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Pengajuan Cuti</h1>
    
    <div class="card mb-4 mt-4">
        <div class="card-body">
            @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <h6 class="mb-0">Terdapat kesalahan pada form:</h6>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <form action="{{ route('cutis.update', $cuti->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="karyawan_id" class="form-label fw-bold">Karyawan <span class="text-danger">*</span></label>
                        <select class="form-select @error('karyawan_id') is-invalid @enderror" id="karyawan_id" name="karyawan_id" required>
                            <option value="">-- Pilih Karyawan --</option>
                            @foreach($karyawans as $karyawan)
                                <option value="{{ $karyawan->id }}" {{ old('karyawan_id', $cuti->karyawan_id) == $karyawan->id ? 'selected' : '' }}>
                                    {{ $karyawan->nama }} ({{ $karyawan->nik }})
                                </option>
                            @endforeach
                        </select>
                        @error('karyawan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="jenis_cuti_id" class="form-label fw-bold">Jenis Cuti <span class="text-danger">*</span></label>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Anda dapat memilih beberapa jenis cuti dan mengatur jumlah hari untuk masing-masing jenis.
                        </div>
                        
                        <div id="jenis-cuti-container">
                            <div class="mb-3">
                                <button type="button" id="btn-add-jenis-cuti" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-plus-circle"></i> Tambah Jenis Cuti
                                </button>
                            </div>
                            
                            <div id="jenis-cuti-list">
                                <!-- Jenis cuti items will be added here -->
                            </div>
                        </div>
                        <input type="hidden" name="jenis_cuti_id" value="{{ old('jenis_cuti_id', $cuti->jenis_cuti_id) }}">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="tanggal_mulai" class="form-label fw-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal_mulai') is-invalid @enderror" id="tanggal_mulai" name="tanggal_mulai" value="{{ old('tanggal_mulai', $cuti->tanggal_mulai) }}" required>
                        @error('tanggal_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="tanggal_selesai" class="form-label fw-bold">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="{{ old('tanggal_selesai', $cuti->tanggal_selesai) }}" readonly>
                        <div class="form-text text-muted">Otomatis dihitung berdasarkan tanggal mulai dan total hari cuti</div>
                        
                        <div class="mt-2">
                            <span class="badge bg-primary">Total Hari Cuti: <span id="total-hari-display">{{ $cuti->lama_hari }}</span> hari</span>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="alasan" class="form-label fw-bold">Alasan Cuti</label>
                    <textarea class="form-control @error('alasan') is-invalid @enderror" id="alasan" name="alasan" rows="3">{{ old('alasan', $cuti->alasan) }}</textarea>
                    @error('alasan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="status_cuti" class="form-label fw-bold">Status Cuti <span class="text-danger">*</span></label>
                    <select class="form-select @error('status_cuti') is-invalid @enderror" id="status_cuti" name="status_cuti" required>
                        <option value="pending" {{ old('status_cuti', $cuti->status_cuti) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="disetujui" {{ old('status_cuti', $cuti->status_cuti) == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ old('status_cuti', $cuti->status_cuti) == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                    @error('status_cuti')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <h5 class="mb-3 mt-4 border-bottom pb-2">Informasi Transportasi</h5>
                
                <div class="mb-3">
                    <div class="form-text mb-2">Pilih transportasi yang diperlukan untuk perjalanan cuti (bisa lebih dari satu). Pastikan untuk mengisi informasi tiket berangkat dan kembali.</div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Untuk setiap jenis transportasi, Anda perlu mengisi informasi tiket pergi (berangkat) dan tiket kembali (pulang).
                    </div>
                    
                    <div class="transportasi-options">
                        @php
                            $selectedTransportasiIds = $cuti->transportasiDetails->pluck('transportasi_id')->toArray();
                        @endphp
                        
                        @foreach($transportasis as $transportasi)
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <div class="form-check">
                                    <input class="form-check-input transportasi-checkbox" type="checkbox" 
                                           name="transportasi_ids[]" 
                                           value="{{ $transportasi->id }}" 
                                           id="transportasi_{{ $transportasi->id }}"
                                           data-transportasi-id="{{ $transportasi->id }}"
                                           {{ in_array($transportasi->id, old('transportasi_ids', $selectedTransportasiIds)) ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="transportasi_{{ $transportasi->id }}">
                                        {{ $transportasi->jenis }}
                                        <small class="text-muted">({{ $transportasi->keterangan }})</small>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="card-body transportasi-details" id="transportasi_details_{{ $transportasi->id }}" style="display: none;">
                                @php
                                    $pergiDetail = $cuti->transportasiDetails
                                        ->where('transportasi_id', $transportasi->id)
                                        ->where('jenis_perjalanan', 'pergi')
                                        ->first();
                                    
                                    $kembaliDetail = $cuti->transportasiDetails
                                        ->where('transportasi_id', $transportasi->id)
                                        ->where('jenis_perjalanan', 'kembali')
                                        ->first();
                                        
                                    $ruteAsalPergi = $pergiDetail ? $pergiDetail->rute_asal : '';
                                    $ruteTujuanPergi = $pergiDetail ? $pergiDetail->rute_tujuan : '';
                                    
                                    $ruteAsalKembali = $kembaliDetail ? $kembaliDetail->rute_asal : '';
                                    $ruteTujuanKembali = $kembaliDetail ? $kembaliDetail->rute_tujuan : '';
                                @endphp
                                
                                <!-- Tiket Pergi -->
                                <h6 class="border-bottom pb-2 mb-3">Tiket Pergi (Berangkat)</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="rute_asal_pergi_{{ $transportasi->id }}" class="form-label">Rute Asal (Berangkat)</label>
                                        <input type="text" class="form-control {{ $transportasi->jenis == 'Pesawat' ? 'input-pesawat' : ($transportasi->jenis == 'Kereta Api' ? 'input-kereta' : '') }}" 
                                               id="rute_asal_pergi_{{ $transportasi->id }}" 
                                               name="rute_asal_pergi_{{ $transportasi->id }}" 
                                               placeholder="Kota/Tempat Asal"
                                               data-jenis="{{ $transportasi->jenis }}"
                                               value="{{ old('rute_asal_pergi_' . $transportasi->id, $ruteAsalPergi) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="rute_tujuan_pergi_{{ $transportasi->id }}" class="form-label">Rute Tujuan (Berangkat)</label>
                                        <input type="text" class="form-control {{ $transportasi->jenis == 'Pesawat' ? 'input-pesawat' : ($transportasi->jenis == 'Kereta Api' ? 'input-kereta' : '') }}" 
                                               id="rute_tujuan_pergi_{{ $transportasi->id }}" 
                                               name="rute_tujuan_pergi_{{ $transportasi->id }}" 
                                               placeholder="Kota/Tempat Tujuan"
                                               data-jenis="{{ $transportasi->jenis }}"
                                               value="{{ old('rute_tujuan_pergi_' . $transportasi->id, $ruteTujuanPergi) }}">
                                    </div>
                                </div>
                                
                                @if($pergiDetail)
                                <div class="mb-3">
                                    <a href="{{ route('transportasi_details.edit', $pergiDetail->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Detail Tiket Pergi
                                    </a>
                                </div>
                                @endif
                                
                                <!-- Tiket Kembali -->
                                <h6 class="border-bottom pb-2 mb-3 mt-4">Tiket Kembali (Pulang)</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="rute_asal_kembali_{{ $transportasi->id }}" class="form-label">Rute Asal (Pulang)</label>
                                        <input type="text" class="form-control {{ $transportasi->jenis == 'Pesawat' ? 'input-pesawat' : ($transportasi->jenis == 'Kereta Api' ? 'input-kereta' : '') }}" 
                                               id="rute_asal_kembali_{{ $transportasi->id }}" 
                                               name="rute_asal_kembali_{{ $transportasi->id }}" 
                                               placeholder="Kota/Tempat Asal (untuk perjalanan pulang)"
                                               data-jenis="{{ $transportasi->jenis }}"
                                               value="{{ old('rute_asal_kembali_' . $transportasi->id, $ruteAsalKembali) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="rute_tujuan_kembali_{{ $transportasi->id }}" class="form-label">Rute Tujuan (Pulang)</label>
                                        <input type="text" class="form-control {{ $transportasi->jenis == 'Pesawat' ? 'input-pesawat' : ($transportasi->jenis == 'Kereta Api' ? 'input-kereta' : '') }}" 
                                               id="rute_tujuan_kembali_{{ $transportasi->id }}" 
                                               name="rute_tujuan_kembali_{{ $transportasi->id }}" 
                                               placeholder="Kota/Tempat Tujuan (untuk perjalanan pulang)"
                                               data-jenis="{{ $transportasi->jenis }}"
                                               value="{{ old('rute_tujuan_kembali_' . $transportasi->id, $ruteTujuanKembali) }}">
                                    </div>
                                </div>
                                
                                @if($kembaliDetail)
                                <div class="mb-3">
                                    <a href="{{ route('transportasi_details.edit', $kembaliDetail->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Detail Tiket Kembali
                                    </a>
                                </div>
                                @endif
                                
                                <div class="form-check mt-3">
                                    <input class="form-check-input auto-swap" type="checkbox" 
                                           id="auto_swap_{{ $transportasi->id }}" 
                                           checked>
                                    <label class="form-check-label" for="auto_swap_{{ $transportasi->id }}">
                                        Otomatis swap rute (tujuan berangkat menjadi asal pulang, dan sebaliknya)
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <a href="{{ route('cutis.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Custom styling for transportasi options */
    .transportasi-options .card-header {
        padding: 0.5rem 1rem;
    }
    .transportasi-options .form-check-label {
        cursor: pointer;
    }
    .transportasi-options .form-check {
        padding-left: 0;
    }
    .transportasi-options .form-check-input {
        margin-left: 0;
        margin-right: 0.5rem;
    }
    .form-label.fw-bold {
        font-size: 0.95rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Edit form script loaded');
        
        // Handle transportasi checkboxes
        $('.transportasi-checkbox').change(function() {
            var transportasiId = $(this).data('transportasi-id');
            var detailsDiv = $('#transportasi_details_' + transportasiId);
            
            if (this.checked) {
                detailsDiv.slideDown(200);
            } else {
                detailsDiv.slideUp(200);
            }
        });
        
        // Initialize transportasi details visibility based on checked state
        $('.transportasi-checkbox').each(function() {
            var transportasiId = $(this).data('transportasi-id');
            var detailsDiv = $('#transportasi_details_' + transportasiId);
            
            if (this.checked) {
                detailsDiv.show();
            } else {
                detailsDiv.hide();
            }
        });
        
        // Handle auto-swap functionality for return tickets
        $('.transportasi-checkbox').each(function() {
            const transportasiId = $(this).data('transportasi-id');
            const autoSwapCheckbox = $(`#auto_swap_${transportasiId}`);
            const ruteAsalPergi = $(`#rute_asal_pergi_${transportasiId}`);
            const ruteTujuanPergi = $(`#rute_tujuan_pergi_${transportasiId}`);
            const ruteAsalKembali = $(`#rute_asal_kembali_${transportasiId}`);
            const ruteTujuanKembali = $(`#rute_tujuan_kembali_${transportasiId}`);
            
            // Update return route when departure route changes
            ruteAsalPergi.on('input', updateReturnRoutes);
            ruteTujuanPergi.on('input', updateReturnRoutes);
            
            function updateReturnRoutes() {
                if (autoSwapCheckbox.prop('checked')) {
                    ruteAsalKembali.val(ruteTujuanPergi.val());
                    ruteTujuanKembali.val(ruteAsalPergi.val());
                }
            }
        });
        
        // Handle multiple jenis cuti selection
        const jenisCutiList = document.getElementById('jenis-cuti-list');
        const btnAddJenisCuti = document.getElementById('btn-add-jenis-cuti');
        const totalHariDisplay = document.getElementById('total-hari-display');
        const tanggalMulaiInput = document.getElementById('tanggal_mulai');
        const tanggalSelesaiInput = document.getElementById('tanggal_selesai');
        
        // Store the available jenis cuti options
        const jenisCutiOptions = [
            @foreach($jenisCutis as $jenisCuti)
            { id: {{ $jenisCuti->id }}, nama: "{{ $jenisCuti->nama_jenis }}", jatah: {{ $jenisCuti->jatah_hari }} },
            @endforeach
        ];
        
        // Current cuti details
        const cutiDetails = [
            @foreach($cuti->cutiDetails as $detail)
            { id: {{ $detail->jenis_cuti_id }}, hari: {{ $detail->jumlah_hari }} },
            @endforeach
        ];
        
        // Add a new jenis cuti item
        btnAddJenisCuti.addEventListener('click', addJenisCutiItem);
        
        function addJenisCutiItem(jenisCutiId = null, jumlahHari = null) {
            const index = jenisCutiList.children.length;
            const jenisCutiItem = document.createElement('div');
            jenisCutiItem.className = 'card mb-3';
            jenisCutiItem.innerHTML = `
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="card-title">Jenis Cuti #${index + 1}</h6>
                        <button type="button" class="btn-close btn-remove-jenis-cuti" aria-label="Close"></button>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-2">
                            <select class="form-select jenis-cuti-select" name="jenis_cuti_details[${index}][jenis_cuti_id]" required>
                                <option value="">-- Pilih Jenis Cuti --</option>
                                ${jenisCutiOptions.map(option => `
                                    <option value="${option.id}" data-jatah="${option.jatah}" ${jenisCutiId == option.id ? 'selected' : ''}>
                                        ${option.nama} (${option.jatah} hari)
                                    </option>
                                `).join('')}
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="input-group">
                                <input type="number" class="form-control jenis-cuti-hari" name="jenis_cuti_details[${index}][jumlah_hari]" 
                                    min="1" placeholder="Hari" required value="${jumlahHari || 1}">
                                <span class="input-group-text">hari</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            jenisCutiList.appendChild(jenisCutiItem);
            
            // Add event listener to remove button
            const removeBtn = jenisCutiItem.querySelector('.btn-remove-jenis-cuti');
            removeBtn.addEventListener('click', function() {
                jenisCutiList.removeChild(jenisCutiItem);
                updateFormIndexes();
                calculateTotalHari();
            });
            
            // Add event listeners to select and input for jatah hari updates
            const jenisCutiSelect = jenisCutiItem.querySelector('.jenis-cuti-select');
            const hariInput = jenisCutiItem.querySelector('.jenis-cuti-hari');
            
            jenisCutiSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const jatahHari = parseInt(selectedOption.dataset.jatah);
                    hariInput.value = jatahHari;
                    hariInput.max = jatahHari;
                    calculateTotalHari();
                }
            });
            
            hariInput.addEventListener('change', calculateTotalHari);
            hariInput.addEventListener('input', calculateTotalHari);
            
            calculateTotalHari();
        }
        
        // Update form indexes when an item is removed
        function updateFormIndexes() {
            const items = jenisCutiList.querySelectorAll('.card');
            items.forEach((item, index) => {
                const title = item.querySelector('.card-title');
                const select = item.querySelector('.jenis-cuti-select');
                const input = item.querySelector('.jenis-cuti-hari');
                
                title.textContent = `Jenis Cuti #${index + 1}`;
                select.name = `jenis_cuti_details[${index}][jenis_cuti_id]`;
                input.name = `jenis_cuti_details[${index}][jumlah_hari]`;
            });
        }
        
        // Calculate total hari and update tanggal_selesai
        function calculateTotalHari() {
            const hariInputs = document.querySelectorAll('.jenis-cuti-hari');
            let totalHari = 0;
            
            hariInputs.forEach(input => {
                if (input.value && !isNaN(input.value)) {
                    totalHari += parseInt(input.value);
                }
            });
            
            totalHariDisplay.textContent = totalHari;
            updateTanggalSelesai(totalHari);
        }
        
        // Update tanggal_selesai based on tanggal_mulai and total hari
        function updateTanggalSelesai(totalHari) {
            const tanggalMulai = tanggalMulaiInput.value;
            if (tanggalMulai && totalHari > 0) {
                const startDate = new Date(tanggalMulai);
                const endDate = new Date(startDate);
                endDate.setDate(startDate.getDate() + totalHari - 1);
                
                // Format the date as YYYY-MM-DD
                const formattedDate = endDate.toISOString().split('T')[0];
                tanggalSelesaiInput.value = formattedDate;
            } else {
                tanggalSelesaiInput.value = '';
            }
        }
        
        // Listen for changes on tanggal_mulai
        tanggalMulaiInput.addEventListener('change', function() {
            calculateTotalHari();
        });
        
        // Initialize with existing cuti details
        if (cutiDetails.length > 0) {
            cutiDetails.forEach(detail => {
                addJenisCutiItem(detail.id, detail.hari);
            });
        } else {
            // If no existing details (fallback to single jenis_cuti_id)
            const primaryJenisCutiId = {{ $cuti->jenis_cuti_id }};
            addJenisCutiItem(primaryJenisCutiId, {{ $cuti->lama_hari }});
        }
        
        // Memo Kompensasi Section
        @if($cuti->isPerluMemoKompensasi() || $cuti->memo_kompensasi_status !== null)
        function toggleMemoDetails() {
            var memoDetailsContainer = document.getElementById('memo-details-container');
            var isMemoNeeded = document.getElementById('is_memo_needed').checked;
            if (isMemoNeeded) {
                memoDetailsContainer.style.display = 'block';
            } else {
                memoDetailsContainer.style.display = 'none';
            }
        }
        @endif
    });
</script>
@endpush