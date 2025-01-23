@extends('layouts.admin')

@section('main-content')
<h1 class="h3 mb-4 text-gray-800">{{ __('Profile') }}</h1>

@if (session('success'))
<div class="alert alert-success border-left-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <i class="fas fa-times"></i>
    </button>
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger border-left-danger" role="alert">
    <ul class="pl-4 my-2">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="row">
    <div class="col-lg-4 order-lg-2">
        <div class="card shadow mb-4">
            <div class="card-profile-image mt-4">
                <figure class="rounded-circle avatar avatar font-weight-bold" style="font-size: 60px; height: 180px; width: 180px;" data-initial="{{ Auth::user()->name[0] }}"></figure>
            </div>
            <div class="card-body text-center">
                <h5 class="font-weight-bold">{{ Auth::user()->name }}</h5>
                @if (Auth::user()->is_admin)
                <p>Administrator</p>
                @else
                <p>Pengguna</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8 order-lg-1">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">My Account</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}" autocomplete="off">
                    @csrf
                    @method('PUT')

                    <h6 class="heading-small text-muted mb-4">Informasi Pengguna</h6>

                    <div class="pl-lg-4">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="name">Nama<span class="small text-danger">*</span></label>
                                <input type="text" id="name" class="form-control" name="name" value="{{ old('name', Auth::user()->name) }}">
                                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="last_name">Nama Akhir</label>
                                <input type="text" id="last_name" class="form-control" name="last_name" value="{{ old('last_name', Auth::user()->last_name) }}">
                                @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="email">Alamat Email<span class="small text-danger">*</span></label>
                                <input type="email" id="email" class="form-control" name="email" value="{{ old('email', Auth::user()->email) }}">
                                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-6 form-group">
                                <label for="phone">Nomor Hp</label>
                                <input type="text" id="phone" class="form-control" placeholder="628123456789" name="phone" value="{{ old('phone', Auth::user()->phone) }}">
                                @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <h6 class="heading-small text-muted mb-4">Ganti Password</h6>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="current_password">Password Sekarang</label>
                                <input type="password" id="current_password" class="form-control" name="current_password" placeholder="Password lama">
                                @error('current_password') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="new_password">Password Baru</label>
                                <input type="password" id="new_password" class="form-control" name="new_password" placeholder="Password baru">
                                @error('new_password') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4 form-group">
                                <label for="password_confirmation">Konfirmasi Password</label>
                                <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" placeholder="Ulangi password">
                                @error('password_confirmation') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pl-lg-4 text-center">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
