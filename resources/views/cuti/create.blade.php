@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Tambah Pengajuan Cuti</h2>
        <a href="{{ route('cutis.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

            @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <h6 class="mb-0">Terdapat kesalahan pada form:</h6>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

    <div class="mb-4">
        <div class="alert alert-info">
            <div class="d-flex">
                <i class="fas fa-info-circle mt-1 me-2"></i>
                <div>
                    <p class="mb-0">Anda dapat menambahkan beberapa pengajuan cuti sekaligus dengan klik tombol "Tambah Form Baru".</p>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-success btn-add-form">
                <i class="fas fa-plus-circle"></i> Tambah Form Baru
            </button>
        </div>
    </div>

    <!-- Form wrapper -->
    <form action="{{ route('cutis.store') }}" method="POST" id="main-form">
        @csrf
        <input type="hidden" name="form_count" id="form-count" value="1">

        <div id="form-container">
            <div class="card mb-4 cuti-form" id="form-1" data-index="1">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Pengajuan Cuti #1</h5>
                    <button type="button" class="btn btn-danger btn-sm btn-remove-form" style="display: none;">
                        <i class="fas fa-trash"></i> Hapus Form
                    </button>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="nik_1" class="form-label fw-bold">NIK Karyawan <span class="text-danger">*</span></label>

                            <div class="input-group mb-3">
                                <input type="text" class="form-control nik-input" id="nik_1" name="forms[1][nik]" placeholder="Masukkan NIK karyawan..." required>
                                <button class="btn btn-primary btn-cari-karyawan" type="button" data-index="1">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                            </div>

                            <div id="karyawan-result_1" class="alert alert-light border d-none mb-3 karyawan-result">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-2 fw-bold">Data Karyawan</h6>
                                    <button type="button" class="btn-close btn-clear-karyawan" data-index="1"></button>
                                </div>
                                <div id="karyawan-data_1" class="karyawan-data"></div>

                                <!-- Leave Analysis Section -->
                                <div id="karyawan-leave-analysis_1" class="karyawan-leave-analysis mt-3 pt-3 border-top d-none">
                                    <div id="leave-analysis-content_1" class="leave-analysis-content">
                                        <div class="d-flex justify-content-center">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="forms[1][karyawan_id]" id="karyawan_id_1" class="karyawan-id-input">
                                <input type="hidden" name="forms[1][poh]" id="karyawan_poh_1" class="karyawan-poh-input">
                            </div>

                            <div class="mb-3">
                                <label for="karyawan_display_1" class="form-label">Data Karyawan Terpilih</label>
                                <input type="text" class="form-control karyawan-display" id="karyawan_display_1" readonly placeholder="Belum ada karyawan yang dipilih">
                            </div>

                            <div id="karyawan-error_1" class="alert alert-danger d-none karyawan-error">
                                Data karyawan dengan NIK tersebut tidak ditemukan
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="jenis_cuti_id_1" class="form-label fw-bold">Jenis Cuti <span class="text-danger">*</span></label>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i> Anda dapat memilih beberapa jenis cuti dan mengatur jumlah hari untuk masing-masing jenis.
                            </div>

                            <div id="jenis-cuti-container_1" class="jenis-cuti-container">
                        <div class="mb-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-add-jenis-cuti" data-index="1">
                                        <i class="fas fa-plus-circle"></i> Tambah Jenis Cuti
                                    </button>
                        </div>

                                <div id="jenis-cuti-list_1" class="jenis-cuti-list">
                                    <!-- Jenis cuti items will be added here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="tanggal_mulai_1" class="form-label fw-bold">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" class="form-control tanggal-mulai" id="tanggal_mulai_1" name="forms[1][tanggal_mulai]" required>

                            <!-- Period Warning Alert - Hidden by default -->
                            <div id="period-warning-alert_1" class="alert alert-warning mt-2 d-none period-warning-alert">
                                <div class="d-flex">
                                    <i class="fas fa-exclamation-triangle mt-1 me-2"></i>
                                    <div>
                                        <p class="mb-0 period-warning-message"></p>
                                        <div class="mt-2 period-recommendation d-none">
                                            <p class="mb-1 fw-bold">Rekomendasi Tanggal:</p>
                                            <div class="d-flex align-items-center">
                                                <span class="recommended-date me-2"></span>
                                                <button type="button" class="btn btn-sm btn-outline-primary btn-use-recommended-date">
                                                    <i class="fas fa-calendar-check"></i> Gunakan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="tanggal_selesai_1" class="form-label fw-bold">Tanggal Selesai</label>
                            <input type="date" class="form-control tanggal-selesai" id="tanggal_selesai_1" readonly>
                            <div class="form-text text-muted">Otomatis dihitung berdasarkan tanggal mulai dan total hari cuti</div>

                            <div class="mt-2">
                                <span class="badge bg-primary">Total Hari Cuti: <span id="total-hari-display_1" class="total-hari-display">0</span> hari</span>
                            </div>
                        </div>
                    </div>

                <div class="mb-4">
                        <label for="alasan_1" class="form-label fw-bold">Alasan Cuti</label>
                        <textarea class="form-control alasan" id="alasan_1" name="forms[1][alasan]" rows="3"></textarea>
                </div>

                <!-- Memo Kompensasi Section -->
                <div id="memo-kompensasi-container_1" class="memo-kompensasi-container d-none">
                    <h5 class="mb-3 mt-4 border-bottom pb-2">Memo Kompensasi</h5>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i> Untuk jenis cuti "Cuti periodik (Lokal)" dan "Cuti periodik (Luar)", Anda dapat mengajukan memo kompensasi. Silakan centang kotak di bawah ini jika Anda akan mengajukan memo kompensasi:
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input is-memo-needed" type="checkbox" id="is_memo_needed_1" name="forms[1][is_memo_needed]" value="1">
                            <label class="form-check-label" for="is_memo_needed_1">
                                Pengajuan ini perlu memo kompensasi
                            </label>
                        </div>
                        <small class="text-muted">Jika dicentang, pengajuan cuti ini akan muncul pada monitoring pengajuan memo kompensasi</small>
                    </div>
                </div>

                <h5 class="mb-3 mt-4 border-bottom pb-2">Informasi Transportasi</h5>

                <div class="mb-3">
                        <div class="form-text mb-2">Pilih transportasi yang diperlukan untuk perjalanan cuti (bisa lebih dari satu). Pastikan untuk mengisi informasi tiket berangkat dan kembali.</div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> Untuk setiap jenis transportasi, Anda perlu mengisi informasi tiket pergi (berangkat) dan tiket kembali (pulang).
                        </div>

                    <div class="transportasi-options">
                        @foreach($transportasis as $transportasi)
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <div class="form-check">
                                    <input class="form-check-input transportasi-checkbox" type="checkbox"
                                               name="forms[1][transportasi_ids][]"
                                           value="{{ $transportasi->id }}"
                                               id="transportasi_1_{{ $transportasi->id }}"
                                           data-transportasi-id="{{ $transportasi->id }}"
                                               data-index="1">
                                        <label class="form-check-label fw-bold" for="transportasi_1_{{ $transportasi->id }}">
                                        {{ $transportasi->jenis }}
                                        <small class="text-muted">({{ $transportasi->keterangan }})</small>
                                    </label>
                                </div>
                            </div>

                                <div class="card-body transportasi-details" id="transportasi_details_1_{{ $transportasi->id }}" style="display: none;">
                                    <h6 class="border-bottom pb-2 mb-3">Tiket Pergi (Berangkat)</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                            <label for="rute_asal_pergi_1_{{ $transportasi->id }}" class="form-label">Rute Asal (Berangkat)</label>
                                            <input type="text" class="form-control {{ $transportasi->jenis == 'Pesawat' ? 'input-pesawat' : ($transportasi->jenis == 'Kereta Api' ? 'input-kereta' : '') }}"
                                                   id="rute_asal_pergi_1_{{ $transportasi->id }}"
                                                   name="forms[1][rute_asal_pergi_{{ $transportasi->id }}]"
                                                   placeholder="Kota/Tempat Asal"
                                                   data-jenis="{{ $transportasi->jenis }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="rute_tujuan_pergi_1_{{ $transportasi->id }}" class="form-label">Rute Tujuan (Berangkat)</label>
                                            <input type="text" class="form-control {{ $transportasi->jenis == 'Pesawat' ? 'input-pesawat' : ($transportasi->jenis == 'Kereta Api' ? 'input-kereta' : '') }}"
                                                   id="rute_tujuan_pergi_1_{{ $transportasi->id }}"
                                                   name="forms[1][rute_tujuan_pergi_{{ $transportasi->id }}]"
                                                   placeholder="Kota/Tempat Tujuan"
                                                   data-jenis="{{ $transportasi->jenis }}">
                                        </div>
                                    </div>

                                    <h6 class="border-bottom pb-2 mb-3 mt-4">Tiket Kembali (Pulang)</h6>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="rute_asal_kembali_1_{{ $transportasi->id }}" class="form-label">Rute Asal (Pulang)</label>
                                            <input type="text" class="form-control {{ $transportasi->jenis == 'Pesawat' ? 'input-pesawat' : ($transportasi->jenis == 'Kereta Api' ? 'input-kereta' : '') }}"
                                                   id="rute_asal_kembali_1_{{ $transportasi->id }}"
                                                   name="forms[1][rute_asal_kembali_{{ $transportasi->id }}]"
                                                   placeholder="Kota/Tempat Asal (untuk perjalanan pulang)"
                                                   data-jenis="{{ $transportasi->jenis }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                            <label for="rute_tujuan_kembali_1_{{ $transportasi->id }}" class="form-label">Rute Tujuan (Pulang)</label>
                                            <input type="text" class="form-control {{ $transportasi->jenis == 'Pesawat' ? 'input-pesawat' : ($transportasi->jenis == 'Kereta Api' ? 'input-kereta' : '') }}"
                                                   id="rute_tujuan_kembali_1_{{ $transportasi->id }}"
                                                   name="forms[1][rute_tujuan_kembali_{{ $transportasi->id }}]"
                                                   placeholder="Kota/Tempat Tujuan (untuk perjalanan pulang)"
                                                   data-jenis="{{ $transportasi->jenis }}">
                                        </div>
                                    </div>

                                    <div class="form-check mt-3">
                                        <input class="form-check-input auto-swap" type="checkbox"
                                               id="auto_swap_1_{{ $transportasi->id }}"
                                               data-index="1"
                                               data-transportasi-id="{{ $transportasi->id }}"
                                               checked>
                                        <label class="form-check-label" for="auto_swap_1_{{ $transportasi->id }}">
                                            Otomatis swap rute (tujuan berangkat menjadi asal pulang, dan sebaliknya)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-4 d-grid gap-2 d-md-flex justify-content-md-end">
            <a href="{{ route('cutis.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-times"></i> Batal
            </a>
            <button type="button" class="btn btn-success btn-add-form">
                <i class="fas fa-plus-circle"></i> Tambah Form Baru
            </button>
            <button type="button" id="btn-submit-all" class="btn btn-primary">
                <i class="fas fa-save"></i> Simpan Semua Pengajuan
            </button>
        </div>
    </form>
