@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detail Transportasi</h2>
        <div>
            <a href="{{ url('/transportasi-details/' . $transportasiDetail->id . '/edit') }}" class="btn btn-warning">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ url('/transportasi-details') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
    
    <div class="row g-4 mb-4">
        <!-- Card Informasi Karyawan & Cuti -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Informasi Karyawan & Cuti</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180" class="ps-0"><i class="fas fa-user me-2 text-primary"></i>Nama Karyawan</th>
                                <td class="fw-bold">{{ $transportasiDetail->cuti->karyawan->nama }}</td>
                            </tr>
                            <tr>
                                <th class="ps-0"><i class="fas fa-building me-2 text-primary"></i>Departemen</th>
                                <td>{{ $transportasiDetail->cuti->karyawan->departemen }}</td>
                            </tr>
                            <tr>
                                <th class="ps-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>Jenis Cuti</th>
                                <td>{{ $transportasiDetail->cuti->jenisCuti->nama_jenis }}</td>
                            </tr>
                            <tr>
                                <th class="ps-0"><i class="fas fa-calendar-week me-2 text-primary"></i>Tanggal Cuti</th>
                                <td>{{ \Carbon\Carbon::parse($transportasiDetail->cuti->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($transportasiDetail->cuti->tanggal_selesai)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th class="ps-0"><i class="fas fa-clock me-2 text-primary"></i>Lama Cuti</th>
                                <td>{{ $transportasiDetail->cuti->lama_hari }} hari</td>
                            </tr>
                            <tr>
                                <th class="ps-0"><i class="fas fa-comment me-2 text-primary"></i>Alasan Cuti</th>
                                <td>{{ $transportasiDetail->cuti->alasan }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Card Informasi Transportasi -->
        <div class="col-lg-6">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-info text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-plane me-2"></i>Informasi Transportasi</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h6 class="text-muted mb-2"><i class="fas fa-subway me-2 text-info"></i>Jenis Transportasi</h6>
                            <p class="fs-5 fw-semibold">{{ $transportasiDetail->transportasi->jenis }}</p>
                        </div>
                        <div class="col-md-6 mb-4">
                            <h6 class="text-muted mb-2"><i class="fas fa-exchange-alt me-2 text-info"></i>Jenis Perjalanan</h6>
                            <p class="fs-5 fw-semibold">
                                @if($transportasiDetail->jenis_perjalanan == 'pergi')
                                    <span class="badge bg-primary">Tiket Pergi (Berangkat)</span>
                                @else
                                    <span class="badge bg-success">Tiket Kembali (Pulang)</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h6 class="text-muted mb-2"><i class="fas fa-building me-2 text-info"></i>Provider/Maskapai</h6>
                            <p class="fs-5 fw-semibold">{{ $transportasiDetail->provider ?? '-' }}</p>
                        </div>
                        <div class="col-md-6 mb-4">
                            <h6 class="text-muted mb-2"><i class="fas fa-ticket-alt me-2 text-info"></i>Nomor Tiket</h6>
                            <p class="fs-5 fw-semibold">{{ $transportasiDetail->nomor_tiket ?? '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="text-muted mb-2"><i class="fas fa-route me-2 text-info"></i>Rute</h6>
                        <div class="d-flex align-items-center">
                            <div class="fs-5 fw-semibold">{{ $transportasiDetail->rute_asal }}</div>
                            <div class="mx-3"><i class="fas fa-long-arrow-alt-right text-primary"></i></div>
                            <div class="fs-5 fw-semibold">{{ $transportasiDetail->rute_tujuan }}</div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h6 class="text-muted mb-2"><i class="fas fa-plane-departure me-2 text-info"></i>Waktu Berangkat</h6>
                            <p class="fs-5 fw-semibold">{{ \Carbon\Carbon::parse($transportasiDetail->waktu_berangkat)->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6 mb-4">
                            <h6 class="text-muted mb-2"><i class="fas fa-plane-arrival me-2 text-info"></i>Waktu Kembali</h6>
                            <p class="fs-5 fw-semibold">{{ \Carbon\Carbon::parse($transportasiDetail->waktu_kembali)->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h6 class="text-muted mb-2"><i class="fas fa-check-circle me-2 text-info"></i>Status Pemesanan</h6>
                            <div>
                                @if($transportasiDetail->status_pemesanan == 'belum_dipesan')
                                    <span class="badge bg-danger fs-6 py-2 px-3">Belum Dipesan</span>
                                @elseif($transportasiDetail->status_pemesanan == 'proses_pemesanan')
                                    <span class="badge bg-warning fs-6 py-2 px-3">Proses Pemesanan</span>
                                @elseif($transportasiDetail->status_pemesanan == 'tiket_terbit')
                                    <span class="badge bg-success fs-6 py-2 px-3">Tiket Terbit</span>
                                @else
                                    <span class="badge bg-secondary fs-6 py-2 px-3">Dibatalkan</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-4 mb-4">
        <!-- Card Informasi Biaya -->
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Informasi Biaya</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h6 class="text-muted mb-2"><i class="fas fa-ticket-alt me-2 text-success"></i>Biaya Transportasi</h6>
                            <p class="fs-5 fw-semibold">Rp {{ number_format($transportasiDetail->biaya_aktual, 0, ',', '.') }}</p>
                        </div>
                        
                        @if($transportasiDetail->perlu_hotel)
                        <div class="col-md-6 mb-4">
                            <h6 class="text-muted mb-2"><i class="fas fa-hotel me-2 text-success"></i>Biaya Hotel</h6>
                            <p class="fs-5 fw-semibold">Rp {{ number_format($transportasiDetail->hotel_biaya, 0, ',', '.') }}</p>
                        </div>
                        
                        <div class="col-12">
                            <hr>
                            <h6 class="text-muted mb-2"><i class="fas fa-calculator me-2 text-success"></i>Total Biaya</h6>
                            <p class="fs-4 fw-bold text-success">Rp {{ number_format($transportasiDetail->biaya_aktual + $transportasiDetail->hotel_biaya, 0, ',', '.') }}</p>
                        </div>
                        @else
                        <div class="col-12 mt-3">
                            <hr>
                            <h6 class="text-muted mb-2"><i class="fas fa-calculator me-2 text-success"></i>Total Biaya</h6>
                            <p class="fs-4 fw-bold text-success">Rp {{ number_format($transportasiDetail->biaya_aktual, 0, ',', '.') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Card Informasi Hotel (jika diperlukan) atau Card Catatan -->
        <div class="col-lg-6">
            @if($transportasiDetail->perlu_hotel)
            <div class="card shadow-sm h-100">
                <div class="card-header bg-secondary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-hotel me-2"></i>Informasi Hotel</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <h6 class="text-muted mb-2"><i class="fas fa-building me-2 text-secondary"></i>Nama Hotel</h6>
                        <p class="fs-5 fw-semibold">{{ $transportasiDetail->hotel_nama ?? '-' }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="text-muted mb-2"><i class="fas fa-info-circle me-2 text-secondary"></i>Status</h6>
                        <span class="badge bg-success fs-6 py-2 px-3">Disetujui</span>
                    </div>
                </div>
            </div>
            @elseif($transportasiDetail->catatan)
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Catatan</h5>
                </div>
                <div class="card-body p-4">
                    <p class="mb-0">{{ $transportasiDetail->catatan }}</p>
                </div>
            </div>
            @else
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light py-3">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Tambahan</h5>
                </div>
                <div class="card-body p-4 d-flex align-items-center justify-content-center">
                    <div class="text-center text-muted">
                        <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                        <p>Tidak ada informasi tambahan untuk pengajuan cuti ini.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Card Catatan (jika ada dan perlu hotel) -->
    @if($transportasiDetail->catatan && $transportasiDetail->perlu_hotel)
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-light py-3">
            <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Catatan</h5>
        </div>
        <div class="card-body p-4">
            <p class="mb-0">{{ $transportasiDetail->catatan }}</p>
        </div>
    </div>
    @endif
    
    <!-- Tombol Aksi -->
    <div class="d-flex justify-content-between gap-3 mt-4 mb-5">
        <a href="{{ url('/transportasi-details/' . $transportasiDetail->id . '/edit') }}" class="btn btn-warning btn-lg px-4">
            <i class="fas fa-edit me-2"></i> Edit Detail Transportasi
        </a>
        <form action="{{ url('/transportasi-details/' . $transportasiDetail->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus detail transportasi ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-lg px-4">
                <i class="fas fa-trash me-2"></i> Hapus Detail Transportasi
            </button>
        </form>
    </div>
</div>

@push('styles')
<style>
    .card {
        border-radius: 10px;
        overflow: hidden;
        border: none;
    }
    
    .card-header {
        border-bottom: none;
    }
    
    .badge {
        font-weight: 500;
    }
    
    .row.g-4 {
        --bs-gutter-x: 1.5rem;
        --bs-gutter-y: 1.5rem;
    }
    
    .table-borderless th,
    .table-borderless td {
        padding-top: 0.7rem;
        padding-bottom: 0.7rem;
    }
</style>
@endpush

@endsection