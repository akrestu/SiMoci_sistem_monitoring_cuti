@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold text-primary">
            <i class="fas fa-chart-line me-2"></i>Dashboard Pengelolaan Transportasi
        </h2>
        <a href="{{ route('transportasi_details.index') }}" class="btn btn-primary rounded-pill px-4 d-flex align-items-center">
            <i class="fas fa-ticket-alt me-2"></i> Lihat Semua Tiket
        </a>
    </div>
    
    <!-- Stats Cards Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient-primary h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Tiket</h6>
                            <h3 class="text-white mb-0 fw-bold">{{ \App\Models\TransportasiDetail::count() }}</h3>
                        </div>
                        <div class="icon-box bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-ticket-alt text-white fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient-danger h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Belum Dipesan</h6>
                            <h3 class="text-white mb-0 fw-bold">{{ \App\Models\TransportasiDetail::where('status_pemesanan', 'belum_dipesan')->count() }}</h3>
                        </div>
                        <div class="icon-box bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-clock text-white fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient-success h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Tiket Terbit</h6>
                            <h3 class="text-white mb-0 fw-bold">{{ \App\Models\TransportasiDetail::where('status_pemesanan', 'tiket_terbit')->count() }}</h3>
                        </div>
                        <div class="icon-box bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-check-circle text-white fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient-warning h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-dark mb-1">Perlu Diproses</h6>
                            <h3 class="text-dark mb-0 fw-bold">{{ isset($pendingBookings) ? $pendingBookings->count() : 0 }}</h3>
                        </div>
                        <div class="icon-box bg-dark bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-tasks text-dark fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Notification for upcoming ticket deadlines -->
    @if(isset($upcomingDeadlines) && $upcomingDeadlines->count() > 0)
    <div class="card border-0 shadow-sm rounded-4 bg-danger bg-opacity-10 mb-4 animate__animated animate__fadeIn">
        <div class="card-body p-3">
            <div class="d-flex align-items-center mb-3">
                <div class="alert-icon bg-danger rounded-circle p-3 me-3">
                    <i class="fas fa-exclamation-triangle text-white"></i>
                </div>
                <div>
                    <h5 class="text-danger mb-0 fw-bold">Prioritas Tinggi: {{ $upcomingDeadlines->count() }} Tiket Mendekati Deadline</h5>
                    <p class="mb-0">Tiket-tiket berikut mendekati deadline dan harus segera diproses</p>
                </div>
                <button class="btn btn-sm ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#deadlineCollapse" aria-expanded="true">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            
            <div class="collapse show" id="deadlineCollapse">
                <div class="table-responsive table-scroll-container">
                    <table class="table table-borderless table-hover align-middle">
                        <thead class="bg-danger bg-opacity-10">
                            <tr>
                                <th>Karyawan</th>
                                <th>Transportasi</th>
                                <th>Perjalanan</th>
                                <th>Rute</th>
                                <th>Deadline</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingDeadlines as $detail)
                            @php
                                $deadlineDate = $detail->jenis_perjalanan == 'pergi' 
                                    ? $detail->cuti->tanggal_mulai 
                                    : $detail->cuti->tanggal_selesai;
                                $daysUntilDeadline = (int)now()->diffInDays(Carbon\Carbon::parse($deadlineDate), false);
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-light text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                            <span>{{ strtoupper(substr($detail->cuti->karyawan->nama, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $detail->cuti->karyawan->nama }}</h6>
                                            <small class="text-muted">{{ $detail->cuti->karyawan->departemen }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $detail->transportasi->jenis }}</td>
                                <td>
                                    @if($detail->jenis_perjalanan == 'pergi')
                                        <span class="badge bg-primary rounded-pill px-3">Tiket Pergi</span>
                                    @else
                                        <span class="badge bg-success rounded-pill px-3">Tiket Kembali</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span>{{ $detail->rute_asal }}</span>
                                        <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                        <span>{{ $detail->rute_tujuan }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="far fa-calendar-alt me-2 text-muted"></i>
                                        <span class="fw-medium me-2">{{ Carbon\Carbon::parse($deadlineDate)->format('d/m/Y') }}</span>
                                        @if($daysUntilDeadline <= 3 && $daysUntilDeadline >= 0)
                                            <span class="badge bg-danger rounded-pill">{{ $daysUntilDeadline }} hari lagi!</span>
                                        @elseif($daysUntilDeadline <= 5 && $daysUntilDeadline >= 0)
                                            <span class="badge bg-warning text-dark rounded-pill">{{ $daysUntilDeadline }} hari lagi</span>
                                        @elseif($daysUntilDeadline < 0)
                                            <span class="badge bg-dark rounded-pill">Terlewat {{ abs($daysUntilDeadline) }} hari</span>
                                        @else
                                            <span class="badge bg-info rounded-pill">{{ $daysUntilDeadline }} hari lagi</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="{{ url('/transportasi-details/' . $detail->id . '/edit') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                                        <i class="fas fa-edit me-1"></i> Proses
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <div class="row">
        <!-- Main content column -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary fw-bold">
                        <i class="fas fa-ticket-alt me-2"></i>Pengelolaan Tiket Transportasi
                    </h5>
                    <span class="badge bg-primary rounded-pill px-3">{{ $pendingBookings->count() }} tiket</span>
                </div>
                <div class="card-body p-0">
                    @php
                        // Group bookings by karyawan
                        $bookingsByKaryawan = $pendingBookings->groupBy(function($booking) {
                            return $booking->cuti->karyawan->id;
                        });
                    @endphp

                    @if($bookingsByKaryawan->count() > 0)
                        <div class="accordion accordion-flush" id="transportasiAccordion">
                            @foreach($bookingsByKaryawan as $karyawanId => $bookings)
                                @php
                                    $karyawan = $bookings->first()->cuti->karyawan;
                                    $urgentCount = $bookings->filter(function($booking) {
                                        $deadlineDate = $booking->jenis_perjalanan == 'pergi' 
                                            ? $booking->cuti->tanggal_mulai 
                                            : $booking->cuti->tanggal_selesai;
                                        $daysUntilDeadline = (int)now()->diffInDays(Carbon\Carbon::parse($deadlineDate), false);
                                        return $daysUntilDeadline <= 7 && $daysUntilDeadline >= 0;
                                    })->count();
                                @endphp
                                <div class="accordion-item border-0 border-bottom">
                                    <h2 class="accordion-header" id="heading{{ $karyawanId }}">
                                        <button class="accordion-button px-4 py-3 {{ $urgentCount > 0 ? '' : 'collapsed' }}" type="button" 
                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $karyawanId }}" 
                                                aria-expanded="{{ $urgentCount > 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $karyawanId }}">
                                            <div class="d-flex justify-content-between align-items-center w-100">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                                        <span>{{ strtoupper(substr($karyawan->nama, 0, 1)) }}</span>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">{{ $karyawan->nama }}</h6> 
                                                        <small class="text-muted">{{ $karyawan->departemen }}</small>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="badge bg-primary rounded-pill px-3">{{ $bookings->count() }} tiket</span>
                                                    @if($urgentCount > 0)
                                                        <span class="badge bg-danger rounded-pill ms-1 px-3">{{ $urgentCount }} urgent</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $karyawanId }}" class="accordion-collapse collapse {{ $urgentCount > 0 ? 'show' : '' }}" 
                                         aria-labelledby="heading{{ $karyawanId }}" data-bs-parent="#transportasiAccordion">
                                        <div class="accordion-body p-0">
                                            <div class="table-responsive table-scroll-container">
                                                <table class="table table-hover mb-0">
                                                    <thead class="bg-light">
                                                        <tr>
                                                            <th>Transportasi</th>
                                                            <th>Perjalanan</th>
                                                            <th>Rute</th>
                                                            <th>Tanggal</th>
                                                            <th>Status</th>
                                                            <th class="text-end">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($bookings as $booking)
                                                            @php
                                                                $deadlineDate = $booking->jenis_perjalanan == 'pergi' 
                                                                    ? $booking->cuti->tanggal_mulai 
                                                                    : $booking->cuti->tanggal_selesai;
                                                                $daysUntilDeadline = (int)now()->diffInDays(Carbon\Carbon::parse($deadlineDate), false);
                                                                $isUrgent = $daysUntilDeadline <= 7 && $daysUntilDeadline >= 0;
                                                            @endphp
                                                            <tr class="{{ $isUrgent ? 'bg-warning bg-opacity-10' : '' }}">
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        @if(strtolower($booking->transportasi->jenis) == 'pesawat')
                                                                            <i class="fas fa-plane text-primary me-2"></i>
                                                                        @elseif(strtolower($booking->transportasi->jenis) == 'kereta')
                                                                            <i class="fas fa-train text-success me-2"></i>
                                                                        @elseif(strtolower($booking->transportasi->jenis) == 'bus')
                                                                            <i class="fas fa-bus text-info me-2"></i>
                                                                        @else
                                                                            <i class="fas fa-car text-secondary me-2"></i>
                                                                        @endif
                                                                        {{ $booking->transportasi->jenis }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    @if($booking->jenis_perjalanan == 'pergi')
                                                                        <span class="badge bg-primary rounded-pill px-3">Pergi</span>
                                                                    @else
                                                                        <span class="badge bg-success rounded-pill px-3">Kembali</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <span>{{ $booking->rute_asal }}</span>
                                                                        <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                                                        <span>{{ $booking->rute_tujuan }}</span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    @if($booking->waktu_berangkat)
                                                                        <div class="d-flex align-items-center">
                                                                            <i class="far fa-calendar-alt me-2 text-muted"></i>
                                                                            {{ Carbon\Carbon::parse($booking->waktu_berangkat)->format('d/m/Y') }}
                                                                        </div>
                                                                    @else
                                                                        <span class="text-muted"><i class="fas fa-ban me-1"></i> Belum diatur</span>
                                                                    @endif
                                                                    
                                                                    @if($isUrgent)
                                                                        <div class="small text-danger mt-1">
                                                                            <i class="fas fa-exclamation-circle me-1"></i>
                                                                            {{ $daysUntilDeadline == 0 ? 'Hari ini!' : $daysUntilDeadline . ' hari lagi' }}
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($booking->status_pemesanan == 'belum_dipesan')
                                                                        <span class="badge bg-danger rounded-pill px-3">Belum Dipesan</span>
                                                                    @elseif($booking->status_pemesanan == 'proses_pemesanan')
                                                                        <span class="badge bg-warning text-dark rounded-pill px-3">Proses Pemesanan</span>
                                                                    @elseif($booking->status_pemesanan == 'tiket_terbit')
                                                                        <span class="badge bg-success rounded-pill px-3">Tiket Terbit</span>
                                                                    @else
                                                                        <span class="badge bg-secondary rounded-pill px-3">Dibatalkan</span>
                                                                    @endif
                                                                </td>
                                                                <td class="text-end">
                                                                    <a href="{{ url('/transportasi-details/' . $booking->id . '/edit') }}" class="btn btn-primary btn-sm rounded-circle">
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
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <img src="{{ asset('images/empty-tickets.svg') }}" alt="No Tickets" class="mb-3" style="height: 150px;" onerror="this.src='https://cdn-icons-png.flaticon.com/512/7486/7486754.png'; this.style.height='120px';">
                            <h5 class="text-primary">Tidak ada tiket yang perlu diproses</h5>
                            <p class="text-muted">Semua transportasi telah diproses atau belum ada pengajuan cuti dengan transportasi.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Statistics column -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-success fw-bold">
                        <i class="fas fa-chart-pie me-2"></i>Statistik Tiket
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-muted mb-3 fw-medium">Status Tiket</h6>
                        <div class="chart-container">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-info fw-bold">
                        <i class="fas fa-tags me-2"></i>Jenis Transportasi
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="jenisChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-purple fw-bold">
                        <i class="fas fa-money-bill-wave me-2"></i>Biaya Transportasi Bulanan
                    </h5>
                </div>
                <div class="card-body">
                    <div id="biayaChart-container" class="chart-container">
                        <canvas id="biayaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Custom gradients */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
    }
    
    .bg-gradient-danger {
        background: linear-gradient(135deg, #e74a3b 0%, #be2617 100%);
    }
    
    .bg-gradient-warning {
        background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
    }
    
    .text-purple {
        color: #6f42c1;
    }
    
    /* Card enhancements */
    .rounded-4 {
        border-radius: 0.75rem !important;
    }
    
    .icon-box {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Table improvements */
    .table {
        font-size: 0.9rem;
    }
    
    .avatar {
        font-weight: bold;
    }
    
    /* Table scrolling */
    .table-scroll-container {
        max-height: 300px;
        overflow-y: auto;
        scrollbar-width: thin;
    }
    
    .table-scroll-container::-webkit-scrollbar {
        width: 6px;
    }
    
    .table-scroll-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .table-scroll-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    .table-scroll-container::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    .table-scroll-container thead {
        position: sticky;
        top: 0;
        z-index: 2;
    }
    
    /* Accordion styling */
    .accordion-button {
        box-shadow: none !important;
    }
    
    .accordion-button:not(.collapsed) {
        color: #4e73df;
        background-color: rgba(78, 115, 223, 0.05);
    }
    
    .accordion-button:focus {
        border-color: transparent;
    }
    
    /* Badge styling */
    .badge {
        font-weight: 500;
    }
    
    /* Animation for alerts */
    .alert-icon {
        width: 46px;
        height: 46px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Chart container heights */
    .chart-container {
        height: 250px;
        position: relative;
    }
    
    #biayaChart-container {
        height: 300px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set chart defaults for consistent styling
    Chart.defaults.font.family = "'Inter', 'Helvetica', 'Arial', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.plugins.legend.position = 'bottom';
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.boxWidth = 6;
    Chart.defaults.plugins.tooltip.padding = 10;
    Chart.defaults.plugins.tooltip.boxPadding = 6;
    Chart.defaults.plugins.tooltip.cornerRadius = 8;
    Chart.defaults.elements.arc.borderWidth = 0;
    
    // Status chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Belum Dipesan', 'Proses Pemesanan', 'Tiket Terbit', 'Dibatalkan'],
            datasets: [{
                data: [
                    {{ \App\Models\TransportasiDetail::where('status_pemesanan', 'belum_dipesan')->count() }},
                    {{ \App\Models\TransportasiDetail::where('status_pemesanan', 'proses_pemesanan')->count() }},
                    {{ \App\Models\TransportasiDetail::where('status_pemesanan', 'tiket_terbit')->count() }},
                    {{ \App\Models\TransportasiDetail::where('status_pemesanan', 'dibatalkan')->count() }}
                ],
                backgroundColor: ['#e74a3b', '#f6c23e', '#1cc88a', '#858796'],
                hoverOffset: 4,
                borderRadius: 4
            }]
        },
        options: {
            cutout: '70%',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20
                    }
                }
            }
        }
    });
    
    // Jenis chart
    const jenisCtx = document.getElementById('jenisChart').getContext('2d');
    new Chart(jenisCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode(\App\Models\Transportasi::pluck('jenis')->toArray()) !!},
            datasets: [{
                data: [
                    @foreach(\App\Models\Transportasi::all() as $transportasi)
                        {{ \App\Models\TransportasiDetail::where('transportasi_id', $transportasi->id)->count() }},
                    @endforeach
                ],
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
    
    // Biaya chart
    const biayaCtx = document.getElementById('biayaChart').getContext('2d');
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agt", "Sep", "Okt", "Nov", "Des"];
    const biayaData = Array(12).fill(0);
    
    // Fill in the data
    @foreach($biayaTransportasi as $item)
        biayaData[{{ $item->bulan - 1 }}] = {{ $item->total }};
    @endforeach
    
    new Chart(biayaCtx, {
        type: 'bar',
        data: {
            labels: monthNames,
            datasets: [{
                label: 'Total Biaya (Rp)',
                data: biayaData,
                backgroundColor: 'rgba(111, 66, 193, 0.5)',
                borderColor: 'rgba(111, 66, 193, 1)',
                borderWidth: 1,
                borderRadius: 4,
                barThickness: 'flex',
                maxBarThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let value = context.raw;
                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000).toFixed(1) + ' jt';
                            } else if (value >= 1000) {
                                return 'Rp ' + (value / 1000).toFixed(0) + ' rb';
                            }
                            return 'Rp ' + value;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush