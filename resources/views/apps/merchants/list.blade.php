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
                                <h4 class=" col-sm-9 col-md-6 card-title mb-3">Daftar Agen </h4>
                                @if (in_array('merchants.excel', $routes_user))
                                <div class="col-sm-3 col-md-12 mb-3">
                                    <div class="export-button-wrapper" style="float: right;">
                                    <a id="export-to-excel" href="{{ route('merchants.excel') }}" 
                                    class="btn" 
                                    style="background-color: #4CAF50; color: white; border-color: #4CAF50;">
                                        <img src="{{ asset('assets/images/new_features/xls.png') }}" alt="Excel Icon" style="width: 20px; height: 20px; margin-right: 5px;">
                                        Ekspor Excel
                                    </a>
                                    <a id="export-to-excel" href="{{ route('merchants.pdf') }}" 
                                    class="btn" 
                                    style="background-color: #ff2429; color: white; border-color: #ff2429;">
                                        <img src="{{ asset('assets/images/new_features/pdf.png') }}" alt="Pdf Icon" style="width: 20px; height: 20px; margin-right: 5px;">
                                        Ekspor Pdf
                                    </a>
                                    </div>
                                </div>
                                @endif
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
                                            <th scope="col">Jenis Kelamin</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Alamat</th>
                                            <th scope="col">Latitude</th>
                                            <th scope="col">Longitude</th>
                                            <th scope="col">No Telepon HP</th>
                                            <th scope="col">Kode Cabang</th>
                                            <th scope="col">Approval oleh</th>
                                            <th scope="col">Status Agen</th>
                                            <th scope="col">Tanggal Aktif</th>
                                            <th scope="col">Tanggal Nonaktif</th>
                                            <th scope="col" {{ session()->get('user')->role_id == 2 ? 'id=b1' : '' }}>Aksi</th>
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
                                            <td>{{$item->jenis_kelamin}}</td>
                                            <td>{{$item->email}}</td>
                                            <td>{{$item->address}}</td>
                                            <td>{{$item->latitude}}</td>
                                            <td>{{$item->longitude}}</td>
                                            <td>{{$item->phone}}</td>
                                            <td>{{$item->branchid}}</td>
                                            <td>{{$item->user->approval_by}}</td>
                                            <td>{{$item->status_text}}</td>
                                            <td>{{$item->active_at}}</td>
                                            <td>{{$item->resign_at}}</td>
                                            <td>
                                                @if (in_array('agen_edit', $routes_user))
                                                    <a href="{{route('agen_edit',[$item->id])}}">
                                                        <button class="btn btn-primary ripple btn-sm m-1 edit-btn" type="button"  @php if(session()->get('user')->role_id == 2) echo 'id="b1"'; @endphp>Edit</button>
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
@endsection
