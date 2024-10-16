@extends('layouts.master')

@php
    $permissionService = new \App\Services\FeatureService();
    $routes_user = $permissionService->getUserAllowedRoutes();
@endphp

@section('page-css')
     <link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
     <link rel="stylesheet" href="{{asset('assets/styles/vendor/pickadate/classic.css')}}">
     <link rel="stylesheet" href="{{asset('assets/styles/vendor/pickadate/classic.date.css')}}">

@endsection
@section('main-content')
            <div class="breadcrumb">
                <h1>Daftar Cabang</h1>
            </div>
            <div class="separator-breadcrumb border-top"></div>
            <div class="row mb-4">
                @if (in_array('branch_create', $routes_user))
                <div class="col-lg-12 col-md-12 col-sm-12 d-flex justify-content-center mb-3">
                    <div class="input-group">
                        <a href="{{route('branch_create')}}">
                            <button class="btn btn-success ripple m-1 add-new-btn" type="button">Tambah</button>
                        </a>
                    </div>
                </div>
                @endif
                <div class="col-md-12 mb-3">
                    <div class="card text-left">
                        <div class="card-body">
                            <div class="row">
                                <h4 class=" col-sm-12 col-md-6 card-title mb-3">Daftar Cabang</h4>
                            </div>
                            @if ($message = Session::get('success'))
                                <div class="alert alert-success">
                                    <p>{{ $message }}</p>
                                </div>
                            @endif

                            @if ($message = Session::get('failed'))
                                <div class="alert alert-danger">
                                    <p>{{ $message }}</p>
                                </div>
                            @endif
                            <div class="table-responsive">
                            <table id="deafult_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">No</th>
                                            <th scope="col">Kode Cabang</th>
                                            <th scope="col">Nama Cabang</th>
                                            <th scope="col">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 1;
                                        @endphp
                                        @foreach($data as $item)
                                        <tr>
                                            <th scope="row">{{ $no }}</th>
                                            <td>{{$item->branch_code}}</td>
                                            <td>{{$item->branch_name}}</td>
                                            <td>
                                            @if (in_array('branch_edit', $routes_user))
                                                <a href="{{route('branch_edit',[$item->branch_id])}}">
                                                    <button class="btn btn-success ripple btn-sm m-1 edit-btn" type="button">Edit</button>
                                                </a>
                                            @endif
                                            @if (in_array('branch_destroy', $routes_user))
                                                    <form action="{{route('branch_destroy',[$item->branch_id])}}" method="POST" style="display:inline-block;" class="delete-form">
                                                        @csrf
                                                        <button class="btn btn-danger ripple btn-sm m-1 delete-btn" type="button" onclick="confirmDelete(this)">Hapus</button>
                                                    </form>
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
     <script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
     <script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script>
     <script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
     <script src="{{asset('assets/js/datatables.script.js')}}"></script>
     <script src="{{asset('assets/js/es5/dashboard.v4.script.js')}}"></script>
     <script src="{{asset('assets/js/vendor/pickadate/picker.js')}}"></script>
     <script src="{{asset('assets/js/vendor/pickadate/picker.date.js')}}"></script>

@endsection
@section('bottom-js')
<script src="{{asset('assets/js/form.basic.script.js')}}"></script>
<style>
    .add-new-btn {
        background-color: #0a6e44;
        border: none;
        color: white;
    }

    .edit-btn {
        background-color: #0182bd;
        border: none;
        color: white;
    }
</style>
<script>
function confirmDelete(button) {
            if (confirm('Apakah Anda yakin ingin menghapus cabang ini?')) {
                button.closest('form').submit();
            } else {
                return false;
            }
        }
        </script>
@endsection
