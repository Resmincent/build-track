@extends('layouts.admin')

@section('main-content')
<h1 class="h3 text-gray-800">{{ __('Manajemen Pengguna') }}</h1>

@if (session('success'))
<div class="alert alert-success border-left-success alert-dismissible fade show" role="alert" id="autoDismissAlert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger border-left-danger alert-dismissible fade show" role="alert" id="autoDismissAlert">
    {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <!-- Action Buttons -->
                <div class="row mb-4">
                    <div class="col-lg-8 col-md-6 col-12 mb-2 mb-md-0">
                        <form method="GET" action="{{ route('users.index') }}" class="d-flex flex-column flex-md-row">
                            <input type="text" name="search" class="form-control form-control-sm mb-2 mb-md-0 mr-md-2" placeholder="{{ __('Cari Pengguna...') }}" value="{{ request()->input('search') }}">
                            <button type="submit" class="btn btn-primary btn-sm">{{ __('Cari') }}</button>
                        </form>
                    </div>

                    <div class="col-lg-4 col-md-6 col-12 d-flex justify-content-md-end justify-content-center">
                        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                            <button type="button" class="btn btn-sm btn-primary w-100 w-md-50">
                                Tambah Pengguna
                            </button>
                        </a>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table align-middle border rounded table-row-dashed fs-6 g-5">
                        <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase">
                                <th>No</th>
                                <th>Nama</th>
                                <th>Nama Belakang</th>
                                <th>Email</th>
                                <th>Telepon</th>
                                <th>Status</th>
                                <th>Tanggal Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            @forelse ($users as $index => $user)
                            <tr>
                                <td>{{ $index + 1 + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-{{ $user->is_admin ? 'primary' : 'secondary' }}">
                                        {{ $user->is_admin ? 'Admin' : 'User' }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                <td>
                                    @if(auth()->user()->is_admin || auth()->id() === $user->id)
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-info">
                                        Edit
                                    </a>
                                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteUserModal-{{ $user->id }}">Hapus</button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada data pengguna</td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-end mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@foreach ($users as $user)
<div class="modal fade" id="deleteUserModal-{{ $user->id }}" tabindex="-1" aria-labelledby="deleteUserModalLabel-{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel-{{ $user->id }}">Hapus Bahan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus pengguna "{{ $user->name }}"?</p>
                    <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection
