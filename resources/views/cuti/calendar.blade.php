@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <!-- Header with Stats -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="header-section animate__animated animate__fadeIn">
                    <h2 class="fw-bold text-dark mb-1">Monitoring Cuti</h2>
                    <p class="text-muted mb-0">Pantau jadwal cuti karyawan dalam tampilan kalender</p>
                </div>

                <div class="mt-3 mt-md-0 animate__animated animate__fadeIn animate__delay-1s">
                    <div class="d-flex flex-wrap gap-2">
                        <div class="btn-group shadow-sm rounded-pill overflow-hidden">
                            <button id="viewMonthBtn" class="btn btn-white active">
                                <i class="fas fa-calendar-alt me-2"></i>Bulanan
                            </button>
                            <button id="viewWeekBtn" class="btn btn-white">
                                <i class="fas fa-calendar-week me-2"></i>Mingguan
                            </button>
                            <button id="viewDayBtn" class="btn btn-white">
                                <i class="fas fa-calendar-day me-2"></i>Harian
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend Cards & Calendar -->
    <div class="row g-4">
        <!-- Stats and Legend Cards -->
        <div class="col-md-4 col-lg-3">
            <div class="row g-4">
                <!-- Quick Stats -->
                <div class="col-12">
                    <div class="card shadow-sm border-0 h-100 animate__animated animate__fadeInUp">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title fw-bold mb-0">Statistik</h5>
                                <span class="badge bg-primary bg-opacity-10 text-primary small rounded-pill px-3">
                                    Bulan Ini
                                </span>
                            </div>

                            <div class="stats-grid">
                                <div class="stats-item p-3 rounded-3 bg-primary bg-opacity-10 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="small text-muted mb-0">Total Cuti</p>
                                            <h3 class="fw-bold text-primary mb-0" id="totalCutiCount">0</h3>
                                        </div>
                                        <div class="stats-icon">
                                            <i class="fas fa-calendar-alt fs-3 text-primary opacity-25"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex gap-3">
                                    <div class="stats-item p-3 rounded-3 bg-info bg-opacity-10 flex-grow-1">
                                        <p class="small text-muted mb-0">Dengan Transportasi</p>
                                        <div class="d-flex align-items-center">
                                            <h4 class="fw-bold text-info mb-0" id="withTransportCount">0</h4>
                                            <i class="fas fa-plane ms-2 text-info"></i>
                                        </div>
                                    </div>

                                    <div class="stats-item p-3 rounded-3 bg-pastel-secondary flex-grow-1">
                                        <p class="small text-muted mb-0">Tanpa Transportasi</p>
                                        <div class="d-flex align-items-center">
                                            <h4 class="fw-bold mb-0" id="withoutTransportCount">0</h4>
                                            <i class="fas fa-walking ms-2 text-secondary"></i> <!-- Re-added text-secondary for icon -->
                                        </div>
                                    </div>
                                </div>

                                <div class="stats-item p-3 rounded-3 bg-pastel-purple mt-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="small text-muted mb-0">Memo Kompensasi</p>
                                            <h4 class="fw-bold text-purple mb-0" id="withKompensasiCount">0">
                                        </div>
                                        <div class="stats-icon">
                                            <i class="fas fa-file-alt fs-4 text-purple opacity-75"></i> <!-- Adjusted opacity -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Legend -->
                <div class="col-12">
                    <div class="card shadow-sm border-0 h-100 animate__animated animate__fadeInUp animate__delay-1s">
                        <div class="card-body">
                            <h5 class="card-title fw-bold mb-3">Keterangan Warna</h5>
                            <div class="legend-container">
                                <div class="legend-item d-flex align-items-center p-2 rounded-3 mb-2 hover-effect">
                                    <div class="legend-color bg-purple rounded-circle me-3"></div>
                                    <div>
                                        <p class="fw-medium mb-0">Memo Kompensasi</p>
                                        <small class="text-muted">Cuti yang memerlukan memo kompensasi</small>
                                    </div>
                                </div>

                                <div class="legend-item d-flex align-items-center p-2 rounded-3 mb-2 hover-effect">
                                    <div class="legend-color bg-info rounded-circle me-3"></div>
                                    <div>
                                        <p class="fw-medium mb-0">Dengan Transportasi</p>
                                        <small class="text-muted">Cuti yang menggunakan fasilitas transportasi</small>
                                    </div>
                                </div>

                                <div class="legend-item d-flex align-items-center p-2 rounded-3 hover-effect">
                                    <div class="legend-color bg-secondary rounded-circle me-3"></div>
                                    <div>
                                        <p class="fw-medium mb-0">Tanpa Transportasi</p>
                                        <small class="text-muted">Cuti tanpa fasilitas transportasi & memo</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's View -->
                <div class="col-12 d-none d-md-block">
                    <div class="card shadow-sm border-0 h-100 animate__animated animate__fadeInUp animate__delay-2s">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title fw-bold mb-0">Cuti Hari Ini</h5>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3" id="todayDate">
                                    <i class="fas fa-calendar-day me-1"></i> <span id="todayDateText"></span>
                                </span>
                            </div>

                            <div id="todayList" class="today-list">
                                <div class="text-center py-4 empty-state">
                                    <div class="empty-icon icon-container mb-3">
                                        <i class="fas fa-calendar-check fs-1 text-muted opacity-25"></i>
                                    </div>
                                    <p class="text-muted mb-0">Tidak ada cuti hari ini</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar -->
        <div class="col-md-8 col-lg-9">
            <div class="card shadow-sm border-0 animate__animated animate__fadeInUp">
                <div class="card-body p-0 p-md-3">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Detail Modal -->
