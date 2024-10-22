@extends('layouts.master')

@section('main-content')
    <div class="breadcrumb">
        <h1>Tambah User</h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    @if ($message = Session::get('error'))
        <div class="alert alert-failed">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-5">
                <div class="card-body">
                    <form action="{{ url('users') }}" method="POST">
                        @csrf

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nama Lengkap</label>
                            <div class="col-sm-10">
                                <input type="text" name="fullname" class="form-control" placeholder="Nama Lengkap" value="{{ old('fullname') }}" required />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Username</label>
                            <div class="col-sm-10">
                                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" placeholder="Username" value="{{ old('username') }}" required />
                                @error('username')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">NRP</label>
                            <div class="col-sm-10">
                                <input type="text" name="nrp" class="form-control" placeholder="NRP" value="{{ old('nrp') }}" required />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nomor Handphone</label>
                            <div class="col-sm-10">
                                <input type="text" name="no_hp" class="form-control" placeholder="Nomor Handphone" value="{{ old('no_hp') }}" required />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Role</label>
                            <div class="col-sm-10">
                                <select name="role_id" class="form-control" required>
                                    <option value="" disabled selected>Pilih Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="branch_id" class="col-sm-2 col-form-label">Cabang</label>
                            <div class="col-sm-10">
                                <select name="branch_id" id="branch_id" class="form-control" required>
                                    <option value="" disabled selected>Pilih Cabang</option>
                                    @foreach ($branch as $branchid)
                                        <option value="{{ $branchid->branch_code }}" {{ old('branch_id') == $branchid->branch_code ? 'selected' : '' }}>{{ $branchid->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12 text-right">
                                <a href="{{ url('users/menu') }}">
                                    <button type="button" class="btn btn-secondary">Kembali</button>
                                </a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
