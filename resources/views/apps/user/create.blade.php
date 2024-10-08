@extends('layouts.master')

@section('main-content')
    <div class="breadcrumb">
        <h1>Create User</h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    @if ($errors->any())
        <ul class="alert alert-warning">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
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
                                <input type="text" name="fullname" class="form-control" placeholder="Name" required />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" name="email" class="form-control" placeholder="Email" required />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Role</label>
                            <div class="col-sm-10">
                                <select name="role_id" class="form-control" required>
                                    <option value="" disabled selected>Pilih Role</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
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
                                        <option value="{{ $branchid->branch_code }}">{{ $branchid->branch_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12 text-right">
                                <a href="{{ url('users/menu') }}">
                                    <button type="button" class="btn btn-danger">Back</button>
                                </a>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
