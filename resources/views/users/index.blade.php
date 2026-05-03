@extends('layouts.master')

@section('title', 'Kelola Akun')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h2 class="mb-0">Kelola Akun</h2>
        <p class="text-secondary mb-0">Manajemen pengguna sistem dan perannya.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="ti ti-plus"></i> Tambah Akun Baru
    </button>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="py-3">Email</th>
                        <th class="py-3">Role</th>
                        <th class="py-3">Dibuat Pada</th>
                        <th class="py-3 text-end px-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td class="px-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="icon-shape bg-light-primary text-primary rounded-circle" style="width: 40px; height: 40px;">
                                        <i class="ti ti-user fs-4"></i>
                                    </div>
                                    <span class="fw-bold">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role == 'owner')
                                    <span class="badge bg-warning-subtle text-warning px-3 py-2 border border-warning-subtle">
                                        <i class="ti ti-crown small"></i> OWNER
                                    </span>
                                @else
                                    <span class="badge bg-info-subtle text-info px-3 py-2 border border-info-subtle">
                                        <i class="ti ti-settings small"></i> ADMIN
                                    </span>
                                @endif
                            </td>
                            <td class="text-secondary small">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-end px-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <button class="btn btn-light-primary btn-sm btn-icon rounded-circle" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editUserModal{{ $user->id }}"
                                            title="Edit Akun">
                                        <i class="ti ti-edit fs-5"></i>
                                    </button>
                                    
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="delete-user-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-light-danger btn-sm btn-icon rounded-circle btn-delete-user" title="Hapus Akun">
                                            <i class="ti ti-trash fs-5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- MODAL EDIT USER -->
                        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header border-bottom-0 pt-4 px-4">
                                        <h5 class="modal-title fw-bold">Edit Akun Pengguna</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body px-4">
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Nama Lengkap</label>
                                                <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Email</label>
                                                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Role (Peran)</label>
                                                <select name="role" class="form-select" required>
                                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                                    <option value="owner" {{ $user->role == 'owner' ? 'selected' : '' }}>Owner</option>
                                                </select>
                                            </div>
                                            <div class="mb-1">
                                                <label class="form-label small fw-bold">Kata Sandi Baru (Opsional)</label>
                                                <input type="password" name="password" class="form-control" placeholder="Isi hanya jika ingin ganti password">
                                                <small class="text-secondary italic" style="font-size: 0.7rem;">* Minimal 8 karakter.</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top-0 pb-4 px-4">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL ADD USER -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Tambah Akun Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Budi Santoso" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="budi@example.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Role (Peran)</label>
                        <select name="role" class="form-select" required>
                            <option value="admin">Admin</option>
                            <option value="owner">Owner</option>
                        </select>
                    </div>
                    <div class="mb-1">
                        <label class="form-label small fw-bold">Kata Sandi</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        <small class="text-secondary italic" style="font-size: 0.7rem;">* Minimal 8 karakter.</small>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 px-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">Buat Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.btn-delete-user').on('click', function() {
            const form = $(this).closest('form');
            Swal.fire({
                title: 'Hapus Akun?',
                text: "Akun ini tidak akan bisa login lagi setelah dihapus.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
</script>

<style>
    .bg-light-primary {
        background-color: rgba(102, 126, 234, 0.1);
    }
    .btn-light-primary {
        background-color: rgba(102, 126, 234, 0.1);
        color: #667eea;
        border: none;
    }
    .btn-light-primary:hover {
        background-color: #667eea;
        color: white;
    }
    .btn-light-danger {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border: none;
    }
    .btn-light-danger:hover {
        background-color: #dc3545;
        color: white;
    }
    .form-control, .form-select {
        border-radius: 8px;
        padding: 10px 12px;
    }
</style>
@endsection
