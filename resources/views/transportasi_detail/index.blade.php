@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold text-primary">
            <i class="fas fa-ticket-alt me-2"></i>Pengelolaan Tiket Transportasi
        </h2>
        <div>
            <a href="{{ route('transportasi_details.export') }}" class="btn btn-success rounded-pill px-3 me-2 d-inline-flex align-items-center">
                <i class="fas fa-file-excel me-2"></i> Export Excel
            </a>
            <a href="{{ url('/transportasi-details/dashboard') }}" class="btn btn-primary rounded-pill px-3 me-2 d-inline-flex align-items-center">
                <i class="fas fa-chart-line me-2"></i> Dashboard
            </a>
            <a href="{{ url('/transportasis') }}" class="btn btn-info rounded-pill px-3 d-inline-flex align-items-center">
                <i class="fas fa-list me-2"></i> Jenis Transportasi
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm rounded-4 mb-3">
        <div class="card-header bg-white py-2">
            <h5 class="mb-0 fw-semibold small">
                <i class="fas fa-filter me-1 text-primary"></i>Filter Tiket
            </h5>
        </div>
        <div class="card-body py-2">
            <form id="filter-form" class="row g-2">
                <div class="col-md-3">
                    <label for="filter-status" class="form-label small mb-1">Status Pemesanan</label>
                    <select id="filter-status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="belum_dipesan">Belum Dipesan</option>
                        <option value="proses_pemesanan">Proses Pemesanan</option>
                        <option value="tiket_terbit">Tiket Terbit</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-transportasi" class="form-label small mb-1">Jenis Transportasi</label>
                    <select id="filter-transportasi" class="form-select form-select-sm">
                        <option value="">Semua Jenis</option>
                        @foreach(\App\Models\Transportasi::all() as $transportasi)
                            <option value="{{ $transportasi->id }}">{{ $transportasi->jenis }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filter-perjalanan" class="form-label small mb-1">Jenis Perjalanan</label>
                    <select id="filter-perjalanan" class="form-select form-select-sm">
                        <option value="">Semua Perjalanan</option>
                        <option value="pergi">Tiket Pergi</option>
                        <option value="kembali">Tiket Kembali</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" id="btn-filter" class="btn btn-primary btn-sm me-2">
                        <i class="fas fa-search me-1"></i> Filter
                    </button>
                    <button type="button" id="btn-reset" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-primary small">
                <i class="fas fa-list me-1"></i>Daftar Tiket Transportasi
            </h5>
            <button type="button" id="btn-delete-selected" class="btn btn-danger btn-sm rounded-pill px-2" style="display: none;">
                <i class="fas fa-trash me-1"></i> Hapus Terpilih
            </button>
        </div>
        <div class="card-body p-2">
            <div class="table-responsive table-scroll-container">
                <table class="table table-hover align-middle table-sm">
                    <thead class="table-light">
                        <tr>
                            <th class="px-2 py-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="check-all">
                                </div>
                            </th>
                            <th class="px-2 py-2">Karyawan</th>
                            <th class="px-2 py-2">Transportasi</th>
                            <th class="px-2 py-2">Jenis</th>
                            <th class="px-2 py-2">Rute</th>
                            <th class="px-2 py-2">Jadwal</th>
                            <th class="px-2 py-2">Biaya</th>
                            <th class="px-2 py-2">Status</th>
                            <th class="px-2 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transportasiDetails as $detail)
                            <tr>
                                <td class="px-2 py-2">
                                    <div class="form-check">
                                        <input class="form-check-input row-checkbox" type="checkbox"
                                               value="{{ $detail->id }}"
                                               data-karyawan="{{ $detail->cuti->karyawan->nama }}"
                                               data-transportasi="{{ $detail->transportasi->jenis }}">
                                    </div>
                                </td>
                                <td class="px-2 py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary bg-opacity-10 text-primary rounded-circle me-1 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <span>{{ strtoupper(substr($detail->cuti->karyawan->nama, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <span class="fw-medium">{{ $detail->cuti->karyawan->nama }}</span>
                                            <div class="small text-muted">{{ $detail->cuti->karyawan->departemen ?? 'Dept. Tidak Tersedia' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 py-2">
                                    <div class="d-flex align-items-center">
                                        @if(strtolower($detail->transportasi->jenis) == 'pesawat')
                                            <i class="fas fa-plane text-primary me-1"></i>
                                        @elseif(strtolower($detail->transportasi->jenis) == 'kereta')
                                            <i class="fas fa-train text-success me-1"></i>
                                        @elseif(strtolower($detail->transportasi->jenis) == 'bus')
                                            <i class="fas fa-bus text-info me-1"></i>
                                        @else
                                            <i class="fas fa-car text-secondary me-1"></i>
                                        @endif
                                        {{ $detail->transportasi->jenis }}
                                    </div>
                                </td>
                                <td class="px-2 py-2">
                                    @if($detail->jenis_perjalanan == 'pergi')
                                        <span class="badge bg-primary rounded-pill">Pergi</span>
                                    @else
                                        <span class="badge bg-success rounded-pill">Kembali</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2">
                                    <div class="d-flex align-items-center">
                                        <span>{{ $detail->rute_asal }}</span>
                                        <i class="fas fa-arrow-right mx-1 text-muted small"></i>
                                        <span>{{ $detail->rute_tujuan }}</span>
                                    </div>
                                </td>
                                <td class="px-2 py-2">
                                    <div class="d-flex align-items-center">
                                        <i class="far fa-calendar-alt me-1 text-muted"></i>
                                        <span class="small">{{ \Carbon\Carbon::parse($detail->waktu_berangkat)->format('d/m/Y H:i') }}</span>
                                    </div>
                                </td>
                                <td class="px-2 py-2">
                                    <div class="fw-medium small">Rp {{ number_format($detail->biaya_aktual, 0, ',', '.') }}</div>
                                    @if($detail->perlu_hotel)
                                        <div class="small mt-1">
                                            <span class="badge bg-light text-dark border">
                                                <i class="fas fa-hotel text-info me-1"></i>
                                                Rp {{ number_format($detail->hotel_biaya, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-2 py-2">
                                    @if($detail->status_pemesanan == 'belum_dipesan')
                                        <span class="badge bg-danger rounded-pill">Belum</span>
                                    @elseif($detail->status_pemesanan == 'proses_pemesanan')
                                        <span class="badge bg-warning text-dark rounded-pill">Proses</span>
                                    @elseif($detail->status_pemesanan == 'tiket_terbit')
                                        <span class="badge bg-success rounded-pill">Terbit</span>
                                        @if($detail->nomor_tiket)
                                            <div class="small mt-1 text-muted">
                                                <i class="fas fa-hashtag me-1"></i>{{ $detail->nomor_tiket }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary rounded-pill">Batal</span>
                                    @endif
                                </td>
                                <td class="px-2 py-2">
                                    <div class="d-flex justify-content-center">
                                        <a href="{{ url('/transportasi-details/' . $detail->id) }}" class="btn btn-primary btn-sm rounded-circle me-1" title="Lihat Detail" style="width: 24px; height: 24px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-eye fa-xs"></i>
                                        </a>
                                        <a href="{{ url('/transportasi-details/' . $detail->id . '/edit') }}" class="btn btn-warning btn-sm rounded-circle me-1" title="Edit Tiket" style="width: 24px; height: 24px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-edit fa-xs"></i>
                                        </a>
                                        <form action="{{ url('/transportasi-details/' . $detail->id) }}" method="POST" class="delete-form" data-no-preloader>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm rounded-circle" title="Hapus Tiket" style="width: 24px; height: 24px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-trash fa-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <img src="{{ asset('images/empty-tickets.svg') }}" alt="Tidak ada data" class="mb-2" style="height: 100px;" onerror="this.src='https://cdn-icons-png.flaticon.com/512/7486/7486754.png'; this.style.height='80px';">
                                    <h6 class="text-muted mt-2">Tidak ada data tiket transportasi</h6>
                                    <p class="text-muted small">Belum ada tiket transportasi yang terdaftar atau sesuai dengan filter</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-3 py-2 d-flex justify-content-between align-items-center border-top">
                <div class="text-muted small">
                    Menampilkan {{ $transportasiDetails->firstItem() ?? 0 }} - {{ $transportasiDetails->lastItem() ?? 0 }} dari {{ $transportasiDetails->total() }} data
                </div>
                <div class="d-flex align-items-center">
                    <div class="me-2">
                        <select class="form-select form-select-sm py-1" id="per-page-selector" aria-label="Tampilkan per halaman" style="font-size: 0.75rem;">
                            <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 baris</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 baris</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris</option>
                            <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Semua data</option>
                        </select>
                    </div>
                    <div>
                        {{ $transportasiDetails->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Delete Single -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus tiket transportasi ini?</p>
                <p class="text-danger">Tindakan ini tidak dapat dibatalkan</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-2"></i>Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Batch Delete -->
<div class="modal fade" id="batchDeleteModal" tabindex="-1" aria-labelledby="batchDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="batchDeleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Anda akan menghapus <span id="checkedCount"></span> tiket transportasi:</p>
                <div class="text-start bg-light p-3 rounded" style="max-height: 200px; overflow-y: auto;">
                    <ul class="list-unstyled mb-0" id="selectedItemsList">
                        <!-- List items will be inserted here dynamically -->
                    </ul>
                </div>
                <p class="text-danger mt-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Tindakan ini tidak dapat dibatalkan
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmBatchDeleteBtn">
                    <i class="fas fa-trash me-2"></i>Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Table scrolling */
    .table-scroll-container {
        max-height: 500px;
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

    /* Card enhancements */
    .rounded-4 {
        border-radius: 0.75rem !important;
    }

    /* Avatar styling */
    .avatar {
        font-weight: bold;
    }

    /* Table improvements */
    .table {
        font-size: 0.9rem;
    }

    /* Badge styling */
    .badge {
        font-weight: 500;
    }

    /* Responsive buttons for smaller screens */
    @media (max-width: 768px) {
        .btn-text {
            display: none;
        }
    }

    /* Pagination styling */
    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
        line-height: 1.2;
    }

    .pagination .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
    }

    /* Compact footer */
    .border-top {
        border-top: 1px solid rgba(0, 0, 0, 0.125) !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('check-all');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const btnDeleteSelected = document.getElementById('btn-delete-selected');
    const btnFilter = document.getElementById('btn-filter');
    const btnReset = document.getElementById('btn-reset');

    // Modal Elements
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const batchDeleteModal = new bootstrap.Modal(document.getElementById('batchDeleteModal'));
    let currentDeleteForm = null;

    // Handle "Check All" functionality
    checkAll.addEventListener('change', function() {
        rowCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateDeleteButtonVisibility();
    });

    // Handle individual checkbox changes
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateDeleteButtonVisibility();

            // Update "Check All" state
            const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
            checkAll.checked = allChecked;
            checkAll.indeterminate = someChecked && !allChecked;
        });
    });

    // Show/hide delete button based on selection
    function updateDeleteButtonVisibility() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        btnDeleteSelected.style.display = checkedBoxes.length > 0 ? 'block' : 'none';
    }

    // Filter functionality - placeholder implementation
    btnFilter.addEventListener('click', function() {
        // This would be connected to an actual AJAX filter in real implementation
        alert('Fitur dalam Pengembangan: Fungsi filter sedang dalam pengembangan');
    });

    // Reset filter
    btnReset.addEventListener('click', function() {
        document.getElementById('filter-status').value = '';
        document.getElementById('filter-transportasi').value = '';
        document.getElementById('filter-perjalanan').value = '';
    });

    // Per Page Selector handling
    const perPageSelector = document.getElementById('per-page-selector');
    perPageSelector.addEventListener('change', function() {
        // Get current URL and params
        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);

        // Update or add the per_page parameter
        params.set('per_page', this.value);

        // Replace the URL with the new one and reload the page
        url.search = params.toString();
        window.location.href = url.toString();
    });

    // Handle batch delete
    btnDeleteSelected.addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);

        // Update modal content
        document.getElementById('checkedCount').textContent = checkedBoxes.length;

        // Populate list of items to delete
        const itemsList = document.getElementById('selectedItemsList');
        itemsList.innerHTML = '';

        Array.from(checkedBoxes).forEach(cb => {
            const li = document.createElement('li');
            li.className = 'mb-2';
            li.innerHTML = `<i class="fas fa-ticket-alt me-2 text-danger"></i>${cb.dataset.karyawan} - ${cb.dataset.transportasi}`;
            itemsList.appendChild(li);
        });

        // Show batch delete modal
        batchDeleteModal.show();
    });

    // Confirm batch delete button
    document.getElementById('confirmBatchDeleteBtn').addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);

        // Create and submit a form for batch delete
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/transportasi-details/batch-delete';
        form.style.display = 'none';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);

        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    });

    // Handle single delete confirmation
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            currentDeleteForm = this;

            // Show delete confirmation modal
            deleteModal.show();
        });
    });

    // Confirm single delete button
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (currentDeleteForm) {
            // Show preloader manually ONLY after confirmation
            const preloader = document.getElementById('preloader');
            if (preloader) {
                preloader.classList.remove('hide');
            }

            // Get form data and URL
            const formData = new FormData(currentDeleteForm);
            const url = currentDeleteForm.getAttribute('action');
            const csrfToken = document.querySelector('input[name="_token"]').value;

            // Use fetch API for AJAX delete
            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    // Success - Redirect to current page
                    window.location.reload();
                } else {
                    // Handle error
                    return response.json().then(error => {
                        throw new Error(error.message || 'Terjadi kesalahan saat menghapus data');
                    });
                }
            })
            .catch(error => {
                // Hide preloader
                if (preloader) {
                    preloader.classList.add('hide');
                }

                // Close modal
                deleteModal.hide();

                // Show error message
                alert('Error: ' + (error.message || 'Terjadi kesalahan saat menghapus data'));
            });
        }
    });
});
</script>
@endpush

@endsection