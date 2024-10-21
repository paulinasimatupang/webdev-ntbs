@extends('layouts.master')

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/styles/vendor/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/styles/vendor/pickadate/classic.css') }}">
<link rel="stylesheet" href="{{ asset('assets/styles/vendor/pickadate/classic.date.css') }}">
@endsection

@php
    $permissionService = new \App\Services\FeatureService();
    $routes_user = $permissionService->getUserAllowedRoutes();
@endphp

@section('main-content')
<div class="breadcrumb">
    <h1>Data User</h1>
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="row mb-4">
    <!-- @if (in_array('users.create', $routes_user))
        <div class="col-lg-12 col-md-12 col-sm-12 d-flex justify-content-center mb-3">
            <div class="input-group">
                <a href="{{ route('users.create') }}">
                    <button class="btn btn-warning ripple m-1 add-new-btn" type="button">Add User</button>
                </a>
            </div>
        </div>
    @endif -->
    <div class="col-md-12 mb-3">
        <div class="card text-left">
            <div class="card-body">
                <div class="row">
                    <h4 class="col-sm-12 col-md-6 card-title mb-3">Daftar User</h4>
                </div>

                @if (session('success'))
                    <div class="alert alert-success mt-2">{{ session('success') }}</div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger mt-2">{{ session('error') }}</div>
                @endif

                <div class="table-responsive">
                    <table id="default_ordering_table" class="display table table-striped table-bordered"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th> 
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @foreach ($users as $user)
                                <tr>
                                    <th scope="row">{{ $no }}</th>
                                    <td>{{ $user->fullname }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->role->name }}</td>
                                    <td>{{ $user->status_text }}</td>
                                    <td>
                                        @if (in_array('users.edit', $routes_user))
                                            <a href="{{ route('users.edit', $user->id) }}">
                                                <button class="btn btn-primary ripple btn-sm m-1 edit-btn"
                                                    type="button">Edit</button>
                                            </a>
                                        @endif
                                        @if (in_array('users.destroy', $routes_user))
                                        <a href="#" onclick="deleteConfirm('{{ $user->id }}'); return false;"
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
    $(document).ready(function () {
        $('#default_ordering_table').DataTable({
            "paging": true, // Menyediakan fitur paging
            "lengthMenu": [10], // Menampilkan 10 data per halaman
            "searching": true, // Menyediakan fitur pencarian
            "info": false, // Menyembunyikan informasi jumlah data
        });
    });

    function deleteConfirm(id) {
        var r = confirm("Apakah Anda yakin akan menghapus data user?");
        if (r == true) {
            var url = '{{ route("users.destroy", ":id") }}';
            url = url.replace(':id', id);

            $.post(url, {
                _token: "{{ csrf_token() }}",
            })
            .done(function (data) {
                window.location.href = '{{ route("users.index") }}';
            })
            .fail(function () {
                alert("Error, Please try again later!");
            });
        }
    }
</script>
@endsection