@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle me-2"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-circle me-2"></i>
            <div>{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Header Section -->
    <div class="card bg-primary bg-gradient text-white rounded-3 mb-4 border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white-50 mb-1">
                        <a href="{{ route('karyawans.index') }}" class="text-white text-decoration-none">
                            <i class="fas fa-users me-2"></i>Karyawan
                        </a> /
                        <a href="{{ route('karyawans.show', $karyawan->id) }}" class="text-white text-decoration-none">
                            {{ $karyawan->nama }}
                        </a> /
                        <span>Monitoring Cuti</span>
                    </h6>
                    <h2 class="mb-0 fw-bold d-flex align-items-center">
                        <i class="fas fa-chart-line me-3"></i>
                        Monitoring Cuti
                    </h2>
                </div>
                <div>
                    <a href="{{ route('karyawans.refresh-leave-balances', $karyawan->id) }}" class="btn btn-light btn-sm rounded-pill me-2">
                        <i class="fas fa-sync-alt me-1"></i> Refresh Data
                    </a>
                    <a href="{{ route('karyawans.show', $karyawan->id) }}" class="btn btn-outline-light btn-sm rounded-pill">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Info Card -->
    <div class="card shadow-sm rounded-3 mb-4 border-0">
        <div class="card-body p-0">
            <div class="row g-0">
                <div class="col-md-2 bg-light p-4 d-flex justify-content-center align-items-center">
                    <div class="text-center">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 mb-3 mx-auto" style="width: 90px; height: 90px;">
                            <i class="fas fa-user fa-3x text-primary"></i>
                        </div>
                        <h5 class="text-primary fw-bold mb-0">{{ $karyawan->nama }}</h5>
                    </div>
                </div>
                <div class="col-md-10">
                    <div class="p-4">
                        <div class="row g-4">
                            <div class="col-md-3 col-sm-6">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                        <i class="fas fa-id-card text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">NIK</div>
                                        <div class="fw-bold">{{ $karyawan->nik ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                        <i class="fas fa-building text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Departemen</div>
                                        <div class="fw-bold">{{ $karyawan->departemen ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                        <i class="fas fa-briefcase text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">Jabatan</div>
                                        <div class="fw-bold">{{ $karyawan->jabatan ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 p-2 rounded me-3">
                                        <i class="fas fa-calendar-day text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small">DOH</div>
                                        <div class="fw-bold">{{ $karyawan->doh ? \Carbon\Carbon::parse($karyawan->doh)->format('d/m/Y') : 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Status Cards -->
    <div class="row g-4 mb-4">
        <!-- Current Leave Status -->
        <div class="col-md-6">
            <div class="card h-100 rounded-3 shadow-sm border-0 overflow-hidden">
                <div class="card-header p-3 {{ $currentCuti ? 'bg-primary' : 'bg-secondary' }} bg-gradient text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-check me-2"></i>
                        <h5 class="mb-0 fw-bold">Status Cuti Saat Ini</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if ($currentCuti)
                        <div class="d-flex align-items-center mb-3">
                            <div class="p-3 rounded-circle bg-primary bg-opacity-10 me-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-user-clock text-primary fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="mb-1 text-primary fw-bold">Sedang Cuti</h5>
                                <p class="mb-0 text-muted">{{ $currentCuti->jenisCuti->nama_jenis }}</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="bg-light rounded p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="text-muted small">Tanggal Mulai</div>
                                            <i class="fas fa-calendar-alt text-primary"></i>
                                        </div>
                                        <div class="fw-bold mt-1">{{ \Carbon\Carbon::parse($currentCuti->tanggal_mulai)->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="bg-light rounded p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="text-muted small">Tanggal Selesai</div>
                                            <i class="fas fa-calendar-check text-primary"></i>
                                        </div>
                                        <div class="fw-bold mt-1">{{ \Carbon\Carbon::parse($currentCuti->tanggal_selesai)->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <h6 class="mb-0 fw-bold">Sisa Hari Cuti</h6>
                                <span class="badge bg-primary rounded-pill">{{ $sisaHariCuti }} hari</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                @php
                                    $totalDays = $currentCuti->lama_hari;
                                    $elapsedDays = $totalDays - $sisaHariCuti;
                                    $elapsedDays = max(0, min($elapsedDays, $totalDays));
                                    $progressPercent = ($totalDays > 0) ? ($elapsedDays / $totalDays) * 100 : 0;
                                    $progressPercent = max(0, min(100, $progressPercent));
                                @endphp
                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progressPercent }}%;"
                                    aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="small text-muted">{{ $elapsedDays }} hari berlalu</span>
                                <span class="small text-muted">{{ $totalDays }} hari total</span>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="alert alert-light mb-0 border">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-quote-left text-primary me-3 opacity-50 fa-2x"></i>
                                    <div>
                                        <small class="d-block text-muted mb-1">Alasan Cuti:</small>
                                        <p class="mb-0 fst-italic">{{ $currentCuti->alasan }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="p-3 rounded-circle bg-secondary bg-opacity-10 icon-container mb-3">
                                <i class="fas fa-calendar-times fa-3x text-secondary"></i>
                            </div>
                            <h5 class="text-secondary">Karyawan tidak sedang cuti</h5>
                            <p class="text-muted mb-0">Tidak ada cuti aktif saat ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Leave Status -->
        <div class="col-md-6">
            <div class="card h-100 rounded-3 shadow-sm border-0 overflow-hidden">
                <div class="card-header p-3 {{ $upcomingCuti ? 'bg-warning' : 'bg-secondary' }} bg-gradient text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-calendar-plus me-2"></i>
                        <h5 class="mb-0 fw-bold">Cuti Mendatang</h5>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if ($upcomingCuti)
                        <div class="d-flex align-items-center mb-3">
                            <div class="p-3 rounded-circle bg-warning bg-opacity-10 me-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-hourglass-half text-warning fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="mb-1 text-warning fw-bold">Akan Cuti</h5>
                                <p class="mb-0 text-muted">{{ $upcomingCuti->jenisCuti->nama_jenis }}</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="bg-light rounded p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="text-muted small">Tanggal Mulai</div>
                                            <i class="fas fa-calendar-alt text-warning"></i>
                                        </div>
                                        <div class="fw-bold mt-1">{{ \Carbon\Carbon::parse($upcomingCuti->tanggal_mulai)->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="bg-light rounded p-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="text-muted small">Durasi</div>
                                            <i class="fas fa-clock text-warning"></i>
                                        </div>
                                        <div class="fw-bold mt-1">{{ (int)$upcomingCuti->lama_hari }} hari</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning d-flex align-items-center mb-3">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <div>
                                Mulai dalam <strong>{{ (int)\Carbon\Carbon::parse($upcomingCuti->tanggal_mulai)->diffInDays(\Carbon\Carbon::now()) }} hari</strong>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="alert alert-light mb-0 border">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-quote-left text-warning me-3 opacity-50 fa-2x"></i>
                                    <div>
                                        <small class="d-block text-muted mb-1">Alasan Cuti:</small>
                                        <p class="mb-0 fst-italic">{{ $upcomingCuti->alasan }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="p-3 rounded-circle bg-secondary bg-opacity-10 icon-container mb-3">
                                <i class="fas fa-calendar fa-3x text-secondary"></i>
                            </div>
                            <h5 class="text-secondary">Tidak ada jadwal cuti mendatang</h5>
                            <p class="text-muted mb-0">Belum ada cuti yang direncanakan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Leave Balance Summary -->
    <div class="card mb-4 border-0 shadow-sm rounded-3">
        <div class="card-header bg-light p-3">
            <div class="d-flex align-items-center">
                <div class="p-2 bg-primary bg-opacity-10 rounded me-3">
                    <i class="fas fa-calculator text-primary"></i>
                </div>
                <h5 class="mb-0 fw-bold">Ringkasan Cuti Tahun {{ date('Y') }}</h5>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th class="py-3 ps-4">Jenis Cuti</th>
                            <th class="py-3 text-center">Jatah Cuti</th>
                            <th class="py-3 text-center">Telah Digunakan</th>
                            <th class="py-3 text-center">Sisa Cuti</th>
                            <th class="py-3 text-end pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sisaCutiPerType as $jenisCutiId => $cutiInfo)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        @php
                                            $isAnnualLeave = stripos($cutiInfo['nama_jenis'], 'tahunan') !== false;
                                            $isPeriodikCuti = in_array($cutiInfo['nama_jenis'], ['Cuti periodik (Lokal)', 'Cuti periodik (Luar)']);

                                            if ($isAnnualLeave) {
                                                $iconClass = 'fa-calendar-alt';
                                                $bgClass = 'bg-success bg-opacity-10';
                                                $textClass = 'text-success';
                                            } elseif ($isPeriodikCuti) {
                                                $iconClass = 'fa-history';
                                                $bgClass = 'bg-primary bg-opacity-10';
                                                $textClass = 'text-primary';
                                            } else {
                                                $iconClass = 'fa-calendar-day';
                                                $bgClass = 'bg-info bg-opacity-10';
                                                $textClass = 'text-info';
                                            }
                                        @endphp
                                        <div class="p-2 rounded me-3 {{ $bgClass }}">
                                            <i class="fas {{ $iconClass }} {{ $textClass }}"></i>
                                        </div>
                                        <span class="fw-bold">{{ $cutiInfo['nama_jenis'] }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold badge bg-light text-dark border px-3 py-2">{{ $cutiInfo['jatah_hari'] }} hari</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold badge bg-light text-dark border px-3 py-2">{{ $cutiInfo['digunakan'] }} hari</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold badge bg-light text-dark border px-3 py-2">{{ $cutiInfo['sisa'] }} hari</span>
                                </td>
                                <td class="text-end pe-4">
                                    @php
                                        if ($cutiInfo['jatah_hari'] > 0) {
                                            $percentage = ($cutiInfo['digunakan'] / $cutiInfo['jatah_hari']) * 100;
                                            $percentage = min(100, $percentage);

                                            if ($percentage > 75) {
                                                $barClass = 'bg-danger';
                                            } elseif ($percentage > 50) {
                                                $barClass = 'bg-warning';
                                            } else {
                                                $barClass = 'bg-success';
                                            }
                                        } else {
                                            $percentage = 0;
                                            $barClass = 'bg-secondary';
                                        }
                                    @endphp
                                    <div style="width: 120px;" class="ms-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-muted small">Terpakai</span>
                                            <span class="text-muted small">{{ round($percentage) }}%</span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar {{ $barClass }}" role="progressbar"
                                                style="width: {{ $percentage }}%"
                                                aria-valuenow="{{ $percentage }}"
                                                aria-valuemin="0"
                                                aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detailed Leave Analysis for Each Type -->
    <div class="row g-4">
        @foreach($sisaCutiPerType as $jenisCutiId => $cutiInfo)
            @php
                $isAnnualLeave = stripos($cutiInfo['nama_jenis'], 'tahunan') !== false;
                $isPeriodikCuti = in_array($cutiInfo['nama_jenis'], ['Cuti periodik (Lokal)', 'Cuti periodik (Luar)']);

                if ($isAnnualLeave) {
                    $cardHeaderClass = 'bg-success';
                    $iconClass = 'fa-calendar-alt';
                } elseif ($isPeriodikCuti) {
                    $cardHeaderClass = 'bg-primary';
                    $iconClass = 'fa-history';
                } else {
                    $cardHeaderClass = 'bg-info';
                    $iconClass = 'fa-calendar-day';
                }
            @endphp
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header {{ $cardHeaderClass }} bg-gradient p-3 text-white">
                        <div class="d-flex align-items-center">
                            <i class="fas {{ $iconClass }} me-2"></i>
                            <h5 class="mb-0 fw-bold">{{ $cutiInfo['nama_jenis'] }}</h5>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Usage Stats -->
                        <div class="row g-3 mb-4">
                            <div class="col-4">
                                <div class="bg-light rounded p-3 h-100 text-center">
                                    <div class="text-muted small mb-2">Jatah</div>
                                    <h3 class="fw-bold text-primary mb-0">{{ $cutiInfo['jatah_hari'] }}</h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-light rounded p-3 h-100 text-center">
                                    <div class="text-muted small mb-2">Digunakan</div>
                                    <h3 class="fw-bold text-danger mb-0">{{ $cutiInfo['digunakan'] }}</h3>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-light rounded p-3 h-100 text-center">
                                    <div class="text-muted small mb-2">Sisa</div>
                                    <h3 class="fw-bold text-success mb-0">{{ $cutiInfo['sisa'] }}</h3>
                                </div>
                            </div>
                        </div>

                        @if(!$isPeriodikCuti && is_numeric($cutiInfo['jatah_hari']) && $cutiInfo['jatah_hari'] > 0)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-muted small">Persentase Penggunaan</span>
                                    @php
                                    $percentage = ($cutiInfo['digunakan'] / $cutiInfo['jatah_hari']) * 100;
                                    $textClass = $percentage > 75 ? 'text-danger' : ($percentage > 50 ? 'text-warning' : 'text-success');
                                    $bgClass = $percentage > 75 ? 'bg-danger' : ($percentage > 50 ? 'bg-warning' : 'bg-success');
                                    @endphp
                                    <span class="small {{ $textClass }} fw-bold">{{ round($percentage) }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar {{ $bgClass }}" role="progressbar" style="width: {{ $percentage }}%;"
                                        aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Period Information for Annual Leave -->
                        @if($isAnnualLeave && isset($cutiInfo['reset_date']) && isset($cutiInfo['next_reset_date']))
                            <div class="alert alert-light border mb-0">
                                <h6 class="mb-2 d-flex align-items-center">
                                    <i class="fas fa-history text-success me-2"></i>
                                    <span>Periode Aktif</span>
                                </h6>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-alt text-muted me-2"></i>
                                    <span>{{ \Carbon\Carbon::parse($cutiInfo['reset_date'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($cutiInfo['next_reset_date'])->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Period Information for Periodic Leave -->
                        @if($isPeriodikCuti)
                            <div class="alert alert-light border mb-0">
                                <h6 class="mb-2 d-flex align-items-center">
                                    <i class="fas fa-clock text-primary me-2"></i>
                                    <span>Informasi Periode</span>
                                </h6>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <span class="badge bg-primary rounded-pill me-2">
                                                <i class="fas fa-history"></i>
                                            </span>
                                            <span>Durasi Periode:</span>
                                        </div>
                                        <strong>{{ $karyawan->status === 'Staff' ? '7 minggu' : '12 minggu' }}</strong>
                                    </div>
                                </div>

                                @if(isset($cutiInfo['current_period_start']) && isset($cutiInfo['current_period_end']))
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <span class="badge bg-info rounded-pill me-2">
                                                <i class="fas fa-calendar-week"></i>
                                            </span>
                                            <span>Periode Saat Ini:</span>
                                        </div>
                                        <strong>{{ \Carbon\Carbon::parse($cutiInfo['current_period_start'])->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($cutiInfo['current_period_end'])->format('d/m/Y') }}</strong>
                                    </div>
                                </div>
                                @endif

                                <!-- Tanggal Ideal (DOH) -->
                                @if(isset($cutiInfo['status_info']['recommendation_based_on_doh']))
                                <div class="mb-2 mt-3 pt-2 border-top">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <span class="badge bg-primary rounded-pill me-2">
                                                <i class="fas fa-calendar-day"></i>
                                            </span>
                                            <span>Tanggal Ideal (DOH):</span>
                                        </div>
                                        <strong>{{ \Carbon\Carbon::parse($cutiInfo['status_info']['recommendation_based_on_doh'])->format('d/m/Y') }}</strong>
                                    </div>
                                </div>
                                @endif

                                <!-- Tanggal Ideal Actual -->
                                @php
                                    $idealActualDate = null;

                                    // Get the latest leave for this type
                                    $latestCuti = null;
                                    $allLeaveStartDates = [];

                                    // Use the current key in the foreach loop as the jenis_cuti_id
                                    // This is the most reliable source since it's the actual key in the sisaCutiPerType array
                                    // $jenisCutiId is already defined in the foreach loop

                                    // Add additional debug info
                                    if (isset($cutiInfo['status_info'])) {
                                        $cutiInfo['status_info']['jenis_cuti_id'] = $jenisCutiId;
                                    }

                                    if ($jenisCutiId) {
                                        $cutiService = new \App\Services\CutiService();
                                        $allLeaveStartDates = $cutiService->getAllLeaveStartDates($karyawan->id, $jenisCutiId);

                                        if (!empty($allLeaveStartDates)) {
                                            $resetWeeks = $karyawan->status === 'Staff' ? 7 : 12;
                                            $idealActualDate = $cutiService->calculateIdealActualDate(
                                                $allLeaveStartDates,
                                                \Carbon\Carbon::now(),
                                                $resetWeeks
                                            );
                                        }
                                    }
                                @endphp

                                @if($idealActualDate)
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <span class="badge bg-info rounded-pill me-2">
                                                <i class="fas fa-calendar-alt"></i>
                                            </span>
                                            <span>Tanggal Ideal Actual:</span>
                                        </div>
                                        <strong>{{ $idealActualDate->format('d/m/Y') }}</strong>
                                    </div>
                                </div>
                                @endif

                                <!-- Rekomendasi Berikutnya section removed as requested -->
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Leave History Data Table -->
    <div class="card mt-4 border-0 shadow-sm rounded-3">
        <div class="card-header bg-light p-3">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="p-2 bg-primary bg-opacity-10 rounded me-3">
                        <i class="fas fa-history text-primary"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">Riwayat Cuti</h5>
                </div>
                @if($karyawan->doh)
                    <span class="badge bg-primary rounded-pill px-3 py-2">
                        <i class="fas fa-calendar-day me-1"></i> DOH: {{ \Carbon\Carbon::parse($karyawan->doh)->format('d/m/Y') }}
                    </span>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            @if(count($cutiAnalytics) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 ps-4">Jenis Cuti</th>
                                <th class="py-3">Tanggal</th>
                                <th class="py-3">Durasi</th>
                                <th class="py-3">Tanggal Ideal Actual</th>
                                <th class="py-3">Tanggal Sesuai DOH</th>
                                <th class="py-3">Tanggal Pengajuan</th>
                                <th class="py-3">Analisis</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cutiAnalytics as $analytic)
                                @php
                                    $isAnnualLeave = stripos($analytic['jenis_cuti'], 'tahunan') !== false;
                                    $isPeriodic = $analytic['is_periodic'] ?? false;

                                    if ($analytic['status_selisih'] == 'lebih_awal') {
                                        $statusClass = 'danger';
                                        $statusText = 'Hutang ' . $analytic['selisih_hari'] . ' hari';
                                    } elseif ($analytic['status_selisih'] == 'lebih_lambat') {
                                        $statusClass = 'success';
                                        $statusText = 'Tepat waktu +' . abs($analytic['selisih_hari']) . ' hari';
                                    } else {
                                        $statusClass = 'primary';
                                        $statusText = 'Tepat waktu';
                                    }

                                    // Calculate Ideal Actual Date for periodic leave types
                                    $idealActualDate = null;
                                    $idealActualFormatted = '-';

                                    if ($isPeriodic && $karyawan->doh) {
                                        // If ideal_actual_date is already set in analytics, use it
                                        if (isset($analytic['ideal_actual_date']) && $analytic['ideal_actual_date']) {
                                            $idealActualDate = $analytic['ideal_actual_date'];
                                            $idealActualFormatted = $idealActualDate->format('d/m/Y');
                                        } else {
                                            // Otherwise calculate it
                                            $tanggalMulai = $analytic['tanggal_mulai'];
                                            $dohDate = \Carbon\Carbon::parse($karyawan->doh);
                                            $resetWeeks = $karyawan->status === 'Staff' ? 7 : 12;

                                            $cutiService = new \App\Services\CutiService();
                                            $allLeaveStartDates = $cutiService->getAllLeaveStartDates($karyawan->id, $analytic['jenis_cuti_id']);

                                            if (!empty($allLeaveStartDates)) {
                                                $idealActualDate = $cutiService->calculateIdealActualDate($allLeaveStartDates, $tanggalMulai, $resetWeeks);
                                                if ($idealActualDate) {
                                                    $idealActualFormatted = $idealActualDate->format('d/m/Y');
                                                }
                                            }
                                        }
                                    }

                                    // Get expected date based on DOH
                                    $expectedLeaveDate = $analytic['expected_date'] ?? null;
                                    $expectedLeaveFormatted = $expectedLeaveDate ? $expectedLeaveDate->format('d/m/Y') : '-';
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            @if($isAnnualLeave)
                                                <span class="badge bg-success me-2">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </span>
                                            @elseif($isPeriodic)
                                                <span class="badge bg-primary me-2">
                                                    <i class="fas fa-history"></i>
                                                </span>
                                            @else
                                                <span class="badge bg-info me-2">
                                                    <i class="fas fa-calendar-day"></i>
                                                </span>
                                            @endif
                                            {{ $analytic['jenis_cuti'] }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="far fa-calendar-alt text-muted me-2"></i>
                                            {{ $analytic['tanggal_mulai']->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">
                                            {{ $analytic['durasi'] }} hari
                                        </span>
                                    </td>
                                    <td>
                                        @if($isPeriodic)
                                            <div class="small fw-medium {{ $idealActualDate ? 'text-primary' : 'text-muted' }}">
                                                {{ $idealActualFormatted }}
                                            </div>
                                            @if($idealActualDate && isset($analytic['selisih_hari_ideal_actual']))
                                                @if($analytic['status_selisih_ideal_actual'] === 'lebih_awal')
                                                    <span class="badge bg-warning text-dark rounded-pill">
                                                        <i class="fas fa-arrow-left me-1"></i> {{ $analytic['selisih_hari_ideal_actual'] }} hari
                                                    </span>
                                                @elseif($analytic['status_selisih_ideal_actual'] === 'lebih_lambat')
                                                    <span class="badge bg-success rounded-pill">
                                                        <i class="fas fa-arrow-right me-1"></i> {{ abs($analytic['selisih_hari_ideal_actual']) }} hari
                                                    </span>
                                                @elseif($analytic['status_selisih_ideal_actual'] === 'tepat_waktu')
                                                    <span class="badge bg-info rounded-pill">
                                                        <i class="fas fa-equals me-1"></i> Tepat waktu
                                                    </span>
                                                @endif
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$isAnnualLeave && $expectedLeaveDate)
                                            <div class="small fw-medium {{ $expectedLeaveDate ? 'text-primary' : 'text-muted' }}">
                                                {{ $expectedLeaveFormatted }}
                                            </div>
                                            @if($expectedLeaveDate && isset($analytic['selisih_hari']))
                                                @if($analytic['status_selisih'] === 'lebih_awal')
                                                    <span class="badge bg-warning text-dark rounded-pill">
                                                        <i class="fas fa-arrow-left me-1"></i> {{ $analytic['selisih_hari'] }} hari
                                                    </span>
                                                @elseif($analytic['status_selisih'] === 'lebih_lambat')
                                                    <span class="badge bg-success rounded-pill">
                                                        <i class="fas fa-arrow-right me-1"></i> {{ abs($analytic['selisih_hari']) }} hari
                                                    </span>
                                                @elseif($analytic['status_selisih'] === 'tepat_waktu')
                                                    <span class="badge bg-info rounded-pill">
                                                        <i class="fas fa-equals me-1"></i> Tepat waktu
                                                    </span>
                                                @endif
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="far fa-edit text-muted me-2"></i>
                                            {{ $analytic['tanggal_pengajuan']->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        @if(!$isAnnualLeave && isset($analytic['status_selisih']))
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-{{ $statusClass }} rounded-pill px-3 py-2">
                                                    @if($analytic['status_selisih'] == 'lebih_awal')
                                                        <i class="fas fa-exclamation-circle me-1"></i>
                                                    @elseif($analytic['status_selisih'] == 'lebih_lambat')
                                                        <i class="fas fa-check-circle me-1"></i>
                                                    @else
                                                        <i class="fas fa-info-circle me-1"></i>
                                                    @endif
                                                    {{ $statusText }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="p-3 rounded-circle bg-light d-inline-block mb-3">
                        <i class="fas fa-calendar-times fa-3x text-secondary"></i>
                    </div>
                    <h5 class="text-secondary">Belum ada data pengajuan cuti</h5>
                    <p class="text-muted">Karyawan belum pernah mengajukan cuti</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .progress {
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar {
        transition: width 0.6s ease;
    }

    .badge {
        font-weight: 500;
    }

    .alert {
        border-radius: 0.5rem;
    }

    /* Custom colors for light backgrounds */
    .bg-light {
        background-color: #f8f9fa !important;
    }

    .bg-primary.bg-opacity-10 {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }

    .bg-success.bg-opacity-10 {
        background-color: rgba(25, 135, 84, 0.1) !important;
    }

    .bg-warning.bg-opacity-10 {
        background-color: rgba(255, 193, 7, 0.1) !important;
    }

    .bg-secondary.bg-opacity-10 {
        background-color: rgba(108, 117, 125, 0.1) !important;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Smooth scroll animation for card hover
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transition = 'transform 0.2s, box-shadow 0.2s';
            });
        });
    });
</script>
@endsection