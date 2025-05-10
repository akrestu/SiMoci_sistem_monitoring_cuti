@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Dashboard HR</h2>
    
    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-filter me-1"></i>
            Filter Data
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.hr') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="departemen" class="form-label">Departemen</label>
                    <select class="form-select" id="departemen" name="departemen">
                        <option value="all" {{ $departemen == 'all' ? 'selected' : '' }}>Semua Departemen</option>
                        @foreach($departements as $dept)
                            <option value="{{ $dept }}" {{ $departemen == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Tanggal Selesai</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Terapkan</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <h5 class="card-title">Total Karyawan</h5>
                    <h2 class="display-4">{{ $totalKaryawanByDept }}</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('karyawans.index') }}">Lihat Detail</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <h5 class="card-title">Cuti Pending</h5>
                    <h2 class="display-4">{{ $cutiPendingByDept }}</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('cutis.index', ['status' => 'pending']) }}">Lihat Detail</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <h5 class="card-title">Cuti Disetujui</h5>
                    <h2 class="display-4">{{ $cutiDisetujuiByDept }}</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('cutis.index', ['status' => 'disetujui']) }}">Lihat Detail</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <h5 class="card-title">Cuti Ditolak</h5>
                    <h2 class="display-4">{{ $cutiDitolakByDept }}</h2>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('cutis.index', ['status' => 'ditolak']) }}">Lihat Detail</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Approval Section -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-warning text-white">
                    <i class="fas fa-clock me-1"></i>
                    Cuti Menunggu Persetujuan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Departemen</th>
                                    <th>Jenis Cuti</th>
                                    <th>Tanggal</th>
                                    <th>Durasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingCutis as $cuti)
                                <tr>
                                    <td>{{ $cuti->karyawan->nama }}</td>
                                    <td>{{ $cuti->karyawan->departemen }}</td>
                                    <td>{{ $cuti->jenisCuti->nama_jenis }}</td>
                                    <td>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') }}</td>
                                    <td>{{ $cuti->lama_hari }} hari</td>
                                    <td>
                                        <div class="d-flex">
                                            <a href="{{ route('cutis.show', $cuti->id) }}" class="btn btn-info btn-sm me-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('cutis.approve', $cuti->id) }}" method="POST" class="me-1">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('cutis.reject', $cuti->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada pengajuan cuti yang menunggu persetujuan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <a href="{{ route('cutis.index', ['status' => 'pending']) }}" class="btn btn-warning mt-2">Lihat Semua</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Employees on Leave -->
    <div class="row mt-2">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-user-clock me-1"></i>
                    Karyawan Sedang Cuti
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Departemen</th>
                                    <th>Jenis Cuti</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Sisa Hari</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($karyawanCutiToday as $karyawan)
                                    @foreach($karyawan->cutis as $cuti)
                                    <tr>
                                        <td>{{ $karyawan->nama }}</td>
                                        <td>{{ $karyawan->departemen }}</td>
                                        <td>{{ $cuti->jenisCuti->nama_jenis }}</td>
                                        <td>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') }}</td>
                                        <td>
                                            @php
                                                $cuti = $karyawan->cutis->first();
                                                $sisaHari = (int)(\Carbon\Carbon::parse($cuti->tanggal_selesai)->diffInDays(\Carbon\Carbon::now()) + 1);
                                            @endphp
                                            <span class="badge bg-{{ $sisaHari <= 2 ? 'danger' : 'primary' }}">{{ $sisaHari }} hari</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada karyawan yang sedang cuti</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Cuti by Department Chart -->
    <div class="row mt-2">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-chart-bar me-1"></i>
                    Statistik Cuti per Departemen
                </div>
                <div class="card-body">
                    <canvas id="cutiByDepartmentChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data from PHP
        const cutiByDepartmentData = @json($cutiByDepartment);
        
        // Prepare data for chart
        const departments = cutiByDepartmentData.map(item => item.departemen);
        const cutiCounts = cutiByDepartmentData.map(item => item.total);
        
        // Create chart
        const ctx = document.getElementById('cutiByDepartmentChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: departments,
                datasets: [{
                    label: 'Jumlah Pengajuan Cuti',
                    data: cutiCounts,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endsection