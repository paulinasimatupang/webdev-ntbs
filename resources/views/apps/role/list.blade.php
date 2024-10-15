@extends('layouts.master')

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/styles/vendor/datatables.min.css') }}">
@endsection

@php
    $permissionService = new \App\Services\FeatureService();
    $routes_user = $permissionService->getUserAllowedRoutes();
@endphp

@section('main-content')
<div class="breadcrumb">
    <h1>Role Management</h1>
</div>
<div class="separator-breadcrumb border-top"></div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

@if ($message = Session::get('failed'))
    <div class="alert alert-failed">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="row mb-4">
    <div class="col-lg-12 col-md-12 col-sm-12 d-flex justify-content-center mb-3">
        <div class="input-group">
            @if (in_array('roles.add', $routes_user))
                <a href="{{ route('roles.add') }}">
                    <button class="btn btn-warning ripple m-1 add-new-btn" type="button">Add Role</button>
                </a>
            @endif         
        </div>
    </div>
    <div class="col-md-12 mb-3">
        <div class="card text-left">
            <div class="card-body">
                <div class="row">
                    <h4 class="col-sm-12 col-md-6 card-title mb-3">List of Roles</h4>
                </div>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <div class="table-responsive">
                    <table id="default_ordering_table" class="display table table-striped table-bordered"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @foreach ($roles as $role)
                                <tr>
                                    <th scope="row">{{ $no }}</th>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        @if (in_array('roles.addPermissionToRole', $routes_user))
                                            <a href="{{ route('roles.addPermissionToRole', $role) }}">
                                                <button class="btn btn-primary" type="button">Add/Edit Permission</button>
                                            </a>
                                        @endif

                                        @if (in_array('roles.edit', $routes_user))
                                            <a href="{{ route('roles.edit', $role) }}">
                                                <button class="btn btn-success" type="button">Edit Role</button>
                                            </a>
                                        @endif

                                        @if (in_array('roles.destroy', $routes_user))
                                            <a href="#" onclick="deleteConfirm('{{ $role->id }}'); return false;"
                                            class="btn btn-danger ripple btn-sm m-1">Hapus</a>
                                        @endif
                                    </td>
                                </tr>
                                @php
                                    $no++;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script src="{{ asset('assets/js/vendor/echarts.min.js') }}"></script>
<script src="{{ asset('assets/js/es5/echart.options.min.js') }}"></script>
<script src="{{ asset('assets/js/vendor/datatables.min.js') }}"></script>
<script src="{{ asset('assets/js/datatables.script.js') }}"></script>
<script src="{{ asset('assets/js/es5/dashboard.v4.script.js') }}"></script>
<script src="{{ asset('assets/js/vendor/pickadate/picker.js') }}"></script>
<script src="{{ asset('assets/js/vendor/pickadate/picker.date.js') }}"></script>
@endsection

@section('bottom-js')
<script src="{{ asset('assets/js/form.basic.script.js') }}"></script>
<script>
    function deleteConfirm(id) {
        var r = confirm("Apakah Anda yakin akan menghapus data role?");
        if (r == true) {
            var url = '{{ route("roles.destroy", ":id") }}';
            url = url.replace(':id', id);

            $.post(url, {
                _token: "{{ csrf_token() }}",
            })
            .done(function (data) {
                window.location.href = '{{ route("roles.list") }}';
            })
            .fail(function () {
                alert("Error, Please try again later!");
            });
        }
    }
</script>

<style>
    .add-new-btn {
        background-color: #0a6e44;
        border: none;
        color: white;
    }

    .edit-btn,
    .delete-btn {
        background-color: #0182bd;
        border: none;
        color: white;
    }
</style>
@endsection