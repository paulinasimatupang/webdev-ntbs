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
                <h1>Assesment - Due Diligence</h1>
                <!-- <ul>
                    <li><a href="">Selada</a></li> -->
            </div>
            <div class="separator-breadcrumb border-top"></div>
            <div class="row mb-4">
                <div class="col-lg-12 col-md-12 col-sm-12 d-flex justify-content-center mb-3">
                    <div class="input-group">
                        <a href="{{route('assesment_create')}}">
                            <button class="btn btn-warning ripple m-1 add-new-btn" type="button">Add</button>
                        </a>
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <div class="card text-left">
                        <div class="card-body">
                            <div class="row">
                                <h4 class=" col-sm-12 col-md-6 card-title mb-3">List Pertanyaan </h4>
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
                            </div> 
                            <div class="table-responsive">
                            <table id="deafult_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">No</th>
					                        <th scope="col">Pertanyaan</th>
                                            <th scope="col">Poin</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 1;
                                        @endphp
                                        @foreach($data as $item)
                                        <tr>
                                            <th scope="row">{{ $no }}</th>
                                            
                                            <td>{{$item->pertanyaan}}</td>
					                        <td>{{$item->poin}}</td>
                                            <td>
                                                @if (in_array('assesment_edit', $routes_user))
                                                    <a href="{{ route('assesment_edit', $item->id) }}">
                                                        <button class="btn btn-warning ripple btn-sm m-1 edit-btn" type="button">Edit</button>
                                                    </a>
                                                @endif
                                                @if (in_array('assesment_destroy', $routes_user))
                                                    <form action="{{ route('assesment_destroy', $item->id) }}" method="POST" style="display:inline-block;" class="delete-form">
                                                        @csrf
                                                        <button class="btn btn-danger ripple btn-sm m-1 delete-btn" type="button" onclick="confirmDelete(this)">Delete</button>
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
    <script src="{{ asset('assets/js/form.basic.script.js') }}"></script>
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

        .delete-btn {
            background-color: #e74c3c;
            border: none;
            color: white;
        }
    </style>

    <script>
        function confirmDelete(button) {
            if (confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')) {
                button.closest('form').submit();
            } else {
                return false;
            }
        }
    </script>
@endsection
