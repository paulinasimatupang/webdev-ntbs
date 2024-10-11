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
                <h1>Agen</h1>
            </div>
            <div class="separator-breadcrumb border-top"></div>
            <div class="row mb-4">
                <div class="col-md-12 mb-3">
                    <div class="card text-left">
                        <div class="card-body">
                            <div class="row">
                                <h4 class=" col-sm-9 col-md-6 card-title mb-3">List Agen </h4>
                                <div class="col-sm-3 col-md-12 mb-3">
                                    <div class="export-button-wrapper" style="float: right;">
                                        <a id="export-to-excel" href="{{ route('merchants.excel') }}" class="btn btn-outline-secondary">Export to Excel</a>
                                        <a id="export-to-pdf" href="{{ route('merchants.pdf') }}" class="btn btn-outline-secondary">Export to PDF</a>
                                    </div>
                                </div>
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
                                            <th scope="col">Kode Agen</th>
					                        <th scope="col">No Rekening</th>
                                            <th scope="col">Nama</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Alamat</th>
                                            <th scope="col">Kota</th>
                                            <th scope="col">No Telepon HP</th>
                                            <th scope="col">TID</th>
                                            <th scope="col">Status Agen</th>
                                            <th scope="col">Active Date</th>
                                            <th scope="col">Resign Date</th>
                                            <th scope="col" {{ session()->get('user')->role_id == 2 ? 'id=b1' : '' }}>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 1;
                                        @endphp
                                        @foreach($data as $item)
                                        <tr>
                                            <th scope="row">{{ $no }}</th>
                                            
                                            <td>{{$item->mid}}</td>
					                        <td>{{$item->no}}</td>
                                            <td>{{$item->name}}</td>
                                            <td>{{$item->email}}</td>
                                            <td>{{$item->address}}</td>
                                            <td>{{$item->city}}</td>
                                            <td>{{$item->phone}}</td>
                                            <td>{{$item->terminal_id}}</td>
                                            <td>{{$item->status_text}}</td>
                                            <td>{{$item->active_at}}</td>
                                            <td>{{$item->resign_at}}</td>
                                            <td>
                                                @if (in_array('agen_edit', $routes_user))
                                                    <a href="{{route('agen_edit',[$item->id])}}">
                                                        <button class="btn btn-warning ripple btn-sm m-1 edit-btn" type="button"  @php if(session()->get('user')->role_id == 2) echo 'id="b1"'; @endphp>Edit</button>
                                                    </a>
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


@endsection
