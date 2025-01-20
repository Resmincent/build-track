@extends('layouts.admin')

@section('main-content')
<!-- Page Heading -->
<h1 class="h3 text-gray-800">{{ __('List Bahan') }}</h1>

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
    <div class="col-lg-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-lg-8 col-md-6 col-12 mb-2 mb-md-0">
                        <form method="GET" action="{{ route('materials.index') }}" class="d-flex flex-column flex-md-row">
                            <input type="text" name="search" class="form-control form-control-sm mb-2 mb-md-0 mr-md-2" placeholder="{{ __('Cari Bahan...') }}" value="{{ request()->input('search') }}">
                            <button type="submit" class="btn btn-primary btn-sm">{{ __('Cari') }}</button>
                        </form>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 d-flex justify-content-md-end justify-content-center">
                        <button type="button" class="btn btn-sm btn-primary w-100 w-md-50" data-toggle="modal" data-target="#createMaterialModal">
                            Tambah Bahan
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle border rounded table-row-dashed fs-6 g-5">
                        <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase">
                                <th>No</th>
                                <th class="min-w-100px">ID Bahan</th>
                                <th class="min-w-100px">Kategori</th>
                                <th class="min-w-100px">Nama Bahan</th>
                                <th class="min-w-100px">Stok Barang</th>
                                <th class="min-w-100px">Satuan</th>
                                <th class="min-w-100px">Tanggal Dibuat</th>
                                <th class="min-w-100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            @foreach ($materials as $index => $material)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $material->material_id }}</td>
                                <td>{{ $material->category->name }}</td>
                                <td>{{ $material->name }}</td>
                                <td>{{ $material->stock }}</td>
                                <td>{{ $material->unit }}</td>
                                <td>{{ $material->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#editMaterialModal-{{ $material->id }}">Edit</button>
                                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteMaterialModal-{{ $material->id }}">Hapus</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal Create Material -->
<div class="modal fade" id="createMaterialModal" tabindex="-1" aria-labelledby="createMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('materials.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createMaterialModalLabel">Tambah Bahan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label required">Nama Bahan</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required maxlength="100">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="category_id" class="form-label required">Kategori</label>
                        <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="stock" class="form-label required">Stok</label>
                                <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock') }}" required min="0">
                                @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="unit" class="form-label required">Satuan</label>
                                <input type="text" class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" value="{{ old('unit') }}" required maxlength="100">
                                @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
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

<!-- Modal Edit Material -->
@foreach($materials as $material)
<div class="modal fade" id="editMaterialModal-{{ $material->id }}" tabindex="-1" aria-labelledby="editMaterialModalLabel-{{ $material->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('materials.update', $material->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editMaterialModalLabel-{{ $material->id }}">Edit Bahan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">ID Bahan</label>
                        <input type="text" class="form-control" value="{{ $material->material_id }}" readonly disabled>
                    </div>

                    <div class="form-group mb-3">
                        <label for="name_{{ $material->id }}" class="form-label required">Nama Bahan</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name_{{ $material->id }}" name="name" value="{{ old('name', $material->name) }}" required maxlength="100">
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="category_id_{{ $material->id }}" class="form-label required">Kategori</label>
                        <select class="form-control @error('category_id') is-invalid @enderror" id="category_id_{{ $material->id }}" name="category_id" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (old('category_id', $material->category_id) == $category->id) ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="stock_{{ $material->id }}" class="form-label required">Stok</label>
                                <input type="number" class="form-control @error('stock') is-invalid @enderror" id="stock_{{ $material->id }}" name="stock" value="{{ old('stock', $material->stock) }}" required min="0">
                                @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="unit_{{ $material->id }}" class="form-label required">Satuan</label>
                                <input type="text" class="form-control @error('unit') is-invalid @enderror" id="unit_{{ $material->id }}" name="unit" value="{{ old('unit', $material->unit) }}" required maxlength="100">
                                @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
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


<!-- Modal Delete Material -->
@foreach ($materials as $material)
<div class="modal fade" id="deleteMaterialModal-{{ $material->id }}" tabindex="-1" aria-labelledby="deleteMaterialModalLabel-{{ $material->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('materials.destroy', $material->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteMaterialModalLabel-{{ $material->id }}">Hapus Bahan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus bahan "{{ $material->name }}"?</p>
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
