@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <h2 class="mb-0 fw-bold text-primary">
            <i class="fas fa-calendar-alt me-2"></i>Daftar Pengajuan Cuti
        </h2>
        <div class="btn-group-lg d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-file-import me-2"></i>Import/Export
            </button>
            <div class="dropdown-menu shadow border-0">
                <a href="{{ route('cutis.import') }}" class="dropdown-item d-flex align-items-center py-2">
                    <i class="fas fa-file-import me-2 text-info"></i> Import Data
                </a>
                <a href="{{ route('cutis.export') }}" class="dropdown-item d-flex align-items-center py-2">
                    <i class="fas fa-file-excel me-2 text-success"></i> Export Excel
                </a>
            </div>
            <a href="{{ route('cutis.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                <i class="fas fa-plus me-2"></i> Tambah Pengajuan Cuti
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-gradient-primary h-100">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Pengajuan</h6>
                            <h3 class="text-white mb-0 fw-bold">{{ \App\Models\Cuti::count() }}</h3>
                        </div>
                        <div class="icon-box bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-calendar-check text-white fa-2x"></i>
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
                            <h6 class="text-dark mb-1">Menunggu Persetujuan</h6>
                            <h3 class="text-dark mb-0 fw-bold">{{ \App\Models\Cuti::where('status_cuti', 'pending')->count() }}</h3>
                        </div>
                        <div class="icon-box bg-dark bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-clock text-dark fa-2x"></i>
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
                            <h6 class="text-white-50 mb-1">Disetujui</h6>
                            <h3 class="text-white mb-0 fw-bold">{{ \App\Models\Cuti::where('status_cuti', 'disetujui')->count() }}</h3>
                        </div>
                        <div class="icon-box bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-check-circle text-white fa-2x"></i>
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
                            <h6 class="text-white-50 mb-1">Ditolak</h6>
                            <h3 class="text-white mb-0 fw-bold">{{ \App\Models\Cuti::where('status_cuti', 'ditolak')->count() }}</h3>
                        </div>
                        <div class="icon-box bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-times-circle text-white fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white py-3">
            <button class="btn w-100 d-flex justify-content-between align-items-center p-0 border-0 bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-filter me-2 text-primary"></i>Filter Pengajuan Cuti
                </h5>
                <i class="fas fa-chevron-down text-primary"></i>
            </button>
        </div>
        <div class="collapse" id="filterCollapse">
            <div class="card-body">
                <form id="filter-form" action="{{ route('cutis.index') }}" method="GET" class="row g-3">
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">Status Cuti</label>
                        <select class="form-select" name="status_cuti">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status_cuti') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="ditolak" {{ request('status_cuti') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            <option value="disetujui" {{ request('status_cuti') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">Jenis Cuti</label>
                        <select class="form-select" name="jenis_cuti_id">
                            <option value="">Semua Jenis</option>
                            @foreach($jenisCutis as $jenisCuti)
                                <option value="{{ $jenisCuti->id }}" {{ request('jenis_cuti_id') == $jenisCuti->id ? 'selected' : '' }}>
                                    {{ $jenisCuti->nama_jenis }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">Transportasi</label>
                        <select class="form-select" name="transportasi_id">
                            <option value="">Semua Transportasi</option>
                            <option value="tanpa" {{ request('transportasi_id') == 'tanpa' ? 'selected' : '' }}>Tanpa Transportasi</option>
                            @foreach($transportasis as $transportasi)
                                <option value="{{ $transportasi->id }}" {{ request('transportasi_id') == $transportasi->id ? 'selected' : '' }}>
                                    {{ $transportasi->jenis }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">Cari Karyawan</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control" name="search" placeholder="Nama/NIK karyawan..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Rentang Tanggal</label>
                        <div class="d-flex gap-2">
                            <div class="input-group flex-grow-1">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-calendar-day text-muted"></i>
                                </span>
                                <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}" placeholder="Tanggal mulai">
                            </div>
                            <div class="input-group flex-grow-1">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-calendar-day text-muted"></i>
                                </span>
                                <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}" placeholder="Tanggal selesai">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="button" id="reset-filter" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-undo me-2"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-filter me-2"></i> Terapkan Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Batch Action Buttons -->
    <div id="batch-actions" class="mb-3 d-none">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-2">
                        <div id="batch-approve-container">
                            <button id="batch-approve-btn" class="btn btn-success rounded-pill px-3" type="button" onclick="batchApprove()">
                                <i class="fas fa-check-circle me-2"></i> Setujui Terpilih
                            </button>
                        </div>
                        <button id="batch-delete-btn" class="btn btn-danger rounded-pill px-3" type="button" onclick="batchDelete()">
                            <i class="fas fa-trash me-2"></i> Hapus Terpilih
                        </button>
                        <button id="clear-selection" class="btn btn-light rounded-pill px-3" type="button" onclick="clearAllSelections()">
                            <i class="fas fa-times me-2"></i> Batal Pilih
                        </button>
                    </div>
                    <div class="badge bg-primary rounded-pill px-3 py-2 d-flex align-items-center">
                        <i class="fas fa-check-square me-2"></i>
                        <span id="selected-count">0</span> item terpilih
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Pengajuan Cuti</h5>
            <span class="badge bg-primary">{{ $cutis->total() }} Data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-sm" id="cutiTable">
                    <thead class="table-light">
                        <tr>
                            <th class="px-2 py-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="select-all" onclick="handleSelectAll(this)">
                                </div>
                            </th>
                            <th class="px-2 py-2">
                                <a href="{{ route('cutis.index', array_merge(request()->except(['sort', 'direction']), [
                                    'sort' => 'id',
                                    'direction' => (request('sort') == 'id' && request('direction') == 'asc') ? 'desc' : 'asc'
                                ])) }}" class="text-dark sortable-header">
                                    # 
                                    @if(request('sort') == 'id')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted opacity-50"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-2 py-2">Karyawan</th>
                            <th class="px-2 py-2">Jenis Cuti</th>
                            <th class="px-2 py-2">
                                <a href="{{ route('cutis.index', array_merge(request()->except(['sort', 'direction']), [
                                    'sort' => 'tanggal_mulai',
                                    'direction' => (request('sort') == 'tanggal_mulai' && request('direction') == 'asc') ? 'desc' : 'asc'
                                ])) }}" class="text-dark sortable-header">
                                    Tanggal 
                                    @if(request('sort') == 'tanggal_mulai')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted opacity-50"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-2 py-2">
                                <a href="{{ route('cutis.index', array_merge(request()->except(['sort', 'direction']), [
                                    'sort' => 'lama_hari',
                                    'direction' => (request('sort') == 'lama_hari' && request('direction') == 'asc') ? 'desc' : 'asc'
                                ])) }}" class="text-dark sortable-header">
                                    Lama 
                                    @if(request('sort') == 'lama_hari')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted opacity-50"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-2 py-2">Transportasi</th>
                            <th class="px-2 py-2">Status Memo</th>
                            <th class="px-2 py-2">
                                <a href="{{ route('cutis.index', array_merge(request()->except(['sort', 'direction']), [
                                    'sort' => 'status_cuti',
                                    'direction' => (request('sort') == 'status_cuti' && request('direction') == 'asc') ? 'desc' : 'asc'
                                ])) }}" class="text-dark sortable-header">
                                    Status 
                                    @if(request('sort') == 'status_cuti')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted opacity-50"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="px-2 py-2 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cutis as $cuti)
                            <tr>
                                <td class="px-2 py-2">
                                    <div class="form-check">
                                        <input class="form-check-input cuti-checkbox" type="checkbox"
                                            id="cuti-checkbox-{{ $cuti->id }}"
                                            data-id="{{ $cuti->id }}"
                                            data-status="{{ $cuti->status_cuti }}"
                                            onclick="handleCheckboxClick(this)">
                                    </div>
                                </td>
                                <td class="px-2 py-2">{{ $loop->iteration }}</td>
                                <td class="px-2 py-2">
                                    <div>
                                        <span class="fw-medium">{{ $cuti->karyawan->nama }}</span>
                                        <div class="small text-muted">{{ $cuti->karyawan->departemen ?? 'Dept. Tidak Tersedia' }}
                                        @if($cuti->karyawan->poh)
                                            | POH: {{ ucfirst($cuti->karyawan->poh) }}
                                        @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 py-2">
                                    @if($cuti->cutiDetails->count() > 0)
                                        @foreach($cuti->cutiDetails as $detail)
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary rounded-pill d-inline-block mb-1">
                                                {{ $detail->jenisCuti->nama_jenis }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary rounded-pill">
                                            {{ $cuti->jenisCuti->nama_jenis }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-2 py-2">
                                    <div class="d-flex align-items-center">
                                        <i class="far fa-calendar-alt me-1 text-primary"></i>
                                        <div>
                                            <div class="small">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') }}</div>
                                            <div class="small text-muted">s/d {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 py-2">
                                    <span class="badge bg-light text-dark border rounded-pill">
                                        <i class="fas fa-calendar-day me-1 text-primary"></i>
                                        {{ $cuti->lama_hari }} hari
                                    </span>
                                </td>
                                <td class="px-2 py-2">
                                    @if($cuti->transportasiDetails->count() > 0)
                                        @php
                                            $uniqueTransportasi = $cuti->transportasiDetails->pluck('transportasi.jenis')->unique()->values()->toArray();
                                        @endphp
                                        @foreach($uniqueTransportasi as $transportasi)
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill d-inline-block mb-1">
                                                @if(strtolower($transportasi) == 'pesawat')
                                                    <i class="fas fa-plane me-1"></i>
                                                @elseif(strtolower($transportasi) == 'kereta')
                                                    <i class="fas fa-train me-1"></i>
                                                @elseif(strtolower($transportasi) == 'bus')
                                                    <i class="fas fa-bus me-1"></i>
                                                @else
                                                    <i class="fas fa-car me-1"></i>
                                                @endif
                                                {{ $transportasi }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill">
                                            <i class="fas fa-ban me-1"></i> Tidak Ada
                                        </span>
                                    @endif
                                </td>
                                <td class="px-2 py-2">
                                    @php
                                        $memoStatus = $cuti->memo_kompensasi_status;
                                    @endphp

                                    @if($memoStatus === true)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i> Sudah
                                        </span>
                                        @if($cuti->memo_kompensasi_nomor)
                                            <div class="small text-muted mt-1">{{ $cuti->memo_kompensasi_nomor }}</div>
                                        @endif
                                    @elseif($memoStatus === false)
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Belum
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill">
                                            <i class="fas fa-minus me-1"></i> Tidak Perlu
                                        </span>
                                    @endif
                                </td>
                                <td class="px-2 py-2">
                                    @if($cuti->status_cuti == 'pending')
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill">
                                            <i class="fas fa-clock me-1"></i> Pending
                                        </span>
                                    @elseif($cuti->status_cuti == 'disetujui')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i> Disetujui
                                        </span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill">
                                            <i class="fas fa-times-circle me-1"></i> Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-2 py-2 text-end action-column">
                                    <div class="dropdown action-dropdown">
                                        <button class="btn btn-light btn-sm dropdown-toggle" type="button" id="dropdownMenuButton{{ $cuti->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="dropdownMenuButton{{ $cuti->id }}">
                                            <li><a class="dropdown-item d-flex align-items-center" href="{{ route('cutis.show', $cuti->id) }}">
                                                <i class="fas fa-eye me-2 text-primary"></i> Detail
                                            </a></li>
                                            <li><a class="dropdown-item d-flex align-items-center" href="{{ route('cutis.edit', $cuti->id) }}">
                                                <i class="fas fa-edit me-2 text-info"></i> Edit
                                            </a></li>
                                            @if($cuti->status_cuti == 'pending')
                                                <li>
                                                    <button type="button" class="dropdown-item d-flex align-items-center approve-btn"
                                                            data-id="{{ $cuti->id }}"
                                                            data-nama="{{ $cuti->karyawan->nama }}"
                                                            data-tanggal="{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') }}">
                                                        <i class="fas fa-check me-2 text-success"></i> Setujui
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item d-flex align-items-center reject-btn"
                                                            data-id="{{ $cuti->id }}"
                                                            data-nama="{{ $cuti->karyawan->nama }}"
                                                            data-tanggal="{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') }}">
                                                        <i class="fas fa-times me-2 text-danger"></i> Tolak
                                                    </button>
                                                </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button type="button" class="dropdown-item d-flex align-items-center text-danger delete-btn"
                                                        data-id="{{ $cuti->id }}"
                                                        data-nama="{{ $cuti->karyawan->nama }}"
                                                        data-tanggal="{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') }}">
                                                    <i class="fas fa-trash me-2 text-danger"></i> Hapus
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="10" class="text-center py-3 empty-message">
                                    <img src="{{ asset('images/empty-calendar.svg') }}" alt="Tidak ada data" class="mb-2" style="height: 120px;" onerror="this.src='https://cdn-icons-png.flaticon.com/512/5445/5445197.png'; this.style.height='100px';">
                                    <h6 class="text-muted mt-2">Tidak ada data pengajuan cuti</h6>
                                    <p class="text-muted small">Tidak ada pengajuan cuti yang tersedia atau sesuai dengan filter</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-3 py-2 d-flex justify-content-between align-items-center border-top">
                <div class="text-muted small">
                    Menampilkan {{ $cutis->firstItem() ?? 0 }} - {{ $cutis->lastItem() ?? 0 }} dari {{ $cutis->total() }} data
                </div>
                <div class="d-flex align-items-center">
                    <div class="me-2">
                        <select class="form-select form-select-sm py-1" id="per-page-selector" aria-label="Tampilkan per halaman" style="font-size: 0.75rem;">
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 baris</option>
                            <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30 baris</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris</option>
                            <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>Semua data</option>
                        </select>
                    </div>
                    <div>
                        {{ $cutis->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden forms for batch actions -->
<form id="batch-approve-form" action="{{ route('cutis.batch-approve-post') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" id="approve-ids" name="approve_ids">
</form>

<form id="batch-delete-form" action="{{ route('cutis.batch-delete-post') }}" method="POST" style="display: none;" onsubmit="return true;">
    @csrf
    <input type="hidden" id="delete-ids" name="delete_ids">
</form>

<!-- Modal untuk Batch Delete -->
<div class="modal fade" id="batchDeleteModal" tabindex="-1" aria-labelledby="batchDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="batchDeleteModalLabel">
                    <i class="fas fa-trash me-2"></i> Hapus Pengajuan Cuti
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="avatar bg-danger bg-opacity-10 text-danger mb-3 mx-auto" style="width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <h4 class="fw-bold">Konfirmasi Penghapusan</h4>
                    <p class="text-muted">Anda akan menghapus <span id="delete-count-text" class="fw-bold text-danger">0</span> pengajuan cuti terpilih. Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i> Semua data terkait pengajuan cuti ini juga akan dihapus termasuk data transportasi dan detail cuti.
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Batal
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirm-batch-delete">
                    <i class="fas fa-trash me-2"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Batch Approve -->
<div class="modal fade" id="batchApproveModal" tabindex="-1" aria-labelledby="batchApproveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title" id="batchApproveModalLabel">
                    <i class="fas fa-check-circle me-2"></i> Setujui Pengajuan Cuti
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="avatar bg-success bg-opacity-10 text-success mb-3 mx-auto" style="width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check-double fa-2x"></i>
                    </div>
                    <h4 class="fw-bold">Konfirmasi Persetujuan</h4>
                    <p class="text-muted">Anda akan menyetujui <span id="approve-count-text" class="fw-bold text-success">0</span> pengajuan cuti terpilih.</p>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Status pengajuan cuti akan diubah menjadi "Disetujui" dan tidak dapat diubah kembali ke status "Pending".
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Batal
                </button>
                <button type="button" class="btn btn-success px-4" id="confirm-batch-approve">
                    <i class="fas fa-check-circle me-2"></i> Ya, Setujui
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Single Delete -->
<div class="modal fade" id="singleDeleteModal" tabindex="-1" aria-labelledby="singleDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="singleDeleteModalLabel">
                    <i class="fas fa-trash me-2"></i> Hapus Pengajuan Cuti
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="avatar bg-danger bg-opacity-10 text-danger mb-3 mx-auto" style="width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <h4 class="fw-bold">Konfirmasi Penghapusan</h4>
                    <p class="text-muted">Apakah Anda yakin ingin menghapus pengajuan cuti ini? Tindakan ini tidak dapat dibatalkan.</p>
                </div>

                <div class="bg-light p-3 rounded-3 border-start border-danger border-4 mb-3">
                    <p class="mb-2"><strong>Nama:</strong> <span id="delete-nama"></span></p>
                    <p class="mb-0"><strong>Tanggal:</strong> <span id="delete-tanggal"></span></p>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i> Semua data terkait pengajuan cuti ini juga akan dihapus termasuk data transportasi dan detail cuti.
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Batal
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirm-single-delete">
                    <i class="fas fa-trash me-2"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Single Approve -->
<div class="modal fade" id="singleApproveModal" tabindex="-1" aria-labelledby="singleApproveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title" id="singleApproveModalLabel">
                    <i class="fas fa-check-circle me-2"></i> Setujui Pengajuan Cuti
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="avatar bg-success bg-opacity-10 text-success mb-3 mx-auto" style="width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-check-double fa-2x"></i>
                    </div>
                    <h4 class="fw-bold">Konfirmasi Persetujuan</h4>
                    <p class="text-muted">Apakah Anda yakin ingin menyetujui pengajuan cuti ini?</p>
                </div>

                <div class="bg-light p-3 rounded-3 border-start border-success border-4 mb-3">
                    <p class="mb-2"><strong>Nama:</strong> <span id="approve-nama"></span></p>
                    <p class="mb-0"><strong>Tanggal:</strong> <span id="approve-tanggal"></span></p>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Status pengajuan cuti akan diubah menjadi "Disetujui" dan tidak dapat diubah kembali ke status "Pending".
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Batal
                </button>
                <button type="button" class="btn btn-success px-4" id="confirm-single-approve">
                    <i class="fas fa-check me-2"></i> Ya, Setujui
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Single Reject -->
<div class="modal fade" id="singleRejectModal" tabindex="-1" aria-labelledby="singleRejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="singleRejectModalLabel">
                    <i class="fas fa-times-circle me-2"></i> Tolak Pengajuan Cuti
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="avatar bg-danger bg-opacity-10 text-danger mb-3 mx-auto" style="width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-times fa-2x"></i>
                    </div>
                    <h4 class="fw-bold">Konfirmasi Penolakan</h4>
                    <p class="text-muted">Apakah Anda yakin ingin menolak pengajuan cuti ini?</p>
                </div>

                <div class="bg-light p-3 rounded-3 border-start border-danger border-4 mb-3">
                    <p class="mb-2"><strong>Nama:</strong> <span id="reject-nama"></span></p>
                    <p class="mb-0"><strong>Tanggal:</strong> <span id="reject-tanggal"></span></p>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i> Status pengajuan cuti akan diubah menjadi "Ditolak" dan tidak dapat diubah kembali ke status "Pending".
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Batal
                </button>
                <button type="button" class="btn btn-danger px-4" id="confirm-single-reject">
                    <i class="fas fa-times me-2"></i> Ya, Tolak
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Form untuk single delete -->
<form id="single-delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Form untuk single approve -->
<form id="single-approve-form" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
</form>

<!-- Form untuk single reject -->
<form id="single-reject-form" method="POST" style="display: none;">
    @csrf
    @method('PATCH')
</form>

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

    /* Custom colors for POH */
    .bg-purple {
        background-color: #6f42c1 !important;
    }

    .text-purple {
        color: #6f42c1 !important;
    }

    .border-purple {
        border-color: #6f42c1 !important;
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

    /* Action button styling */
    .action-column {
        width: 50px;
    }

    .empty-message {
        color: #6c757d;
    }

    .badge {
        font-weight: 500;
        font-size: 0.75rem;
        padding: 0.3em 0.7em;
    }

    /* Table scrolling */
    .table-scroll-container {
        max-height: 640px;
        overflow-y: auto;
        scrollbar-width: thin;
        padding-bottom: 250px; /* Menambahkan padding bawah untuk memastikan dropdown tidak terpotong */
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

    /* Checkbox styling */
    .form-check {
        margin: 0;
    }

    .form-check-input {
        cursor: pointer;
    }

    /* Material design checkbox */
    .form-check-input {
        width: 18px;
        height: 18px;
        margin-top: 0.25em;
        margin-left: -1.5em;
        background-color: #fff;
        border: 2px solid #adb5bd;
        border-radius: 3px;
        transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
    }

    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    /* Avatar styling */
    .avatar {
        font-weight: bold;
    }

    /* Dropdown styling */
    .dropdown-item {
        padding: 0.5rem 1rem;
    }

    .dropdown-menu {
        border-radius: 0.5rem;
        overflow: hidden;
    }

    /* Filter collapse styling */
    .collapse {
        transition: all 0.2s ease-out;
    }

    .card-header button {
        outline: none !important;
    }

    .card-header button:hover {
        color: var(--bs-primary);
    }

    .card-header button:hover .fa-chevron-down {
        color: var(--bs-primary);
    }

    .fa-chevron-down {
        transition: transform 0.2s ease-out;
    }

    .card-header {
        border-bottom: 0;
    }

    .collapse.show + .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
</style>
@endpush

@push('scripts')
<script>
// Checkbox & batch action functionality
const cutiTable = document.getElementById('cutiTable');
const cutiCheckboxes = cutiTable.querySelectorAll('.cuti-checkbox');
const batchActions = document.getElementById('batch-actions');
const selectedCount = document.getElementById('selected-count');
const batchDeleteModal = new bootstrap.Modal(document.getElementById('batchDeleteModal'));
const batchApproveModal = new bootstrap.Modal(document.getElementById('batchApproveModal'));
const singleDeleteModal = new bootstrap.Modal(document.getElementById('singleDeleteModal'));
const singleApproveModal = new bootstrap.Modal(document.getElementById('singleApproveModal'));
const singleRejectModal = new bootstrap.Modal(document.getElementById('singleRejectModal'));

function handleCheckboxClick(checkbox) {
    const id = checkbox.getAttribute('data-id');
    const isChecked = checkbox.checked;

    if (isChecked) {
        checkbox.closest('tr').classList.add('table-primary');
    } else {
        checkbox.closest('tr').classList.remove('table-primary');
    }

    updateBatchActions();
}

function handleSelectAll(selectAllCheckbox) {
    const isChecked = selectAllCheckbox.checked;

    cutiCheckboxes.forEach(checkbox => {
        checkbox.checked = isChecked;
        checkbox.closest('tr').classList.toggle('table-primary', isChecked);
    });

    updateBatchActions();
}

function updateBatchActions() {
    const checkedCheckboxes = cutiTable.querySelectorAll('.cuti-checkbox:checked');
    const checkedCount = checkedCheckboxes.length;

    selectedCount.textContent = checkedCount;

    if (checkedCount > 0) {
        batchActions.classList.remove('d-none');
    } else {
        batchActions.classList.add('d-none');
    }
}

function batchApprove() {
    const selectedIds = Array.from(cutiTable.querySelectorAll('.cuti-checkbox:checked')).map(checkbox => checkbox.getAttribute('data-id'));
    if (selectedIds.length > 0) {
        // Memperbarui counter dalam modal
        document.getElementById('approve-count-text').textContent = selectedIds.length;

        // Menampilkan modal approve
        batchApproveModal.show();

        // Menambahkan event listener untuk tombol konfirmasi
        document.getElementById('confirm-batch-approve').addEventListener('click', function() {
            const form = document.getElementById('batch-approve-form');
            const approveIdsInput = document.getElementById('approve-ids');
            approveIdsInput.value = selectedIds.join(',');

            // Menutup modal dan mengirimkan form
            batchApproveModal.hide();
            form.submit();
        });
    }
}

function batchDelete() {
    const selectedIds = Array.from(cutiTable.querySelectorAll('.cuti-checkbox:checked')).map(checkbox => checkbox.getAttribute('data-id'));
    if (selectedIds.length > 0) {
        // Memperbarui counter dalam modal
        document.getElementById('delete-count-text').textContent = selectedIds.length;

        // Menampilkan modal delete
        batchDeleteModal.show();

        // Menambahkan event listener untuk tombol konfirmasi
        document.getElementById('confirm-batch-delete').addEventListener('click', function() {
            const form = document.getElementById('batch-delete-form');
            const deleteIdsInput = document.getElementById('delete-ids');
            deleteIdsInput.value = selectedIds.join(',');

            // Menutup modal dan mengirimkan form
            batchDeleteModal.hide();
            form.submit();
        });
    }
}

function clearAllSelections() {
    cutiCheckboxes.forEach(checkbox => {
        checkbox.checked = false;
        checkbox.closest('tr').classList.remove('table-primary');
    });

    updateBatchActions();
}

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
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

    const filterCollapse = document.getElementById('filterCollapse');
    const filterButton = filterCollapse.previousElementSibling.querySelector('button');
    const filterIcon = filterButton.querySelector('.fa-chevron-down');
    const resetButton = document.getElementById('reset-filter');

    // Check localStorage for saved state
    const isExpanded = localStorage.getItem('filterCollapsed') === 'true';

    // Initialize collapse state
    if (isExpanded) {
        filterCollapse.classList.add('show');
        filterIcon.style.transform = 'rotate(180deg)';
    }

    // Add transition
    filterCollapse.style.transition = 'all 0.2s ease-out';
    filterIcon.style.transition = 'transform 0.2s ease-out';

    // Listen for collapse events
    filterCollapse.addEventListener('show.bs.collapse', function() {
        filterIcon.style.transform = 'rotate(180deg)';
        localStorage.setItem('filterCollapsed', 'true');
    });

    filterCollapse.addEventListener('hide.bs.collapse', function() {
        filterIcon.style.transform = 'rotate(0deg)';
        localStorage.setItem('filterCollapsed', 'false');
    });

    // Reset filter handler
    resetButton.addEventListener('click', function() {
        const form = document.getElementById('filter-form');
        const inputs = form.querySelectorAll('input, select');

        // Reset all inputs and selects
        inputs.forEach(input => {
            if (input.type === 'text' || input.type === 'date') {
                input.value = '';
            } else if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            }
        });

        // Submit form
        form.submit();
    });

    // Show collapse if there are active filters
    const urlParams = new URLSearchParams(window.location.search);
    const hasFilters = ['status_cuti', 'jenis_cuti_id', 'transportasi_id', 'search', 'start_date', 'end_date']
        .some(param => urlParams.has(param) && urlParams.get(param) !== '');

    if (hasFilters && !isExpanded) {
        const bsCollapse = new bootstrap.Collapse(filterCollapse);
        bsCollapse.show();
    }

    // Inisialisasi event listener untuk tombol konfirmasi modal
    // Gunakan event handler sekali untuk menghindari multiple event binding
    document.getElementById('confirm-batch-delete').onclick = function() {
        const selectedIds = Array.from(cutiTable.querySelectorAll('.cuti-checkbox:checked')).map(checkbox => checkbox.getAttribute('data-id'));
        const form = document.getElementById('batch-delete-form');
        const deleteIdsInput = document.getElementById('delete-ids');
        deleteIdsInput.value = selectedIds.join(',');
        batchDeleteModal.hide();
        form.submit();
    };

    document.getElementById('confirm-batch-approve').onclick = function() {
        const selectedIds = Array.from(cutiTable.querySelectorAll('.cuti-checkbox:checked')).map(checkbox => checkbox.getAttribute('data-id'));
        const form = document.getElementById('batch-approve-form');
        const approveIdsInput = document.getElementById('approve-ids');
        approveIdsInput.value = selectedIds.join(',');
        batchApproveModal.hide();
        form.submit();
    };

    // Tangani tombol delete di setiap baris
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const tanggal = this.getAttribute('data-tanggal');

            // Update content in the single delete modal
            document.getElementById('delete-nama').textContent = nama;
            document.getElementById('delete-tanggal').textContent = tanggal;

            singleDeleteModal.show();

            document.getElementById('confirm-single-delete').onclick = function() {
                const form = document.getElementById('single-delete-form');
                form.action = '/cutis/' + id;
                singleDeleteModal.hide();
                form.submit();
            }
        });
    });

    // Tangani tombol approve di setiap baris
    const approveButtons = document.querySelectorAll('.approve-btn');
    approveButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const tanggal = this.getAttribute('data-tanggal');

            // Update content in the single approve modal
            document.getElementById('approve-nama').textContent = nama;
            document.getElementById('approve-tanggal').textContent = tanggal;

            singleApproveModal.show();

            document.getElementById('confirm-single-approve').onclick = function() {
                const form = document.getElementById('single-approve-form');
                form.action = '/cutis/' + id + '/approve';
                singleApproveModal.hide();
                form.submit();
            }
        });
    });

    // Tangani tombol reject di setiap baris
    const rejectButtons = document.querySelectorAll('.reject-btn');
    rejectButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const tanggal = this.getAttribute('data-tanggal');

            // Update content in the single reject modal
            document.getElementById('reject-nama').textContent = nama;
            document.getElementById('reject-tanggal').textContent = tanggal;

            singleRejectModal.show();

            document.getElementById('confirm-single-reject').onclick = function() {
                const form = document.getElementById('single-reject-form');
                form.action = '/cutis/' + id + '/reject';
                singleRejectModal.hide();
                form.submit();
            }
        });
    });
});
</script>
@endpush