<div class="modal fade" id="eventDetailModal" tabindex="-1" aria-labelledby="eventDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="employee-info mb-4">
                    <div class="d-flex align-items-center">
                        <div class="employee-avatar rounded-circle text-center text-white d-flex align-items-center justify-content-center me-3" style="width: 64px; height: 64px;">
                            <span id="modal-avatar-initial" class="fs-3 fw-bold"></span>
                        </div>
                        <div>
                            <h4 id="modal-karyawan" class="fw-bold mb-0"></h4>
                            <p id="modal-departemen" class="text-muted mb-1"></p>
                            <div id="modal-status-tiket" class="mt-1"></div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Leave Details -->
                    <div class="col-md-6">
                        <div class="detail-card p-3 rounded-3 bg-light mb-3">
                            <h6 class="text-uppercase small fw-bold text-muted mb-3">Informasi Cuti</h6>

                            <div class="detail-item mb-3">
                                <p class="text-muted mb-1 small">Jenis Cuti</p>
                                <p id="modal-jenis-cuti" class="fw-medium fs-5 mb-0"></p>
                            </div>

                            <div class="detail-item mb-3">
                                <p class="text-muted mb-1 small">Detail Jenis Cuti</p>
                                <div id="modal-jenis-cuti-details" class="badge-container"></div>
                            </div>

                            <div class="detail-item">
                                <p class="text-muted mb-1 small">Lama Cuti</p>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-week text-primary me-2"></i>
                                    <p id="modal-lama-cuti" class="fw-medium mb-0"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Date Details -->
                    <div class="col-md-6">
                        <div class="detail-card p-3 rounded-3 bg-light mb-3">
                            <h6 class="text-uppercase small fw-bold text-muted mb-3">Tanggal Cuti</h6>

                            <div class="date-range d-flex flex-column">
                                <div class="date-item d-flex mb-2">
                                    <div class="date-icon bg-primary bg-opacity-10 rounded text-primary p-2 me-3 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="date-content">
                                        <p class="text-muted small mb-0">Mulai</p>
                                        <p id="modal-tanggal-mulai" class="fw-medium mb-0"></p>
                                    </div>
                                </div>

                                <div class="date-separator text-center">
                                    <div class="separator-line bg-primary bg-opacity-25"></div>
                                </div>

                                <div class="date-item d-flex mt-2">
                                    <div class="date-icon bg-primary bg-opacity-10 rounded text-primary p-2 me-3 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="date-content">
                                        <p class="text-muted small mb-0">Selesai</p>
                                        <p id="modal-tanggal-selesai" class="fw-medium mb-0"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="col-12">
                        <div class="detail-card p-3 rounded-3 bg-light">
                            <h6 class="text-uppercase small fw-bold text-muted mb-3">Keterangan</h6>
                            <div class="p-3 bg-white rounded-3 border">
                                <p id="modal-keterangan" class="mb-0"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-2">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="modal-detail-link" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-external-link-alt me-1"></i> Lihat Detail
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/main.min.css' rel='stylesheet' />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<style>
    :root {
        /* Custom Color Variables */
        --primary-color: #4361ee;
        --primary-hover: #3a56e3;
        --primary-color-rgb: 67, 97, 238;
        --success-color: #2ecc71;
        --warning-color: #f39c12;
        --info-color: #3498db; /* Blue */
        --danger-color: #e74c3c;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
        --white-color: #ffffff;
        --pink-color: #e91e63; /* Pink */
        --teal-color: #1abc9c; /* Teal */
        --purple-color: #6f42c1; /* Bootstrap Purple */
        --secondary-color: #6c757d; /* Bootstrap Secondary */

        /* Pastel Colors */
        --pastel-purple: #e6e0f8; /* Lighter purple */
        --pastel-secondary: #f1f3f5; /* Lighter gray */

        /* Border radiuses */
        --border-radius-sm: 0.25rem;
        --border-radius: 0.5rem;
        --border-radius-lg: 0.75rem;
        --border-radius-xl: 1rem;

        /* Shadows */
        --shadow-sm: 0 2px 5px rgba(0,0,0,0.05);
        --shadow: 0 4px 12px rgba(0,0,0,0.08);
        --shadow-lg: 0 8px 24px rgba(0,0,0,0.12);
    }

    /* Add utility classes for new colors */
    .bg-pink { background-color: var(--pink-color) !important; }
    .text-pink { color: var(--pink-color) !important; }
    .bg-pink-opacity-10 { background-color: rgba(233, 30, 99, 0.1) !important; }

    .bg-blue { background-color: var(--info-color) !important; } /* Using info color for blue */
    .text-blue { color: var(--info-color) !important; }
    .bg-blue-opacity-10 { background-color: rgba(52, 152, 219, 0.1) !important; }

    .bg-teal { background-color: var(--teal-color) !important; }
    .text-teal { color: var(--teal-color) !important; }
    .bg-teal-opacity-10 { background-color: rgba(26, 188, 156, 0.1) !important; }

    .bg-purple { background-color: var(--purple-color) !important; }
    .text-purple { color: var(--purple-color) !important; }
    .bg-purple-opacity-10 { background-color: rgba(111, 66, 193, 0.1) !important; }

    .bg-pastel-purple { background-color: var(--pastel-purple) !important; }
    .bg-pastel-secondary { background-color: var(--pastel-secondary) !important; }

    /* Bootstrap secondary is usually available, but define for consistency if needed */
    .bg-secondary { background-color: var(--secondary-color) !important; }
    .text-secondary { color: var(--secondary-color) !important; }
    .bg-secondary-opacity-10 { background-color: rgba(108, 117, 125, 0.1) !important; }

    /* General Styles */
    body {
        color: #334155;
        background-color: #f8fafc;
    }

    h1, h2, h3, h4, h5, h6 {
        color: #1e293b;
    }

    .card {
        border-radius: var(--border-radius);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    /* Button Styles */
    .btn {
        font-weight: 500;
        border-radius: var(--border-radius);
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
    }

    .btn-white {
        background-color: #fff;
        border-color: #e5e7eb;
        color: #4b5563;
    }

    .btn-white:hover, .btn-white:focus {
        background-color: #f9fafb;
        border-color: #d1d5db;
        color: #111827;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .btn-white.active {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: #fff;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-primary:hover {
        background-color: var(--primary-hover);
        border-color: var(--primary-hover);
    }

    .rounded-pill {
        border-radius: 50rem !important;
    }

    /* Stats Styling */
    .stats-grid {
        width: 100%;
    }

    .stats-item {
        transition: all 0.3s ease;
    }

    .stats-item:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-sm);
    }

    .stats-icon {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Legend Styling */
    .legend-container {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .legend-color {
        width: 20px;
        height: 20px;
        min-width: 20px;
    }

    .legend-item {
        transition: all 0.2s ease;
    }

    .hover-effect:hover {
        background-color: #f8f9fa;
    }

    /* Today's list styling */
    .today-list {
        max-height: 300px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: rgba(0,0,0,0.2) rgba(0,0,0,0.05);
    }

    .today-list::-webkit-scrollbar {
        width: 6px;
    }

    .today-list::-webkit-scrollbar-track {
        background: rgba(0,0,0,0.05);
        border-radius: 10px;
    }

    .today-list::-webkit-scrollbar-thumb {
        background: rgba(0,0,0,0.2);
        border-radius: 10px;
    }

    .today-list-item {
        padding: 10px;
        border-radius: var(--border-radius);
        border: 1px solid #f0f0f0;
        margin-bottom: 8px;
        transition: all 0.2s ease;
    }

    .today-list-item:hover {
        background-color: #f8f9fa;
        transform: translateX(3px);
    }

    /* Empty state styling */
    .empty-state {
        opacity: 0.7;
        transition: opacity 0.3s ease;
    }

    .empty-state:hover {
        opacity: 1;
    }

    .empty-icon {
        animation: pulse 2s infinite;
    }

    /* Date separator styling */
    .date-separator {
        padding: 10px 0;
        position: relative;
    }

    .separator-line {
        width: 2px;
        height: 20px;
        margin: 0 auto;
    }

    /* Calendar Event Styling */
    .fc .fc-event {
        cursor: pointer;
        border-radius: var(--border-radius);
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        border: none;
        padding: 3px 5px;
        margin: 2px 0;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .fc .fc-event:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .fc .fc-event-title {
        font-weight: 600;
        padding: 2px 0;
        font-size: 0.85rem;
    }

    .fc .fc-event-time {
        font-weight: normal;
        opacity: 0.8;
        font-size: 0.75rem;
    }

    /* Make the calendar container responsive */
    #calendar {
        min-height: 700px;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }

    /* Custom styling for the calendar toolbar */
    .fc .fc-toolbar {
        flex-wrap: wrap;
        margin-bottom: 1.5rem !important;
    }

    .fc .fc-toolbar h2 {
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1.2;
    }

    /* Custom styling for the calendar buttons */
    .fc .fc-button-primary {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        box-shadow: none !important;
        text-transform: capitalize;
        font-weight: 500;
        border-radius: 0 !important; /* Remove rounded corners */
        transition: all 0.2s ease !important;
    }

    .fc .fc-button-primary:hover {
        background-color: var(--primary-hover) !important;
        border-color: var(--primary-hover) !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important;
    }

    .fc .fc-button-primary:disabled {
        background-color: rgba(var(--primary-color-rgb), 0.65) !important;
        border-color: rgba(var(--primary-color-rgb), 0.65) !important;
    }

    .fc-theme-standard td, .fc-theme-standard th {
        border-color: #e5e7eb;
    }

    .fc-theme-standard .fc-scrollgrid {
        border-color: #e5e7eb;
    }

    .fc-col-header-cell {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .fc-day-today {
        background-color: rgba(var(--primary-color-rgb), 0.05) !important;
    }

    .fc-daygrid-day-number {
        font-weight: 500;
        color: #4b5563;
    }

    /* Today's date highlight */
    .fc-day-today .fc-daygrid-day-number {
        color: var(--primary-color);
        font-weight: 600;
    }

    /* Modal customization */
    .modal-content {
        border: none;
        overflow: hidden;
    }

    .badge-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .detail-card {
        transition: all 0.3s ease;
    }

    .detail-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
    }

    /* Employee Avatar Color Classes */
    .bg-letter-a { background-color: #4f46e5; }
    .bg-letter-b { background-color: #06b6d4; }
    .bg-letter-c { background-color: #10b981; }
    .bg-letter-d { background-color: #f59e0b; }
    .bg-letter-e { background-color: #ef4444; }
    .bg-letter-f { background-color: #8b5cf6; }
    .bg-letter-g { background-color: #ec4899; }
    .bg-letter-h { background-color: #84cc16; }
    .bg-letter-i { background-color: #14b8a6; }
    .bg-letter-j { background-color: #f97316; }
    .bg-letter-k { background-color: #6366f1; }
    .bg-letter-l { background-color: #0ea5e9; }
    .bg-letter-m { background-color: #22c55e; }
    .bg-letter-n { background-color: #eab308; }
    .bg-letter-o { background-color: #ef4444; }
    .bg-letter-p { background-color: #a855f7; }
    .bg-letter-q { background-color: #d946ef; }
    .bg-letter-r { background-color: #84cc16; }
    .bg-letter-s { background-color: #14b8a6; }
    .bg-letter-t { background-color: #f97316; }
    .bg-letter-u { background-color: #6366f1; }
    .bg-letter-v { background-color: #0ea5e9; }
    .bg-letter-w { background-color: #22c55e; }
    .bg-letter-x { background-color: #eab308; }
    .bg-letter-y { background-color: #ef4444; }
    .bg-letter-z { background-color: #8b5cf6; }

    /* Custom animations */
    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 0.5;
        }
        50% {
            transform: scale(1.05);
            opacity: 0.8;
        }
        100% {
            transform: scale(1);
            opacity: 0.5;
        }
    }

    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .fc .fc-toolbar.fc-header-toolbar {
            margin-bottom: 1rem;
        }

        .fc-toolbar-chunk {
            display: flex;
            justify-content: center;
            margin-bottom: 0.5rem;
        }

        .fc .fc-toolbar-title {
            font-size: 1.2rem;
        }
    }

    @media (max-width: 767.98px) {
        #calendar {
            min-height: 500px;
        }

        .fc .fc-toolbar-title {
            font-size: 1rem;
        }

        .card-body {
            padding: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/locales/id.global.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Format tanggal hari ini
        const today = new Date();
        const options = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
        document.getElementById('todayDateText').textContent = today.toLocaleDateString('id-ID', options);

        // Data event dari controller
        const events = @json($calendarEvents);

        // Total memo kompensasi langsung dari server
        const totalMemoKompensasi = {{ $totalMemoKompensasi ?? 0 }};

        // Counter untuk statistik
        let totalCuti = 0;
        let withTransport = 0;
        let withoutTransport = 0;
        let withKompensasi = 0;

        // Hitung statistik dan siapkan data untuk hari ini
        today.setHours(0, 0, 0, 0);
        const todayEvents = [];

        // Dapatkan bulan dan tahun saat ini untuk filter "bulan ini"
        const currentMonth = today.getMonth();
        const currentYear = today.getFullYear();

        events.forEach(event => {
            const eventStart = new Date(event.start);
            const eventEnd = new Date(event.end);
            eventStart.setHours(0, 0, 0, 0);
            eventEnd.setHours(0, 0, 0, 0);

            // Hanya hitung cuti yang tanggal mulainya di bulan ini
            const eventMonth = eventStart.getMonth();
            const eventYear = eventStart.getFullYear();

            // Filter hanya untuk cuti pada bulan dan tahun saat ini
            if (eventMonth === currentMonth && eventYear === currentYear) {
                // Hitung total
                totalCuti++;

                // Hitung berdasarkan memo kompensasi dan transportasi
                if (event.extendedProps.hasKompensasi) {
                    withKompensasi++;
                } else if (event.extendedProps.hasTransportation) {
                    withTransport++;
                } else {
                    withoutTransport++;
                }
            }

            // Cek apakah event untuk hari ini
            if (today >= eventStart && today < eventEnd) {
                todayEvents.push(event);
            }
        });

        // Update statistik di UI dengan animasi counter
        animateCounter('totalCutiCount', 0, totalCuti);
        animateCounter('withTransportCount', 0, withTransport);
        animateCounter('withoutTransportCount', 0, withoutTransport);
        animateCounter('withKompensasiCount', 0, withKompensasi);

        // Update daftar cuti hari ini
        updateTodayList(todayEvents);

        // Inisialisasi Kalender
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'multiMonthYear,dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            views: {
                dayGridMonth: {
                    dayMaxEventRows: 3,
                    dayMaxEvents: true,
                }
            },
            locale: 'id',
            events: events,
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            },
            dayMaxEvents: true,
            eventClick: function(info) {
                showEventDetails(info.event);
            },
            eventDidMount: function(info) {
                // Menambahkan tooltip ke event
                const event = info.event;
                const tooltip = new bootstrap.Tooltip(info.el, {
                    title: `${event.title} - ${event.extendedProps.lamaCuti}`,
                    placement: 'top',
                    trigger: 'hover',
                    container: 'body'
                });
            },
            eventContent: function(arg) {
                let italicEl = document.createElement('i');

                if (arg.event.extendedProps.ticketStatus === 'Tiket Tersedia') {
                    italicEl.className = 'fas fa-check-circle me-1 text-white';
                } else {
                    italicEl.className = 'fas fa-clock me-1 text-white';
                }

                let eventTitle = document.createElement('span');
                eventTitle.innerHTML = arg.event.title;

                let arrayOfDomNodes = [ italicEl, eventTitle ];
                return { domNodes: arrayOfDomNodes }
            },
            datesSet: function() {
                // Efek animasi ketika mengubah bulan
                document.querySelectorAll('.fc-event').forEach(event => {
                    event.classList.add('animate__animated', 'animate__fadeInUp');
                    event.addEventListener('animationend', () => {
                        event.classList.remove('animate__animated', 'animate__fadeInUp');
                    });
                });
            }
        });

        calendar.render();

        // Function untuk animasi counter
        function animateCounter(elementId, start, end) {
            const duration = 1000;
            const element = document.getElementById(elementId);
            const startTime = performance.now();

            function updateCounter(currentTime) {
                const elapsedTime = currentTime - startTime;
                const progress = Math.min(elapsedTime / duration, 1);

                const value = Math.floor(start + progress * (end - start));
                element.textContent = value;

                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                } else {
                    element.textContent = end;
                }
            }

            requestAnimationFrame(updateCounter);
        }

        // Function untuk mengupdate daftar cuti hari ini
        function updateTodayList(todayEvents) {
            const todayListElement = document.getElementById('todayList');

            if (todayEvents.length === 0) {
                todayListElement.innerHTML = `
                    <div class="text-center py-4 empty-state">
                        <div class="empty-icon icon-container mb-3">
                            <i class="fas fa-calendar-check fs-1 text-muted opacity-25"></i>
                        </div>
                        <p class="text-muted mb-0">Tidak ada cuti hari ini</p>
                    </div>
                `;
                return;
            }

            let listHTML = '';

            todayEvents.forEach((event, index) => {
                const hasTicket = event.extendedProps.ticketStatus === 'Tiket Tersedia';
                const nameInitial = event.title.charAt(0).toUpperCase();
                const avatarColorClass = `bg-letter-${nameInitial.toLowerCase()}`;
                const animationDelay = index * 0.1;

                listHTML += `
                    <div class="today-list-item animate__animated animate__fadeInRight" style="animation-delay: ${animationDelay}s">
                        <div class="d-flex align-items-center">
                            <div class="employee-avatar rounded-circle text-center text-white ${avatarColorClass} d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                <span class="small fw-bold">${nameInitial}</span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-0 fw-medium">${event.title}</p>
                                <div class="d-flex align-items-center">
                                    <small class="text-muted me-2">${event.extendedProps.departemen || 'Tidak ada departemen'}</small>
                                </div>
                            </div>
                            <span class="badge ${hasTicket ? 'bg-success' : 'bg-warning'} rounded-pill ms-1">
                                <i class="fas ${hasTicket ? 'fa-check' : 'fa-clock'} fa-xs"></i>
                            </span>
                        </div>
                    </div>
                `;
            });

            todayListElement.innerHTML = listHTML;

            // Tambahkan event click untuk setiap item
            setTimeout(() => {
                document.querySelectorAll('.today-list-item').forEach((item, index) => {
                    item.addEventListener('click', () => {
                        showEventDetails(todayEvents[index]);
                    });
                });
            }, 100);
        }

        // Function untuk menampilkan detail event di modal
        function showEventDetails(event) {
            // Formatting date to display
            const startDate = new Date(event.start);
            // End date is exclusive, so subtract one day
            const endDate = new Date(event.end);
            endDate.setDate(endDate.getDate() - 1);

            const formatDate = (date) => {
                return date.toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            };

            // Get employee name initial
            const nameInitial = event.title.charAt(0).toUpperCase();
            const avatarColorClass = `bg-letter-${nameInitial.toLowerCase()}`;

            // Set initial
            const avatarElement = document.getElementById('modal-avatar-initial');
            avatarElement.textContent = nameInitial;

            // Set avatar background color class based on initial
            const avatarDiv = document.querySelector('.employee-avatar');
            avatarDiv.classList.remove(...[...avatarDiv.classList].filter(c => c.startsWith('bg-letter-')));
            avatarDiv.classList.add(avatarColorClass);

            // Set employee name and department
            document.getElementById('modal-karyawan').textContent = event.title;
            document.getElementById('modal-departemen').textContent = event.extendedProps.departemen || 'Tidak tersedia';

            // Set leave dates
            document.getElementById('modal-tanggal-mulai').textContent = formatDate(startDate);
            document.getElementById('modal-tanggal-selesai').textContent = formatDate(endDate);

            // Set leave details
            document.getElementById('modal-jenis-cuti').textContent = event.extendedProps.jenisCuti;
            document.getElementById('modal-lama-cuti').textContent = event.extendedProps.lamaCuti;

            // Set leave description/reason
            document.getElementById('modal-keterangan').textContent = event.extendedProps.description || 'Tidak ada keterangan';

            // Set jenis cuti details
            let jenisCutiDetailsHTML = '';
            if (event.extendedProps.jenisCutiDetails && event.extendedProps.jenisCutiDetails.length > 0) {
                event.extendedProps.jenisCutiDetails.forEach(detail => {
                    jenisCutiDetailsHTML += `<div class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill mb-1 px-3 py-2">
                        ${detail.nama} <span class="ms-1">(${detail.jumlah_hari} hari)</span>
                    </div> `;
                });
            }
            document.getElementById('modal-jenis-cuti-details').innerHTML = jenisCutiDetailsHTML || 'Tidak ada detail';

            // Set ticket status
            const hasTicket = event.extendedProps.ticketStatus === 'Tiket Tersedia';
            const ticketIcon = hasTicket ? 'fa-check-circle' : 'fa-clock';
            const ticketClass = hasTicket ? 'bg-success' : 'bg-warning';

            document.getElementById('modal-status-tiket').innerHTML = `
                <span class="badge ${ticketClass} rounded-pill py-2 px-3">
                    <i class="fas ${ticketIcon} me-1"></i> ${event.extendedProps.ticketStatus}
                </span>
            `;

            // Set link to detail page
            const detailLink = document.getElementById('modal-detail-link');
            detailLink.href = `/cutis/${event.id}`;

            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
            modal.show();
        }

        // Button event listeners to change view
        document.getElementById('viewMonthBtn').addEventListener('click', function() {
            calendar.changeView('dayGridMonth');
            toggleActiveViewButton(this);
        });

        document.getElementById('viewWeekBtn').addEventListener('click', function() {
            calendar.changeView('timeGridWeek');
            toggleActiveViewButton(this);
        });

        document.getElementById('viewDayBtn').addEventListener('click', function() {
            calendar.changeView('timeGridDay');
            toggleActiveViewButton(this);
        });

        function toggleActiveViewButton(activeButton) {
            // Remove active class from all buttons
            document.querySelectorAll('.btn-white').forEach(btn => {
                btn.classList.remove('active');
            });
            // Add active class to clicked button
            activeButton.classList.add('active');
        }
    });
</script>
@endpush