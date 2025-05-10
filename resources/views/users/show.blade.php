@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Detail Pengguna</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">ID Pengguna</div>
                        <div class="col-md-9">{{ $user->id }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Nama</div>
                        <div class="col-md-9">{{ $user->name }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Username</div>
                        <div class="col-md-9">{{ $user->username }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Email</div>
                        <div class="col-md-9">
                            <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Dibuat Pada</div>
                        <div class="col-md-9">{{ $user->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Diperbarui Pada</div>
                        <div class="col-md-9">{{ $user->updated_at->format('d M Y, H:i') }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-3 fw-bold">Peran</div>
                        <div class="col-md-9">
                            @foreach($user->roles as $role)
                                <span class="badge bg-{{ $role->name == 'admin' ? 'danger' : 'primary' }}">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    
                    @if(auth()->id() != $user->id && $isAdmin)
                        <div class="mt-4 d-flex justify-content-end">
                            <button type="button" class="btn btn-danger delete-btn" 
                                data-user-id="{{ $user->id }}" 
                                data-user-name="{{ $user->name }}">
                                <i class="fas fa-trash-alt me-1"></i> Hapus Pengguna
                            </button>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Hapus Pengguna?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle text-warning fa-3x"></i>
                </div>
                <p id="deleteModalText"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Konfirmasi hapus dengan modal Bootstrap
        const deleteBtn = document.querySelector('.delete-btn');
        
        if (deleteBtn) {
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            const deleteModalText = document.getElementById('deleteModalText');
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            
            deleteBtn.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                
                deleteModalText.innerHTML = `Apakah Anda yakin ingin menghapus pengguna <strong>${userName}</strong>? Tindakan ini tidak dapat dibatalkan.`;
                
                // Setup confirmation button
                confirmDeleteBtn.addEventListener('click', function submitDeleteForm() {
                    const deleteForm = document.getElementById('deleteForm');
                    deleteForm.action = `/users/${userId}`;
                    deleteForm.submit();
                    
                    // Remove event listener to prevent duplicates
                    confirmDeleteBtn.removeEventListener('click', submitDeleteForm);
                    
                    // Hide modal
                    deleteModal.hide();
                });
                
                // Show the modal
                deleteModal.show();
            });
        }
    });
</script>
@endpush