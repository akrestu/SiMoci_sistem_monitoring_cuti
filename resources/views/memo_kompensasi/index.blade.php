@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold"><i class="fas fa-file-alt me-2"></i>Monitoring Memo Kompensasi</h4>
    </div>

    <!-- Dashboard Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-1">Total Memo Diperlukan</h6>
                            <h3 class="mb-0">{{ $totalMemoCount }}</h3>
                        </div>
                        <div class="icon-box bg-light text-primary rounded p-3">
                            <i class="fas fa-file-alt fa-fw"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-1">Sudah Diajukan</h6>
                            <h3 class="mb-0">{{ $sudahDiajukanCount }}</h3>
                            <small class="text-success">
                                @if($totalMemoCount > 0)
                                    {{ round($persentaseSudah) }}% dari total
                                @else
                                    0% dari total
                                @endif
                            </small>
                        </div>
                        <div class="icon-box bg-light text-success rounded p-3">
                            <i class="fas fa-check-circle fa-fw"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-1">Belum Diajukan</h6>
                            <h3 class="mb-0">{{ $belumDiajukanCount }}</h3>
                            <small class="text-warning">
                                @if($totalMemoCount > 0)
                                    {{ round($persentaseBelum) }}% dari total
                                @else
                                    0% dari total
                                @endif
                            </small>
                        </div>
                        <div class="icon-box bg-light text-warning rounded p-3">
                            <i class="fas fa-exclamation-triangle fa-fw"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-muted mb-1">Memo Bulan Ini</h6>
                            <h3 class="mb-0">{{ $bulanIniCount }}</h3>
                            <small class="text-info">{{ \Carbon\Carbon::now()->format('F Y') }}</small>
                        </div>
                        <div class="icon-box bg-light text-info rounded p-3">
                            <i class="fas fa-calendar-alt fa-fw"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="card-title">Status Pengajuan Memo Kompensasi</h6>

            <div class="position-relative mb-1">
                <div class="progress" style="height: 30px; border-radius: 15px;">
                    <div class="progress-bar bg-success" role="progressbar"
                         style="width: {{ $persentaseSudah }}%;"
                         aria-valuenow="{{ $persentaseSudah }}"
                         aria-valuemin="0"
                         aria-valuemax="100">
                    </div>
                    <div class="progress-bar bg-warning" role="progressbar"
                         style="width: {{ $persentaseBelum }}%;"
                         aria-valuenow="{{ $persentaseBelum }}"
                         aria-valuemin="0"
                         aria-valuemax="100">
                    </div>
                </div>

                @if($persentaseSudah > 0)
                    <div class="position-absolute" style="top: 5px; left: {{ max(2, $persentaseSudah/2) }}%;">
                        <span class="badge bg-success">{{ round($persentaseSudah) }}%</span>
                    </div>
                @endif

                @if($persentaseBelum > 0)
                    <div class="position-absolute" style="top: 5px; right: {{ max(2, $persentaseBelum/2) }}%; {{ $persentaseSudah > 85 ? 'display:none;' : '' }}">
                        <span class="badge bg-warning text-dark">{{ round($persentaseBelum) }}%</span>
                    </div>
                @endif
            </div>

            <div class="d-flex justify-content-between mt-2">
                <div class="legend">
                    <span class="badge bg-success me-2">&nbsp;</span>
                    <span class="me-3">Sudah Diajukan ({{ $sudahDiajukanCount }})</span>
                    <span class="badge bg-warning me-2">&nbsp;</span>
                    <span>Belum Diajukan ({{ $belumDiajukanCount }})</span>
                </div>
                <small class="text-muted">Update terakhir: {{ now()->format('d M Y') }}</small>
            </div>
        </div>
    </div>

    <!-- Filter Panel -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <button class="btn btn-link text-decoration-none p-0 text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                <i class="fas fa-filter me-2"></i> <strong>Filter</strong>
            </button>
        </div>
        <div class="collapse" id="filterCollapse">
            <div class="card-body">
                <form action="{{ route('memo-kompensasi.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label">Cari Karyawan</label>
                            <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Nama atau NIK...">
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label">Status Memo</label>
                            <select class="form-select" name="status_memo">
                                <option value="">Semua Status</option>
                                <option value="sudah" {{ request('status_memo') == 'sudah' ? 'selected' : '' }}>Sudah Diajukan</option>
                                <option value="belum" {{ request('status_memo') == 'belum' ? 'selected' : '' }}>Belum Diajukan</option>
                            </select>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="d-flex mt-4">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-search me-1"></i> Terapkan Filter
                                </button>
                                <a href="{{ route('memo-kompensasi.index') }}" class="btn btn-light">
                                    <i class="fas fa-redo me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Pengajuan Memo Kompensasi</h5>
            <span class="badge bg-primary">{{ $totalMemoCount }} Data</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="memoKompensasiTable">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Nama Karyawan</th>
                            <th class="px-4 py-3">NIK</th>
                            <th class="px-4 py-3">Departemen</th>
                            <th class="px-4 py-3">Jenis Cuti</th>
                            <th class="px-4 py-3">Tanggal Cuti</th>
                            <th class="px-4 py-3">Status Memo</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cutis as $index => $cuti)
                            <tr>
                                <td class="px-4 py-3">{{ $cutis->firstItem() + $index }}</td>
                                <td class="px-4 py-3">{{ $cuti->karyawan->nama }}</td>
                                <td class="px-4 py-3">{{ $cuti->karyawan->nik }}</td>
                                <td class="px-4 py-3">{{ $cuti->karyawan->departemen }}</td>
                                <td class="px-4 py-3">
                                    @if($cuti->cutiDetails->count() > 0)
                                        @foreach($cuti->cutiDetails as $detail)
                                            <span class="badge bg-info">{{ $detail->jenisCuti->nama_jenis }} ({{ $detail->jumlah_hari }} hari)</span>
                                        @endforeach
                                    @else
                                        <span class="badge bg-info">{{ $cuti->jenisCuti->nama_jenis }} ({{ $cuti->lama_hari }} hari)</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    {{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d/m/Y') }}
                                    s/d
                                    {{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d/m/Y') }}
                                    <small class="d-block text-muted">{{ $cuti->lama_hari }} hari</small>
                                </td>
                                <td class="px-4 py-3">
                                    @if($cuti->memo_kompensasi_status && !empty($cuti->memo_kompensasi_nomor) && !empty($cuti->memo_kompensasi_tanggal))
                                        <span class="badge bg-success">Sudah Diajukan (true)</span>
                                        <small class="d-block mt-1">No: {{ $cuti->memo_kompensasi_nomor }}</small>
                                        <small class="d-block">Tgl: {{ \Carbon\Carbon::parse($cuti->memo_kompensasi_tanggal)->format('d/m/Y') }}</small>
                                    @elseif($cuti->memo_kompensasi_status === false)
                                        <span class="badge bg-warning text-dark">Belum Diajukan (false)</span>
                                    @elseif($cuti->memo_kompensasi_status === null)
                                        <span class="badge bg-secondary">Tidak Perlu (null)</span>
                                    @else
                                        <span class="badge bg-info">Status: {{ var_export($cuti->memo_kompensasi_status, true) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <button type="button" class="btn btn-sm btn-primary update-memo-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#updateMemoModal"
                                            data-id="{{ $cuti->id }}"
                                            data-nama="{{ $cuti->karyawan->nama }}"
                                            data-nik="{{ $cuti->karyawan->nik }}"
                                            data-status="{{ $cuti->memo_kompensasi_status ? '1' : '0' }}"
                                            data-nomor="{{ $cuti->memo_kompensasi_nomor }}"
                                            data-tanggal="{{ $cuti->memo_kompensasi_tanggal }}">
                                        <i class="fas fa-edit me-1"></i> Update Status
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-row">
                                <td colspan="8" class="text-center py-5">
                                    <div class="py-5">
                                        <i class="fas fa-file-alt fa-3x mb-3 text-muted"></i>
                                        <p class="mt-3 mb-0 text-muted">Tidak ada data pengajuan memo kompensasi</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Menampilkan {{ $cutis->firstItem() ?? 0 }} - {{ $cutis->lastItem() ?? 0 }} dari {{ $totalMemoCount }} data
                </div>
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <select class="form-select form-select-sm" id="per-page-selector" aria-label="Tampilkan per halaman">
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

<!-- Modal Update Memo -->
<div class="modal fade" id="updateMemoModal" tabindex="-1" aria-labelledby="updateMemoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateMemoModalLabel">Update Status Memo Kompensasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="update-memo-form" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-4 p-3 bg-light rounded">
                        <p class="mb-1"><strong>Nama Karyawan:</strong> <span id="modal-nama"></span></p>
                        <p class="mb-0"><strong>NIK:</strong> <span id="modal-nik"></span></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status Memo Kompensasi</label>
                        <div class="d-flex">
                            <div class="form-check me-4">
                                <input class="form-check-input" type="radio" name="memo_kompensasi_status" id="status-belum" value="0">
                                <label class="form-check-label" for="status-belum">
                                    Belum Diajukan
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="memo_kompensasi_status" id="status-sudah" value="1">
                                <label class="form-check-label" for="status-sudah">
                                    Sudah Diajukan
                                </label>
                            </div>
                        </div>
                    </div>

                    <div id="detail-memo-container" style="display: none;">
                        <div class="mb-3">
                            <label for="memo_kompensasi_nomor" class="form-label">Nomor Memo</label>
                            <input type="text" class="form-control" id="memo_kompensasi_nomor" name="memo_kompensasi_nomor">
                        </div>
                        <div class="mb-3">
                            <label for="memo_kompensasi_tanggal" class="form-label">Tanggal Memo</label>
                            <input type="date" class="form-control" id="memo_kompensasi_tanggal" name="memo_kompensasi_tanggal">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .icon-box {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .progress {
        border-radius: 20px;
        overflow: hidden;
    }

    .progress-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        font-size: 0.9rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show/hide filter collapse
        const urlParams = new URLSearchParams(window.location.search);
        const hasFilters = ['search', 'status_memo', 'start_date', 'end_date'].some(param =>
            urlParams.has(param) && urlParams.get(param) !== '');

        if (hasFilters) {
            const bsCollapse = new bootstrap.Collapse(document.getElementById('filterCollapse'));
            bsCollapse.show();
        }

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

        // Update memo modal functionality
        const updateMemoModal = document.getElementById('updateMemoModal');
        const updateButtons = document.querySelectorAll('.update-memo-btn');
        const updateForm = document.getElementById('update-memo-form');
        const statusBelum = document.getElementById('status-belum');
        const statusSudah = document.getElementById('status-sudah');
        const detailContainer = document.getElementById('detail-memo-container');

        updateButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                const nik = this.getAttribute('data-nik');
                const status = this.getAttribute('data-status');
                const nomor = this.getAttribute('data-nomor');
                const tanggal = this.getAttribute('data-tanggal');

                // Set data to modal
                document.getElementById('modal-nama').textContent = nama;
                document.getElementById('modal-nik').textContent = nik;

                // Set form action
                updateForm.action = `/memo-kompensasi/${id}/update-status`;

                // Set current status
                if (status === '1') {
                    statusSudah.checked = true;
                    detailContainer.style.display = 'block';
                } else {
                    statusBelum.checked = true;
                    detailContainer.style.display = 'none';
                }

                // Set current values
                document.getElementById('memo_kompensasi_nomor').value = nomor || '';
                document.getElementById('memo_kompensasi_tanggal').value = tanggal || '';
            });
        });

        // Show/hide detail container based on status selection
        statusBelum.addEventListener('change', function() {
            if (this.checked) {
                detailContainer.style.display = 'none';
            }
        });

        statusSudah.addEventListener('change', function() {
            if (this.checked) {
                detailContainer.style.display = 'block';
            }
        });
    });
</script>
@endpush
@endsection