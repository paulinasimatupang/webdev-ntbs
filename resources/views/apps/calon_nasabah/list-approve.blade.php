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
                <h1>Permintaan Pembukaan Rekening BSA Tahap 2</h1>
            </div>
            <div class="separator-breadcrumb border-top"></div>
            <div class="row mb-4">
                <div class="col-md-12 mb-3">
                    <div class="card text-left">
                        <div class="card-body">
                            <div class="row">
                                <h4 class=" col-sm-12 col-md-6 card-title mb-3">Daftar Calon Nasabah</h4>
                            </div>
                            @if ($message = Session::get('success'))
                                <div class="alert alert-success">
                                    <p>{{ $message }}</p>
                                </div>
                            @endif

                            @if ($message = Session::get('error'))
                                <div class="alert alert-danger">
                                    <p>{{ $message }}</p>
                                </div>
                            @endif
                            <div class="table-responsive">
                            <table id="deafult_ordering_table" class="display table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th scope="col">No</th>
                                            <th scope="col">NIK</th>
                                            <th scope="col">Nama</th>
                                            <th scope="col">Alamat</th>
                                            <th scope="col">No HP</th>
                                            <th scope="col">Waktu Permintaan</th>
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
                                            <td>{{$item->no_identitas}}</td>
                                            <td>{{$item->nama_lengkap}}</td>
                                            <td>{{$item->alamat}}</td>
                                            <td>{{$item->no_hp}}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->request_time)->format('Y-m-d H:i:s') }}</td>
                                            <td>
                                            @if (in_array('nasabah_detail_approve', $routes_user))
                                                <form action="{{ route('nasabah_detail_approve', [$item->id]) }}" style="display: inline;">
                                                    @csrf
                                                    <button class="btn btn-primary ripple btn-sm m-1 primary-btn" type="submit">Detail</button>
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

@endsection
