@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-md-8">
            <h4 class="fw-bold"><i class="fas fa-list me-2 text-primary"></i>Data Jenis Cuti</h4>
            <p class="text-muted mb-0">Kelola daftar jenis cuti yang tersedia dalam sistem</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('jenis-cutis.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus me-2"></i> Tambah Jenis Cuti
            </a>
        </div>
    </div>

    <!-- Search and Filter Bar -->
    <div class="card mb-4 border-0 shadow-sm rounded-4">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="searchInput" class="form-control border-0 bg-light" placeholder="Cari jenis cuti..." aria-label="Search">
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary view-toggle active" data-view="table">
                            <i class="fas fa-table me-1"></i> Tabel
                        </button>
                        <button type="button" class="btn btn-outline-secondary view-toggle" data-view="cards">
                            <i class="fas fa-th-large me-1"></i> Kartu
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table View -->
    <div class="card border-0 shadow-sm rounded-4" id="tableView">
        <div class="card-header bg-white p-4 border-0">
            <h5 class="card-title mb-0"><i class="fas fa-table me-2 text-primary"></i>Daftar Jenis Cuti</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="jenisCutiTable">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Nama Jenis</th>
                            <th class="px-4 py-3">Jatah Hari</th>
                            <th class="px-4 py-3">POH</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jenisCutis as $jenisCuti)
                            <tr data-name="{{ strtolower($jenisCuti->nama_jenis) }}">
                                <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle p-2 me-3 
                                            @if(strpos(strtolower($jenisCuti->nama_jenis), 'tahunan') !== false)
                                                bg-success bg-opacity-10 text-success
                                            @elseif(strpos(strtolower($jenisCuti->nama_jenis), 'sakit') !== false)
                                                bg-danger bg-opacity-10 text-danger
                                            @elseif(strpos(strtolower($jenisCuti->nama_jenis), 'melahirkan') !== false || strpos(strtolower($jenisCuti->nama_jenis), 'bersalin') !== false)
                                                bg-pink bg-opacity-10 text-pink
                                            @elseif(strpos(strtolower($jenisCuti->nama_jenis), 'penting') !== false)
                                                bg-warning bg-opacity-10 text-warning
                                            @else
                                                bg-info bg-opacity-10 text-info
                                            @endif
                                        ">
                                            @if(strpos(strtolower($jenisCuti->nama_jenis), 'tahunan') !== false)
                                                <i class="fas fa-calendar-check"></i>
                                            @elseif(strpos(strtolower($jenisCuti->nama_jenis), 'sakit') !== false)
                                                <i class="fas fa-procedures"></i>
                                            @elseif(strpos(strtolower($jenisCuti->nama_jenis), 'melahirkan') !== false || strpos(strtolower($jenisCuti->nama_jenis), 'bersalin') !== false)
                                                <i class="fas fa-baby"></i>
                                            @elseif(strpos(strtolower($jenisCuti->nama_jenis), 'penting') !== false)
                                                <i class="fas fa-exclamation-circle"></i>
                                            @else
                                                <i class="fas fa-calendar-alt"></i>
                                            @endif
                                        </div>
                                        <div class="fw-medium">{{ $jenisCuti->nama_jenis }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3">
                                        <i class="fas fa-clock me-1"></i> {{ $jenisCuti->jatah_hari }} hari
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if(isset($jenisCuti->jenis_poh))
                                        @if($jenisCuti->jenis_poh == 'lokal')
                                            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3">
                                                <i class="fas fa-map-marker-alt me-1"></i> Lokal
                                            </span>
                                        @elseif($jenisCuti->jenis_poh == 'luar')
                                            <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning-subtle px-3">
                                                <i class="fas fa-plane me-1"></i> Luar
                                            </span>
                                        @elseif($jenisCuti->jenis_poh == 'lokal_luar')
                                            <span class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info-subtle px-3">
                                                <i class="fas fa-globe-asia me-1"></i> Lokal & Luar
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3">
                                            <i class="fas fa-map-marker-alt me-1"></i> Lokal
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    @if($jenisCuti->keterangan)
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $jenisCuti->keterangan }}">
                                            {{ $jenisCuti->keterangan }}
                                        </span>
                                    @else 
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end action-column">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('jenis-cutis.edit', $jenisCuti->id) }}" class="btn btn-sm btn-outline-primary rounded-pill me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill delete-btn"
                                                data-id="{{ $jenisCuti->id }}"
                                                data-nama="{{ $jenisCuti->nama_jenis }}"
                                                data-jatah="{{ $jenisCuti->jatah_hari }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="6" class="text-center py-5 empty-message">
                                    <div class="py-5">
                                        <img src="https://cdn-icons-png.flaticon.com/512/6598/6598519.png" alt="No data" style="width: 120px; height: 120px; opacity: 0.5">
                                        <h5 class="mt-3 text-muted">Tidak ada data jenis cuti</h5>
                                        <p class="text-muted">Silakan tambahkan jenis cuti baru dengan mengklik tombol "Tambah Jenis Cuti"</p>
                                        <a href="{{ route('jenis-cutis.create') }}" class="btn btn-primary rounded-pill mt-3">
                                            <i class="fas fa-plus me-2"></i> Tambah Jenis Cuti
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cards View (Hidden by default) -->
    <div class="row g-4" id="cardsView" style="display: none;">
        @forelse($jenisCutis as $jenisCuti)
            <div class="col-md-6 col-lg-4" data-name="{{ strtolower($jenisCuti->nama_jenis) }}">
                <div class="card h-100 border-0 shadow-sm rounded-4 hover-shadow">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="rounded-circle p-3 
                                @if(strpos(strtolower($jenisCuti->nama_jenis), 'tahunan') !== false)
                                    bg-success bg-opacity-10 text-success
                                @elseif(strpos(strtolower($jenisCuti->nama_jenis), 'sakit') !== false)
                                    bg-danger bg-opacity-10 text-danger
                                @elseif(strpos(strtolower($jenisCuti->nama_jenis), 'melahirkan') !== false || strpos(strtolower($jenisCuti->nama_jenis), 'bersalin') !== false)
                                    bg-pink bg-opacity-10 text-pink
                                @elseif(strpos(strtolower($jenisCuti->nama_jenis), 'penting') !== false)
                                    bg-warning bg-opacity-10 text-warning
                                @else
                                    bg-info bg-opacity-10 text-info
                                @endif
                            ">
                                @if(strpos(strtolower($jenisCuti->nama_jenis), 'tahunan') !== false)
                                    <i class="fas fa-calendar-check fa-lg"></i>
                                @elseif(strpos(strtolower($jenisCuti->nama_jenis), 'sakit') !== false)
                                    <i class="fas fa-procedures fa-lg"></i>
                                @elseif(strpos(strtolower($jenisCuti->nama_jenis), 'melahirkan') !== false || strpos(strtolower($jenisCuti->nama_jenis), 'bersalin') !== false)
                                    <i class="fas fa-baby fa-lg"></i>
                                @elseif(strpos(strtolower($jenisCuti->nama_jenis), 'penting') !== false)
                                    <i class="fas fa-exclamation-circle fa-lg"></i>
                                @else
                                    <i class="fas fa-calendar-alt fa-lg"></i>
                                @endif
                            </div>
                            <div class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3 py-2">
                                <i class="fas fa-clock me-1"></i> {{ $jenisCuti->jatah_hari }} hari
                            </div>
                        </div>
                        <h5 class="card-title mb-3">{{ $jenisCuti->nama_jenis }}</h5>
                        
                        <div class="mb-3">
                            @if(isset($jenisCuti->jenis_poh))
                                @if($jenisCuti->jenis_poh == 'lokal')
                                    <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3">
                                        <i class="fas fa-map-marker-alt me-1"></i> Lokal
                                    </span>
                                @elseif($jenisCuti->jenis_poh == 'luar')
                                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning-subtle px-3">
                                        <i class="fas fa-plane me-1"></i> Luar
                                    </span>
                                @elseif($jenisCuti->jenis_poh == 'lokal_luar')
                                    <span class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info-subtle px-3">
                                        <i class="fas fa-globe-asia me-1"></i> Lokal & Luar
                                    </span>
                                @endif
                            @else
                                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary border border-primary-subtle px-3">
                                    <i class="fas fa-map-marker-alt me-1"></i> Lokal
                                </span>
                            @endif
                        </div>
                            
                        @if($jenisCuti->keterangan)
                            <p class="card-text text-muted mb-4">{{ $jenisCuti->keterangan }}</p>
                        @else
                            <p class="card-text text-muted mb-4">Tidak ada keterangan tambahan</p>
                        @endif
                        
                        <div class="d-flex justify-content-end mt-3">
                            <a href="{{ route('jenis-cutis.edit', $jenisCuti->id) }}" class="btn btn-sm btn-outline-primary rounded-pill me-2">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill delete-btn"
                                    data-id="{{ $jenisCuti->id }}"
                                    data-nama="{{ $jenisCuti->nama_jenis }}"
                                    data-jatah="{{ $jenisCuti->jatah_hari }}">
                                <i class="fas fa-trash me-1"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-5 text-center">
                        <img src="https://cdn-icons-png.flaticon.com/512/6598/6598519.png" alt="No data" style="width: 150px; height: 150px; opacity: 0.5">
                        <h4 class="mt-4 text-muted">Tidak ada data jenis cuti</h4>
                        <p class="text-muted">Silakan tambahkan jenis cuti baru dengan mengklik tombol di bawah</p>
                        <a href="{{ route('jenis-cutis.create') }}" class="btn btn-primary rounded-pill px-4 mt-3">
                            <i class="fas fa-plus me-2"></i> Tambah Jenis Cuti
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Konfirmasi Delete - Improved Design -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-danger bg-opacity-10 border-0">
                <h5 class="modal-title text-danger" id="deleteModalLabel">
                    <i class="fas fa-trash-alt me-2"></i> Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="avatar bg-danger bg-opacity-10 text-danger mx-auto mb-3" style="width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <h4 class="text-danger">Konfirmasi Penghapusan</h4>
                    <p>Apakah Anda yakin ingin menghapus jenis cuti ini?</p>
                </div>
                
                <div class="alert alert-light border border-danger-subtle rounded-3">
                    <p class="mb-2"><strong><i class="fas fa-list me-2 text-danger"></i>Nama Jenis:</strong> <span id="delete-nama" class="fw-medium"></span></p>
                    <p class="mb-0"><strong><i class="fas fa-clock me-2 text-danger"></i>Jatah Hari:</strong> <span id="delete-jatah" class="fw-medium"></span> hari</p>
                </div>
                
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i> Perhatian: Tindakan ini akan menghapus data jenis cuti secara permanen dan tidak dapat dibatalkan.
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Batal
                </button>
                <form id="delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="fas fa-trash me-2"></i> Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Custom Colors */
    .text-pink {
        color: #e83e8c !important;
    }
    
    .bg-pink {
        background-color: #e83e8c !important;
    }
    
    /* Fixed circular icons */
    .rounded-circle {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        aspect-ratio: 1/1;
    }
    
    /* For larger icons in card view */
    #cardsView .rounded-circle {
        width: 50px;
        height: 50px;
    }
    
    /* Card styling */
    .card {
        transition: all 0.3s ease;
    }
    
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }
    
    /* Table styling */
    .table-container {
        width: 100%;
        overflow-x: auto;
    }
    
    .table {
        width: 100%;
        margin-bottom: 0;
    }
    
    /* Table rows */
    .table tbody tr {
        transition: all 0.15s ease;
    }
    
    .table tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.03);
    }
    
    /* Empty state */
    .empty-row {
        height: 300px;
    }
    
    .empty-message {
        vertical-align: middle;
    }
    
    /* Table header */
    #jenisCutiTable thead th {
        font-weight: 600;
        background-color: #f9f9f9;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    /* Rounded pill badges */
    .badge.rounded-pill {
        font-weight: 500;
        padding: 0.35em 0.8em;
        font-size: 0.85em;
    }
    
    /* Action buttons */
    .btn-group .btn {
        padding: 0.35rem 0.75rem;
    }
    
    /* Smooth transition for view toggle */
    #tableView, #cardsView {
        transition: all 0.3s ease;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .btn-sm {
            padding: 0.4rem 0.6rem;
        }
        
        .card-title {
            font-size: 1.1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Delete confirmation
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                const jatah = this.getAttribute('data-jatah');

                // Set data in modal
                document.getElementById('delete-nama').textContent = nama;
                document.getElementById('delete-jatah').textContent = jatah;
                document.getElementById('delete-form').action = `/jenis-cutis/${id}`;

                // Show modal
                deleteModal.show();
            });
        });
        
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        const tableRows = document.querySelectorAll('#tableView tbody tr:not(.empty-row)');
        const cardItems = document.querySelectorAll('#cardsView [data-name]');
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            // Search in table view
            tableRows.forEach(row => {
                const name = row.getAttribute('data-name');
                if (name && name.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Search in cards view
            cardItems.forEach(card => {
                const name = card.getAttribute('data-name');
                if (name && name.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
        
        // View toggle functionality
        const viewToggles = document.querySelectorAll('.view-toggle');
        const tableView = document.getElementById('tableView');
        const cardsView = document.getElementById('cardsView');
        
        viewToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const view = this.getAttribute('data-view');
                
                // Remove active class from all toggles
                viewToggles.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked toggle
                this.classList.add('active');
                
                // Show appropriate view
                if (view === 'table') {
                    tableView.style.display = '';
                    cardsView.style.display = 'none';
                } else {
                    tableView.style.display = 'none';
                    cardsView.style.display = '';
                }
            });
        });
    });
</script>
@endpush
@endsection