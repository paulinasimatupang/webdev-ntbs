@extends('layouts.master')

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/styles/vendor/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/styles/vendor/pickadate/classic.css') }}">
<link rel="stylesheet" href="{{ asset('assets/styles/vendor/pickadate/classic.date.css') }}">
@endsection

@php
    $featureService = new \App\Services\FeatureService();
    $features = $featureService->getFeatureItems();
@endphp

@section('main-content')
<style type="text/css">
    #b1, #b2, #b3 {
        display: none;
    }

    .card-link {
        text-decoration: none;
        color: inherit;
    }

    .card-link .card {
        transition: transform 0.2s;
    }

    .card-link:hover .card {
        transform: scale(1.05);
    }

    .card-body {
        cursor: pointer;
    }
</style>

<div class="breadcrumb">
    <h1>Agen</h1>
</div>
<div class="separator-breadcrumb border-top"></div>

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

<div class="row mb-4">
    @if(isset($features['add agen']))
        <div class="col-lg-4 col-md-4 col-sm-4">
            <a href="/agen/create/inquiry" class="card-link">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('assets/images/new_features/add.png') }}" alt="Add Agen" class="mr-3" style="width: 50px;">
                            <div class="text-left">
                                <p class="text-muted mt-2 mb-0">Tambah Agen</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endif

    @if(isset($features['request agen']))
        <div class="col-lg-4 col-md-4 col-sm-4">
            <a href="/agen/request" class="card-link">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                    <div class="card-body d-flex align-items-center">
                        <img src="{{ asset('assets/images/new_features/request.png') }}" alt="Request Agen" class="mr-3" style="width: 50px;">
                        <div class="flex-grow-1">
                            <p class="text-muted mt-2 mb-0">Permintaan Pendaftaran Agen</p>
                        </div>
                        <div class="text-right">
                            <h3 class="mb-0" style="font-size: 30px; color: #0a6e44;">{{ $jumlah_request }}</h3>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endif


    @if(isset($features['list agen']))
        <div class="col-lg-4 col-md-4 col-sm-4">
            <a href="/agen/list" class="card-link">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('assets/images/new_features/list.png') }}" alt="List Agen" class="mr-3" style="width: 50px;">
                            <div class="text-left">
                                <p class="text-muted mt-2 mb-0">Data Agen</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endif
</div>

@if(isset($features['list block agen']))
    <div class="row mb-4">
        <div class="col-lg-4 col-md-4 col-sm-4">
            <a href="/agen/blocked" class="card-link">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                    <div class="card-body d-flex align-items-center">
                        <img src="{{ asset('assets/images/new_features/block.png') }}" alt="List Block Agen" class="mr-3" style="width: 50px;">
                        <div class="flex-grow-1">
                            <p class="text-muted mt-2 mb-0">Agen Terblokir</p>
                        </div>
                        <div class="text-right">
                            <h3 class="mb-0" style="font-size: 30px; color: #0a6e44;">{{ $jumlah_blocked }}</h3>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
@endif

@endsection
