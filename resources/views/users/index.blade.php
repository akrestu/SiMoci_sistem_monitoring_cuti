@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between bg-gradient-primary text-white">
            <div class="d-flex align-items-center">
                <i class="fas fa-users-cog fa-fw me-2"></i>
                <h5 class="mb-0 fw-semibold">Manajemen Pengguna</h5>
            </div>
            <a href="{{ route('users.create') }}" class="btn btn-light text-primary">
                <i class="fas fa-plus-circle me-1"></i> Tambah Pengguna
            </a>
        </div>

        <div class="card-body">
            <!-- Filter Form -->
            <div class="mb-4">
                <form action="{{ route('users.index') }}" method="GET" id="filterForm">
                    <div class="card bg-light border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 py-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-filter text-primary me-2"></i>
                                <h6 class="mb-0 fw-semibold">Filter Pengguna</h6>
                            </div>
                        </div>
                        <div class="card-body py-3">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label small fw-semibold">Nama</label>
                                    <div class="input-group input-group-seamless">
                                        <span class="input-group-text bg-transparent border-end-0">
                                            <i class="fas fa-user text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0 ps-0" name="name" value="{{ request('name') }}" placeholder="Cari berdasarkan nama">
                                        @if(request('name'))
                                            <button type="button" class="btn btn-outline-secondary border-0" onclick="clearInput('name')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label small fw-semibold">Email</label>
                                    <div class="input-group input-group-seamless">
                                        <span class="input-group-text bg-transparent border-end-0">
                                            <i class="fas fa-envelope text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0 ps-0" name="email" value="{{ request('email') }}" placeholder="Cari berdasarkan email">
                                        @if(request('email'))
                                            <button type="button" class="btn btn-outline-secondary border-0" onclick="clearInput('email')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-2 d-flex align-items-end">
                                    <div class="d-flex gap-2 w-100">
                                        <button type="submit" class="btn btn-primary flex-grow-1">
                                            <i class="fas fa-search me-1"></i> Filter
                                        </button>
                                        @if(request('name') || request('email'))
                                            <button type="button" class="btn btn-outline-secondary" onclick="clearAllFilters()">
                                                <i class="fas fa-eraser"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- User Data Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-table text-primary me-2"></i>
                        <h6 class="mb-0 fw-semibold">Daftar Pengguna</h6>
                    </div>
                    <div class="badge bg-primary rounded-pill">
                        <i class="fas fa-users me-1"></i> {{ $users->total() }} pengguna
                    </div>
                </div>

                <div class="card-body p-0">
                    <!-- Bulk Actions -->
                    <form id="massDeleteForm" action="{{ route('users.mass-delete') }}" method="POST">
                        @csrf

                        @if($isAdmin)
                        <div class="bg-light p-3 border-bottom d-flex align-items-center">
                            <div class="form-check me-3">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label small fw-semibold" for="selectAll">Pilih Semua</label>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger" id="massDeleteBtn" disabled>
                                <i class="fas fa-trash-alt me-1"></i> Hapus Terpilih
                            </button>
                        </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th width="50px" class="border-0"></th>
                                        <th class="border-0">Nama</th>
                                        <th class="border-0">Username</th>
                                        <th class="border-0">Email</th>
                                        <th class="border-0">Dibuat Pada</th>
                                        <th class="border-0 text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <tr class="user-row">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input user-checkbox" type="checkbox" value="{{ $user->id }}" name="user_ids[]"
                                                        {{ auth()->id() == $user->id ? 'disabled' : '' }}>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle bg-primary text-white me-2">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $user->name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $user->username }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-envelope text-muted me-2"></i>
                                                    {{ $user->email }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-calendar-alt text-muted me-2"></i>
                                                    {{ $user->created_at->format('d M Y, H:i') }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-end gap-2">
                                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit Pengguna">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    @if(auth()->id() != $user->id && $isAdmin)
                                                        <button type="button" class="btn btn-sm btn-outline-danger delete-btn"
                                                            data-user-id="{{ $user->id }}"
                                                            data-user-name="{{ $user->name }}"
                                                            data-bs-toggle="tooltip" title="Hapus Pengguna">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5">
                                                <div class="empty-state">
                                                    <div class="empty-state-icon bg-light text-secondary mb-3">
                                                        <i class="fas fa-user-slash"></i>
                                                    </div>
                                                    <h5>Tidak ada pengguna ditemukan</h5>
                                                    <p class="text-muted">Tidak ada data pengguna yang sesuai dengan kriteria filter.</p>
                                                    @if(request('name') || request('email'))
                                                        <button type="button" class="btn btn-outline-primary mt-3" onclick="clearAllFilters()">
                                                            <i class="fas fa-eraser me-1"></i> Reset Filter
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <!-- Pagination -->
                    @if($users->hasPages())
                    <div class="d-flex justify-content-center p-3 border-top">
                        {{ $users->withQueryString()->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Single Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-trash-alt me-2"></i> Hapus Pengguna
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="modal-icon-container bg-danger-subtle text-danger rounded-circle mb-3">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <h5 class="mb-3">Konfirmasi Penghapusan</h5>
                    <p id="deleteModalText" class="mb-0">Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash-alt me-1"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Mass Delete Modal -->
<div class="modal fade" id="massDeleteModal" tabindex="-1" aria-labelledby="massDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 bg-danger text-white">
                <h5 class="modal-title" id="massDeleteModalLabel">
                    <i class="fas fa-trash-alt me-2"></i> Hapus Pengguna Terpilih
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="modal-icon-container bg-danger-subtle text-danger rounded-circle mb-3">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <h5 class="mb-3">Konfirmasi Penghapusan Massal</h5>
                    <p id="massDeleteModalText" class="mb-0">Apakah Anda yakin ingin menghapus pengguna yang dipilih? Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmMassDeleteBtn">
                    <i class="fas fa-trash-alt me-1"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Modern styling for users page */
    .avatar-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, #3a56e3 100%);
    }

    .empty-state {
        padding: 2rem 1rem;
        text-align: center;
    }

    .empty-state-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 2rem;
    }

    .user-row {
        transition: all 0.2s ease;
    }

    .user-row:hover {
        background-color: rgba(var(--primary-color-rgb, 13, 110, 253), 0.03);
    }

    .input-group-seamless .input-group-text {
        border-right: 0;
    }

    .input-group-seamless .form-control {
        border-left: 0;
    }

    .modal-icon-container {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        border-radius: 50%;
    }

    /* Tooltip styling */
    .tooltip {
        font-size: 0.75rem;
    }

    /* Animation for table rows */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .user-row {
        animation: fadeIn 0.3s ease-out forwards;
    }

    /* Pagination styling */
    .pagination {
        margin-bottom: 0;
    }

    .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .page-link {
        color: var(--primary-color);
    }

    .page-link:hover {
        color: var(--primary-hover);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                delay: { show: 500, hide: 100 }
            });
        });

        // Single delete confirmation (using Bootstrap modal)
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        const deleteModalText = document.getElementById('deleteModalText');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        let currentUserId = null;

        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');

                currentUserId = userId;
                deleteModalText.innerHTML = `Apakah Anda yakin ingin menghapus pengguna <strong>${userName}</strong>? Tindakan ini tidak dapat dibatalkan.`;
                deleteModal.show();
            });
        });

        confirmDeleteBtn.addEventListener('click', function() {
            if (currentUserId) {
                // Show loading state
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menghapus...';
                this.disabled = true;

                const deleteForm = document.getElementById('deleteForm');
                deleteForm.action = `/users/${currentUserId}`;
                deleteForm.submit();
            }
            deleteModal.hide();
        });

        // Handle Pilih Semua
        const selectAllCheckbox = document.getElementById('selectAll');
        const userCheckboxes = document.querySelectorAll('.user-checkbox:not(:disabled)');
        const massDeleteBtn = document.getElementById('massDeleteBtn');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                userCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });

                updateMassDeleteButton();
            });
        }

        userCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (selectAllCheckbox) {
                    const allChecked = [...userCheckboxes].every(c => c.checked);
                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = !allChecked && [...userCheckboxes].some(c => c.checked);
                }

                updateMassDeleteButton();
            });
        });

        function updateMassDeleteButton() {
            if (!massDeleteBtn) return;

            const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
            massDeleteBtn.disabled = checkedCount === 0;

            if (checkedCount > 0) {
                massDeleteBtn.innerHTML = `<i class="fas fa-trash-alt me-1"></i> Hapus ${checkedCount} Terpilih`;
                massDeleteBtn.classList.add('btn-danger');
                massDeleteBtn.classList.remove('btn-outline-danger');
            } else {
                massDeleteBtn.innerHTML = `<i class="fas fa-trash-alt me-1"></i> Hapus Terpilih`;
                massDeleteBtn.classList.remove('btn-danger');
                massDeleteBtn.classList.add('btn-outline-danger');
            }
        }

        // Mass delete confirmation (using Bootstrap modal)
        const massDeleteModal = new bootstrap.Modal(document.getElementById('massDeleteModal'));
        const massDeleteModalText = document.getElementById('massDeleteModalText');
        const confirmMassDeleteBtn = document.getElementById('confirmMassDeleteBtn');

        if (massDeleteBtn) {
            massDeleteBtn.addEventListener('click', function(e) {
                e.preventDefault();

                const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
                massDeleteModalText.innerHTML = `Apakah Anda yakin ingin menghapus <strong>${checkedCount}</strong> pengguna yang dipilih? Tindakan ini tidak dapat dibatalkan.`;
                massDeleteModal.show();
            });
        }

        if (confirmMassDeleteBtn) {
            confirmMassDeleteBtn.addEventListener('click', function() {
                // Show loading state
                this.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Menghapus...';
                this.disabled = true;

                document.getElementById('massDeleteForm').submit();
                massDeleteModal.hide();
            });
        }

        // Add staggered animation to table rows
        const userRows = document.querySelectorAll('.user-row');
        userRows.forEach((row, index) => {
            row.style.animationDelay = `${index * 0.05}s`;
        });
    });

    function clearInput(fieldName) {
        const form = document.querySelector('#filterForm');
        const input = form.querySelector(`[name="${fieldName}"]`);
        input.value = '';
        form.submit();
    }

    function clearAllFilters() {
        window.location.href = "{{ route('users.index') }}";
    }
</script>
@endpush