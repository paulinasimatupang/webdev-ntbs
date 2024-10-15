@extends('layouts.master')

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/styles/vendor/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/styles/vendor/pickadate/classic.css') }}">
<link rel="stylesheet" href="{{ asset('assets/styles/vendor/pickadate/classic.date.css') }}">
@endsection

@section('main-content')
<div class="breadcrumb">
    <h1>Pemintaan Pengguna</h1>
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="row mb-4">
    <div class="col-lg-12 col-md-12 col-sm-12 d-flex justify-content-center mb-3">
    </div>
    <div class="col-md-12 mb-3">
        <div class="card text-left">
            <div class="card-body">
                <div class="row">
                    <h4 class="col-sm-12 col-md-6 card-title mb-3">Daftar Permintaan Pengguna</h4>
                </div>

                @if (session('status'))
                <div class="alert alert-success mt-2">{{ session('status') }}</div>
                @endif

                <div class="table-responsive">
                    <table id="default_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Fullname</th>
                                <th>Email</th>
                                <th>Role</th> 
                                <th>Action</th>
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
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->role->name }}</td>
                                <td>
                                    <a href="{{ route('users.detail', $user->id) }}">
                                        <button class="btn btn-warning ripple btn-sm m-1 edit-btn" type="button">Detail</button>
                                    </a>
                                    <!-- <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form> -->
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
    $(document).ready(function() {
        $('#default_ordering_table').DataTable({
            "paging": true, // Menyediakan fitur paging
            "lengthMenu": [10], // Menampilkan 10 data per halaman
            "searching": true, // Menyediakan fitur pencarian
            "info": false, // Menyembunyikan informasi jumlah data
        });
    });

    function deleteConfirm(id) {
        if (confirm("Are you sure you want to delete this user?")) {
            $.ajax({
                url: '{{ url("users") }}/' + id + '/delete',
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    location.reload(true);
                },
                error: function(response) {
                    alert("Error, Please try again later!");
                }
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