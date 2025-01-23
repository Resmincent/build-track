@extends('layouts.admin')

@section('main-content')
<!-- Page Heading -->
<h1 class="h3 text-gray-800">Laporan Stok Bulanan - {{ $summary['month_name'] }}</h1>

@if (session('success'))
<div class="alert alert-success border-left-success alert-dismissible fade show" role="alert" id="autoDismissAlert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="row">
    <div class="col-lg-12 mx-auto">
        <div class="card">
            <div class="card-body">
                <!-- Summary Row -->
                <div class="row mb-4">
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Jenis Barang</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['total_items'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Nilai Terpakai</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($summary['total_value_used'], 0, ',', '.') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Month Selection and Search Row -->
                <div class="row mb-4">
                    <div class="col-lg-8 col-md-6 col-12 mb-2 mb-md-0">
                        <form method="GET" action="{{ route('admin.reports.monthly-stock') }}" class="d-flex flex-column flex-md-row">
                            <select name="month" class="form-control form-control-sm mb-2 mb-md-0 mr-md-2">
                                @foreach($availableMonths as $month)
                                <option value="{{ $month['value'] }}" {{ $selectedDate->format('Y-m') == $month['value'] ? 'selected' : '' }}>
                                    {{ $month['label'] }}
                                </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm" style="width: 125px">{{ __('Pilih Bulan') }}</button>
                        </form>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table align-middle border rounded table-row-dashed fs-6 g-5">
                        <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase">
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Stok Tersisa</th>
                                <th>Jumlah Terpakai</th>
                                <th>Harga Satuan</th>
                                <th>Total Harga Terpakai</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            @foreach ($materials as $material)
                            <tr>
                                <td style="width: 50px;">{{ $material->material_id }}</td>
                                <td style="width: 100px;">{{ $material->name }}</td>
                                <td style="width: 150px;">{{ $material->category->name }}</td>
                                <td style="width: 50px;">
                                    <div>
                                        {{ $material->stock }}
                                        <span class="badge rounded-pill ms-1">{{ $material->unit }}</span>
                                    </div>
                                </td>
                                <td style="width: 50px;">{{ $material->used_quantity ?? 0 }} {{ $material->unit }}</td>
                                <td style="width: 150px;">Rp {{ number_format($material->price, 0, ',', '.') }}</td>
                                <td style="width: 200px;">Rp {{ number_format($material->total_used_value, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" class="text-end fw-bold">Total Keseluruhan:</td>
                                <td class="fw-bold">Rp {{ number_format($summary['total_value_used'], 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
