@extends('layouts.admin')

@section('main-content')

<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800">{{ __('Dashboard') }}</h1>

@if (session('success'))
<div class="alert alert-success border-left-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if (session('status'))
<div class="alert alert-success border-left-success" role="alert">
    {{ session('status') }}
</div>
@endif

<div class="row">
    @if (Auth::user()->is_admin)
    @foreach ($widget as $key => $value)
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">{{ ucfirst(str_replace('_', ' ', $key)) }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $value }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-bar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
    @endif
</div>

@if (Auth::user()->is_admin)
<div class="row">
    <div class="col-lg-6 col-md-8 col-sm-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header border-0 bg-white">
                <h6 class="card-title font-weight-bold text-dark mb-0">List Barang dengan Stok Kurang dari 10</h6>
            </div>
            <div class="card-body h-300px">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="text-uppercase text-gray-500 fw-bold fs-7">
                            <tr>
                                <th>Nama Bahan</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stockUnderLimit as $material)
                            <tr>
                                <td>{{ $material->name }}</td>
                                <td>{{ $material->category->name ?? '-' }}</td>
                                <td><span class="badge badge-danger">{{ $material->stock }}</span></td>
                                <td>{{ $material->unit }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Tidak ada bahan dengan stok kurang dari 10</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-8 col-sm-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header border-0 bg-white">
                <h6 class="card-title font-weight-bold text-dark mb-0">Bahan Yang Terakhir Diajukan</h6>
            </div>
            <div class="card-body h-300px">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="text-uppercase text-gray-500 fw-bold fs-7">
                            <tr>
                                <th>No</th>
                                <th>Material</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuanTerakhir as $index => $material)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $material->material->name }}</td>
                                <td>{{ $material->quantity }}</td>
                                <td><span class="badge badge-info">{{ $material->status }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada pengajuan terbaru</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 col-md-8 col-sm-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header border-0 bg-white">
                <h6 class="card-title font-weight-bold text-dark mb-0">Total harga dari stock yang terpakai</h6>
            </div>
            <div class="card-body h-300px">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="text-uppercase text-gray-500 fw-bold fs-7">
                            <tr>
                                <th>Nama Bahan</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Harga</th>
                                <th>Jumlah Terpakai</th>
                                <th>Total Harga Terpakai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($materials as $material)
                            <tr>
                                <td>{{ $material->name }}</td>
                                <td>{{ $material->category->name ?? '-' }}</td>
                                <td><span class="badge badge-danger">{{ $material->stock }}</span></td>
                                <td>Rp {{ number_format($material->price, 0, ',', '.') }}</td>
                                <td>{{ $material->used_quantity ?? 0 }} {{ $material->unit }}</td>
                                <td>Rp {{ number_format($material->total_used_value, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Tidak ada bahan dengan stok kurang dari 10</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-8 col-sm-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header border-0 bg-white">
                <h6 class="card-title font-weight-bold text-dark mb-0">Permintaan Bahan</h6>
            </div>
            <div class="card-body h-300px">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="text-uppercase text-gray-500 fw-bold fs-7">
                            <tr>
                                <th>No</th>
                                <th>Material</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchasematerials as $index => $material)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $material->name }}</td>
                                <td>{{ $material->quantity }}</td>
                                <td><span class="badge badge-info">{{ $material->status }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada pengajuan terbaru</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif



@if (!Auth::user()->is_admin)
<!-- Displaying Stock Available for Non-Admin Users -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header border-0 bg-white">
                <h6 class="card-title font-weight-bold text-dark mb-0">Stock Available</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="text-uppercase text-gray-500 fw-bold fs-7">
                            <tr>
                                <th>No</th>
                                <th>Material</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                                <th>Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stockAvailable as $index => $material)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $material->name }}</td>
                                <td>{{ $material->category->name ?? '-' }}</td>
                                <td>{{ $material->stock }}</td>
                                <td>{{ $material->unit }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada bahan dengan stok tersedia</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Displaying Pengajuan Terakhir Table -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header border-0 bg-white">
                <h6 class="card-title font-weight-bold text-dark mb-0">Pengajuan Terakhir</h6>
            </div>
            <div class="card-body h-300px">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="text-uppercase text-gray-500 fw-bold fs-7">
                            <tr>
                                <th>No</th>
                                <th>Material</th>
                                <th>Jumlah</th>
                                <th>Tanggal Dibutuhkan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuanTerakhir as $index => $material)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $material->material->name }}</td>
                                <td>{{ $material->quantity }}</td>
                                <td>{{ $material->date_needed->format('d/m/Y')}}</td>
                                <td><span class="badge badge-info">{{ $material->status }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada pengajuan terbaru</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow-sm">
            <div class="card-header border-0 bg-white">
                <h6 class="card-title font-weight-bold text-dark mb-0">Permintaan Material</h6>
            </div>
            <div class="card-body h-300px">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="text-uppercase text-gray-500 fw-bold fs-7">
                            <tr>
                                <th>No</th>
                                <th>Material</th>
                                <th>Jumlah</th>
                                <th>Tanggal Dibuat</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchasematerials as $index => $material)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $material->name }}</td>
                                <td>{{ $material->quantity }}</td>
                                <td>{{ $material->created_at->format('d/m/Y')}}</td>
                                <td><span class="badge badge-info">{{ $material->status }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada pengajuan terbaru</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endif


@endsection
