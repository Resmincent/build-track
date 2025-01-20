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
                    <div class="col-md-6">
                        @if(auth()->user()->is_admin)
                        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> Tambah Pengguna
                        </a>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('users.index') }}" class="d-flex justify-content-end">
                            <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Cari pengguna..." value="{{ request()->input('search') }}" style="max-width: 200px;">
                            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                        </form>
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
                                <!-- Perbaiki dengan menggunakan $index + 1 + ($users->currentPage() - 1) * $users->perPage() -->
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

                                    @if(auth()->user()->is_admin && auth()->id() !== $user->id)
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">
                                            Hapus
                                        </button>
                                    </form>
                                    @endif
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

@endsection

@push('scripts')
<script>
    // Auto dismiss alerts after 5 seconds
    window.setTimeout(function() {
        $("#autoDismissAlert").fadeTo(500, 0).slideUp(500, function() {
            $(this).remove();
        });
    }, 5000);

</script>
@endpush
