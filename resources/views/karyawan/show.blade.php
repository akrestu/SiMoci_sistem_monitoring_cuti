@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('karyawans.index') }}">Karyawan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $karyawan->nama }}</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="fw-bold text-primary mb-0">Detail Karyawan</h2>
                <a href="{{ route('karyawans.index') }}" class="btn btn-outline-secondary rounded-pill">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden h-100">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle me-2 fs-4"></i>
                        <h5 class="card-title mb-0 fw-bold">Informasi Karyawan</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="avatar-circle bg-primary bg-opacity-10 mb-3 mx-auto">
                            <i class="fas fa-user-tie text-primary fa-3x"></i>
                        </div>
                        <h4 class="fw-bold mb-1">{{ $karyawan->nama }}</h4>
                        <p class="text-muted mb-0">{{ $karyawan->jabatan }}</p>
                    </div>
                    
                    <div class="bg-light p-3 rounded-3 mb-3">
                        <h6 class="mb-2 fw-bold text-primary"><i class="fas fa-id-badge me-2"></i> Informasi Utama</h6>
                        <div class="info-item py-2 border-bottom">
                            <div class="row align-items-center">
                                <div class="col-5 text-muted">
                                    <i class="fas fa-id-card me-2"></i> NIK
                                </div>
                                <div class="col-7">
                                    <span class="badge bg-light text-dark border rounded-pill px-3 fw-medium">{{ $karyawan->nik }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="info-item py-2 border-bottom">
                            <div class="row align-items-center">
                                <div class="col-5 text-muted">
                                    <i class="fas fa-building me-2"></i> Departemen
                                </div>
                                <div class="col-7 fw-medium">
                                    {{ $karyawan->departemen }}
                                </div>
                            </div>
                        </div>

                        <div class="info-item py-2 border-bottom">
                            <div class="row align-items-center">
                                <div class="col-5 text-muted">
                                    <i class="fas fa-user-tag me-2"></i> Status
                                </div>
                                <div class="col-7">
                                    @if($karyawan->status)
                                        <span class="badge {{ $karyawan->status == 'Staff' ? 'bg-info' : 'bg-secondary' }} text-white rounded-pill px-3">
                                            {{ $karyawan->status }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="info-item py-2">
                            <div class="row align-items-center">
                                <div class="col-5 text-muted">
                                    <i class="fas fa-envelope me-2"></i> Email
                                </div>
                                <div class="col-7">
                                    {{ $karyawan->email ?: '-' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-light p-3 rounded-3 mb-3">
                        <h6 class="mb-2 fw-bold text-primary"><i class="fas fa-briefcase me-2"></i> Informasi Kepegawaian</h6>
                        <div class="info-item py-2 border-bottom">
                            <div class="row align-items-center">
                                <div class="col-5 text-muted">
                                    <i class="fas fa-calendar-day me-2"></i> Tanggal Masuk
                                </div>
                                <div class="col-7">
                                    @if($karyawan->doh)
                                        {{ \Carbon\Carbon::parse($karyawan->doh)->format('d F Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="info-item py-2">
                            <div class="row align-items-center">
                                <div class="col-5 text-muted">
                                    <i class="fas fa-map-marker-alt me-2"></i> Tempat Penerimaan
                                </div>
                                <div class="col-7">
                                    {{ $karyawan->poh ?: '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-light p-3 rounded-3 mb-3">
                        <h6 class="mb-2 fw-bold text-primary"><i class="fas fa-history me-2"></i> Informasi Sistem</h6>
                        <div class="info-item py-2 border-bottom">
                            <div class="row align-items-center">
                                <div class="col-5 text-muted">
                                    <i class="fas fa-calendar-plus me-2"></i> Dibuat
                                </div>
                                <div class="col-7 small">
                                    {{ $karyawan->created_at->format('d F Y H:i') }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="info-item py-2">
                            <div class="row align-items-center">
                                <div class="col-5 text-muted">
                                    <i class="fas fa-calendar-check me-2"></i> Diperbarui
                                </div>
                                <div class="col-7 small">
                                    {{ $karyawan->updated_at->format('d F Y H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('karyawans.edit', $karyawan->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i> Edit Karyawan
                        </a>
                        <button type="button" class="btn btn-danger delete-btn" 
                            data-id="{{ $karyawan->id }}"
                            data-nama="{{ $karyawan->nama }}"
                            data-nik="{{ $karyawan->nik }}">
                            <i class="fas fa-trash me-1"></i> Hapus Karyawan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden mb-4">
                <div class="card-header bg-info text-white py-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-chart-bar me-2 fs-5"></i>
                            <h5 class="card-title mb-0 fw-bold">Monitoring Cuti</h5>
                        </div>
                        <a href="{{ route('karyawans.cuti-monitoring', $karyawan->id) }}" class="btn btn-sm btn-light rounded-pill">
                            <i class="fas fa-external-link-alt me-1"></i> Lihat Detail
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="p-3 bg-success bg-opacity-10 rounded-3 mb-2">
                                <i class="fas fa-check-circle text-success fa-2x"></i>
                            </div>
                            <h5 class="fw-bold">{{ $karyawan->cutis->where('status_cuti', 'disetujui')->count() }}</h5>
                            <p class="text-muted small">Cuti Disetujui</p>
                        </div>
                        <div class="col-4">
                            <div class="p-3 bg-warning bg-opacity-10 rounded-3 mb-2">
                                <i class="fas fa-clock text-warning fa-2x"></i>
                            </div>
                            <h5 class="fw-bold">{{ $karyawan->cutis->where('status_cuti', 'pending')->count() }}</h5>
                            <p class="text-muted small">Cuti Pending</p>
                        </div>
                        <div class="col-4">
                            <div class="p-3 bg-danger bg-opacity-10 rounded-3 mb-2">
                                <i class="fas fa-times-circle text-danger fa-2x"></i>
                            </div>
                            <h5 class="fw-bold">{{ $karyawan->cutis->where('status_cuti', 'ditolak')->count() }}</h5>
                            <p class="text-muted small">Cuti Ditolak</p>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($karyawan->cutis->count() > 0)
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header bg-primary text-white py-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-history me-2"></i>
                            <h5 class="card-title mb-0 fw-bold">Riwayat Cuti</h5>
                        </div>
                        <span class="badge bg-white text-primary rounded-pill px-2 py-1 small">{{ $karyawan->cutis->count() }} Entri</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-2 px-2">#</th>
                                    <th class="py-2 px-2">Jenis Cuti</th>
                                    <th class="py-2 px-2">Tanggal</th>
                                    <th class="py-2 px-2">Durasi</th>
                                    <th class="py-2 px-2">Ideal/Actual</th>
                                    <th class="py-2 px-2">Sesuai DOH</th>
                                    <th class="py-2 px-2">Status</th>
                                    <th class="py-2 px-2 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cutiAnalytics as $cutiData)
                                    <tr>
                                        <td class="py-2 px-2">{{ $loop->iteration }}</td>
                                        <td class="py-2 px-2">
                                            <span class="d-inline-block text-truncate" style="max-width: 120px;">
                                                {{ $cutiData['jenis_cuti'] }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-2">
                                            <div class="small">{{ $cutiData['tanggal_mulai_formatted'] }}</div>
                                            <div class="small text-muted">s/d {{ $cutiData['tanggal_selesai_formatted'] }}</div>
                                        </td>
                                        <td class="py-2 px-2">
                                            <span class="badge bg-secondary rounded-pill px-2 small">{{ $cutiData['jumlah_hari'] }} hari</span>
                                        </td>
                                        <td class="py-2 px-2">
                                            @php
                                                // Check if this is a periodic leave and has idealActualDate data
                                                $isPeriodic = stripos($cutiData['jenis_cuti'], 'periodik') !== false;
                                                $cuti = \App\Models\Cuti::find($cutiData['cuti_id']);
                                                $idealActualDate = null;
                                                $idealActualFormatted = '-';
                                                
                                                if ($isPeriodic && $cuti && $karyawan->doh) {
                                                    $tanggalMulai = \Carbon\Carbon::parse($cuti->tanggal_mulai);
                                                    $dohDate = \Carbon\Carbon::parse($karyawan->doh);
                                                    
                                                    // Calculate reset period based on employee status
                                                    $resetWeeks = $karyawan->status === 'Staff' ? 7 : 12;
                                                    
                                                    // Create CutiService to get ideal date
                                                    $cutiService = new \App\Services\CutiService();
                                                    $allLeaveStartDates = $cutiService->getAllLeaveStartDates($karyawan->id, $cutiData['jenis_cuti_id']);
                                                    
                                                    if (!empty($allLeaveStartDates)) {
                                                        $idealActualDate = $cutiService->calculateIdealActualDate($allLeaveStartDates, $tanggalMulai, $resetWeeks);
                                                        if ($idealActualDate) {
                                                            $idealActualFormatted = $idealActualDate->format('d/m/Y');
                                                        }
                                                    }
                                                }
                                            @endphp
                                            @if($isPeriodic)
                                                <div class="small {{ $idealActualDate ? 'text-primary' : 'text-muted' }}">
                                                    {{ $idealActualFormatted }}
                                                </div>
                                                @if($idealActualDate && isset($cutiData['status_selisih']))
                                                    @if($cutiData['status_selisih'] === 'lebih_awal')
                                                        <span class="badge bg-warning text-dark rounded-pill py-0 px-1 small">
                                                            <i class="fas fa-arrow-left"></i> {{ $cutiData['selisih_hari'] ?? 0 }}
                                                        </span>
                                                    @elseif($cutiData['status_selisih'] === 'lebih_lambat')
                                                        <span class="badge bg-success rounded-pill py-0 px-1 small">
                                                            <i class="fas fa-arrow-right"></i> {{ $cutiData['selisih_hari'] ?? 0 }}
                                                        </span>
                                                    @elseif($cutiData['status_selisih'] === 'tepat_waktu')
                                                        <span class="badge bg-info rounded-pill py-0 px-1 small">
                                                            <i class="fas fa-equals"></i>
                                                        </span>
                                                    @endif
                                                @endif
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-2">
                                            @php
                                                $expectedLeaveDate = $cutiData['expected_leave_date'] ?? null;
                                                $expectedLeaveFormatted = $expectedLeaveDate ? 
                                                    \Carbon\Carbon::parse($expectedLeaveDate)->format('d/m/Y') : '-';
                                            @endphp
                                            <div class="small {{ $expectedLeaveDate ? 'text-primary' : 'text-muted' }}">
                                                {{ $expectedLeaveFormatted }}
                                            </div>
                                            @if($expectedLeaveDate && isset($cutiData['status_selisih']))
                                                @if($cutiData['status_selisih'] === 'lebih_awal')
                                                    <span class="badge bg-warning text-dark rounded-pill py-0 px-1 small">
                                                        <i class="fas fa-arrow-left"></i> {{ $cutiData['selisih_hari'] ?? 0 }}
                                                    </span>
                                                @elseif($cutiData['status_selisih'] === 'lebih_lambat')
                                                    <span class="badge bg-success rounded-pill py-0 px-1 small">
                                                        <i class="fas fa-arrow-right"></i> {{ abs($cutiData['selisih_hari'] ?? 0) }}
                                                    </span>
                                                @elseif($cutiData['status_selisih'] === 'tepat_waktu')
                                                    <span class="badge bg-info rounded-pill py-0 px-1 small">
                                                        <i class="fas fa-equals"></i>
                                                    </span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="py-2 px-2">
                                            @php
                                                $cuti = \App\Models\Cuti::find($cutiData['cuti_id']);
                                                $statusCuti = $cuti ? $cuti->status_cuti : 'pending';
                                            @endphp
                                            @if($statusCuti == 'pending')
                                                <span class="badge bg-warning text-dark rounded-pill px-2 py-1 small">
                                                    <i class="fas fa-clock"></i> Pending
                                                </span>
                                            @elseif($statusCuti == 'disetujui')
                                                <span class="badge bg-success rounded-pill px-2 py-1 small">
                                                    <i class="fas fa-check"></i> Disetujui
                                                </span>
                                            @else
                                                <span class="badge bg-danger rounded-pill px-2 py-1 small">
                                                    <i class="fas fa-times"></i> Ditolak
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-2 text-center">
                                            <a href="{{ route('cutis.show', $cutiData['cuti_id']) }}" class="btn btn-sm btn-outline-primary btn-icon rounded-circle" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @else
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-history me-2 fs-5"></i>
                        <h5 class="card-title mb-0 fw-bold">Riwayat Cuti</h5>
                    </div>
                </div>
                <div class="card-body p-5 text-center">
                    <div class="py-4">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                        <h5 class="text-muted">Belum ada riwayat pengajuan cuti</h5>
                        <p class="text-muted mb-4">Karyawan belum pernah mengajukan cuti</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <form id="delete-form" action="{{ route('karyawans.destroy', $karyawan->id) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
    
    <!-- Modal Konfirmasi Hapus Karyawan -->
    <div class="modal fade" id="deleteKaryawanModal" tabindex="-1" aria-labelledby="deleteKaryawanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteKaryawanModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Hapus Data Karyawan?
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menghapus data karyawan?</p>
                    <div class="mt-3 border-top pt-3">
                        <div class="mb-2">
                            <strong>Nama:</strong> <span id="modal-karyawan-nama"></span>
                        </div>
                        <div>
                            <strong>NIK:</strong> <span id="modal-karyawan-nik"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="button" id="confirmDelete" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Ya, Hapus!
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete confirmation
        const deleteBtn = document.querySelector('.delete-btn');
        const deleteForm = document.getElementById('delete-form');
        const confirmDeleteBtn = document.getElementById('confirmDelete');
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteKaryawanModal'));
        
        deleteBtn.addEventListener('click', function() {
            const id = this.dataset.id;
            const nama = this.dataset.nama;
            const nik = this.dataset.nik;
            
            // Set data in modal
            document.getElementById('modal-karyawan-nama').textContent = nama;
            document.getElementById('modal-karyawan-nik').textContent = nik;
            
            // Show modal
            deleteModal.show();
        });
        
        // Submit form when confirm button is clicked
        confirmDeleteBtn.addEventListener('click', function() {
            deleteForm.submit();
        });
    });
</script>
@endpush