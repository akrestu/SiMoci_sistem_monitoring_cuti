@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary">
            <span class="bg-primary bg-opacity-10 p-2 rounded-circle me-2">
                <i class="fas fa-users text-primary"></i>
            </span>
            Data Karyawan
        </h4>
        <div class="d-flex gap-2">
            <button id="massDeleteBtn" class="btn btn-danger d-none">
                <i class="fas fa-trash-alt me-1"></i> Hapus Terpilih
            </button>
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-file-export me-1"></i> Export/Import
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                    <li><a class="dropdown-item" href="{{ route('karyawans.export') }}">
                        <i class="fas fa-file-excel me-2 text-success"></i> Export Excel
                    </a></li>
                    <li><a class="dropdown-item" href="#" id="download-template-btn">
                        <i class="fas fa-file-download me-2 text-primary"></i> Download Template
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="fas fa-file-upload me-2 text-warning"></i> Import Data
                    </a></li>
                </ul>
            </div>
            <a href="{{ route('karyawans.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Karyawan
            </a>
        </div>
    </div>
    
    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('warning') }}
        @if(session('import_failures'))
            <button class="btn btn-sm btn-warning mt-2" type="button" data-bs-toggle="collapse" data-bs-target="#failureDetails" aria-expanded="false" aria-controls="failureDetails">
                Lihat Detail
            </button>
            <div class="collapse mt-2" id="failureDetails">
                <div class="card card-body">
                    <h6>Detail Error:</h6>
                    <ul>
                        @foreach(session('import_failures') as $failure)
                            <li>Baris {{ $failure['row'] }}: {{ implode(', ', $failure['errors']) }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <form action="{{ route('karyawans.index') }}" method="GET" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5 col-lg-3">
                        <label for="search" class="form-label small text-muted mb-1 fw-medium">Pencarian</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-primary"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control border-0 bg-light shadow-none" id="search" name="search" placeholder="Cari nama atau NIK..." value="{{ request('search') }}">
                            @if(request('search'))
                                <button type="button" class="btn btn-light border-0" onclick="clearInput('search')">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-lg-2">
                        <label for="departemen" class="form-label small text-muted mb-1 fw-medium">Departemen</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-primary"><i class="fas fa-building"></i></span>
                            <select class="form-select border-0 bg-light shadow-none" id="departemen" name="departemen">
                                <option value="">Semua Departemen</option>
                                @foreach($departemen as $dept)
                                    <option value="{{ $dept }}" {{ request('departemen') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                @endforeach
                            </select>
                            @if(request('departemen'))
                                <button type="button" class="btn btn-light border-0" onclick="clearInput('departemen')">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-lg-2">
                        <label for="perPage" class="form-label small text-muted mb-1 fw-medium">Tampilan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-primary"><i class="fas fa-list"></i></span>
                            <select class="form-select border-0 bg-light shadow-none" id="perPage" name="perPage" onchange="this.form.submit()">
                                <option value="10" {{ request('perPage') == 10 || !request('perPage') ? 'selected' : '' }}>10 Baris</option>
                                <option value="25" {{ request('perPage') == 25 ? 'selected' : '' }}>25 Baris</option>
                                <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50 Baris</option>
                                <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100 Baris</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-lg-3">
                        <label for="sort_by" class="form-label small text-muted mb-1 fw-medium">Urutan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0 text-primary"><i class="fas fa-sort"></i></span>
                            <select class="form-select border-0 bg-light shadow-none" id="sort_by" name="sort_by">
                                <option value="nama" {{ request('sort_by') == 'nama' ? 'selected' : '' }}>Nama</option>
                                <option value="nik" {{ request('sort_by') == 'nik' ? 'selected' : '' }}>NIK</option>
                                <option value="departemen" {{ request('sort_by') == 'departemen' ? 'selected' : '' }}>Departemen</option>
                                <option value="jabatan" {{ request('sort_by') == 'jabatan' ? 'selected' : '' }}>Jabatan</option>
                            </select>
                            <select class="form-select border-0 bg-light shadow-none" id="sort_direction" name="sort_direction" style="max-width: 100px;">
                                <option value="asc" {{ request('sort_direction', 'asc') == 'asc' ? 'selected' : '' }}>Naik</option>
                                <option value="desc" {{ request('sort_direction') == 'desc' ? 'selected' : '' }}>Turun</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-12 col-lg-2">
                        <div class="d-flex gap-2 h-100">
                            <button type="submit" class="btn btn-primary w-100 h-100">
                                <i class="fas fa-filter me-1"></i> Filter
                            </button>
                            @if(request('search') || request('departemen') || request('sort_by') && request('sort_by') != 'nama' || request('sort_direction') && request('sort_direction') != 'asc' || request('perPage') && request('perPage') != '10')
                            <a href="{{ route('karyawans.index') }}" class="btn btn-outline-secondary h-100" title="Reset semua filter">
                                <i class="fas fa-undo-alt"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <form id="massDeleteForm" action="{{ route('karyawans.mass-delete') }}" method="POST">
        @csrf
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle border-0 mb-0" id="karyawanTable">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3 text-center" width="5%">
                                    <div class="form-check">
                                        <input class="form-check-input rounded-0" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th class="px-4 py-3 text-center" width="5%">#</th>
                                <th class="px-4 py-3" width="16%">
                                    <a href="{{ route('karyawans.index', ['sort_by' => 'nama', 'sort_direction' => request('sort_by') == 'nama' && request('sort_direction') == 'asc' ? 'desc' : 'asc', 'search' => request('search'), 'departemen' => request('departemen')]) }}" class="text-decoration-none text-dark d-flex align-items-center">
                                        <span>Nama</span>
                                        @if(request('sort_by') == 'nama')
                                            <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 py-3" width="10%">
                                    <a href="{{ route('karyawans.index', ['sort_by' => 'nik', 'sort_direction' => request('sort_by') == 'nik' && request('sort_direction') == 'asc' ? 'desc' : 'asc', 'search' => request('search'), 'departemen' => request('departemen')]) }}" class="text-decoration-none text-dark d-flex align-items-center">
                                        <span>NIK</span>
                                        @if(request('sort_by') == 'nik')
                                            <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 py-3" width="12%">
                                    <a href="{{ route('karyawans.index', ['sort_by' => 'departemen', 'sort_direction' => request('sort_by') == 'departemen' && request('sort_direction') == 'asc' ? 'desc' : 'asc', 'search' => request('search'), 'departemen' => request('departemen')]) }}" class="text-decoration-none text-dark d-flex align-items-center">
                                        <span>Departemen</span>
                                        @if(request('sort_by') == 'departemen')
                                            <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 py-3" width="12%">
                                    <a href="{{ route('karyawans.index', ['sort_by' => 'jabatan', 'sort_direction' => request('sort_by') == 'jabatan' && request('sort_direction') == 'asc' ? 'desc' : 'asc', 'search' => request('search'), 'departemen' => request('departemen')]) }}" class="text-decoration-none text-dark d-flex align-items-center">
                                        <span>Jabatan</span>
                                        @if(request('sort_by') == 'jabatan')
                                            <i class="fas fa-sort-{{ request('sort_direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1 text-muted"></i>
                                        @endif
                                    </a>
                                </th>
                                <th class="px-4 py-3" width="10%">
                                    <span>DOH</span>
                                </th>
                                <th class="px-4 py-3" width="10%">
                                    <span>POH</span>
                                </th>
                                <th class="px-4 py-3" width="8%">
                                    <span>Status</span>
                                </th>
                                <th class="px-4 py-3" width="12%">Email</th>
                                <th class="px-4 py-3 text-center" width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($karyawans as $karyawan)
                                <tr class="border-bottom">
                                    <td class="px-4 py-3 text-center">
                                        <div class="form-check">
                                            <input class="form-check-input rounded-0 employee-checkbox" type="checkbox" 
                                                   name="employee_ids[]" value="{{ $karyawan->id }}"
                                                   data-nama="{{ $karyawan->nama }}"
                                                   data-nik="{{ $karyawan->nik }}">
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 fw-medium text-primary">{{ $karyawan->nama }}</td>
                                    <td class="px-4 py-3"><span class="badge bg-light text-dark border">{{ $karyawan->nik }}</span></td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                                            {{ $karyawan->departemen }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-info-subtle text-info rounded-pill px-3 py-2">
                                            {{ $karyawan->jabatan }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $karyawan->doh ? \Carbon\Carbon::parse($karyawan->doh)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $karyawan->poh ?: '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($karyawan->status)
                                            <span class="badge {{ $karyawan->status == 'Staff' ? 'bg-info' : 'bg-secondary' }} text-white rounded-pill px-3 py-2">
                                                {{ $karyawan->status }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $karyawan->email ?: '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="btn-group">
                                            <a href="{{ route('karyawans.show', $karyawan->id) }}" class="btn btn-sm btn-primary" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('karyawans.edit', $karyawan->id) }}" class="btn btn-sm btn-warning text-white" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                    data-id="{{ $karyawan->id }}" 
                                                    data-nama="{{ $karyawan->nama }}"
                                                    data-nik="{{ $karyawan->nik }}"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr class="empty-row">
                                    <td colspan="11" class="text-center py-5 empty-message">
                                        <div class="py-5">
                                            <i class="fas fa-users fa-3x mb-3 text-muted opacity-50"></i>
                                            <p class="mt-3 mb-0 text-muted">Tidak ada data karyawan</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if(method_exists($karyawans, 'links') && $karyawans->total() > 0)
                <div class="card-footer bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="text-muted small">
                            Menampilkan <span class="fw-medium">{{ $karyawans->firstItem() }}</span> - <span class="fw-medium">{{ $karyawans->lastItem() }}</span> dari <span class="fw-medium">{{ $karyawans->total() }}</span> data
                        </div>
                        <div class="pagination-container">
                            {{ $karyawans->withQueryString()->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Data Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('karyawans.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">File Excel</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx, .xls, .csv" required>
                        <div class="form-text">
                            Format file: .xlsx, .xls, .csv (Maks. 10MB)
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <h6 class="mb-1"><i class="fas fa-info-circle me-1"></i> Petunjuk Import</h6>
                        <ul class="mb-0 ps-3">
                            <li>Download <a href="#" id="template-link-modal" class="alert-link">template</a> terlebih dahulu</li>
                            <li>Isi data sesuai format dalam template</li>
                            <li>Pastikan NIK dan email bersifat unik</li>
                            <li>Upload file yang sudah diisi</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-upload me-1"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Hapus Data Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Anda yakin ingin menghapus data karyawan?</p>
                <div class="mt-3">
                    <p><strong>Nama:</strong> <span id="delete-nama"></span></p>
                    <p><strong>NIK:</strong> <span id="delete-nik"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus!</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Mass Delete -->
<div class="modal fade" id="massDeleteModal" tabindex="-1" aria-labelledby="massDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="massDeleteModalLabel">Hapus Karyawan Terpilih</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Anda yakin ingin menghapus <span id="selected-count"></span> data karyawan terpilih?</p>
                <div class="mt-3 text-start">
                    <p>Data yang akan dihapus:</p>
                    <ul id="selected-names" class="mt-2"></ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirm-mass-delete">Ya, Hapus!</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Pagination styling */
    .pagination {
        --bs-pagination-padding-x: 0.8rem;
        --bs-pagination-padding-y: 0.5rem;
        --bs-pagination-font-size: 0.875rem;
        --bs-pagination-border-radius: 0.5rem;
        --bs-pagination-border-width: 0;
        --bs-pagination-margin-left: 5px;
        box-shadow: none;
        margin-bottom: 0;
    }
    
    .page-item:first-child .page-link {
        border-top-left-radius: var(--bs-pagination-border-radius);
        border-bottom-left-radius: var(--bs-pagination-border-radius);
    }
    
    .page-item:last-child .page-link {
        border-top-right-radius: var(--bs-pagination-border-radius);
        border-bottom-right-radius: var(--bs-pagination-border-radius);
    }
    
    .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: #fff;
        font-weight: 500;
        position: relative;
        z-index: 5;
    }
    
    .page-link {
        transition: all 0.2s ease;
        color: var(--primary-color);
        margin-left: var(--bs-pagination-margin-left);
        border-radius: var(--bs-pagination-border-radius);
    }
    
    .page-link:hover {
        z-index: 2;
        background-color: rgba(13, 110, 253, 0.1);
        border-color: transparent;
        transform: translateY(-2px);
    }
    
    .page-item.disabled .page-link {
        color: #6c757d;
        opacity: 0.6;
    }
    
    /* Card Footer */
    .card-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.05) !important;
        padding-top: 1rem;
        padding-bottom: 1rem;
        background-color: #fafafa !important;
        border-radius: 0 0 0.7rem 0.7rem !important;
    }
    
    .pagination-container .pagination {
        justify-content: flex-end;
    }
    
    @media (max-width: 768px) {
        .card-footer .d-flex {
            flex-direction: column;
            gap: 1rem;
        }
        
        .pagination-container .pagination {
            justify-content: center;
        }
    }
    
    /* Tabel dan container */
    .card {
        margin-bottom: 30px;
        transition: all 0.3s ease;
        border-radius: 0.7rem;
        background-color: #fff;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    
    .card-body {
        position: relative;
        min-height: auto;
    }
    
    .table-container {
        width: 100%;
        height: 100%;
        position: relative;
    }
    
    .table {
        width: 100%;
        margin-bottom: 0;
        vertical-align: middle;
    }
    
    /* Filter and input styling */
    #filterForm .form-control,
    #filterForm .form-select,
    #filterForm .btn {
        height: 38px;
        border-radius: 0.5rem;
    }
    
    #filterForm .input-group-text {
        height: 38px;
        display: flex;
        align-items: center;
        border-radius: 0.5rem 0 0 0.5rem;
    }
    
    /* Baris tabel */
    .table tbody tr {
        transition: all 0.2s ease;
        border-color: rgba(0, 0, 0, 0.05);
    }
    
    .table tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
        transform: translateY(-1px);
    }
    
    /* Empty state */
    .empty-row {
        height: 300px;
    }
    
    .empty-message {
        vertical-align: middle;
    }
    
    /* Header tabel */
    #karyawanTable thead th {
        font-weight: 600;
        color: #495057;
        background-color: #f9fafc;
        padding-top: 16px;
        padding-bottom: 16px;
        position: sticky;
        top: 0;
        z-index: 10;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }
    
    #karyawanTable thead th a {
        font-weight: 600;
    }
    
    /* Badge styling */
    .badge {
        font-weight: 500;
        letter-spacing: 0.3px;
    }
    
    .badge.bg-primary-subtle {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }
    
    .badge.bg-info-subtle {
        background-color: rgba(13, 202, 240, 0.1) !important;
    }
    
    /* Fix for oval icons - make them circle */
    .rounded-circle {
        width: 40px;
        height: 40px;
        aspect-ratio: 1/1 !important;
        border-radius: 50% !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Fix specific badge styling untuk departemen dan jabatan */
    span.badge.rounded-pill {
        width: auto !important;
        aspect-ratio: auto !important;
        border-radius: 50rem !important;
    }
    
    /* Dropdown dan action button */
    .action-column {
        position: relative;
        width: 80px;
    }
    
    .action-dropdown {
        position: static;
    }
    
    /* Action buttons styling */
    .btn-group .btn {
        border-radius: 0.25rem;
        margin: 0 2px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease;
    }
    
    .btn-group .btn:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .btn-group .btn:active {
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        transform: translateY(0);
    }
    
    /* Checkbox styling */
    .form-check-input.rounded-0 {
        border-radius: 0.2rem !important;
        cursor: pointer;
    }
    
    .form-check-input.rounded-0:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    
    /* Responsive styling */
    @media (max-width: 992px) {
        .card-body {
            min-height: auto;
        }
        
        .table {
            min-width: 900px;
        }
    }
    
    @media (max-width: 768px) {
        .dropdown-menu {
            position: fixed !important;
            bottom: 0 !important;
            top: auto !important;
            left: 0 !important;
            right: 0 !important;
            width: 100%;
            border-radius: 1rem 1rem 0 0;
            max-height: 80vh;
            overflow-y: auto;
            margin: 0;
            padding: 1rem 0;
            transform: none !important;
        }
        
        .dropdown-item {
            padding: 1rem 1.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function clearInput(fieldName) {
        const form = document.querySelector('#filterForm');
        const input = form.querySelector(`[name="${fieldName}"]`);
        input.value = '';
        form.submit();
    }
    
    function clearAllFilters() {
        window.location.href = "{{ route('karyawans.index') }}";
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Delete confirmation
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const nama = this.dataset.nama;
                const nik = this.dataset.nik;
                
                // Set data ke dalam modal
                document.getElementById('delete-nama').textContent = nama;
                document.getElementById('delete-nik').textContent = nik;
                document.getElementById('delete-form').action = `/karyawans/${id}`;
                
                // Tampilkan modal
                deleteModal.show();
            });
        });

        // Select all functionality
        const selectAllCheckbox = document.getElementById('selectAll');
        const employeeCheckboxes = document.querySelectorAll('.employee-checkbox');
        const massDeleteBtn = document.getElementById('massDeleteBtn');
        const massDeleteForm = document.getElementById('massDeleteForm');
        const massDeleteModal = new bootstrap.Modal(document.getElementById('massDeleteModal'));
        
        // Toggle individual checkboxes when "Select All" is clicked
        selectAllCheckbox.addEventListener('change', function() {
            employeeCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateMassDeleteButton();
        });
        
        // Update "Select All" when individual checkboxes change
        employeeCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAllCheckbox();
                updateMassDeleteButton();
            });
        });
        
        // Update the "Select All" checkbox based on individual selections
        function updateSelectAllCheckbox() {
            const checkedCount = document.querySelectorAll('.employee-checkbox:checked').length;
            const totalCount = employeeCheckboxes.length;
            
            if (checkedCount === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedCount === totalCount) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }
        
        // Show/hide the mass delete button based on selections
        function updateMassDeleteButton() {
            const checkedCount = document.querySelectorAll('.employee-checkbox:checked').length;
            
            if (checkedCount > 0) {
                massDeleteBtn.classList.remove('d-none');
                massDeleteBtn.innerHTML = `<i class="fas fa-trash-alt me-1"></i> Hapus ${checkedCount} Terpilih`;
            } else {
                massDeleteBtn.classList.add('d-none');
            }
        }
        
        // Handle mass delete button click
        massDeleteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const checkedBoxes = document.querySelectorAll('.employee-checkbox:checked');
            const selectedCount = checkedBoxes.length;
            const selectedNames = Array.from(checkedBoxes)
                .map(box => box.dataset.nama);
            
            if (checkedBoxes.length > 0) {
                // Set data ke dalam modal
                document.getElementById('selected-count').textContent = selectedCount;
                
                // Tambahkan nama karyawan ke list
                const namesList = document.getElementById('selected-names');
                namesList.innerHTML = '';
                selectedNames.forEach(name => {
                    const li = document.createElement('li');
                    li.textContent = name;
                    namesList.appendChild(li);
                });
                
                // Tampilkan modal
                massDeleteModal.show();
            }
        });
        
        // Handler untuk tombol konfirmasi penghapusan massal
        document.getElementById('confirm-mass-delete').addEventListener('click', function() {
            massDeleteForm.submit();
        });

        $('.select2-ajax').select2({
            theme: 'bootstrap-5',
            ajax: {
                url: '/api/karyawans/search',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.nama + ' - ' + item.nik + ' (' + item.departemen + ')',
                                id: item.id
                            }
                        })
                    };
                },
                cache: true
            },
            minimumInputLength: 2,
            placeholder: 'Cari karyawan berdasarkan nama atau NIK',
            allowClear: true
        });

        // Download template button click handler
        document.getElementById('download-template-btn').addEventListener('click', function(e) {
            e.preventDefault();
            // Perform the download action using JavaScript
            window.location.href = "{{ route('karyawans.template') }}";
        });
        
        // Template link in modal click handler
        document.getElementById('template-link-modal').addEventListener('click', function(e) {
            e.preventDefault();
            // Perform the download action using JavaScript
            window.location.href = "{{ route('karyawans.template') }}";
        });
    });
</script>
@endpush
@endsection