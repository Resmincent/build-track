@extends('layouts.admin')

@section('main-content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        Form Pengajuan Bahan
                    </h3>
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

                    <form action="{{ route('request-for-materials.store') }}" method="POST">
                        @csrf
                        <div class="form-group row mb-4">
                            <label for="material_id" class="col-lg-3 col-md-4 col-12 col-form-label fw-bold">
                                Material <span class="text-danger">*</span>
                            </label>
                            <div class="col-lg-9 col-md-8 col-12">
                                <select name="material_id" id="material_id" class="form-select @error('material_id') is-invalid @enderror" required>
                                    <option value="">-- Select Material --</option>
                                    @foreach($materials as $material)
                                    <option value="{{ $material->id }}">
                                        {{ $material->name }} -
                                        Stock: {{ $material->stock }} {{ $material->unit }} -
                                        Rp {{ number_format($material->price, 0, ',', '.') }} / {{ $material->unit }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('material_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <div class="form-group row mb-4">
                            <label for="quantity" class="col-sm-3 col-form-label fw-bold">Quantity <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity') }}" min="1" required>
                                @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-4">
                            <label for="date_needed" class="col-sm-3 col-form-label fw-bold">Date Needed <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control @error('date_needed') is-invalid @enderror" id="date_needed" name="date_needed" value="{{ old('date_needed') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                @error('date_needed')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Submit Request
                                </button>
                                <a href="{{ route('request-for-materials.index') }}" class="btn btn-secondary ms-2">
                                    <i class="fas fa-times me-1"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
