@extends('layouts.master')

@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/styles/vendor/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/styles/vendor/pickadate/classic.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/styles/vendor/pickadate/classic.date.css') }}">
@endsection

@section('main-content')
    <div class="breadcrumb">
        <h1>Users Management</h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    <div class="container mt-2">
        <a href="{{ url('roles') }}" class="btn btn-primary mx-1">Roles</a>
        <a href="{{ url('permissions') }}" class="btn btn-info mx-1">Permissions</a>
        <a href="{{ url('users') }}" class="btn btn-warning mx-1">Users</a>
    </div>

    <div class="row mb-4">
        <div class="col-lg-12 col-md-12 col-sm-12">
            @if (session('status'))
                <div class="alert alert-success mt-2">{{ session('status') }}</div>
            @endif

            <div class="card mt-3">
                <div class="card-header">
                    <h4>Users
                        @can('create user')
                        <a href="{{ url('users/create') }}" class="btn btn-primary float-end">Add User</a>
                        @endcan
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="default_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if (!empty($user->getRoleNames()))
                                            @foreach ($user->getRoleNames() as $rolename)
                                                <label class="badge bg-primary mx-1">{{ $rolename }}</label>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ url('users/'.$user->id.'/edit') }}" class="btn btn-success btn-sm m-1" type="button">Edit</a>
                                        <a href="{{ url('users/'.$user->id.'/delete') }}" class="btn btn-danger btn-sm m-1 delete-btn" type="button">Delete</a>
                                    </td>
                                </tr>
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
            var r = confirm("Are you sure?");
            if (r == true) {
                var url = '{{ url("users") }}/' + id + '/delete';
                
                $.post(url, {
                    _token: "{{ csrf_token() }}",
                }, function(data, status) {
                    location.reload(true);
                }).done(function() {
                    location.reload(true);
                }).fail(function() {
                    alert("Error, Please try again later!");
                });
            }
        }
    </script>
@endsection
