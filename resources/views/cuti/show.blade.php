@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <!-- Header and status bar -->
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('cutis.index') }}" class="text-decoration-none">Pengajuan Cuti</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </nav>
                <h4 class="fw-bold text-primary mt-1 d-flex align-items-center">
                    <i class="fas fa-file-alt me-2"></i>Detail Pengajuan Cuti
                    @php 
                        $statusClass = $cuti->status_cuti == 'pending' ? 'warning' : 
                                      ($cuti->status_cuti == 'disetujui' ? 'success' : 'danger');
                        $statusIcon = $cuti->status_cuti == 'pending' ? 'clock' : 
                                     ($cuti->status_cuti == 'disetujui' ? 'check-circle' : 'times-circle');
                        $statusText = $cuti->status_cuti == 'pending' ? 'Menunggu Persetujuan' : 
                                     ($cuti->status_cuti == 'disetujui' ? 'Disetujui' : 'Ditolak');
                    @endphp
                    <span class="ms-2 badge bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }} border border-{{ $statusClass }} rounded-pill px-3 py-2">
                        <i class="fas fa-{{ $statusIcon }} me-1"></i> {{ $statusText }}
                    </span>
                </h4>
            </div>
            <div class="d-flex gap-2">
                <div class="badge bg-light text-dark border px-3 py-2">
                    <i class="far fa-calendar-alt me-1"></i>
                    {{ \Carbon\Carbon::parse($cuti->created_at)->format('d M Y') }}
                </div>
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
    
    <!-- Main info card -->
    <div class="card border-0 shadow-sm rounded-3 mb-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
            <h5 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i>Informasi Cuti</h5>
            <div>
                <span class="badge bg-primary rounded-pill px-3 py-2">ID: #{{ $cuti->id }}</span>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Karyawan info -->
                <div class="col-md-4">
                    <div class="border-end pe-md-3 h-100">
                        <h6 class="text-muted mb-3"><i class="fas fa-user me-2"></i>Data Karyawan</h6>
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; font-size: 18px;">
                                {{ strtoupper(substr($cuti->karyawan->nama, 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0">{{ $cuti->karyawan->nama }}</h6>
                                <p class="text-muted small mb-0">{{ $cuti->karyawan->nik }}</p>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="bg-light rounded p-2">
                                    <span class="text-muted small d-block">Departemen</span>
                                    <span class="fw-medium">{{ $cuti->karyawan->departemen ?: 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light rounded p-2">
                                    <span class="text-muted small d-block">Jabatan</span>
                                    <span class="fw-medium">{{ $cuti->karyawan->jabatan ?: 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                        @if($cuti->karyawan->poh)
                        <div class="mb-2">
                            <span class="text-muted small d-block">POH</span>
                            <span class="badge bg-info bg-opacity-10 text-info px-2 py-1 rounded">{{ $cuti->karyawan->poh }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Cuti details -->
                <div class="col-md-8">
                    <h6 class="text-muted mb-3"><i class="fas fa-calendar-alt me-2"></i>Detail Cuti</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="bg-light rounded p-3 d-flex align-items-center justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Lama Cuti</small>
                                    <span class="fw-bold fs-5">{{ $cuti->lama_hari }}</span> <small>hari</small>
                                </div>
                                <i class="fas fa-calendar-day text-primary opacity-50 fa-2x"></i>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="bg-light rounded p-3">
                                <small class="text-muted d-block mb-2">Periode Cuti</small>
                                <div class="d-flex align-items-center flex-wrap">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-plus text-success me-2"></i>
                                        <span>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }}</span>
                                    </div>
                                    <i class="fas fa-arrow-right mx-3 text-muted"></i>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-minus text-danger me-2"></i>
                                        <span>{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="bg-light rounded p-3">
                                <small class="text-muted d-block mb-2">Jenis Cuti</small>
                                <div class="d-flex flex-wrap gap-2">
                                    @if($cuti->cutiDetails->count() > 0)
                                        @foreach($cuti->cutiDetails as $detail)
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3 py-2">
                                                {{ $detail->jenisCuti->nama_jenis }} 
                                                <strong>({{ $detail->jumlah_hari }} hari)</strong>
                                                @if(isset($detail->jenisCuti->jenis_poh))
                                                <small class="ms-1 badge {{ $detail->jenisCuti->jenis_poh == 'lokal' ? 'bg-info' : 'bg-warning text-dark' }}">
                                                    {{ ucfirst($detail->jenisCuti->jenis_poh) }}
                                                </small>
                                                @endif
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3 py-2">
                                            {{ $cuti->jenisCuti->nama_jenis }}
                                            @if(isset($cuti->jenisCuti->jenis_poh))
                                            <small class="ms-1 badge {{ $cuti->jenisCuti->jenis_poh == 'lokal' ? 'bg-info' : 'bg-warning text-dark' }}">
                                                {{ ucfirst($cuti->jenis_poh) }}
                                            </small>
                                            @endif
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <!-- Alasan -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-light d-flex align-items-center py-2">
                    <i class="fas fa-comment-alt text-info me-2"></i>
                    <h6 class="mb-0 fw-bold">Alasan Cuti</h6>
                </div>
                <div class="card-body">
                    @if($cuti->alasan)
                        <div class="alert alert-light mb-0 border">
                            <div class="d-flex">
                                <i class="fas fa-quote-left text-primary me-3 opacity-50 fa-2x"></i>
                                <p class="mb-0">{{ $cuti->alasan }}</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-comment-slash text-muted fa-2x mb-2"></i>
                            <p class="text-muted mb-0">Tidak ada alasan yang dicantumkan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Memo Kompensasi / Status Info -->
        <div class="col-md-6">
            @if($cuti->isPerluMemoKompensasi())
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-light d-flex align-items-center py-2">
                        <i class="fas fa-file-alt text-warning me-2"></i>
                        <h6 class="mb-0 fw-bold">Memo Kompensasi</h6>
                    </div>
                    <div class="card-body">
                        @if($cuti->memo_kompensasi_status)
                            <div class="alert alert-success bg-success bg-opacity-10 mb-2 border-success d-flex align-items-center">
                                <i class="fas fa-check-circle text-success fa-2x me-3"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">Memo Kompensasi Sudah Diajukan</h6>
                                    <p class="mb-0 small">Nomor: <strong>{{ $cuti->memo_kompensasi_nomor }}</strong> | 
                                       Tanggal: <strong>{{ \Carbon\Carbon::parse($cuti->memo_kompensasi_tanggal)->format('d M Y') }}</strong></p>
                                </div>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('memo-kompensasi.index') }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-search me-1"></i> Lihat Memo
                                </a>
                            </div>
                        @else
                            <div class="alert alert-warning bg-warning bg-opacity-10 border-warning d-flex">
                                <i class="fas fa-exclamation-triangle text-warning fa-2x me-3"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">Memo Kompensasi Belum Diajukan</h6>
                                    <p class="mb-0 small">Pengajuan cuti ini memerlukan memo kompensasi</p>
                                </div>
                            </div>
                            <div class="text-center mt-2">
                                <a href="{{ route('memo-kompensasi.index') }}" class="btn btn-warning">
                                    <i class="fas fa-file-alt me-2"></i> Buat Memo Kompensasi
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-light d-flex align-items-center py-2">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        <h6 class="mb-0 fw-bold">Status Pengajuan</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span>ID Pengajuan</span>
                                        <span class="badge bg-primary rounded-pill">#{{ $cuti->id }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span>Tanggal Pengajuan</span>
                                        <span class="fw-medium">{{ \Carbon\Carbon::parse($cuti->created_at)->format('d M Y') }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <span>Status</span>
                                        <span class="badge bg-{{ $statusClass }} rounded-pill">{{ $statusText }}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="text-center mt-3">
                                @if($cuti->status_cuti == 'pending')
                                    <div class="d-flex gap-2 justify-content-center">
                                        <form action="{{ route('cutis.approve', $cuti->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success">
                                                <i class="fas fa-check-circle me-1"></i> Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('cutis.reject', $cuti->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="fas fa-times-circle me-1"></i> Tolak
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Transportasi -->
    @if($cuti->transportasiDetails->count() > 0)
    <div class="card border-0 shadow-sm rounded-3 mb-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
            <h6 class="mb-0 fw-bold"><i class="fas fa-ticket-alt text-warning me-2"></i>Transportasi</h6>
            @if($cuti->status_cuti == 'disetujui')
            <a href="{{ url('/cutis/' . $cuti->id . '/transportasi-details/create') }}" class="btn btn-sm btn-success">
                <i class="fas fa-plus me-1"></i> Tambah
            </a>
            @endif
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Jenis</th>
                            <th>Rute</th>
                            <th>Waktu</th>
                            <th>Status</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cuti->transportasiDetails as $detail)
                        <tr>
                            <td class="ps-3">
                                <div class="d-flex align-items-center">
                                    @php
                                        $iconClass = 'car';
                                        $colorClass = 'secondary';
                                        if(strtolower($detail->transportasi->jenis) == 'pesawat') {
                                            $iconClass = 'plane';
                                            $colorClass = 'primary';
                                        } elseif(strtolower($detail->transportasi->jenis) == 'kereta') {
                                            $iconClass = 'train';
                                            $colorClass = 'success';
                                        } elseif(strtolower($detail->transportasi->jenis) == 'bus') {
                                            $iconClass = 'bus';
                                            $colorClass = 'info';
                                        }
                                    @endphp
                                    <div class="bg-{{ $colorClass }} bg-opacity-10 rounded-circle p-2 me-2">
                                        <i class="fas fa-{{ $iconClass }} text-{{ $colorClass }}"></i>
                                    </div>
                                    <div>
                                        <span class="fw-medium d-block">{{ $detail->transportasi->jenis }}</span>
                                        <span class="badge bg-{{ $detail->jenis_perjalanan == 'pergi' ? 'primary' : 'success' }} bg-opacity-10 text-{{ $detail->jenis_perjalanan == 'pergi' ? 'primary' : 'success' }} border border-{{ $detail->jenis_perjalanan == 'pergi' ? 'primary' : 'success' }}-subtle">
                                            {{ ucfirst($detail->jenis_perjalanan) }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <small class="fw-medium">{{ $detail->rute_asal }}</small>
                                    <i class="fas fa-long-arrow-alt-right mx-2 text-muted"></i>
                                    <small class="fw-medium">{{ $detail->rute_tujuan }}</small>
                                </div>
                                @if($detail->nomor_tiket)
                                <small class="text-muted d-block">Tiket: {{ $detail->nomor_tiket }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="small">
                                    @if($detail->waktu_berangkat)
                                    <div class="mb-1">
                                        <i class="far fa-calendar text-primary me-1"></i>
                                        {{ \Carbon\Carbon::parse($detail->waktu_berangkat)->format('d/m/Y') }}
                                    </div>
                                    @endif
                                    
                                    <div class="d-flex align-items-center">
                                        @if($detail->biaya_aktual > 0)
                                        <span class="badge bg-success bg-opacity-10 text-success me-2">
                                            <i class="fas fa-money-bill-wave me-1"></i>
                                            {{ number_format($detail->biaya_aktual, 0, ',', '.') }}
                                        </span>
                                        @endif
                                        
                                        <span class="badge {{ 
                                            $detail->status_pemesanan == 'belum_dipesan' ? 'bg-warning bg-opacity-10 text-warning' : 
                                            ($detail->status_pemesanan == 'dipesan' ? 'bg-info bg-opacity-10 text-info' : 
                                            'bg-success bg-opacity-10 text-success') 
                                        }} border border-{{ 
                                            $detail->status_pemesanan == 'belum_dipesan' ? 'warning' : 
                                            ($detail->status_pemesanan == 'dipesan' ? 'info' : 
                                            'success') 
                                        }}-subtle">
                                            {{ ucwords(str_replace('_', ' ', $detail->status_pemesanan)) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-muted">
                                    @if($detail->provider)
                                    {{ $detail->provider }}
                                    @else
                                    -
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('transportasi_details.edit', $detail->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @elseif($cuti->status_cuti == 'disetujui')
    <div class="card border-0 shadow-sm rounded-3 mb-3">
        <div class="card-header bg-light d-flex align-items-center py-2">
            <i class="fas fa-ticket-alt text-warning me-2"></i>
            <h6 class="mb-0 fw-bold">Transportasi</h6>
        </div>
        <div class="card-body text-center py-4">
            <img src="https://cdn-icons-png.flaticon.com/512/9841/9841744.png" alt="No Transportation" class="mb-3" style="width: 80px; height: 80px; opacity: 0.5">
            <h6 class="text-muted">Belum Ada Informasi Transportasi</h6>
            <p class="text-muted small mb-3 mx-auto" style="max-width: 400px;">Cuti ini belum memiliki detail transportasi. Jika diperlukan, silakan tambahkan informasi transportasi.</p>
            <a href="{{ url('/cutis/' . $cuti->id . '/transportasi-details/create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Tambah Transportasi
            </a>
        </div>
    </div>
    @endif
    
    <!-- Action Buttons -->
    <div class="card border-0 shadow-sm rounded-3 mb-3">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap gap-2 justify-content-between">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('cutis.edit', $cuti->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i> Edit
                    </a>
                    
                    <form action="{{ route('cutis.destroy', $cuti->id) }}" method="POST" id="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-outline-danger delete-btn" 
                                data-id="{{ $cuti->id }}"
                                data-nama="{{ $cuti->karyawan->nama }}"
                                data-tanggal="{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') }}">
                            <i class="fas fa-trash me-2"></i> Hapus
                        </button>
                    </form>
                </div>
                
                <a href="{{ route('cutis.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-list me-2"></i> Daftar Pengajuan
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Cuti -->
<div class="modal fade" id="deleteCutiModal" tabindex="-1" aria-labelledby="deleteCutiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="deleteCutiModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i> Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="avatar bg-danger bg-opacity-10 text-danger mb-3 mx-auto" style="width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-trash fa-2x"></i>
                    </div>
                    <h4 class="fw-bold">Konfirmasi Penghapusan</h4>
                    <p class="text-muted">Apakah Anda yakin ingin menghapus pengajuan cuti ini?</p>
                </div>

                <div class="bg-light p-3 rounded-3 border-start border-danger border-4 mb-3">
                    <p class="mb-2"><strong>Nama:</strong> <span id="modal-karyawan-nama"></span></p>
                    <p class="mb-0"><strong>Tanggal:</strong> <span id="modal-tanggal-cuti"></span></p>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i> Semua data terkait pengajuan cuti ini juga akan dihapus termasuk data transportasi.
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Batal
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirmDelete">
                    <i class="fas fa-trash me-2"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Clean and compact styling */
    .badge {
        font-weight: 500;
        letter-spacing: 0.3px;
    }
    
    .card {
        transition: transform 0.2s;
        overflow: hidden;
    }
    
    .avatar {
        font-weight: bold;
    }
    
    .rounded-pill {
        border-radius: 50rem !important;
    }
    
    .rounded-3 {
        border-radius: 0.5rem !important;
    }
    
    /* Modern compact table overrides */
    .table > :not(caption) > * > * {
        padding: 0.6rem 0.75rem;
    }
    
    /* Badge styling */
    .bg-opacity-10 {
        --bs-bg-opacity: 0.1;
    }
    
    /* Card header styling */
    .card-header {
        padding: 0.75rem 1rem;
    }
    
    /* More compact padding */
    .p-3 {
        padding: 0.75rem !important;
    }
    
    /* Fix list group flush */
    .list-group-flush > .list-group-item {
        border-width: 0 0 1px;
    }
    
    .list-group-flush > .list-group-item:last-child {
        border-bottom-width: 0;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteBtn = document.querySelector('.delete-btn');
        const deleteForm = document.getElementById('delete-form');
        const confirmDeleteBtn = document.getElementById('confirmDelete');
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteCutiModal'));
        
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                const nama = this.getAttribute('data-nama');
                const tanggal = this.getAttribute('data-tanggal');
                
                // Set data in modal
                document.getElementById('modal-karyawan-nama').textContent = nama;
                document.getElementById('modal-tanggal-cuti').textContent = tanggal;
                
                // Show modal
                deleteModal.show();
            });
        }
        
        // Submit form when confirm button is clicked
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function() {
                deleteForm.submit();
            });
        }
    });
</script>
@endpush
@endsection