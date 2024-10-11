@extends('layouts.master')

@php
    $featureService = new \App\Services\FeatureService();
    $feature = $featureService->getFeatureItems();
@endphp

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/styles/vendor/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/styles/vendor/pickadate/classic.css') }}">
<link rel="stylesheet" href="{{ asset('assets/styles/vendor/pickadate/classic.date.css') }}">
@endsection

@section('main-content')
<style type="text/css">
    #b1,
    #b2,
    #b3 {
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
    <h1>Biller</h1>
</div>
<div class="separator-breadcrumb border-top"></div>
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
<div class="row mb-4">
    @if(isset($feature['rek penampung']))
        <div class="col-lg-4 col-md-4 col-sm-4">
            <a href="/parameter" class="card-link">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                    <div class="card-body d-flex align-items-center">
                        <img src="{{ asset('assets/images/new_features/request.png') }}" alt="Request Nasabah" class="mr-3" style="width: 50px;">
                        <div class="flex-grow-1">
                            <p class="text-muted mt-2 mb-0">Rekening Penampung</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endif

    @if(isset($feature['sub produk']))
        <div class="col-lg-4 col-md-4 col-sm-4">
            <a href="/sub-produk" class="card-link">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                    <div class="card-body d-flex align-items-center">
                        <img src="{{ asset('assets/images/new_features/request.png') }}" alt="Request Nasabah" class="mr-3" style="width: 50px;">
                        <div class="flex-grow-1">
                            <p class="text-muted mt-2 mb-0">Sub Produk Biller</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endif

    @if(isset($feature['produk']))
        <div class="col-lg-4 col-md-4 col-sm-4">
            <a href="/produk" class="card-link">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('assets/images/new_features/list.png') }}" alt="Total Transaction"
                                class="mr-3" style="width: 50px">
                            <div class="text-left">
                                <p class="text-muted mt-2 mb-0">Produk Biller</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endif
</div>

@endsection