</div>

<!-- Modal Peringatan Tanggal Mulai -->
<div class="modal fade" id="tanggalMulaiModal" tabindex="-1" aria-labelledby="tanggalMulaiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-white border-0">
                <h5 class="modal-title" id="tanggalMulaiModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i> Peringatan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div class="avatar bg-warning bg-opacity-10 text-warning mb-3 mx-auto" style="width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-calendar-alt fa-2x"></i>
                    </div>
                    <h4 class="fw-bold">Data Belum Lengkap</h4>
                </div>
                <p class="text-muted" id="tanggalMulaiText"></p>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-check me-2"></i> Mengerti
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Peringatan Transportasi -->
<div class="modal fade" id="transportasiModal" tabindex="-1" aria-labelledby="transportasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="transportasiModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i> Peringatan Transportasi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div class="avatar bg-danger bg-opacity-10 text-danger mb-3 mx-auto" style="width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-plane fa-2x"></i>
                    </div>
                    <h4 class="fw-bold">Data Transportasi Belum Lengkap</h4>
                </div>
                <p class="text-muted" id="transportasiText"></p>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-check me-2"></i> Mengerti
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus Form Cuti -->
<div class="modal fade" id="deleteFormModal" tabindex="-1" aria-labelledby="deleteFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteFormModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Hapus Form Pengajuan Cuti?
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Anda akan menghapus <strong>Form Pengajuan Cuti #<span id="form-index-to-delete"></span></strong>.</p>
                <p class="text-danger mt-2"><i class="fas fa-exclamation-triangle me-2"></i>Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <button type="button" id="confirmDeleteForm" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i>Ya, Hapus!
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    /* Custom styling for employee result */
    .karyawan-result {
        background-color: #f8f9fa;
        border-radius: 0.5rem;
    }
    .karyawan-result table {
        margin-bottom: 0;
    }
    .karyawan-result th {
        font-weight: 600;
        color: #495057;
    }
    .karyawan-data {
        font-size: 0.9rem;
    }
    .btn-close {
        font-size: 0.8rem;
    }
    .form-label.fw-bold {
        font-size: 0.95rem;
    }

    /* Custom styling for transportasi options */
    .transportasi-options .card-header {
        padding: 0.5rem 1rem;
    }
    .transportasi-options .form-check-label {
        cursor: pointer;
    }
    .transportasi-options .form-check {
        padding-left: 0;
    }
    .transportasi-options .form-check-input {
        margin-left: 0;
    }
    .transportasi-details {
        border-top: 1px solid rgba(0,0,0,.125);
    }

    /* Custom styling for multiple forms */
    .btn-remove-form {
        transition: all 0.2s;
    }
    .cuti-form {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        transition: all 0.3s;
    }
    .cuti-form:hover {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .card-header {
        border-top-left-radius: 0.5rem !important;
        border-top-right-radius: 0.5rem !important;
    }

    /* Custom styling for date comparison badges */
    .date-comparison-doh span.badge,
    .date-comparison-actual span.badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        white-space: nowrap;
        margin-left: 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/city-dropdown.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Global variables
        let formCount = 1;
        const formContainer = document.getElementById('form-container');
        const formCountInput = document.getElementById('form-count');
        const addFormButtons = document.querySelectorAll('.btn-add-form');
        let formToDelete = null; // Variable to track which form to delete

        // Initialize modals
        const deleteFormModal = new bootstrap.Modal(document.getElementById('deleteFormModal'));
        const transportasiModal = new bootstrap.Modal(document.getElementById('transportasiModal'));
        const confirmDeleteFormBtn = document.getElementById('confirmDeleteForm');

        const jenisCutiOptions = [
            @foreach($jenisCutis as $jenisCuti)
            {
                id: {{ $jenisCuti->id }},
                nama: "{{ $jenisCuti->nama_jenis }}",
                jatah: {{ $jenisCuti->jatah_hari }},
                jenis_poh: "{{ $jenisCuti->jenis_poh }}",
                perlu_memo_kompensasi: {{ $jenisCuti->perlu_memo_kompensasi ? 'true' : 'false' }}
            },
            @endforeach
        ];

        // Submit all forms
        document.getElementById('btn-submit-all').addEventListener('click', function() {
            // Collect all forms and submit them
            const mainForm = document.getElementById('main-form');

            // Validate all forms before submission
            if (validateAllForms()) {
                mainForm.submit();
            }
        });

        // Add new form buttons
        addFormButtons.forEach(button => {
            button.addEventListener('click', function() {
                addNewForm();
            });
        });

        // Function to add a new form
        function addNewForm() {
            formCount++;
            formCountInput.value = formCount;

            // Clone the first form
            const firstForm = document.querySelector('.cuti-form');
            const newForm = firstForm.cloneNode(true);

            // Update form ID and index
            newForm.id = `form-${formCount}`;
            newForm.setAttribute('data-index', formCount);

            // Clear form inputs and update IDs and names
            updateFormElements(newForm, formCount);

            // Add remove button functionality
            const removeBtn = newForm.querySelector('.btn-remove-form');
            removeBtn.style.display = 'block';
            removeBtn.addEventListener('click', function() {
                removeForm(newForm);
            });

            // Update header title
            const headerTitle = newForm.querySelector('.card-header h5');
            headerTitle.textContent = `Form Pengajuan Cuti #${formCount}`;

            // Add the new form to the container
            formContainer.appendChild(newForm);

            // Initialize the new form
            initializeForm(formCount);

            // Auto-scroll to the new form
            newForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        // Function to remove a form
        function removeForm(form) {
            const index = form.getAttribute('data-index');

            // Show Bootstrap modal instead of SweetAlert2
            document.getElementById('form-index-to-delete').textContent = index;
            formToDelete = form;
            deleteFormModal.show();
        }

        // Confirm delete form button event listener
        confirmDeleteFormBtn.addEventListener('click', function() {
            if (formToDelete) {
                formToDelete.remove();
                updateFormIndexes();
                deleteFormModal.hide();
                formToDelete = null;
            }
        });

        // Function to update form indexes after removal
        function updateFormIndexes() {
            const forms = document.querySelectorAll('.cuti-form');
            formCount = forms.length;
            formCountInput.value = formCount;

            // Update form index attributes
            forms.forEach((form, index) => {
                const formIndex = index + 1;
                form.id = `form-${formIndex}`;
                form.setAttribute('data-index', formIndex);

                // Update header title
                const headerTitle = form.querySelector('.card-header h5');
                headerTitle.textContent = `Form Pengajuan Cuti #${formIndex}`;


                const removeBtn = form.querySelector('.btn-remove-form');
                if (formIndex === 1 && forms.length === 1) {
                    removeBtn.style.display = 'none';
                } else {
                    removeBtn.style.display = 'block';
                }
            });
        }

        // Function to update form elements with new index
        function updateFormElements(form, index) {
            // Update all input names
            form.querySelectorAll('input, select, textarea').forEach(input => {
                if (input.name) {
                    // Update name attribute
                    input.name = input.name.replace(/forms\[\d+\]/, `forms[${index}]`);
                    console.log(`Updated input name: ${input.name}`);
                }

                if (input.id) {
                    // Get correct new ID format
                    // Handle special case for transportasi details which have complex IDs
                    if (input.id.includes('transportasi_') || input.id.includes('auto_swap_') ||
                        input.id.includes('rute_asal_pergi_') || input.id.includes('rute_tujuan_pergi_') ||
                        input.id.includes('rute_asal_kembali_') || input.id.includes('rute_tujuan_kembali_')) {
                        // For these special cases, only update the form index part but keep transportasi_id
                        const parts = input.id.split('_');
                        // Find the index that needs to be replaced (it should be a number)
                        for (let i = 0; i < parts.length; i++) {
                            if (!isNaN(parseInt(parts[i])) && i+1 < parts.length && !isNaN(parseInt(parts[i+1]))) {
                                // This is the form index (the one before transportasi_id)
                                parts[i] = index;
                                break;
                            }
                        }
                        input.id = parts.join('_');
                    } else {
                        // Regular case - replace the numeric index at the end
                        const baseParts = input.id.split('_');
                        // Find if there's a numeric part to replace
                        let hasNumeric = false;
                        for (let i = 0; i < baseParts.length; i++) {
                            if (!isNaN(parseInt(baseParts[i]))) {
                                baseParts[i] = index;
                                hasNumeric = true;
                                break;
                            }
                        }
                        // If no numeric part was found, just append the index
                        if (hasNumeric) {
                            input.id = baseParts.join('_');
                        } else {
                            input.id = input.id + '_' + index;
                        }
                    }
                }

                // Clear value for most inputs
                if (input.type !== 'hidden' && input.type !== 'checkbox' && !input.classList.contains('auto-swap')) {
                    input.value = '';
                }

                // Uncheck checkboxes
                if (input.type === 'checkbox') {
                    if (input.classList.contains('auto-swap')) {
                        input.checked = true; // Keep auto-swap checked
                    } else {
                        input.checked = false;
                    }
                }

                // Update data-index attribute if it exists
                if (input.hasAttribute('data-index')) {
                    input.setAttribute('data-index', index);
                }
            });

            // Update all label for attributes
            form.querySelectorAll('label').forEach(label => {
                if (label.htmlFor) {
                    // Handle special transportasi-related labels similarly to inputs
                    if (label.htmlFor.includes('transportasi_') || label.htmlFor.includes('auto_swap_') ||
                        label.htmlFor.includes('rute_asal_pergi_') || label.htmlFor.includes('rute_tujuan_pergi_') ||
                        label.htmlFor.includes('rute_asal_kembali_') || label.htmlFor.includes('rute_tujuan_kembali_')) {
                        const parts = label.htmlFor.split('_');
                        for (let i = 0; i < parts.length; i++) {
                            if (!isNaN(parseInt(parts[i])) && i+1 < parts.length && !isNaN(parseInt(parts[i+1]))) {
                                parts[i] = index;
                                break;
                            }
                        }
                        label.htmlFor = parts.join('_');
                    } else {
                        // Regular labels
                        const parts = label.htmlFor.split('_');
                        let hasNumeric = false;
                        for (let i = 0; i < parts.length; i++) {
                            if (!isNaN(parseInt(parts[i]))) {
                                parts[i] = index;
                                hasNumeric = true;
                                break;
                            }
                        }
                        if (hasNumeric) {
                            label.htmlFor = parts.join('_');
                        } else {
                            label.htmlFor = label.htmlFor + '_' + index;
                        }
                    }

                    // Log the updated label
                    console.log(`Updated label htmlFor: ${label.htmlFor}`);
                }
            });

            // Update all IDs for divs that need specific IDs
            const containersWithIds = form.querySelectorAll('[id^="karyawan-"], [id^="jenis-cuti-"], [id^="transportasi_details_"], [id^="memo-kompensasi-container_"], [id^="leave-analysis-content_"], [id^="karyawan-leave-analysis_"], [id^="period-warning-alert_"]');
            containersWithIds.forEach(container => {
                if (container.id) {
                    console.log(`Updating container ID: ${container.id}`);

                    // Special handling for transportasi_details because it has multiple indices
                    if (container.id.startsWith('transportasi_details_')) {
                        // For transportasi_details_1_2, we want to change 1 to new index but keep 2
                        const parts = container.id.split('_');
                        if (parts.length >= 3) {
                            parts[2] = index; // Update the form index part
                            container.id = parts.join('_');
                        }
                    } else {
                        // Normal case like karyawan-result_1
                        const idParts = container.id.split('_');
                        if (idParts.length > 1 && !isNaN(parseInt(idParts[idParts.length-1]))) {
                            idParts[idParts.length-1] = index;
                            container.id = idParts.join('_');
                        } else {
                            const baseId = container.id.split('_')[0];
                            container.id = `${baseId}_${index}`;
                        }
                    }

                    console.log(`Updated container ID to: ${container.id}`);
                }
            });

            // Clear jenis cuti list and reset
            const jenisCutiList = form.querySelector('.jenis-cuti-list');
            jenisCutiList.innerHTML = '';

            // Make sure all alerts are hidden
            form.querySelectorAll('.alert').forEach(alert => {
                if (!alert.classList.contains('alert-info')) {
                    alert.classList.add('d-none');
                }
            });

            // Hide memo kompensasi container
            const memoKompensasiContainer = form.querySelector('.memo-kompensasi-container');
            if(memoKompensasiContainer) {
                memoKompensasiContainer.classList.add('d-none');
            }
        }

        // Initialize all forms
        function initializeForm(index) {
            // Initialize employee search for this form
            initEmployeeSearch(index);

            // Initialize jenis cuti for this form
            initJenisCuti(index);

            // Initialize transportasi checkboxes for this form
            initTransportasi(index);
        }

        // Initialize employee search functionality
        function initEmployeeSearch(index) {
            const form = document.getElementById(`form-${index}`);
            const nikInput = form.querySelector('.nik-input');
            const btnCariKaryawan = form.querySelector('.btn-cari-karyawan');
            const karyawanResult = form.querySelector('.karyawan-result');
            const karyawanData = form.querySelector('.karyawan-data');
            const karyawanError = form.querySelector('.karyawan-error');
            const karyawanIdInput = form.querySelector('.karyawan-id-input');
            const karyawanPohInput = form.querySelector('.karyawan-poh-input');
            const karyawanDisplay = form.querySelector('.karyawan-display');
            const btnClearKaryawan = form.querySelector('.btn-clear-karyawan');

            // Add event listeners
            btnCariKaryawan.addEventListener('click', function() {
                searchKaryawan(index);
            });

            nikInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    searchKaryawan(index);
                }
            });

            btnClearKaryawan.addEventListener('click', function() {
                clearKaryawanSelection(index);
            });
        }

        // Search for employee
        function searchKaryawan(index) {
            const form = document.getElementById(`form-${index}`);
            const nikInput = form.querySelector('.nik-input');
            const nik = nikInput.value.trim();

            if (!nik) return;

            fetch(`/api/karyawans/search?nik=${nik}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.length > 0) {
                        // API returns an array of karyawan objects, use the first one
                        displayKaryawanData(index, data[0]);
                    } else {
                        showKaryawanError(index);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showKaryawanError(index);
                });
        }

        // Display employee data
        function displayKaryawanData(index, karyawan) {
            const form = document.getElementById(`form-${index}`);
            const karyawanResult = form.querySelector('.karyawan-result');
            const karyawanData = form.querySelector('.karyawan-data');
            const karyawanError = form.querySelector('.karyawan-error');
            const karyawanIdInput = form.querySelector('.karyawan-id-input');
            const karyawanPohInput = form.querySelector('.karyawan-poh-input');
            const karyawanDisplay = form.querySelector('.karyawan-display');
            const leaveAnalysisContent = form.querySelector(`#leave-analysis-content_${index}`);

            karyawanResult.classList.remove('d-none');
            karyawanError.classList.add('d-none');

            // Display POH information if available
            const pohValue = karyawan.poh || 'Tidak ditentukan';

            karyawanData.innerHTML = `
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <th width="100">NIK</th>
                        <td>${karyawan.nik}</td>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <td>${karyawan.nama}</td>
                    </tr>
                    <tr>
                        <th>Departemen</th>
                        <td>${karyawan.departemen}</td>
                    </tr>
                    <tr>
                        <th>Jabatan</th>
                        <td>${karyawan.jabatan}</td>
                    </tr>
                    <tr>
                        <th>POH</th>
                        <td>${pohValue}</td>
                    </tr>
                </table>
            `;

            karyawanIdInput.value = karyawan.id;
            karyawanDisplay.value = `${karyawan.nama} (${karyawan.nik})`;
            karyawanPohInput.value = pohValue;

            // Check if any jenis cuti has been selected and update memo kompensasi visibility
            checkAndUpdateMemoKompensasi(index);

            // Fetch and display leave analysis
            fetchLeaveAnalysis(index, karyawan.id);
        }

        // Show employee error
        function showKaryawanError(index) {
            const form = document.getElementById(`form-${index}`);
            const karyawanResult = form.querySelector('.karyawan-result');
            const karyawanError = form.querySelector('.karyawan-error');
            const karyawanIdInput = form.querySelector('.karyawan-id-input');
            const karyawanPohInput = form.querySelector('.karyawan-poh-input');
            const karyawanDisplay = form.querySelector('.karyawan-display');

            karyawanResult.classList.add('d-none');
            karyawanError.classList.remove('d-none');
            karyawanIdInput.value = '';
            karyawanPohInput.value = '';
            karyawanDisplay.value = '';
        }

        // Clear employee selection
        function clearKaryawanSelection(index) {
            const form = document.getElementById(`form-${index}`);
            const nikInput = form.querySelector('.nik-input');
            const karyawanResult = form.querySelector('.karyawan-result');
            const karyawanError = form.querySelector('.karyawan-error');
            const karyawanIdInput = form.querySelector('.karyawan-id-input');
            const karyawanDisplay = form.querySelector('.karyawan-display');
            const karyawanPohInput = form.querySelector('.karyawan-poh-input');

            karyawanResult.classList.add('d-none');
            karyawanError.classList.add('d-none');
            karyawanIdInput.value = '';
            karyawanDisplay.value = '';
            karyawanPohInput.value = '';
            nikInput.value = '';

            // Check if any jenis cuti has been selected and update memo kompensasi visibility
            checkAndUpdateMemoKompensasi(index);
        }

        // Initialize jenis cuti functionality
        function initJenisCuti(index) {
            const form = document.getElementById(`form-${index}`);
            const btnAddJenisCuti = form.querySelector('.btn-add-jenis-cuti');
            const jenisCutiList = form.querySelector('.jenis-cuti-list');
            const tanggalMulaiInput = form.querySelector('.tanggal-mulai');

            // Add event listener for adding new jenis cuti
            btnAddJenisCuti.addEventListener('click', function() {
                addJenisCutiItem(index);
            });

            // Listen for changes on tanggal_mulai
            tanggalMulaiInput.addEventListener('change', function() {
                calculateTotalHari(index);
                // Calculate date difference with ideal dates
                console.log(`Tanggal Mulai changed for form #${index}, calculating date difference`);
                calculateDateDifference(index);

                // Also validate the leave period to show warnings if needed
                validateLeavePeriod(index);
            });

            // Add at least one jenis cuti item by default
            addJenisCutiItem(index);
        }

        // Add jenis cuti item
        function addJenisCutiItem(index) {
            const form = document.getElementById(`form-${index}`);
            const jenisCutiList = form.querySelector('.jenis-cuti-list');
            const jenisCutiCount = jenisCutiList.children.length;

            // Debug - log yang kita buat
            console.log(`Adding jenis cuti item to form #${index}, current count: ${jenisCutiCount}`);

            const jenisCutiItem = document.createElement('div');
            jenisCutiItem.className = 'card mb-3';
            jenisCutiItem.innerHTML = `
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="card-title">Jenis Cuti #${jenisCutiCount + 1}</h6>
                        <button type="button" class="btn-close btn-remove-jenis-cuti" aria-label="Close"></button>
                    </div>
                    <div class="row">
                        <div class="col-md-8 mb-2">
                            <select class="form-select jenis-cuti-select" name="forms[${index}][jenis_cuti_details][${jenisCutiCount}][jenis_cuti_id]" required>
                                <option value="">-- Pilih Jenis Cuti --</option>
                                ${jenisCutiOptions.map(option => `
                                    <option value="${option.id}" data-jatah="${option.jatah}" data-poh="${option.jenis_poh}" data-perlu-memo="${option.perlu_memo_kompensasi}">
                                        ${option.nama} (${option.jatah} hari)
                                    </option>
                                `).join('')}
                            </select>
                        </div>
                        <div class="col-md-4 mb-2">
                            <div class="input-group">
                                <input type="number" class="form-control jenis-cuti-hari" name="forms[${index}][jenis_cuti_details][${jenisCutiCount}][jumlah_hari]"
                                    min="1" placeholder="Hari" required value="1">
                                <span class="input-group-text">hari</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            jenisCutiList.appendChild(jenisCutiItem);

            // Add event listener to remove button
            const removeBtn = jenisCutiItem.querySelector('.btn-remove-jenis-cuti');
            removeBtn.addEventListener('click', function() {
                jenisCutiList.removeChild(jenisCutiItem);
                updateJenisCutiIndexes(index);
                calculateTotalHari(index);

                // After removal, check if any remaining jenis cuti requires memo kompensasi
                checkAndUpdateMemoKompensasi(index);

                // Update leave analysis when a leave type is removed
                const karyawanIdInput = form.querySelector('.karyawan-id-input');
                if (karyawanIdInput.value) {
                    // Refresh leave analysis with the remaining selected leave types
                    fetchLeaveAnalysis(index, karyawanIdInput.value);
                }
            });

            // Add event listeners to select and input for jatah hari updates
            const jenisCutiSelect = jenisCutiItem.querySelector('.jenis-cuti-select');
            const hariInput = jenisCutiItem.querySelector('.jenis-cuti-hari');

            jenisCutiSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    const jatahHari = parseInt(selectedOption.dataset.jatah);
                    hariInput.value = jatahHari;
                    hariInput.max = jatahHari;
                    calculateTotalHari(index);

                    // Check if this jenis cuti requires memo kompensasi
                    checkAndUpdateMemoKompensasi(index);

                    // Update leave analysis when leave type changes
                    const karyawanIdInput = form.querySelector('.karyawan-id-input');
                    if (karyawanIdInput.value) {
                        // Refresh leave analysis with the selected leave type
                        fetchLeaveAnalysis(index, karyawanIdInput.value);
                    }
                }
            });

            hariInput.addEventListener('change', function() {
                calculateTotalHari(index);
            });

            hariInput.addEventListener('input', function() {
                calculateTotalHari(index);
            });

            // If there's more than one item, disable the first remove button
            if (jenisCutiList.children.length === 1) {
                removeBtn.disabled = true;
            } else {
                jenisCutiList.querySelectorAll('.btn-remove-jenis-cuti').forEach(btn => {
                    btn.disabled = false;
                });
            }

            calculateTotalHari(index);
        }

        // Check if any selected jenis cuti requires memo kompensasi
        function checkAndUpdateMemoKompensasi(index) {
            const form = document.getElementById(`form-${index}`);
            const jenisCutiSelects = form.querySelectorAll('.jenis-cuti-select');
            const karyawanPohInput = form.querySelector('.karyawan-poh-input');
            const memoKompensasiContainer = form.querySelector('.memo-kompensasi-container');
            const isMemoNeededCheckbox = form.querySelector('.is-memo-needed');

            let perluMemo = false;
            const karyawanPoh = karyawanPohInput.value;

            // Check each selected jenis cuti
            jenisCutiSelects.forEach(select => {
                if (select.value) {
                    const selectedOption = select.options[select.selectedIndex];
                    const jenisPoh = selectedOption.dataset.poh;
                    const jenisCutiName = selectedOption.textContent.trim().toLowerCase();

                    // Show memo kompensasi option for all periodic leave types
                    if (jenisCutiName.includes('periodik')) {
                        perluMemo = true;
                    }
                }
            });

            // Show or hide memo kompensasi section based on the check
            if (perluMemo) {
                memoKompensasiContainer.classList.remove('d-none');

                // Add event listener to the memo needed checkbox
                if (isMemoNeededCheckbox) {
                    // Remove previous event listener first to prevent duplicates
                    isMemoNeededCheckbox.removeEventListener('change', memoNeededChangeHandler);

                    // Add new event listener
                    isMemoNeededCheckbox.addEventListener('change', memoNeededChangeHandler);
                }
            } else {
                memoKompensasiContainer.classList.add('d-none');

                // Reset is_memo_needed checkbox value to ensure it's not submitted
                if (isMemoNeededCheckbox) {
                    isMemoNeededCheckbox.checked = false;
                }
            }
        }

        // Handle memo needed checkbox change
        function memoNeededChangeHandler() {
            const formIndex = this.id.split('_').pop();
            toggleMemoDetailsVisibility(formIndex);
        }

        // Toggle memo details container visibility based on checkbox
        function toggleMemoDetailsVisibility(index) {
            const form = document.getElementById(`form-${index}`);
            const isMemoNeededCheckbox = form.querySelector('.is-memo-needed');
            const memoDetailsContainer = form.querySelector('.memo-details-container');

            if (memoDetailsContainer) {
                memoDetailsContainer.style.display = isMemoNeededCheckbox.checked ? 'block' : 'none';
            } else {
                console.error(`Memo details container not found for form #${index}`);
            }
        }

        // Update jenis cuti indexes after removal
        function updateJenisCutiIndexes(index) {
            const form = document.getElementById(`form-${index}`);
            const jenisCutiList = form.querySelector('.jenis-cuti-list');
            const items = jenisCutiList.querySelectorAll('.card');

            items.forEach((item, idx) => {
                const title = item.querySelector('.card-title');
                const select = item.querySelector('.jenis-cuti-select');
                const input = item.querySelector('.jenis-cuti-hari');

                title.textContent = `Jenis Cuti #${idx + 1}`;
                select.name = `forms[${index}][jenis_cuti_details][${idx}][jenis_cuti_id]`;
                input.name = `forms[${index}][jenis_cuti_details][${idx}][jumlah_hari]`;
            });

            // Disable the remove button if only one item is left
            if (items.length === 1) {
                items[0].querySelector('.btn-remove-jenis-cuti').disabled = true;
            }
        }

        // Calculate total hari and update tanggal_selesai
        function calculateTotalHari(index) {
            const form = document.getElementById(`form-${index}`);
            const hariInputs = form.querySelectorAll('.jenis-cuti-hari');
            const totalHariDisplay = form.querySelector('.total-hari-display');
            const tanggalMulaiInput = form.querySelector('.tanggal-mulai');
            const tanggalSelesaiInput = form.querySelector('.tanggal-selesai');

            let totalHari = 0;

            hariInputs.forEach(input => {
                if (input.value && !isNaN(input.value)) {
                    totalHari += parseInt(input.value);
                }
            });

            totalHariDisplay.textContent = totalHari;

            // Update tanggal_selesai
            const tanggalMulai = tanggalMulaiInput.value;
            if (tanggalMulai && totalHari > 0) {
                const startDate = new Date(tanggalMulai);
                const endDate = new Date(startDate);
                endDate.setDate(startDate.getDate() + totalHari - 1);

                // Format as YYYY-MM-DD
                const formattedDate = endDate.toISOString().split('T')[0];
                tanggalSelesaiInput.value = formattedDate;

                // Check if this is a periodic leave and validate the period
                validateLeavePeriod(index);
            } else {
                tanggalSelesaiInput.value = '';
            }
        }

        // Check if the start date is too early compared to ideal dates
        function checkIfDateIsTooEarly(index) {
            console.log(`Checking if date is too early for form #${index}`);
            const form = document.getElementById(`form-${index}`);
            const tanggalMulaiInput = form.querySelector('.tanggal-mulai');
            const periodWarningAlert = form.querySelector('.period-warning-alert');
            const periodWarningMessage = form.querySelector('.period-warning-message');
            const periodRecommendation = form.querySelector('.period-recommendation');
            const recommendedDateSpan = form.querySelector('.recommended-date');
            const btnUseRecommendedDate = form.querySelector('.btn-use-recommended-date');

            // Get the selected start date
            const startDate = new Date(tanggalMulaiInput.value);
            console.log(`Selected start date: ${startDate.toISOString().split('T')[0]}`);

            // Get the selected leave type
            const jenisCutiSelects = form.querySelectorAll('.jenis-cuti-select');
            let selectedLeaveType = null;
            let selectedLeaveId = null;

            jenisCutiSelects.forEach(select => {
                if (select.value) {
                    const selectedOption = select.options[select.selectedIndex];
                    const jenisCutiName = selectedOption.text.toLowerCase();

                    if (jenisCutiName.includes('periodik')) {
                        selectedLeaveType = jenisCutiName;
                        selectedLeaveId = select.value;
                        console.log(`Selected periodic leave type: ${jenisCutiName}, ID: ${selectedLeaveId}`);
                    }
                }
            });

            // If no periodic leave type is selected, return
            if (!selectedLeaveType) {
                console.log('No periodic leave type selected, skipping date check');
                return false;
            }

            // Find the leave data in the global periodicLeaves array
            const isLokal = selectedLeaveType.includes('lokal');
            const searchTerm = isLokal ? 'lokal' : 'luar';
            console.log(`Searching for leave type containing '${searchTerm}' in window.periodicLeaves`);

            const leaveData = window.periodicLeaves.find(leave =>
                leave.nama_jenis.toLowerCase().includes(searchTerm)
            );

            if (!leaveData) {
                console.log(`No matching leave data found in window.periodicLeaves for '${searchTerm}'`);
                return false;
            }

            console.log(`Found matching leave data: ${leaveData.nama_jenis}`);
            console.log(`Has nextIdealActualDateObj: ${!!leaveData.nextIdealActualDateObj}, Has nextIdealDohDateObj: ${!!leaveData.nextIdealDohDateObj}`);

            // Check if we have Tanggal Ideal Actual (priority 1)
            if (leaveData.nextIdealActualDateObj) {
                const diffDays = calculateDaysDifference(startDate, leaveData.nextIdealActualDateObj);
                console.log(`Difference in days from Ideal Actual: ${diffDays}`);

                // If start date is earlier than Tanggal Ideal Actual, show warning
                if (diffDays < 0) {
                    // Gunakan langsung tanggal ideal actual tanpa tambahan 7 hari
                    const recommendedDate = new Date(leaveData.nextIdealActualDateObj);

                    // Show warning
                    periodWarningAlert.classList.remove('d-none');
                    periodWarningMessage.textContent = `Tanggal mulai cuti terlalu awal. Anda mengajukan ${Math.abs(diffDays)} hari lebih awal dari Tanggal Ideal Actual.`;

                    // Show recommendation
                    periodRecommendation.classList.remove('d-none');
                    const formattedDate = recommendedDate.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    recommendedDateSpan.textContent = formattedDate;

                    // Store the raw date for the button
                    const isoDate = recommendedDate.toISOString().split('T')[0];
                    btnUseRecommendedDate.setAttribute('data-date', isoDate);

                    // Add event listener to the button
                    btnUseRecommendedDate.removeEventListener('click', useRecommendedDateHandler);
                    btnUseRecommendedDate.addEventListener('click', function() {
                        useRecommendedDateHandler.call(this, index);
                    });

                    console.log(`Warning shown for form #${index}: Date is too early compared to Ideal Actual`);
                    return true;
                }
            }
            // If no Tanggal Ideal Actual, check Tanggal Ideal (DOH) (priority 2)
            else if (leaveData.nextIdealDohDateObj) {
                const diffDays = calculateDaysDifference(startDate, leaveData.nextIdealDohDateObj);
                console.log(`Difference in days from Ideal DOH: ${diffDays}`);

                // If start date is earlier than Tanggal Ideal (DOH), show warning
                if (diffDays < 0) {
                    // Gunakan langsung tanggal ideal DOH tanpa tambahan 7 hari
                    const recommendedDate = new Date(leaveData.nextIdealDohDateObj);

                    // Show warning
                    periodWarningAlert.classList.remove('d-none');
                    periodWarningMessage.textContent = `Tanggal mulai cuti terlalu awal. Anda mengajukan ${Math.abs(diffDays)} hari lebih awal dari Tanggal Ideal (DOH).`;

                    // Show recommendation
                    periodRecommendation.classList.remove('d-none');
                    const formattedDate = recommendedDate.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                    recommendedDateSpan.textContent = formattedDate;

                    // Store the raw date for the button
                    const isoDate = recommendedDate.toISOString().split('T')[0];
                    btnUseRecommendedDate.setAttribute('data-date', isoDate);

                    // Add event listener to the button
                    btnUseRecommendedDate.removeEventListener('click', useRecommendedDateHandler);
                    btnUseRecommendedDate.addEventListener('click', function() {
                        useRecommendedDateHandler.call(this, index);
                    });

                    console.log(`Warning shown for form #${index}: Date is too early compared to Ideal DOH`);
                    return true;
                }
            }

            // If we get here, the date is not too early
            console.log(`Date is not too early for form #${index}`);
            return false;
        }

        // Handler function for the "Gunakan" button
        function useRecommendedDateHandler(formIndex) {
            const dateToUse = this.getAttribute('data-date');
            // If formIndex is not provided, try to get it from the button's closest form
            if (!formIndex) {
                const form = this.closest('.cuti-form');
                if (form) {
                    formIndex = form.getAttribute('data-index');
                }
            }

            console.log(`useRecommendedDateHandler called for form #${formIndex}, date: ${dateToUse}`);

            if (dateToUse && formIndex) {
                const form = document.getElementById(`form-${formIndex}`);
                const tanggalMulaiInput = form.querySelector('.tanggal-mulai');
                const periodWarningAlert = form.querySelector('.period-warning-alert');

                // Update the start date field
                tanggalMulaiInput.value = dateToUse;

                // Recalculate total days and update end date
                calculateTotalHari(formIndex);

                // Hide the warning alert
                periodWarningAlert.classList.add('d-none');

                // Recalculate date difference to update the comparison badges
                calculateDateDifference(formIndex);

                console.log(`Updated tanggal_mulai to recommended date: ${dateToUse} for form #${formIndex}`);
            } else {
                console.error('Missing date or form index in useRecommendedDateHandler');
            }
        }

        // Validate if the leave date is within the allowed period
        function validateLeavePeriod(index) {
            const form = document.getElementById(`form-${index}`);
            const karyawanIdInput = form.querySelector('.karyawan-id-input');
            const tanggalMulaiInput = form.querySelector('.tanggal-mulai');
            const jenisCutiSelects = form.querySelectorAll('.jenis-cuti-select');
            const periodWarningAlert = form.querySelector('.period-warning-alert');
            const periodWarningMessage = form.querySelector('.period-warning-message');
            const periodRecommendation = form.querySelector('.period-recommendation');
            const recommendedDateSpan = form.querySelector('.recommended-date');
            const btnUseRecommendedDate = form.querySelector('.btn-use-recommended-date');

            // Check if we have all required data
            if (!karyawanIdInput.value || !tanggalMulaiInput.value) {
                return;
            }

            // Check if any periodic leave type is selected
            let periodicLeaveFound = false;
            let periodicLeaveId = null;
            let annualLeaveFound = false;

            jenisCutiSelects.forEach(select => {
                if (select.value) {
                    const selectedOption = select.options[select.selectedIndex];
                    const jenisCutiName = selectedOption.text.toLowerCase();

                    if (jenisCutiName.includes('periodik')) {
                        periodicLeaveFound = true;
                        periodicLeaveId = select.value;
                    }

                    // Check if annual leave is selected
                    if (jenisCutiName.includes('tahunan')) {
                        annualLeaveFound = true;
                    }
                }
            });

            // If annual leave is selected, we don't need to validate period
            // Annual leave is flexible and only depends on the leave balance
            if (annualLeaveFound && !periodicLeaveFound) {
                periodWarningAlert.classList.add('d-none');
                return;
            }

            // If no periodic leave type is selected, hide the warning and return
            if (!periodicLeaveFound) {
                periodWarningAlert.classList.add('d-none');
                return;
            }

            // First check if the date is too early compared to ideal dates
            // This is our new warning system with priority on Tanggal Ideal Actual
            if (window.periodicLeaves && window.periodicLeaves.length > 0) {
                const isTooEarly = checkIfDateIsTooEarly(index);
                if (isTooEarly) {
                    // We've already shown the warning, so we can return
                    return;
                }
            }

            // If not too early or no leave analysis data yet, proceed with the API validation
            fetch(`/api/cuti/validate-period?karyawan_id=${karyawanIdInput.value}&jenis_cuti_id=${periodicLeaveId}&tanggal_mulai=${tanggalMulaiInput.value}`)
                .then(response => response.json())
                .catch(error => {
                    console.error('Error validating leave period:', error);
                    return null;
                })
                .then(data => {
                    if (!data) return;

                    if (!data.within_period) {
                        // Show warning
                        periodWarningAlert.classList.remove('d-none');
                        periodWarningMessage.textContent = data.message;

                        // Show recommendation if available
                        if (data.recommendation_dates) {
                            // Use the recommended_date if available (this is Tanggal Ideal Actual + 7 or Tanggal Ideal (DOH) + 7)
                            if (data.recommendation_dates.recommended_date) {
                                periodRecommendation.classList.remove('d-none');

                                // Use the recommended date (Tanggal Ideal + 7 days)
                                const recommendedDate = new Date(data.recommendation_dates.recommended_date);
                                const formattedDate = recommendedDate.toLocaleDateString('id-ID', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                });
                                recommendedDateSpan.textContent = formattedDate;

                                // Store the raw date for the button
                                btnUseRecommendedDate.setAttribute('data-date', data.recommendation_dates.recommended_date);

                                // Add debug info if available
                                if (data.recommendation_dates.debug_info) {
                                    console.log('Debug info:', data.recommendation_dates.debug_info);
                                }

                                // Show all periods in the console for debugging
                                if (data.recommendation_dates.all_ideal_dates) {
                                    console.log('All ideal dates:', data.recommendation_dates.all_ideal_dates);
                                }
                            }
                            // Fallback to old logic if recommended_date is not available
                            else if (data.recommendation_dates.all_ideal_dates && data.recommendation_dates.all_ideal_dates.length > 0) {
                                periodRecommendation.classList.remove('d-none');

                                // Always use the first period's date for new leave requests
                                const firstPeriodDate = new Date(data.recommendation_dates.all_ideal_dates[0].date);
                                const formattedDate = firstPeriodDate.toLocaleDateString('id-ID', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                });
                                recommendedDateSpan.textContent = formattedDate;

                                // Store the raw date for the button
                                btnUseRecommendedDate.setAttribute('data-date', data.recommendation_dates.all_ideal_dates[0].date);
                            }
                            // Prefer ideal_date (current period) if available
                            else if (data.recommendation_dates.ideal_date) {
                                periodRecommendation.classList.remove('d-none');
                                const recommendedDate = new Date(data.recommendation_dates.ideal_date);
                                const formattedDate = recommendedDate.toLocaleDateString('id-ID', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                });
                                recommendedDateSpan.textContent = formattedDate;

                                // Store the raw date for the button
                                btnUseRecommendedDate.setAttribute('data-date', data.recommendation_dates.ideal_date);
                            }
                            // Fallback to first_ideal_date or based_on_doh
                            else if (data.recommendation_dates.first_ideal_date || data.recommendation_dates.based_on_doh) {
                                periodRecommendation.classList.remove('d-none');
                                // Prefer first_ideal_date if available, otherwise fall back to based_on_doh
                                const recommendedDateValue = data.recommendation_dates.first_ideal_date || data.recommendation_dates.based_on_doh;
                                const recommendedDate = new Date(recommendedDateValue);
                                const formattedDate = recommendedDate.toLocaleDateString('id-ID', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                });
                                recommendedDateSpan.textContent = formattedDate;

                                // Store the raw date for the button
                                btnUseRecommendedDate.setAttribute('data-date', recommendedDateValue);
                            }

                            // Remove any existing event listeners to prevent duplicates
                            btnUseRecommendedDate.removeEventListener('click', useRecommendedDateHandler);

                            // Add event listener to the button with a named handler function
                            btnUseRecommendedDate.addEventListener('click', useRecommendedDateHandler);

                            // Handler function for the "Gunakan" button
                            function useRecommendedDateHandler() {
                                const dateToUse = this.getAttribute('data-date');
                                if (dateToUse) {
                                    // Format the date as YYYY-MM-DD for the input field
                                    const dateObj = new Date(dateToUse);
                                    const formattedDate = dateObj.toISOString().split('T')[0];

                                    // Update the start date field
                                    tanggalMulaiInput.value = formattedDate;

                                    // Recalculate total days and update end date
                                    calculateTotalHari(index);

                                    // Hide the warning alert
                                    periodWarningAlert.classList.add('d-none');

                                    // Recalculate date difference to update the comparison badges
                                    calculateDateDifference(index);

                                    console.log(`Updated tanggal_mulai to recommended date: ${formattedDate}`);
                                }
                            }
                        } else {
                            periodRecommendation.classList.add('d-none');
                        }
                    } else {
                        // Hide warning
                        periodWarningAlert.classList.add('d-none');
                    }
                });
        }

        // Initialize transportasi checkboxes
        function initTransportasi(index) {
            const form = document.getElementById(`form-${index}`);
            const transportasiCheckboxes = form.querySelectorAll('.transportasi-checkbox');

            transportasiCheckboxes.forEach(checkbox => {
                const transportasiId = checkbox.dataset.transportasiId;

                // Update the data-index attribute to match the current form index
                checkbox.setAttribute('data-index', index);

                // Log the checkbox details for debugging
                console.log(`Initializing transportasi checkbox: id=${checkbox.id}, transportasi-id=${transportasiId}, index=${index}`);

                // Unbind existing event handlers first to prevent duplicates
                checkbox.removeEventListener('change', transportasiChangeHandler);

                // Add the event listener with a named handler function
                checkbox.addEventListener('change', transportasiChangeHandler);

                // Initialize auto-swap functionality
                initAutoSwap(index, transportasiId);
            });

            // Function to handle transportasi checkbox changes
            function transportasiChangeHandler() {
                const transportasiId = this.dataset.transportasiId;
                const checkboxIndex = this.dataset.index;

                console.log(`Transportasi checkbox changed: transportasi-id=${transportasiId}, index=${checkboxIndex}`);

                const detailsDiv = document.getElementById(`transportasi_details_${checkboxIndex}_${transportasiId}`);

                if (!detailsDiv) {
                    console.error(`Details div not found: transportasi_details_${checkboxIndex}_${transportasiId}`);
                    return;
                }

                if (this.checked) {
                    detailsDiv.style.display = 'block';
                } else {
                    detailsDiv.style.display = 'none';
                }
            }
        }

        // Initialize auto-swap functionality
        function initAutoSwap(index, transportasiId) {
            const form = document.getElementById(`form-${index}`);
            const autoSwapCheckbox = form.querySelector(`#auto_swap_${index}_${transportasiId}`);
            const ruteAsalPergi = form.querySelector(`#rute_asal_pergi_${index}_${transportasiId}`);
            const ruteTujuanPergi = form.querySelector(`#rute_tujuan_pergi_${index}_${transportasiId}`);
            const ruteAsalKembali = form.querySelector(`#rute_asal_kembali_${index}_${transportasiId}`);
            const ruteTujuanKembali = form.querySelector(`#rute_tujuan_kembali_${index}_${transportasiId}`);

            console.log(`Initializing auto-swap for form #${index}, transportasi #${transportasiId}`);
            console.log(`Elements found: autoSwap=${!!autoSwapCheckbox}, asalPergi=${!!ruteAsalPergi}, tujuanPergi=${!!ruteTujuanPergi}, asalKembali=${!!ruteAsalKembali}, tujuanKembali=${!!ruteTujuanKembali}`);

            if (autoSwapCheckbox && ruteAsalPergi && ruteTujuanPergi && ruteAsalKembali && ruteTujuanKembali) {
                // Remove existing event listeners first
                ruteAsalPergi.removeEventListener('input', ruteAsalPergiHandler);
                ruteTujuanPergi.removeEventListener('input', ruteTujuanPergiHandler);

                // Add event listeners with named handler functions
                ruteAsalPergi.addEventListener('input', ruteAsalPergiHandler);
                ruteTujuanPergi.addEventListener('input', ruteTujuanPergiHandler);

                // Handler functions
                function ruteAsalPergiHandler() {
                    if (autoSwapCheckbox.checked) {
                        ruteTujuanKembali.value = this.value;
                        console.log(`Auto-swap: copied asal pergi "${this.value}" to tujuan kembali`);
                    }
                }

                function ruteTujuanPergiHandler() {
                    if (autoSwapCheckbox.checked) {
                        ruteAsalKembali.value = this.value;
                        console.log(`Auto-swap: copied tujuan pergi "${this.value}" to asal kembali`);
                    }
                }
            } else {
                console.error(`One or more auto-swap elements not found for form #${index}, transportasi #${transportasiId}`);
            }
        }

        // Function to fetch leave analysis for an employee
        function fetchLeaveAnalysis(index, karyawanId) {
            const form = document.getElementById(`form-${index}`);
            const leaveAnalysisSection = form.querySelector(`#karyawan-leave-analysis_${index}`);
            const leaveAnalysisContent = form.querySelector(`#leave-analysis-content_${index}`);

            // Show the section and display loading spinner
            leaveAnalysisSection.classList.remove('d-none');

            // Fetch leave analysis data from API
            fetch(`/api/karyawans/${karyawanId}/leave-analysis`)
                .then(response => response.json())
                .then(data => {
                    // Hide loading spinner and display results
                    displayLeaveAnalysis(index, data);
                })
                .catch(error => {
                    console.error('Error fetching leave analysis:', error);
                    leaveAnalysisContent.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Gagal memuat analisis cuti
                        </div>
                    `;
                });
        }

        // Function to calculate difference between start date and ideal dates
        function calculateDateDifference(index) {
            console.log(`Calculating date difference for form #${index}`);
            const form = document.getElementById(`form-${index}`);
            const tanggalMulaiInput = form.querySelector('.tanggal-mulai');
            const tanggalMulai = tanggalMulaiInput.value;

            if (!tanggalMulai) {
                console.log('No tanggal_mulai value, skipping calculation');
                return;
            }

            console.log(`Tanggal Mulai: ${tanggalMulai}`);
            const startDate = new Date(tanggalMulai);

            // Process all periodic leave types from the global window.periodicLeaves array
            if (window.periodicLeaves && window.periodicLeaves.length > 0) {
                console.log(`Processing ${window.periodicLeaves.length} periodic leave types from global array`);

                window.periodicLeaves.forEach(leave => {
                    const leaveName = leave.nama_jenis;
                    const leaveNameSlug = leaveName.replace(/\s+/g, '-').toLowerCase();

                    console.log(`Processing leave type: ${leaveName}, slug: ${leaveNameSlug}`);

                    // Check for DOH comparison element
                    const dohComparisonElement = document.getElementById(`date-comparison-doh-${index}-${leaveNameSlug}`);
                    console.log(`DOH comparison element found: ${dohComparisonElement ? 'Yes' : 'No'}`);

                    if (dohComparisonElement && leave.nextIdealDohDateObj) {
                        const diffDays = calculateDaysDifference(startDate, leave.nextIdealDohDateObj);
                        console.log(`Difference in days (DOH): ${diffDays}`);
                        updateComparisonDisplay(dohComparisonElement, diffDays);
                    }

                    // Check for Actual comparison element
                    const actualComparisonElement = document.getElementById(`date-comparison-actual-${index}-${leaveNameSlug}`);
                    console.log(`Actual comparison element found: ${actualComparisonElement ? 'Yes' : 'No'}`);

                    if (actualComparisonElement && leave.nextIdealActualDateObj) {
                        const diffDays = calculateDaysDifference(startDate, leave.nextIdealActualDateObj);
                        console.log(`Difference in days (Actual): ${diffDays}`);
                        updateComparisonDisplay(actualComparisonElement, diffDays);
                    }
                });

                // After updating the comparison displays, check if we need to show a warning
                // for dates that are too early compared to ideal dates
                checkIfDateIsTooEarly(index);
            } else {
                console.log('No periodic leave data available in window.periodicLeaves');
            }
        }

        // Calculate days difference between two dates
        function calculateDaysDifference(date1, date2) {
            // Convert both dates to UTC to avoid timezone issues
            const utc1 = Date.UTC(date1.getFullYear(), date1.getMonth(), date1.getDate());
            const utc2 = Date.UTC(date2.getFullYear(), date2.getMonth(), date2.getDate());

            // Calculate difference in days
            const diffMs = utc1 - utc2;
            const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

            return diffDays;
        }

        // Update comparison display with appropriate styling
        function updateComparisonDisplay(element, diffDays) {
            let badgeClass, icon, message;

            console.log(`Updating comparison display for element ${element.id} with diffDays=${diffDays}`);

            // Logika baru: diffDays negatif berarti tanggal mulai lebih awal dari tanggal ideal (tidak bagus)
            // diffDays positif berarti tanggal mulai lebih lambat dari tanggal ideal (bagus)

            if (diffDays < -14) {
                // Lebih dari 2 minggu terlalu awal (sangat tidak direkomendasikan)
                badgeClass = 'bg-danger';
                icon = 'fa-exclamation-circle';
                message = `${Math.abs(diffDays)} hari terlalu awal`;
            } else if (diffDays < -7) {
                // 1-2 minggu terlalu awal (tidak direkomendasikan)
                badgeClass = 'bg-warning text-dark';
                icon = 'fa-exclamation-triangle';
                message = `${Math.abs(diffDays)} hari terlalu awal`;
            } else if (diffDays < 0) {
                // Kurang dari 1 minggu terlalu awal (perhatian)
                badgeClass = 'bg-warning text-dark';
                icon = 'fa-exclamation-triangle';
                message = `${Math.abs(diffDays)} hari terlalu awal`;
            } else if (diffDays === 0) {
                // Tepat waktu
                badgeClass = 'bg-success';
                icon = 'fa-check-circle';
                message = 'Tepat waktu';
            } else if (diffDays <= 7) {
                // Sampai 1 minggu setelah tanggal ideal (bagus)
                badgeClass = 'bg-success';
                icon = 'fa-check-circle';
                message = `${diffDays} hari setelah tanggal ideal`;
            } else if (diffDays <= 14) {
                // 1-2 minggu setelah tanggal ideal (sangat bagus)
                badgeClass = 'bg-success';
                icon = 'fa-check-circle';
                message = `${diffDays} hari setelah tanggal ideal`;
            } else {
                // Lebih dari 2 minggu setelah tanggal ideal (sangat bagus)
                badgeClass = 'bg-success';
                icon = 'fa-check-circle';
                message = `${diffDays} hari setelah tanggal ideal`;
            }

            element.innerHTML = `<span class="badge ${badgeClass}"><i class="fas ${icon} me-1"></i> ${message}</span>`;
            console.log(`Updated element ${element.id} with HTML: ${element.innerHTML}`);
        }

        // Function to get selected leave types from the form
        function getSelectedLeaveTypes(index) {
            const form = document.getElementById(`form-${index}`);
            const jenisCutiSelects = form.querySelectorAll('.jenis-cuti-select');
            const selectedLeaveTypes = [];

            jenisCutiSelects.forEach(select => {
                if (select.value) {
                    const selectedOption = select.options[select.selectedIndex];
                    const jenisCutiId = select.value;

                    // Get the full name without the days part
                    // Example: "Cuti periodik (Lokal) (12 hari)" -> "Cuti periodik (Lokal)"
                    let fullText = selectedOption.textContent.trim();
                    let jenisCutiName = fullText;

                    // Extract just the name part without the days
                    const daysMatch = fullText.match(/\(\d+\s+hari\)$/);
                    if (daysMatch) {
                        jenisCutiName = fullText.substring(0, fullText.lastIndexOf(daysMatch[0])).trim();
                    }

                    console.log(`Selected leave type: ID=${jenisCutiId}, Name="${jenisCutiName}"`);

                    selectedLeaveTypes.push({
                        id: jenisCutiId,
                        name: jenisCutiName
                    });
                }
            });

            return selectedLeaveTypes;
        }

        // Function to display leave analysis data
        function displayLeaveAnalysis(index, data) {
            console.log(`Displaying leave analysis for form #${index}`);
            const form = document.getElementById(`form-${index}`);
            const leaveAnalysisContent = form.querySelector(`#leave-analysis-content_${index}`);

            if (!leaveAnalysisContent) {
                console.error(`Leave analysis content element not found for form #${index}`);
                return;
            }

            // If no data available
            if (!data || Object.keys(data).length === 0) {
                leaveAnalysisContent.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Tidak ada data analisis cuti tersedia
                    </div>
                `;
                return;
            }

            // Get selected leave types from the form
            const selectedLeaveTypes = getSelectedLeaveTypes(index);
            console.log(`Selected leave types for form #${index}:`, selectedLeaveTypes);

            // If no leave types are selected yet, show a message
            if (selectedLeaveTypes.length === 0) {
                leaveAnalysisContent.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Pilih jenis cuti untuk melihat analisis cuti
                    </div>
                `;
                return;
            }

            // Group by leave types (regular and periodic)
            const regularLeaves = [];
            // Use window.periodicLeaves to make it accessible globally
            window.periodicLeaves = [];

            // Process and categorize leave types, but only include selected ones
            Object.keys(data).forEach(key => {
                const leaveInfo = data[key];

                // Check if this leave type exactly matches one of the selected types
                const isSelected = selectedLeaveTypes.some(selectedType => {
                    // For exact matching, we need to be very specific about the leave type name
                    const leaveInfoName = leaveInfo.nama_jenis.toLowerCase().trim();
                    const selectedTypeName = selectedType.name.toLowerCase().trim();

                    // For periodic leave types, we need to be more specific
                    if (leaveInfoName.includes('periodik')) {
                        // For "Cuti periodik (Lokal)" and "Cuti periodik (Luar)",
                        // we need to match the exact type including the (Lokal) or (Luar) part
                        return leaveInfoName === selectedTypeName;
                    } else {
                        // For other leave types, we can be a bit more flexible
                        return leaveInfoName === selectedTypeName ||
                               leaveInfoName.startsWith(selectedTypeName);
                    }
                });

                // Only include selected leave types
                if (isSelected) {
                    if (leaveInfo.nama_jenis.toLowerCase().includes('periodik')) {
                        window.periodicLeaves.push(leaveInfo);
                    } else {
                        // For annual leave, we only need to show the balance
                        // We don't need to show ideal dates for annual leave
                        if (leaveInfo.nama_jenis.toLowerCase().includes('tahunan')) {
                            leaveInfo.is_annual_leave = true;
                        }
                        regularLeaves.push(leaveInfo);
                    }
                }
            });

            console.log(`Periodic leaves for form #${index}:`, window.periodicLeaves);
            console.log(`Regular leaves for form #${index}:`, regularLeaves);

            let htmlContent = '';

            // Display periodic leaves - each in its own container
            window.periodicLeaves.forEach(leave => {
                const isPeriodikLokal = leave.nama_jenis.toLowerCase().includes('lokal');
                const leaveTypeLabel = isPeriodikLokal ? 'Lokal' : 'Luar';
                const leaveNameSlug = leave.nama_jenis.toLowerCase().replace(/\s+/g, '-');

                console.log(`Creating card for ${leave.nama_jenis} in form #${index}`);

                // Start a new container for this leave type
                htmlContent += `
                    <div class="card mb-3 leave-analysis-card" data-leave-type="${leaveNameSlug}" data-form-index="${index}">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">${leave.nama_jenis}</h6>
                        </div>
                        <div class="card-body">
                `;

                // Determine recommendation based on DOH and Ideal Actual dates
                let recommendationHtml = '';

                // Prepare DOH-based ideal date for next period
                let dohIdealDateHtml = '';
                if (leave.next_ideal_doh_date) {
                    const nextIdealDohDate = new Date(leave.next_ideal_doh_date);
                    const formattedDohIdealDate = nextIdealDohDate.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });

                    // Store the date for later comparison
                    leave.nextIdealDohDateObj = nextIdealDohDate;

                    const dohComparisonId = `date-comparison-doh-${index}-${leaveNameSlug}`;
                    console.log(`Creating DOH comparison element with ID: ${dohComparisonId}`);

                    dohIdealDateHtml = `
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge bg-primary me-2">
                                <i class="fas fa-calendar-check me-1"></i>
                            </span>
                            <span>Tanggal Ideal (DOH): <strong>${formattedDohIdealDate}</strong></span>
                            <span class="date-comparison-doh" id="${dohComparisonId}"></span>
                        </div>
                    `;
                }

                // Prepare Ideal Actual date for next period
                let idealActualDateHtml = '';
                // Only show Ideal Actual date if the employee has applied for this leave type before
                if (leave.next_ideal_actual_date && leave.has_applied_for_leave) {
                    const nextIdealActualDate = new Date(leave.next_ideal_actual_date);
                    const formattedIdealActualDate = nextIdealActualDate.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });

                    // Store the date for later comparison
                    leave.nextIdealActualDateObj = nextIdealActualDate;

                    const actualComparisonId = `date-comparison-actual-${index}-${leaveNameSlug}`;
                    console.log(`Creating Actual comparison element with ID: ${actualComparisonId}`);

                    idealActualDateHtml = `
                        <div class="d-flex align-items-center">
                            <span class="badge bg-info me-2">
                                <i class="fas fa-calendar-alt me-1"></i>
                            </span>
                            <span>Tanggal Ideal Actual: <strong>${formattedIdealActualDate}</strong></span>
                            <span class="date-comparison-actual" id="${actualComparisonId}"></span>
                        </div>
                    `;
                }

                // Combine both recommendations
                if (dohIdealDateHtml || idealActualDateHtml) {
                    recommendationHtml = `
                        <div class="mt-2">
                            ${dohIdealDateHtml}
                            ${idealActualDateHtml}
                        </div>
                    `;
                }

                // Add the recommendation HTML to the container
                htmlContent += recommendationHtml;

                // Close the container for this leave type
                htmlContent += `
                        </div>
                    </div>
                `;
            });

            // Display regular leave types - each in its own container
            regularLeaves.forEach(leave => {
                let statusText = '';
                let statusClass = 'info';
                let additionalInfo = '';
                const leaveNameSlug = leave.nama_jenis.toLowerCase().replace(/\s+/g, '-');

                console.log(`Creating regular leave card for ${leave.nama_jenis} in form #${index}`);

                if (!leave.eligible) {
                    statusText = leave.message || 'Tidak memenuhi syarat';
                    statusClass = 'warning';
                } else {
                    statusText = `Sisa: ${leave.sisa} hari`;
                    statusClass = leave.sisa > 0 ? 'success' : 'danger';

                    // Add special note for annual leave
                    if (leave.is_annual_leave) {
                        additionalInfo = `
                            <div class="mt-2 small text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Cuti Tahunan dapat diambil kapan saja dalam periode 1 tahun tanpa batasan waktu.
                            </div>
                        `;
                    }
                }

                // Start a new container for this leave type
                htmlContent += `
                    <div class="card mb-3 leave-analysis-card" data-leave-type="${leaveNameSlug}" data-form-index="${index}">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">${leave.nama_jenis}</h6>
                            <span class="badge bg-${statusClass}">${statusText}</span>
                        </div>
                        <div class="card-body">
                            ${additionalInfo}
                        </div>
                    </div>
                `;
            });

            // If no leave types were found in the analysis, show a message
            if (window.periodicLeaves.length === 0 && regularLeaves.length === 0) {
                htmlContent = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Tidak ada data analisis untuk jenis cuti yang dipilih
                    </div>
                `;
            }

            console.log(`Setting HTML content for leave analysis in form #${index}`);
            leaveAnalysisContent.innerHTML = htmlContent;

            // Calculate date difference if tanggal_mulai is already filled
            const tanggalMulaiInput = form.querySelector('.tanggal-mulai');
            if (tanggalMulaiInput.value) {
                console.log(`Tanggal mulai already filled for form #${index}, calculating date difference`);
                // Use setTimeout to ensure the DOM is updated before calculating
                setTimeout(() => {
                    calculateDateDifference(index);
                }, 100);
            } else {
                console.log(`Tanggal mulai not filled for form #${index}, skipping date difference calculation`);
            }
        }

        // Function to validate all forms before submission
        function validateAllForms() {
            let isValid = true;
            const forms = document.querySelectorAll('.cuti-form');

            console.log(`Validating ${forms.length} forms`);

            // Loop through each form
            for (let idx = 0; idx < forms.length; idx++) {
                const form = forms[idx];
                const formIndex = idx + 1;

                console.log(`Validating form #${formIndex}`);

                const karyawanId = form.querySelector('.karyawan-id-input').value;
                const tanggalMulai = form.querySelector('.tanggal-mulai').value;
                const jenisCutiSelects = form.querySelectorAll('.jenis-cuti-select');
                const memoKompensasiContainer = form.querySelector('.memo-kompensasi-container');

                console.log(`Form #${formIndex}: karyawan_id=${karyawanId}, tanggal_mulai=${tanggalMulai}, jenis_cuti_selects=${jenisCutiSelects.length}`);

                // Check karyawan
                if (!karyawanId) {
                    alert(`Form #${formIndex}: Pilih karyawan terlebih dahulu`);
                    isValid = false;
                    break;
                }

                // Check tanggal mulai
                if (!tanggalMulai) {
                    // Gunakan modal Bootstrap alih-alih alert
                    document.getElementById('tanggalMulaiText').textContent = `Form #${formIndex}: Masukkan tanggal mulai cuti`;
                    const tanggalMulaiModal = new bootstrap.Modal(document.getElementById('tanggalMulaiModal'));
                    tanggalMulaiModal.show();
                    isValid = false;
                    break;
                }

                // Check jenis cuti
                let hasSelectedJenisCuti = false;
                jenisCutiSelects.forEach(select => {
                    if (select.value) {
                        hasSelectedJenisCuti = true;
                    }
                });

                if (!hasSelectedJenisCuti) {
                    alert(`Form #${formIndex}: Pilih minimal satu jenis cuti`);
                    isValid = false;
                    break;
                }

                // Check memo kompensasi if visible
                if (!memoKompensasiContainer.classList.contains('d-none')) {
                    const isMemoNeeded = form.querySelector('.is-memo-needed').checked;

                    // Only validate memo details if the checkbox is checked
                    if (isMemoNeeded) {
                        // Removed validation for memo fields - they are now optional
                        // User can fill them later in the Monitoring Memo page
                        console.log(`Form #${formIndex}: Memo kompensasi akan diproses, data bisa dilengkapi nanti`);
                    }
                }

                // Check transportasi details if checked
                const transportasiCheckboxes = form.querySelectorAll('.transportasi-checkbox:checked');
                for (let i = 0; i < transportasiCheckboxes.length; i++) {
                    const checkbox = transportasiCheckboxes[i];
                    const transportasiId = checkbox.dataset.transportasiId;
                    const checkboxIndex = checkbox.dataset.index; // Get the correct form index

                    console.log(`Form #${formIndex}: Validating transportasi ID=${transportasiId}, checkbox index=${checkboxIndex}`);

                    // Make sure the checkbox has the correct index
                    if (checkboxIndex != formIndex) {
                        console.warn(`Form #${formIndex}: Checkbox index mismatch: ${checkboxIndex}, updating to ${formIndex}`);
                        checkbox.setAttribute('data-index', formIndex);
                    }

                    // Use the correct form index from the checkbox's data attribute
                    const ruteAsalPergiInput = form.querySelector(`#rute_asal_pergi_${formIndex}_${transportasiId}`);
                    const ruteTujuanPergiInput = form.querySelector(`#rute_tujuan_pergi_${formIndex}_${transportasiId}`);
                    const ruteAsalKembaliInput = form.querySelector(`#rute_asal_kembali_${formIndex}_${transportasiId}`);
                    const ruteTujuanKembaliInput = form.querySelector(`#rute_tujuan_kembali_${formIndex}_${transportasiId}`);

                    if (!ruteAsalPergiInput || !ruteTujuanPergiInput || !ruteAsalKembaliInput || !ruteTujuanKembaliInput) {
                        console.error(`Form #${formIndex}: Transportasi input elements not found!`);
                        console.error(`Looking for: rute_asal_pergi_${formIndex}_${transportasiId}, rute_tujuan_pergi_${formIndex}_${transportasiId}, etc.`);

                        // Try to find the elements with the checkbox index instead
                        const altRuteAsalPergiInput = form.querySelector(`#rute_asal_pergi_${checkboxIndex}_${transportasiId}`);
                        const altRuteTujuanPergiInput = form.querySelector(`#rute_tujuan_pergi_${checkboxIndex}_${transportasiId}`);
                        const altRuteAsalKembaliInput = form.querySelector(`#rute_asal_kembali_${checkboxIndex}_${transportasiId}`);
                        const altRuteTujuanKembaliInput = form.querySelector(`#rute_tujuan_kembali_${checkboxIndex}_${transportasiId}`);

                        console.log(`Alternative elements found: ${!!altRuteAsalPergiInput}, ${!!altRuteTujuanPergiInput}, ${!!altRuteAsalKembaliInput}, ${!!altRuteTujuanKembaliInput}`);

                        // Use modal instead of alert
                        document.getElementById('transportasiText').textContent = `Form #${formIndex}: Elemen input rute transportasi tidak ditemukan. Silakan coba klik ulang checkbox transportasi.`;
                        const transportasiModal = new bootstrap.Modal(document.getElementById('transportasiModal'));
                        transportasiModal.show();

                        isValid = false;
                        break;
                    }

                    if (!ruteAsalPergiInput.value || !ruteTujuanPergiInput.value ||
                        !ruteAsalKembaliInput.value || !ruteTujuanKembaliInput.value) {
                        // Get the name of the transportation type
                        const transportasiName = checkbox.closest('.card').querySelector('.form-check-label').textContent.trim();

                        // Use modal instead of alert
                        document.getElementById('transportasiText').textContent = `Form #${formIndex}: Lengkapi detail rute transportasi ${transportasiName} yang dipilih`;
                        const transportasiModal = new bootstrap.Modal(document.getElementById('transportasiModal'));
                        transportasiModal.show();

                        isValid = false;
                        break;
                    }
                }

                if (!isValid) break;
            }

            console.log(`Final validation result: ${isValid}`);
            return isValid;
        }

        // Initialize the first form
        initializeForm(1);
    });
</script>
@endpush