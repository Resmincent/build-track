@extends('layouts.admin')

@section('main-content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Form Permintaan Pembelian Bahan</h3>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('purchase-materials.store') }}" method="POST">
                        @csrf
                        <div class="form-group row mb-4">
                            <label for="name" class="col-lg-3 col-md-4 col-12 col-form-label fw-bold">
                                Nama Material <span class="text-danger">*</span>
                            </label>
                            <div class="col-lg-9 col-md-8 col-12">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" id="name" name="name" required maxlength="255">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label for="quantity" class="col-sm-3 col-form-label fw-bold">
                                Quantity <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity') }}" min="1" required>
                                @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label for="unit" class="col-lg-3 col-md-4 col-12 col-form-label fw-bold">
                                Satuan <span class="text-danger">*</span>
                            </label>
                            <div class="col-lg-9 col-md-8 col-12">
                                <input type="text" class="form-control @error('unit') is-invalid @enderror" value="{{ old('unit') }}" id="unit" name="unit" required maxlength="255">
                                @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row mb-4">
                            <label for="description" class="col-sm-3 col-form-label fw-bold">
                                Deskripsi <span class="text-danger">*</span>
                            </label>
                            <div class="col-sm-9">
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" required maxlength="1000">{{ old('description') }}</textarea>
                                @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-9 offset-sm-3 d-flex">
                                <button type="submit" class="btn btn-primary mr-3">Submit</button>
                                <a href="{{ route('purchase-materials.index') }}" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
