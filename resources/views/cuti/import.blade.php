@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('cutis.index') }}" class="text-decoration-none">Pengajuan Cuti</a></li>
                    <li class="breadcrumb-item active">Import Data</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-primary"><i class="fas fa-file-import me-2"></i>Import Data Pengajuan Cuti</h2>
        </div>
        <a href="{{ route('cutis.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
            <div class="d-flex">
                <div class="me-3">
                    <i class="fas fa-exclamation-circle fa-2x text-danger"></i>
                </div>
                <div>
                    <h5 class="alert-heading mb-1">Terjadi Kesalahan</h5>
                    <p class="mb-0">{{ session('error') }}</p>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <!-- Header with Steps -->
        <div class="card-header bg-primary bg-gradient text-white p-4 border-0">
            <h4 class="mb-3">Langkah-langkah Import Data</h4>
            <div class="d-flex flex-column flex-lg-row gap-4">
                <div class="step d-flex align-items-center">
                    <div class="step-icon rounded-circle bg-white text-primary d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="fas fa-download"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">1. Download Template</h6>
                        <p class="mb-0 text-white-50 small">Unduh template Excel</p>
                    </div>
                </div>
                <div class="step-divider d-none d-lg-block text-white px-2">
                    <i class="fas fa-chevron-right"></i>
                </div>
                <div class="step d-flex align-items-center">
                    <div class="step-icon rounded-circle bg-white text-primary d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">2. Isi Data</h6>
                        <p class="mb-0 text-white-50 small">Sesuai format yang tersedia</p>
                    </div>
                </div>
                <div class="step-divider d-none d-lg-block text-white px-2">
                    <i class="fas fa-chevron-right"></i>
                </div>
                <div class="step d-flex align-items-center">
                    <div class="step-icon rounded-circle bg-white text-primary d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        <i class="fas fa-upload"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">3. Upload File</h6>
                        <p class="mb-0 text-white-50 small">Dan proses import</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body p-4 p-lg-5">
            <div class="row">
                <div class="col-lg-5 mb-4 mb-lg-0">
                    <div class="pe-lg-4">
                        <!-- Instructions Card -->
                        <div class="card border-0 bg-light rounded-4 mb-4 h-100">
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold mb-3">
                                    <i class="fas fa-info-circle text-primary me-2"></i>Petunjuk Import
                                </h5>
                                <div class="mb-4">
                                    <ol class="ps-3">
                                        <li class="mb-2">Download <a href="#" id="download-template-btn" class="fw-bold text-decoration-none">template Excel</a> terlebih dahulu</li>
                                        <li class="mb-2">Pastikan NIK karyawan sudah terdaftar dalam sistem</li>
                                        <li class="mb-2">Pastikan nama jenis cuti sesuai dengan yang tersedia dalam sistem</li>
                                        <li class="mb-2">Format tanggal harus menggunakan format DD/MM/YYYY (contoh: 26/04/2025)</li>
                                        <li class="mb-2">Untuk memo kompensasi, isi kolom "perlu_memo_kompensasi" dengan "ya" atau "tidak"</li>
                                        <li class="mb-2">Untuk transportasi, pastikan jenis transportasi yang diisi sudah terdaftar dalam sistem</li>
                                        <li class="mb-2">Upload file yang sudah diisi</li>
                                    </ol>
                                </div>

                                <div class="alert alert-info rounded-3 border-0 shadow-sm">
                                    <i class="fas fa-info-circle me-2"></i> Status cuti default akan diatur sebagai "pending" jika tidak diisi
                                </div>

                                <div class="alert alert-primary rounded-3 border-0 shadow-sm">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            <i class="fas fa-lightbulb"></i>
                                        </div>
                                        <div>
                                            <strong>Fitur baru:</strong> Sekarang Anda dapat mengimpor data transportasi dan memo kompensasi langsung melalui file Excel
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-7">
                    <!-- Template Structure Card -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-light py-3 border-0">
                            <h5 class="card-title mb-0 fw-bold">
                                <i class="fas fa-table text-primary me-2"></i>Struktur Template
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <h6 class="fw-bold text-primary mb-3">Kolom Wajib:</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><span class="badge bg-primary me-2">nik</span> NIK Karyawan</li>
                                        <li class="mb-2"><span class="badge bg-primary me-2">jenis_cuti</span> Nama jenis cuti</li>
                                        <li class="mb-2"><span class="badge bg-primary me-2">tanggal_mulai</span> Format DD/MM/YYYY</li>
                                        <li class="mb-2"><span class="badge bg-primary me-2">tanggal_selesai</span> Format DD/MM/YYYY</li>
                                        <li class="mb-2"><span class="badge bg-primary me-2">alasan</span> Alasan pengajuan cuti</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold text-success mb-3">Kolom Opsional:</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2"><span class="badge bg-success me-2">status_cuti</span> pending, disetujui, ditolak</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">perlu_memo_kompensasi</span> ya/tidak</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">memo_nomor</span> Nomor memo kompensasi</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">memo_tanggal</span> Format DD/MM/YYYY</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">transportasi_jenis</span> Pesawat, Kereta Api, dll</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">transportasi_rute_pergi_asal</span> Asal keberangkatan</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">transportasi_rute_pergi_tujuan</span> Tujuan keberangkatan</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">transportasi_rute_kembali_asal</span> Asal kepulangan</li>
                                        <li class="mb-2"><span class="badge bg-success me-2">transportasi_rute_kembali_tujuan</span> Tujuan kepulangan</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Form Card -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header bg-light py-3 border-0">
                            <h5 class="card-title mb-0 fw-bold">
                                <i class="fas fa-upload text-primary me-2"></i>Upload File Excel
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('cutis.import.process') }}" method="POST" enctype="multipart/form-data" class="upload-form">
                                @csrf
                                <div class="mb-4">
                                    <div class="file-upload-container">
                                        <input type="file" class="file-input-hidden @error('file') is-invalid @enderror" id="file" name="file" accept=".xlsx, .xls, .csv">
                                        <div class="file-upload-box border border-2 border-dashed rounded-3 p-4 mb-3 text-center position-relative">
                                            <div class="file-upload-placeholder" id="file-upload-placeholder">
                                                <i class="fas fa-file-excel fa-3x text-primary mb-3"></i>
                                                <h5>Pilih file atau seret ke sini</h5>
                                                <p class="text-muted mb-2">Format file: XLSX, XLS, CSV</p>
                                                <button type="button" class="btn btn-primary px-4" onclick="document.getElementById('file').click()">
                                                    <i class="fas fa-folder-open me-2"></i>Pilih File
                                                </button>
                                            </div>
                                            <div class="file-upload-preview d-none" id="file-upload-preview">
                                                <i class="fas fa-file-excel fa-2x text-success mb-3"></i>
                                                <h5 id="file-name" class="mb-2">filename.xlsx</h5>
                                                <p class="text-muted mb-2">Klik tombol "Ganti File" untuk memilih file lain</p>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="resetFileInput()">
                                                        <i class="fas fa-sync-alt me-2"></i>Ganti File
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        @error('file')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">
                                            <i class="fas fa-info-circle me-1"></i>Maksimum ukuran file: 2MB
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('cutis.template.download') }}" class="btn btn-outline-primary px-4">
                                        <i class="fas fa-download me-2"></i>Download Template
                                    </a>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-file-import me-2"></i>Import Data
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .step-icon {
        box-shadow: 0 0 0 5px rgba(255, 255, 255, 0.2);
    }
    
    .file-input-hidden {
        width: 0.1px;
        height: 0.1px;
        opacity: 0;
        overflow: hidden;
        position: absolute;
        z-index: -1;
    }
    
    .file-upload-box {
        cursor: pointer;
        transition: all 0.3s;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .file-upload-box:hover {
        border-color: var(--bs-primary) !important;
        background-color: rgba(13, 110, 253, 0.04);
    }
    
    .file-upload-placeholder, .file-upload-preview {
        width: 100%;
    }
    
    @media (max-width: 991.98px) {
        .step {
            margin-bottom: 1.5rem;
        }
        .step-divider {
            display: none;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // File upload preview handling
        const fileInput = document.getElementById('file');
        const fileUploadBox = document.querySelector('.file-upload-box');
        const fileUploadPlaceholder = document.getElementById('file-upload-placeholder');
        const fileUploadPreview = document.getElementById('file-upload-preview');
        const fileName = document.getElementById('file-name');
        
        // Click on box to trigger file input
        fileUploadBox.addEventListener('click', function(e) {
            if (e.target.tagName !== 'BUTTON') {
                fileInput.click();
            }
        });
        
        // Handle file selection
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const file = this.files[0];
                fileName.textContent = file.name;
                fileUploadPlaceholder.classList.add('d-none');
                fileUploadPreview.classList.remove('d-none');
            }
        });
        
        // Handle drag and drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileUploadBox.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            fileUploadBox.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            fileUploadBox.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            fileUploadBox.classList.add('border-primary');
            fileUploadBox.style.backgroundColor = 'rgba(13, 110, 253, 0.04)';
        }
        
        function unhighlight() {
            fileUploadBox.classList.remove('border-primary');
            fileUploadBox.style.backgroundColor = '';
        }
        
        fileUploadBox.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files && files.length) {
                fileInput.files = files;
                const file = files[0];
                fileName.textContent = file.name;
                fileUploadPlaceholder.classList.add('d-none');
                fileUploadPreview.classList.remove('d-none');
            }
        }
        
        // Download template button handler
        document.getElementById('download-template-btn').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = "{{ route('cutis.template.download') }}";
        });
    });
    
    // Reset file input
    function resetFileInput() {
        const fileInput = document.getElementById('file');
        const fileUploadPlaceholder = document.getElementById('file-upload-placeholder');
        const fileUploadPreview = document.getElementById('file-upload-preview');
        
        fileInput.value = '';
        fileUploadPlaceholder.classList.remove('d-none');
        fileUploadPreview.classList.add('d-none');
    }
</script>
@endpush