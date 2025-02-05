@extends('layouts.admin')

@section('main-content')
<!-- Page Heading -->
<h1 class="h3 text-gray-800">{{ __('Penggunaan Bahan') }}</h1>

@if (session('success'))
<div class="alert alert-success border-left-success alert-dismissible fade show" role="alert" id="autoDismissAlert">
    {{ session('success') }}
    @if (session('whatsapp_url'))
    <a href="{{ session('whatsapp_url') }}" target="_blank" class="btn btn-sm btn-success ml-2">
        <i class="fab fa-whatsapp"></i> Send WhatsApp Notification
    </a>
    @endif
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
                <!-- Status Tabs -->
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') === null ? 'active' : '' }}" href="{{ route('request-for-materials.index') }}">
                            Semua
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') === 'pending' ? 'active' : '' }}" href="{{ route('request-for-materials.index', ['status' => 'pending']) }}">
                            Pending
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') === 'approved' ? 'active' : '' }}" href="{{ route('request-for-materials.index', ['status' => 'approved']) }}">
                            Disetujui
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request('status') === 'rejected' ? 'active' : '' }}" href="{{ route('request-for-materials.index', ['status' => 'rejected']) }}">
                            Ditolak
                        </a>
                    </li>
                </ul>

                <div class="row mb-4">
                    <div class="col-md-8">
                        <form method="GET" action="{{ route('request-for-materials.index') }}" class="d-flex">
                            <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Cari pengajuan..." value="{{ request()->input('search') }}">
                            <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle border rounded table-row-dashed fs-6 g-5">
                        <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase">
                                <th>No</th>
                                <th>Pemohon</th>
                                <th>Material</th>
                                <th>Jumlah</th>
                                <th>Tanggal Dibutuhkan</th>
                                <th>Status</th>
                                <th>Alasan Ditolak</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-600">
                            @forelse ($requests as $index => $request)
                            <tr>
                                <td>{{ $index + $requests->firstItem() }}</td>
                                <td>{{ $request->user->name }}</td>
                                <td>{{ $request->material->name }}</td>
                                <td>{{ $request->quantity }}</td>
                                <td>{{ $request->date_needed->format('d/m/Y') }}</td>
                                <td>
                                    @if($request->status === 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                    @elseif($request->status === 'approved')
                                    <span class="badge badge-success">Disetujui</span>
                                    @elseif($request->status === 'rejected')
                                    <span class="badge badge-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $request->reject_reason }}
                                </td>
                                <td>
                                    @if(auth()->user()->is_admin)
                                    @if($request->status === 'pending')
                                    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#approveModal-{{ $request->id }}">
                                        Setujui
                                    </button>
                                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal-{{ $request->id }}">
                                        Tolak
                                    </button>
                                    @endif
                                    @endif
                                    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deletePengajuanModal-{{ $request->id }}">Hapus</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data pengajuan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    {{ $requests->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approve Modal -->
@foreach($requests as $request)
<div class="modal fade" id="approveModal-{{ $request->id }}" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel-{{ $request->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('request-for-materials.approve', $request->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel-{{ $request->id }}">Setujui Pengajuan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menyetujui pengajuan ini?</p>
                    <dl class="row">
                        <dt class="col-sm-4">Pemohon</dt>
                        <dd class="col-sm-8">{{ $request->user->name }}</dd>

                        <dt class="col-sm-4">Material</dt>
                        <dd class="col-sm-8">{{ $request->material->name }}</dd>

                        <dt class="col-sm-4">Jumlah</dt>
                        <dd class="col-sm-8">{{ $request->quantity }}</dd>

                        <dt class="col-sm-4">Tanggal Dibutuhkan</dt>
                        <dd class="col-sm-8">{{ $request->date_needed->format('d/m/Y') }}</dd>
                    </dl>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Reject Modal -->
@foreach($requests as $request)
<div class="modal fade" id="rejectModal-{{ $request->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel-{{ $request->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('request-for-materials.reject', $request->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel-{{ $request->id }}">Tolak Pengajuan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <dl class="row">
                        <dt class="col-sm-4">Pemohon</dt>
                        <dd class="col-sm-8">{{ $request->user->name }}</dd>

                        <dt class="col-sm-4">Material</dt>
                        <dd class="col-sm-8">{{ $request->material->name }}</dd>

                        <dt class="col-sm-4">Jumlah</dt>
                        <dd class="col-sm-8">{{ $request->quantity }}</dd>

                        <dt class="col-sm-4">Tanggal Dibutuhkan</dt>
                        <dd class="col-sm-8">{{ $request->date_needed->format('d/m/Y') }}</dd>
                    </dl>

                    <div class="form-group">
                        <label for="reject_reason" class="required">Alasan Penolakan</label>
                        <textarea class="form-control" id="reject_reason" name="reject_reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- Delete modal --}}
@foreach ($requests as $request)
<div class="modal fade" id="deletePengajuanModal-{{ $request->id }}" tabindex="-1" aria-labelledby="deletePengajuanModalLabel-{{ $request->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('request-for-materials.destroy', $request->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePengajuanModalLabel-{{ $request->id }}">Hapus Bahan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus pengajuan bahan dari "{{ $request->user->full_name }}"?</p>
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
