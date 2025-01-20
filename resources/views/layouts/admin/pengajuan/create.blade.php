@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pengajuan Bahan</h3>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('request-for-materials.store') }}" method="POST">
                        @csrf
                        <div class="form-group row mb-3">
                            <label for="material_id" class="col-sm-2 col-form-label">Material</label>
                            <div class="col-sm-10">
                                <select name="material_id" id="material_id" class="form-control @error('material_id') is-invalid @enderror" required>
                                    <option value="">Select Material</option>
                                    @foreach($materials as $material)
                                    <option value="{{ $material->id }}" data-stock="{{ $material->stock }}" {{ old('material_id') == $material->id ? 'selected' : '' }}>
                                        {{ $material->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('material_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="quantity" class="col-sm-2 col-form-label">Quantity</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity') }}" min="1" required>
                                @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-3">
                            <label for="date_needed" class="col-sm-2 col-form-label">Date Needed</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control @error('date_needed') is-invalid @enderror" id="date_needed" name="date_needed" value="{{ old('date_needed') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                                @error('date_needed')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- <div class="form-group row mb-3">
                            <label for="stock" class="col-sm-2 col-form-label">Available Stock</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="stock" name="stock" value="" disabled>
                            </div>
                        </div> --}}

                        <div class="form-group row mb-3">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="submit" class="btn btn-primary">Submit Request</button>
                                <a href="{{ route('request-for-materials.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const materialSelect = document.getElementById('material_id');
        const stockInput = document.getElementById('stock');

        // Update stock input when material is selected
        materialSelect.addEventListener('change', function() {
            const selectedMaterial = materialSelect.options[materialSelect.selectedIndex];
            const stock = selectedMaterial.getAttribute('data-stock'); // Get stock value from data attribute
            stockInput.value = stock; // Update stock input value
            stockInput.disabled = true; // Disable stock input since it's just for display
        });

        // Optional: Set default stock value if there is already a selected material
        const defaultMaterial = materialSelect.options[materialSelect.selectedIndex];
        if (defaultMaterial) {
            const stock = defaultMaterial.getAttribute('data-stock');
            stockInput.value = stock;
            stockInput.disabled = true; // Disable stock input if a default material is selected
        }
    });

</script>
@endpush
