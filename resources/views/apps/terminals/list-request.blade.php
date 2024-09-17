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
                <h1>Request Imei</h1>
                <!-- <ul>
                    <li><a href="">Selada</a></li> -->
            </div>
            <div class="separator-breadcrumb border-top"></div>
            <div class="row mb-4">
                <div class="col-md-12 mb-3">
                    <div class="card text-left">
                        <div class="card-body">
                            <div class="row">
                                <h4 class=" col-sm-12 col-md-6 card-title mb-3">List Request Imei</h4>
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
                                            <th scope="col">TID</th>
                                            <th scope="col">MID</th>
                                            <th scope="col">Imei</th>
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
                                            <td>{{$item->tid}}</td>
                                            <td>{{$item->mid}}</td>
                                            <td>{{$item->imei}}</td>
                                            <td>
                                                    <a href="{{ route('imei_accept', $item->id) }}">
                                                        <button class="btn btn-success ripple btn-sm m-1" type="button">Approve</button>
                                                    </a>
                                    
                                                    <a href="{{ route('imei_reject', $item->id) }}">
                                                        <button class="btn btn-danger ripple btn-sm m-1" type="button">Reject</button>
                                                    </a>
                                            
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rejectButtons = document.querySelectorAll('.delete-btn');
        
        rejectButtons.forEach(function(button) {
            button.addEventListener('click', function(event) {
                event.preventDefault(); 

                const confirmAction = confirm("Apakah Anda yakin ingin menolak permintaan ini?");
                if (confirmAction) {
                    button.closest('form').submit(); 
                }
            });
        });
    });
</script>
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
@endsection
