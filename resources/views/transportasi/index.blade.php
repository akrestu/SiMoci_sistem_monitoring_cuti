@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-md-8">
            <h4 class="fw-bold"><i class="fas fa-plane me-2 text-primary"></i>Data Transportasi</h4>
            <p class="text-muted mb-0">Kelola jenis transportasi yang tersedia untuk pengajuan cuti</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('transportasis.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus me-2"></i> Tambah Transportasi
            </a>
        </div>
    </div>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle text-success me-2 fa-lg"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle text-danger me-2 fa-lg"></i>
            <div>{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Search and Filter Bar -->
    <div class="card mb-4 border-0 shadow-sm rounded-4">
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="searchInput" class="form-control border-0 bg-light" placeholder="Cari transportasi..." aria-label="Search">
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
    <div class="card border-0 shadow-sm rounded-4 mb-4" id="tableView">
        <div class="card-header bg-white p-4 border-0">
            <h5 class="card-title mb-0"><i class="fas fa-table me-2 text-primary"></i>Daftar Transportasi</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="transportasiTable">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Jenis</th>
                            <th class="px-4 py-3">Keterangan</th>
                            <th class="px-4 py-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transportasis as $transportasi)
                            <tr data-name="{{ strtolower($transportasi->jenis) }}">
                                <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle p-2 me-3
                                            @if(strpos(strtolower($transportasi->jenis), 'pesawat') !== false)
                                                bg-primary bg-opacity-10 text-primary
                                            @elseif(strpos(strtolower($transportasi->jenis), 'kereta') !== false)
                                                bg-success bg-opacity-10 text-success
                                            @elseif(strpos(strtolower($transportasi->jenis), 'bus') !== false)
                                                bg-info bg-opacity-10 text-info
                                            @elseif(strpos(strtolower($transportasi->jenis), 'kapal') !== false)
                                                bg-indigo bg-opacity-10 text-indigo
                                            @elseif(strpos(strtolower($transportasi->jenis), 'taksi') !== false || strpos(strtolower($transportasi->jenis), 'taxi') !== false)
                                                bg-warning bg-opacity-10 text-warning
                                            @else
                                                bg-secondary bg-opacity-10 text-secondary
                                            @endif
                                        ">
                                            @if(strpos(strtolower($transportasi->jenis), 'pesawat') !== false)
                                                <i class="fas fa-plane"></i>
                                            @elseif(strpos(strtolower($transportasi->jenis), 'kereta') !== false)
                                                <i class="fas fa-train"></i>
                                            @elseif(strpos(strtolower($transportasi->jenis), 'bus') !== false)
                                                <i class="fas fa-bus"></i>
                                            @elseif(strpos(strtolower($transportasi->jenis), 'kapal') !== false)
                                                <i class="fas fa-ship"></i>
                                            @elseif(strpos(strtolower($transportasi->jenis), 'taksi') !== false || strpos(strtolower($transportasi->jenis), 'taxi') !== false)
                                                <i class="fas fa-taxi"></i>
                                            @else
                                                <i class="fas fa-car"></i>
                                            @endif
                                        </div>
                                        <div class="fw-medium">{{ $transportasi->jenis }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    @if($transportasi->keterangan)
                                        <span class="text-truncate d-inline-block" style="max-width: 350px;" title="{{ $transportasi->keterangan }}">
                                            {{ $transportasi->keterangan }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end action-column">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('transportasis.edit', $transportasi->id) }}" class="btn btn-sm btn-outline-primary rounded-pill me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill delete-btn" 
                                                data-id="{{ $transportasi->id }}" 
                                                data-jenis="{{ $transportasi->jenis }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="4" class="text-center py-5 empty-message">
                                    <div class="py-5">
                                        <img src="https://cdn-icons-png.flaticon.com/512/5220/5220262.png" alt="No data" style="width: 120px; height: 120px; opacity: 0.5">
                                        <h5 class="mt-3 text-muted">Tidak ada data transportasi</h5>
                                        <p class="text-muted">Silakan tambahkan transportasi baru dengan mengklik tombol "Tambah Transportasi"</p>
                                        <a href="{{ route('transportasis.create') }}" class="btn btn-primary rounded-pill mt-3">
                                            <i class="fas fa-plus me-2"></i> Tambah Transportasi
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
        @forelse($transportasis as $transportasi)
            <div class="col-md-6 col-lg-4" data-name="{{ strtolower($transportasi->jenis) }}">
                <div class="card h-100 border-0 shadow-sm rounded-4 hover-shadow">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="rounded-circle p-3 me-3
                                @if(strpos(strtolower($transportasi->jenis), 'pesawat') !== false)
                                    bg-primary bg-opacity-10 text-primary
                                @elseif(strpos(strtolower($transportasi->jenis), 'kereta') !== false)
                                    bg-success bg-opacity-10 text-success
                                @elseif(strpos(strtolower($transportasi->jenis), 'bus') !== false)
                                    bg-info bg-opacity-10 text-info
                                @elseif(strpos(strtolower($transportasi->jenis), 'kapal') !== false)
                                    bg-indigo bg-opacity-10 text-indigo
                                @elseif(strpos(strtolower($transportasi->jenis), 'taksi') !== false || strpos(strtolower($transportasi->jenis), 'taxi') !== false)
                                    bg-warning bg-opacity-10 text-warning
                                @else
                                    bg-secondary bg-opacity-10 text-secondary
                                @endif
                            ">
                                @if(strpos(strtolower($transportasi->jenis), 'pesawat') !== false)
                                    <i class="fas fa-plane fa-lg"></i>
                                @elseif(strpos(strtolower($transportasi->jenis), 'kereta') !== false)
                                    <i class="fas fa-train fa-lg"></i>
                                @elseif(strpos(strtolower($transportasi->jenis), 'bus') !== false)
                                    <i class="fas fa-bus fa-lg"></i>
                                @elseif(strpos(strtolower($transportasi->jenis), 'kapal') !== false)
                                    <i class="fas fa-ship fa-lg"></i>
                                @elseif(strpos(strtolower($transportasi->jenis), 'taksi') !== false || strpos(strtolower($transportasi->jenis), 'taxi') !== false)
                                    <i class="fas fa-taxi fa-lg"></i>
                                @else
                                    <i class="fas fa-car fa-lg"></i>
                                @endif
                            </div>
                            <h5 class="card-title mb-0">{{ $transportasi->jenis }}</h5>
                        </div>
                        
                        @if($transportasi->keterangan)
                            <p class="card-text text-muted mb-4">{{ $transportasi->keterangan }}</p>
                        @else
                            <p class="card-text text-muted mb-4">Tidak ada keterangan tambahan</p>
                        @endif
                        
                        <div class="d-flex justify-content-end mt-auto">
                            <a href="{{ route('transportasis.edit', $transportasi->id) }}" class="btn btn-sm btn-outline-primary rounded-pill me-2">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill delete-btn" 
                                    data-id="{{ $transportasi->id }}" 
                                    data-jenis="{{ $transportasi->jenis }}">
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
                        <img src="https://cdn-icons-png.flaticon.com/512/5220/5220262.png" alt="No data" style="width: 150px; height: 150px; opacity: 0.5">
                        <h4 class="mt-4 text-muted">Tidak ada data transportasi</h4>
                        <p class="text-muted">Silakan tambahkan transportasi baru dengan mengklik tombol di bawah</p>
                        <a href="{{ route('transportasis.create') }}" class="btn btn-primary rounded-pill px-4 mt-3">
                            <i class="fas fa-plus me-2"></i> Tambah Transportasi
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
                    <p>Apakah Anda yakin ingin menghapus data transportasi ini?</p>
                </div>
                
                <div class="alert alert-light border border-danger-subtle rounded-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-2 me-3 bg-danger bg-opacity-10 text-danger">
                            <i class="fas fa-plane"></i>
                        </div>
                        <div>
                            <span class="d-block fw-medium" id="delete-jenis"></span>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i> Perhatian: Tindakan ini akan menghapus data transportasi secara permanen dan tidak dapat dibatalkan.
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
    .text-indigo {
        color: #6610f2 !important;
    }
    
    .bg-indigo {
        background-color: #6610f2 !important;
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
    #transportasiTable thead th {
        font-weight: 600;
        background-color: #f9f9f9;
        position: sticky;
        top: 0;
        z-index: 10;
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
                const jenis = this.getAttribute('data-jenis');
                
                // Set data in modal
                document.getElementById('delete-jenis').textContent = jenis;
                document.getElementById('delete-form').action = `/transportasis/${id}`;
                
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