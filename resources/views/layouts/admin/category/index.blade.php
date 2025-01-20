@extends('layouts.admin')

@section('main-content')
<!-- Page Heading -->
<h1 class="h3 text-gray-800">{{ __('List Kategori') }}</h1>

@if (session('success'))
<div class="alert alert-success border-left-success alert-dismissible fade show" role="alert" id="autoDismissAlert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger border-left-danger" role="alert" id="autoDismissError">
    <ul class="pl-4 my-2">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-lg-8 col-md-6 col-12 mb-2 mb-md-0">
                        <form method="GET" action="{{ route('categories.index') }}" class="d-flex">
                            <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="{{ __('Cari Merek...') }}" value="{{ request()->input('search') }}">
                            <button type="submit" class="btn btn-primary btn-sm">{{ __('Cari') }}</button>
                        </form>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 d-flex justify-content-end">
                        <button type="button" class="btn btn-sm btn-primary w-100" data-toggle="modal" data-target="#tambahCategory">
                            Tambah Kategori
                        </button>

                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle border rounded table-row-dashed fs-6 g-5">
                        <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase">
                                <th class="min-w-100px">Kategori</th>
                                <th class="min-w-100px">Deskripsi</th>
                                <th class="min-w-100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            @foreach ($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->description }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editCategory-{{ $category->id }}">Edit</button>
                                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteCategory-{{ $category->id }}">Hapus</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pagination justify-content-center mb-10">
                {{ $categories->appends(['search' => request()->input('search')])->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Create Category -->
<div class="modal fade" id="tambahCategory" tabindex="-1" aria-labelledby="tambahCategoryLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahCategoryLabel">Tambah Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nama Kategori</label>
                        <input type="text" class="form-control" id="name" name="name" required maxlength="255">
                    </div>
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required maxlength="1000"></textarea>
                        <small class="text-muted">Maksimal 1000 karakter</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Category -->
@foreach ($categories as $category)
<div class="modal fade" id="editCategory-{{ $category->id }}" tabindex="-1" aria-labelledby="editCategoryLabel-{{ $category->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryLabel-{{ $category->id }}">Edit Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name_{{ $category->id }}">Nama Kategori</label>
                        <input type="text" class="form-control" id="edit_name_{{ $category->id }}" name="name" value="{{ $category->name }}" required maxlength="255">
                    </div>
                    <div class="form-group">
                        <label for="edit_description_{{ $category->id }}">Deskripsi</label>
                        <textarea class="form-control" id="edit_description_{{ $category->id }}" name="description" rows="3" required maxlength="1000">{{ $category->description }}</textarea>
                        <small class="text-muted">Maksimal 1000 karakter</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Modal Delete Category -->
@foreach ($categories as $category)
<div class="modal fade" id="deleteCategory-{{ $category->id }}" tabindex="-1" aria-labelledby="deleteCategoryLabel-{{ $category->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('categories.destroy', $category->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCategoryLabel-{{ $category->id }}">Hapus Kategori</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus kategori "{{ $category->name }}"?</p>
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
