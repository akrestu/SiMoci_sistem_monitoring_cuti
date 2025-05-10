@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold"><i class="fas fa-tachometer-alt me-2"></i>Dashboard SiMoci - Sistem Monitoring Cuti</h4>
    </div>
    
    <!-- Notification for upcoming ticket deadlines -->
    @if(isset($upcomingDeadlines) && $upcomingDeadlines->count() > 0)
    <div class="alert alert-danger mb-4">
        <div class="d-flex align-items-center mb-2">
            <i class="fas fa-exclamation-triangle me-2 fa-lg"></i>
            <h5 class="mb-0 fw-bold">Peringatan: {{ $upcomingDeadlines->count() }} Tiket Belum Dipesan</h5>
        </div>
        <p>Berikut adalah daftar tiket yang mendekati deadline keberangkatan/kepulangan namun belum dipesan:</p>
        <div class="table-responsive">
            <table class="table table-sm table-bordered bg-white">
                <thead class="table-danger">
                    <tr>
                        <th>Nama Karyawan</th>
                        <th>Jenis Transportasi</th>
                        <th>Jenis Perjalanan</th>
                        <th>Rute</th>
                        <th>Tanggal Deadline</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upcomingDeadlines as $detail)
                    <tr>
                        <td>{{ $detail->cuti->karyawan->nama }}</td>
                        <td>{{ $detail->transportasi->jenis }}</td>
                        <td>
                            @if($detail->jenis_perjalanan == 'pergi')
                                <span class="badge bg-primary">Tiket Pergi (Berangkat)</span>
                            @else
                                <span class="badge bg-success">Tiket Kembali (Pulang)</span>
                            @endif
                        </td>
                        <td>{{ $detail->rute_asal }} → {{ $detail->rute_tujuan }}</td>
                        <td>
                            @if($detail->jenis_perjalanan == 'pergi')
                                <span class="fw-bold text-danger">{{ \Carbon\Carbon::parse($detail->cuti->tanggal_mulai)->format('d/m/Y') }}</span>
                                <small class="d-block">({{ \Carbon\Carbon::parse($detail->cuti->tanggal_mulai)->diffForHumans() }})</small>
                            @else
                                <span class="fw-bold text-danger">{{ \Carbon\Carbon::parse($detail->cuti->tanggal_selesai)->format('d/m/Y') }}</span>
                                <small class="d-block">({{ \Carbon\Carbon::parse($detail->cuti->tanggal_selesai)->diffForHumans() }})</small>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('cutis.show', $detail->cuti_id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    
    <!-- Overview Stats -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 h-100">
                <div class="card-body position-relative p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted fw-semibold mb-2">Total Karyawan</h6>
                            <h2 class="display-5 fw-bold mb-0">{{ $totalKaryawan }}</h2>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                    </div>
                    <a href="{{ route('karyawans.index') }}" class="stretched-link"></a>
                </div>
                <div class="card-footer border-0 bg-transparent pt-0 pb-3 px-4">
                    <div class="mt-2 detailed-data">
                        <p class="mb-1 fw-semibold">Karyawan per Departemen:</p>
                        <div style="height: 180px; position: relative;">
                            <canvas id="karyawanPerDeptChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 h-100">
                <div class="card-body position-relative p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted fw-semibold mb-2">Pengajuan Cuti</h6>
                            <h2 class="display-5 fw-bold mb-0">{{ $totalCutiPending }}</h2>
                            <div class="mt-2">
                                <span class="badge bg-info me-1">{{ $totalCutiMonthly }} bulan ini</span>
                                <span class="badge bg-secondary">{{ $totalCutiYearly }} tahun ini</span>
                            </div>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-clock fa-lg"></i>
                        </div>
                    </div>
                    <a href="{{ route('cutis.index', ['status' => 'pending']) }}" class="stretched-link"></a>
                </div>
                <div class="card-footer border-0 bg-transparent pt-0 pb-3 px-4">
                    <div class="mt-2 detailed-data">
                        <p class="mb-1 fw-semibold">Pengajuan Cuti per Departemen:</p>
                        <div style="height: 180px; position: relative;">
                            <canvas id="cutiPerDeptChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 h-100">
                <div class="card-body position-relative p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted fw-semibold mb-2">Status Cuti</h6>
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-success d-inline-flex align-items-center justify-content-center rounded-circle me-2" style="width: 26px; height: 26px; min-width: 26px; padding: 0;">
                                        <i class="fas fa-check" style="font-size: 11px;"></i>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center w-100">
                                        <span class="fw-medium">Disetujui</span>
                                        <span class="fw-bold">{{ $totalCutiDisetujui }}</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-danger d-inline-flex align-items-center justify-content-center rounded-circle me-2" style="width: 26px; height: 26px; min-width: 26px; padding: 0;">
                                        <i class="fas fa-times" style="font-size: 11px;"></i>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center w-100">
                                        <span class="fw-medium">Ditolak</span>
                                        <span class="fw-bold">{{ $totalCutiDitolak }}</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="badge bg-warning d-inline-flex align-items-center justify-content-center rounded-circle me-2" style="width: 26px; height: 26px; min-width: 26px; padding: 0;">
                                        <i class="fas fa-clock" style="font-size: 11px;"></i>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center w-100">
                                        <span class="fw-medium">Pending</span>
                                        <span class="fw-bold">{{ $totalCutiPending }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-check-circle fa-lg"></i>
                        </div>
                    </div>
                    <a href="{{ route('cutis.index') }}" class="stretched-link"></a>
                </div>
                <div class="card-footer border-0 bg-transparent pt-0 pb-3 px-4">
                    <div class="mt-2 detailed-data">
                        <p class="mb-1 fw-semibold">Status Cuti per Departemen:</p>
                        <div style="height: 180px; position: relative;">
                            <canvas id="statusCutiPerDeptChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 h-100">
                <div class="card-body position-relative p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted fw-semibold mb-2">Rata-rata Cuti</h6>
                            <h2 class="display-5 fw-bold mb-0">{{ number_format($avgLeaveDays, 1) }}</h2>
                            <div class="mt-2">
                                <span class="text-muted small">hari/karyawan/tahun</span>
                            </div>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10 text-info rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-calendar-day fa-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer border-0 bg-transparent pt-0 pb-3 px-4">
                    <div class="mt-2 detailed-data">
                        <p class="mb-1 fw-semibold">Rata-rata Cuti per Departemen:</p>
                        <div style="height: 180px; position: relative;">
                            <canvas id="avgLeavePerDeptChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-3">
        <!-- Karyawan Sedang Cuti & Akan Cuti -->
        <div class="col-lg-6">
            <div class="card border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-user-clock text-info me-2"></i>Status Cuti Real-Time
                    </h5>
                </div>
                <div class="card-body p-0">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs" id="cutiTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="cutiToday-tab" data-bs-toggle="tab" data-bs-target="#cutiToday" type="button" role="tab" aria-controls="cutiToday" aria-selected="true">
                                Sedang Cuti <span class="badge bg-primary ms-1">{{ $onLeaveTodayCount }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cutiWeek-tab" data-bs-toggle="tab" data-bs-target="#cutiWeek" type="button" role="tab" aria-controls="cutiWeek" aria-selected="false">
                                Minggu Ini
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cutiMonth-tab" data-bs-toggle="tab" data-bs-target="#cutiMonth" type="button" role="tab" aria-controls="cutiMonth" aria-selected="false">
                                Bulan Ini
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cutiNextMonth-tab" data-bs-toggle="tab" data-bs-target="#cutiNextMonth" type="button" role="tab" aria-controls="cutiNextMonth" aria-selected="false">
                                Bulan Depan
                            </button>
                        </li>
                    </ul>
                    
                    <!-- Tab Content -->
                    <div class="tab-content" id="cutiTabsContent">
                        <!-- Tab: Sedang Cuti -->
                        <div class="tab-pane fade show active" id="cutiToday" role="tabpanel" aria-labelledby="cutiToday-tab">
                            <div class="p-3">
                                @if($karyawanCutiByDepartement->count() > 0)
                                <div style="height: 220px; position: relative;">
                                    <canvas id="onLeaveTodayChart"></canvas>
                                </div>
                                @else
                                <div class="text-center py-5 text-muted">
                                    <i class="far fa-calendar-check fa-3x mb-3"></i>
                                    <p>Tidak ada karyawan yang sedang cuti hari ini</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Tab: Minggu Ini -->
                        <div class="tab-pane fade" id="cutiWeek" role="tabpanel" aria-labelledby="cutiWeek-tab">
                            <div class="p-3">
                                @if($karyawanCutiThisWeek->count() > 0)
                                <div style="height: 220px; position: relative;">
                                    <canvas id="onLeaveThisWeekChart"></canvas>
                                </div>
                                @else
                                <div class="text-center py-5 text-muted">
                                    <i class="far fa-calendar fa-3x mb-3"></i>
                                    <p>Tidak ada karyawan yang akan cuti minggu ini</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Tab: Bulan Ini -->
                        <div class="tab-pane fade" id="cutiMonth" role="tabpanel" aria-labelledby="cutiMonth-tab">
                            <div class="p-3">
                                @if($karyawanCutiThisMonth->count() > 0)
                                <div style="height: 220px; position: relative;">
                                    <canvas id="onLeaveThisMonthChart"></canvas>
                                </div>
                                @else
                                <div class="text-center py-5 text-muted">
                                    <i class="far fa-calendar-alt fa-3x mb-3"></i>
                                    <p>Tidak ada karyawan yang akan cuti bulan ini</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Tab: Bulan Depan -->
                        <div class="tab-pane fade" id="cutiNextMonth" role="tabpanel" aria-labelledby="cutiNextMonth-tab">
                            <div class="p-3">
                                @if(isset($karyawanCutiNextMonthByDept) && count($karyawanCutiNextMonthByDept) > 0)
                                <div style="height: 220px; position: relative;">
                                    <canvas id="onLeaveNextMonthChart"></canvas>
                                </div>
                                @else
                                <div id="nextMonthNoData" class="text-center py-5 text-muted">
                                    <i class="far fa-calendar-alt fa-3x mb-3"></i>
                                    <p>Tidak ada karyawan yang akan cuti bulan depan</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-clipboard-list text-warning me-2"></i>Pengajuan Cuti Terbaru
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Karyawan</th>
                                    <th>Jenis Cuti</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentCutis as $index => $cuti)
                                    <tr>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-mini rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                    <span class="text-secondary">{{ substr($cuti->karyawan->nama, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 small fw-semibold">{{ $cuti->karyawan->nama }}</h6>
                                                    <small class="text-muted">{{ $cuti->karyawan->departemen }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle small">{{ $cuti->jenisCuti->nama_jenis }}</td>
                                        <td class="align-middle small">
                                            {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }} - 
                                            {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}
                                            <span class="d-block text-muted">{{ $cuti->lama_hari }} hari</span>
                                        </td>
                                        <td class="align-middle">
                                            @if($cuti->status_cuti == 'pending')
                                                <span class="badge bg-warning bg-opacity-10 text-warning py-1 px-2">
                                                    <i class="fas fa-clock me-1 small"></i>Pending
                                                </span>
                                            @elseif($cuti->status_cuti == 'disetujui')
                                                <span class="badge bg-success bg-opacity-10 text-success py-1 px-2">
                                                    <i class="fas fa-check me-1 small"></i>Disetujui
                                                </span>
                                            @elseif($cuti->status_cuti == 'ditolak')
                                                <span class="badge bg-danger bg-opacity-10 text-danger py-1 px-2">
                                                    <i class="fas fa-times me-1 small"></i>Ditolak
                                                </span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('cutis.show', $cuti->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye small"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fas fa-clipboard-check fa-3x mb-3"></i>
                                            <p>Tidak ada pengajuan cuti terbaru</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts & Trends -->
    <div class="row g-2 mt-2">
        <!-- Monthly Trend Chart -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-2 border-bottom">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-chart-line text-success me-1"></i>Tren Pengajuan Cuti ({{ now()->year }})
                    </h6>
                </div>
                <div class="card-body p-2">
                    <canvas id="monthlyTrendChart" height="180"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Department Stats -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-2 border-bottom">
                    <h6 class="card-title mb-0 fw-semibold">
                        <i class="fas fa-chart-bar text-primary me-1"></i>Statistik Cuti per Departemen
                    </h6>
                </div>
                <div class="card-body p-2">
                    <canvas id="cutiByDepartmentChart" height="180"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Persetujuan Cuti -->
<div class="modal fade" id="approveCutiModal" tabindex="-1" aria-labelledby="approveCutiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="approveCutiModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Setujui Pengajuan Cuti?
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Pengajuan cuti akan disetujui.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <button type="button" id="confirmApprove" class="btn btn-success">
                    <i class="fas fa-check me-1"></i>Ya, Setujui!
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Penolakan Cuti -->
<div class="modal fade" id="rejectCutiModal" tabindex="-1" aria-labelledby="rejectCutiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectCutiModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Tolak Pengajuan Cuti?
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Pengajuan cuti akan ditolak.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <button type="button" id="confirmReject" class="btn btn-danger">
                    <i class="fas fa-ban me-1"></i>Ya, Tolak!
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stat-icon {
        height: 50px;
        width: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .activity-item:hover {
        background-color: #f9fafc;
    }
    
    .employee-leave-item:hover {
        background-color: #f9fafc;
    }
    
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        border: none;
        padding: 0.75rem 1rem;
    }
    
    .nav-tabs .nav-link.active {
        color: var(--primary-color);
        border-bottom: 2px solid var(--primary-color);
    }
    
    .tab-content {
        max-height: 350px;
        overflow-y: auto;
    }
    
    /* Table optimizations */
    .table th {
        font-weight: 600;
        color: #495057;
        background-color: #f8f9fa;
        white-space: nowrap;
    }
    
    .table-hover tbody tr:hover {
        background-color: #f9fafc;
    }
    
    .progress {
        background-color: #e9ecef;
        overflow: hidden;
    }
    
    .text-nowrap {
        white-space: nowrap;
    }
    
    /* Department-based dashboard cards */
    .detailed-data {
        border-top: 1px solid rgba(0,0,0,0.05);
        padding-top: 0.75rem;
    }
    
    .detailed-list {
        scrollbar-width: thin;
        scrollbar-color: rgba(0,0,0,0.2) transparent;
    }
    
    .detailed-list::-webkit-scrollbar {
        width: 4px;
    }
    
    .detailed-list::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .detailed-list::-webkit-scrollbar-thumb {
        background-color: rgba(0,0,0,0.2);
        border-radius: 20px;
    }
    
    /* Accordion customizations */
    .accordion-button:not(.collapsed) {
        color: var(--primary-color);
        background-color: rgba(33, 150, 243, 0.05);
        box-shadow: none;
    }
    
    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0,0,0,0.1);
    }
    
    /* Compact Pagination styling */
    .compact-pagination .pagination {
        margin-bottom: 0;
    }
    
    .compact-pagination .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .compact-pagination .page-link {
        color: var(--primary-color);
        padding: 0.3rem 0.6rem;
        font-size: 0.85rem;
    }
    
    .compact-pagination .page-link:hover {
        color: var(--primary-hover);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                boundary: document.body
            })
        });
        
        // Bootstrap Modal confirmation for approve/reject buttons
        const approveForms = document.querySelectorAll('.approve-form');
        const rejectForms = document.querySelectorAll('.reject-form');
        let currentForm = null;
        
        // Initialize modals
        const approveModal = new bootstrap.Modal(document.getElementById('approveCutiModal'));
        const rejectModal = new bootstrap.Modal(document.getElementById('rejectCutiModal'));
        
        // Approve button event listeners
        approveForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                currentForm = form;
                approveModal.show();
            });
        });
        
        // Reject button event listeners
        rejectForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                currentForm = form;
                rejectModal.show();
            });
        });
        
        // Submit form when approve confirmed
        document.getElementById('confirmApprove').addEventListener('click', function() {
            if (currentForm) {
                currentForm.submit();
            }
            approveModal.hide();
        });
        
        // Submit form when reject confirmed
        document.getElementById('confirmReject').addEventListener('click', function() {
            if (currentForm) {
                currentForm.submit();
            }
            rejectModal.hide();
        });
        
        // 1. Karyawan per Departemen Chart
        const karyawanPerDeptData = @json($karyawanPerDepartemen);
        if (karyawanPerDeptData && karyawanPerDeptData.length > 0) {
            const karyawanDeptLabels = karyawanPerDeptData.map(item => item.departemen);
            const karyawanDeptValues = karyawanPerDeptData.map(item => item.total);
            
            const backgroundColors = [
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 99, 132, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(255, 159, 64, 0.7)',
                'rgba(153, 102, 255, 0.7)',
                'rgba(255, 205, 86, 0.7)',
                'rgba(201, 203, 207, 0.7)',
                'rgba(255, 99, 71, 0.7)',
                'rgba(50, 205, 50, 0.7)',
                'rgba(106, 90, 205, 0.7)'
            ];
            
            const karyawanDeptCtx = document.getElementById('karyawanPerDeptChart').getContext('2d');
            new Chart(karyawanDeptCtx, {
                type: 'doughnut',
                data: {
                    labels: karyawanDeptLabels,
                    datasets: [{
                        data: karyawanDeptValues,
                        backgroundColor: karyawanDeptLabels.map((_, i) => backgroundColors[i % backgroundColors.length]),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                font: {
                                    size: 10
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // 2. Pengajuan Cuti per Departemen Chart
        const cutiPerDeptData = @json($cutiPerDepartemen);
        if (cutiPerDeptData && cutiPerDeptData.length > 0) {
            const cutiDeptLabels = cutiPerDeptData.map(item => item.departemen);
            const cutiDeptValues = cutiPerDeptData.map(item => item.total);
            
            const cutiDeptCtx = document.getElementById('cutiPerDeptChart').getContext('2d');
            new Chart(cutiDeptCtx, {
                type: 'bar',
                data: {
                    labels: cutiDeptLabels,
                    datasets: [{
                        label: 'Pengajuan Pending',
                        data: cutiDeptValues,
                        backgroundColor: 'rgba(255, 193, 7, 0.7)',
                        borderColor: 'rgb(255, 193, 7)',
                        borderWidth: 1,
                        borderRadius: 4,
                        maxBarThickness: 25
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: {
                                    size: 10
                                }
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    size: 10
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        } else {
            document.getElementById('cutiPerDeptChart').parentNode.innerHTML = 
                '<div class="text-center text-muted py-5">' +
                '<p class="small mb-0">Tidak ada pengajuan cuti pending</p>' +
                '</div>';
        }
        
        // 3. Status Cuti per Departemen Chart - Changed to a grouped bar chart
        const statusCutiDeptData = @json($statusCutiPerDepartemen);
        
        if (statusCutiDeptData && Object.keys(statusCutiDeptData).length > 0) {
            const statusLabels = Object.keys(statusCutiDeptData);
            const disetujuiData = statusLabels.map(dept => statusCutiDeptData[dept].disetujui || 0);
            const ditolakData = statusLabels.map(dept => statusCutiDeptData[dept].ditolak || 0);
            const pendingData = statusLabels.map(dept => statusCutiDeptData[dept].pending || 0);
            
            new Chart(document.getElementById('statusCutiPerDeptChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: statusLabels,
                    datasets: [
                        {
                            label: 'Disetujui',
                            backgroundColor: 'rgba(40, 167, 69, 0.7)',
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 1,
                            data: disetujuiData
                        },
                        {
                            label: 'Ditolak',
                            backgroundColor: 'rgba(220, 53, 69, 0.7)',
                            borderColor: 'rgba(220, 53, 69, 1)',
                            borderWidth: 1,
                            data: ditolakData
                        },
                        {
                            label: 'Pending',
                            backgroundColor: 'rgba(255, 193, 7, 0.7)',
                            borderColor: 'rgba(255, 193, 7, 1)',
                            borderWidth: 1,
                            data: pendingData
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            ticks: {
                                font: {
                                    size: 9
                                }
                            },
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: {
                                    size: 10
                                }
                            },
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 12,
                                font: {
                                    size: 10
                                }
                            }
                        }
                    }
                }
            });
        } else {
            document.getElementById('statusCutiPerDeptChart').parentNode.innerHTML = 
                '<div class="text-center text-muted py-5">' +
                '<p class="small mb-0">Tidak ada data status cuti</p>' +
                '</div>';
        }
        
        // 4. Average Leave Days per Department Chart - Using real data
        const avgLeaveData = @json($cutiByDepartment);
        
        if (avgLeaveData && avgLeaveData.length > 0) {
            const avgLeaveLabels = avgLeaveData.map(item => item.departemen);
            const avgLeaveValues = avgLeaveData.map(item => parseFloat(item.rata_rata_hari).toFixed(1));
            
            const avgLeaveCtx = document.getElementById('avgLeavePerDeptChart').getContext('2d');
            new Chart(avgLeaveCtx, {
                type: 'bar',
                data: {
                    labels: avgLeaveLabels,
                    datasets: [{
                        label: 'Rata-rata Hari Cuti',
                        data: avgLeaveValues,
                        backgroundColor: 'rgba(52, 152, 219, 0.7)',
                        borderColor: 'rgb(52, 152, 219)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + ' hari';
                                },
                                font: {
                                    size: 10
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw} hari`;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            document.getElementById('avgLeavePerDeptChart').parentNode.innerHTML = 
                '<div class="text-center text-muted py-5">' +
                '<p class="small mb-0">Tidak ada data rata-rata cuti per departemen</p>' +
                '</div>';
        }
        
        // Department Chart
        const cutiByDepartmentData = @json($cutiByDepartment);

        if (cutiByDepartmentData && cutiByDepartmentData.length > 0) {
            // Prepare data for chart - Make sure we're using the correct field names
            const departments = cutiByDepartmentData.map(item => item.departemen);
            const cutiCounts = cutiByDepartmentData.map(item => item.total);
            
            // Create chart
            const deptCtx = document.getElementById('cutiByDepartmentChart').getContext('2d');
            new Chart(deptCtx, {
                type: 'bar',
                data: {
                    labels: departments,
                    datasets: [{
                        label: 'Jumlah Cuti',
                        data: cutiCounts,
                        backgroundColor: departments.map((_, i) => {
                            const colors = [
                                'rgba(54, 162, 235, 0.6)',
                                'rgba(255, 99, 132, 0.6)',
                                'rgba(75, 192, 192, 0.6)',
                                'rgba(255, 159, 64, 0.6)',
                                'rgba(153, 102, 255, 0.6)',
                                'rgba(255, 205, 86, 0.6)',
                                'rgba(201, 203, 207, 0.6)'
                            ];
                            return colors[i % colors.length];
                        }),
                        borderColor: departments.map((_, i) => {
                            const colors = [
                                'rgb(54, 162, 235)',
                                'rgb(255, 99, 132)',
                                'rgb(75, 192, 192)',
                                'rgb(255, 159, 64)',
                                'rgb(153, 102, 255)',
                                'rgb(255, 205, 86)',
                                'rgb(201, 203, 207)'
                            ];
                            return colors[i % colors.length];
                        }),
                        borderWidth: 1,
                        borderRadius: 4,
                        maxBarThickness: 50
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            precision: 0,
                            ticks: {
                                font: {
                                    size: 10
                                }
                            },
                            grid: {
                                display: true,
                                drawBorder: false,
                                color: 'rgba(0, 0, 0, 0.03)'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 10
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            padding: 8,
                            bodyFont: {
                                size: 12
                            },
                            titleFont: {
                                size: 13,
                                weight: 'bold'
                            },
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw} pengajuan`;
                                }
                            }
                        }
                    }
                }
            });
        } else {
            document.getElementById('cutiByDepartmentChart').parentNode.innerHTML = 
                '<div class="text-center text-muted py-3">' +
                '<i class="fas fa-chart-bar fa-2x mb-2"></i>' +
                '<p class="small mb-0">Tidak ada data cuti departemen untuk periode ini</p>' +
                '<p class="small mb-0">Silahkan ubah filter tanggal untuk melihat data</p>' +
                '</div>';
        }
        
        // Monthly Trend Chart
        const monthlyData = @json($monthlyData);
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        const trendData = Object.values(monthlyData);
        
        const trendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: monthNames,
                datasets: [{
                    label: 'Jumlah Pengajuan Cuti',
                    data: trendData,
                    fill: {
                        target: 'origin',
                        above: 'rgba(46, 204, 113, 0.05)'
                    },
                    borderColor: 'rgb(46, 204, 113)',
                    backgroundColor: 'rgba(46, 204, 113, 0.6)',
                    pointBackgroundColor: 'rgb(46, 204, 113)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 1,
                    pointRadius: 3,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0,
                        ticks: {
                            font: {
                                size: 10
                            }
                        },
                        grid: {
                            display: true,
                            drawBorder: false,
                            color: 'rgba(0, 0, 0, 0.03)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 10
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        padding: 8,
                        bodyFont: {
                            size: 12
                        },
                        titleFont: {
                            size: 13,
                            weight: 'bold'
                        }
                    }
                }
            }
        });
        
        // Real-Time Leave Status Charts
        // 1. Employees currently on leave today by department
        const karyawanCutiByDept = @json($karyawanCutiByDepartement);
        if (karyawanCutiByDept && Object.keys(karyawanCutiByDept).length > 0) {
            const deptLabels = Object.keys(karyawanCutiByDept);
            const deptCounts = deptLabels.map(dept => karyawanCutiByDept[dept].length);
            
            const todayCtx = document.getElementById('onLeaveTodayChart').getContext('2d');
            new Chart(todayCtx, {
                type: 'bar',
                data: {
                    labels: deptLabels,
                    datasets: [{
                        label: 'Jumlah Karyawan Cuti',
                        data: deptCounts,
                        backgroundColor: 'rgba(0, 123, 255, 0.7)',
                        borderColor: 'rgb(0, 123, 255)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: { size: 10 }
                            },
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 10 }
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Karyawan Sedang Cuti per Departemen',
                            font: {
                                size: 14
                            },
                            padding: {
                                bottom: 10
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw} orang`;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // 2. Process this week's leave data by department
        const karyawanCutiThisWeek = @json($karyawanCutiThisWeek);
        if (karyawanCutiThisWeek && karyawanCutiThisWeek.length > 0) {
            // Group by department
            const deptGrouped = {};
            karyawanCutiThisWeek.forEach(karyawan => {
                if (!deptGrouped[karyawan.departemen]) {
                    deptGrouped[karyawan.departemen] = 0;
                }
                deptGrouped[karyawan.departemen]++;
            });
            
            const weekLabels = Object.keys(deptGrouped);
            const weekCounts = weekLabels.map(dept => deptGrouped[dept]);
            
            const weekCtx = document.getElementById('onLeaveThisWeekChart').getContext('2d');
            new Chart(weekCtx, {
                type: 'bar',
                data: {
                    labels: weekLabels,
                    datasets: [{
                        label: 'Jumlah Karyawan Cuti',
                        data: weekCounts,
                        backgroundColor: 'rgba(255, 193, 7, 0.7)',
                        borderColor: 'rgb(255, 193, 7)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: { size: 10 }
                            },
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 10 }
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Karyawan Cuti Minggu Ini per Departemen',
                            font: {
                                size: 14
                            },
                            padding: {
                                bottom: 10
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw} orang`;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // 3. Process this month's leave data by department
        const karyawanCutiThisMonth = @json($karyawanCutiThisMonth);
        if (karyawanCutiThisMonth && karyawanCutiThisMonth.length > 0) {
            // Group by department
            const deptGrouped = {};
            karyawanCutiThisMonth.forEach(karyawan => {
                if (!deptGrouped[karyawan.departemen]) {
                    deptGrouped[karyawan.departemen] = 0;
                }
                deptGrouped[karyawan.departemen]++;
            });
            
            const monthLabels = Object.keys(deptGrouped);
            const monthCounts = monthLabels.map(dept => deptGrouped[dept]);
            
            const monthCtx = document.getElementById('onLeaveThisMonthChart').getContext('2d');
            new Chart(monthCtx, {
                type: 'bar',
                data: {
                    labels: monthLabels,
                    datasets: [{
                        label: 'Jumlah Karyawan Cuti',
                        data: monthCounts,
                        backgroundColor: 'rgba(23, 162, 184, 0.7)',
                        borderColor: 'rgb(23, 162, 184)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: { size: 10 }
                            },
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 10 }
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Karyawan Cuti Bulan Ini per Departemen',
                            font: {
                                size: 14
                            },
                            padding: {
                                bottom: 10
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw} orang`;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // 4. Process next month's leave data by department
        const karyawanCutiNextMonthByDept = @json($karyawanCutiNextMonthByDept);
        
        if (karyawanCutiNextMonthByDept && Object.keys(karyawanCutiNextMonthByDept).length > 0) {
            // Process real data for next month
            const nextMonthData = {};
            
            // Count employees on leave by department
            Object.keys(karyawanCutiNextMonthByDept).forEach(dept => {
                nextMonthData[dept] = karyawanCutiNextMonthByDept[dept].length;
            });
            
            // Create chart if we have data
            const nextMonthLabels = Object.keys(nextMonthData);
            const nextMonthCounts = nextMonthLabels.map(dept => nextMonthData[dept]);
            
            const nextMonthCtx = document.getElementById('onLeaveNextMonthChart').getContext('2d');
            new Chart(nextMonthCtx, {
                type: 'bar',
                data: {
                    labels: nextMonthLabels,
                    datasets: [{
                        label: 'Jumlah Karyawan Cuti',
                        data: nextMonthCounts,
                        backgroundColor: 'rgba(108, 117, 125, 0.7)',
                        borderColor: 'rgb(108, 117, 125)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: { size: 10 }
                            },
                            grid: {
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 10 }
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Karyawan Cuti Bulan Depan per Departemen',
                            font: {
                                size: 14
                            },
                            padding: {
                                bottom: 10
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw} orang`;
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush