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
    <h1>Nasabah</h1>
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
    @if(isset($feature['request nasabah']))
        <div class="col-lg-4 col-md-4 col-sm-4">
            <a href="/nasabah/request" class="card-link">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                    <div class="card-body d-flex align-items-center">
                        <img src="{{ asset('assets/images/new_features/request.png') }}" alt="Request Nasabah" class="mr-3" style="width: 50px;">
                        <div class="flex-grow-1">
                            <p class="text-muted mt-2 mb-0">Request Nasabah</p>
                        </div>
                        <div class="text-right">
                            <h3 class="mb-0" style="font-size: 30px; color: #0a6e44;">{{ $jumlah_request }}</h3>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endif

    @if(isset($feature['approve nasabah']))
        <div class="col-lg-4 col-md-4 col-sm-4">
            <a href="/nasabah/approve" class="card-link">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                    <div class="card-body d-flex align-items-center">
                        <img src="{{ asset('assets/images/new_features/request.png') }}" alt="Request Nasabah" class="mr-3" style="width: 50px;">
                        <div class="flex-grow-1">
                            <p class="text-muted mt-2 mb-0">Approve Nasabah</p>
                        </div>
                        <div class="text-right">
                            <h3 class="mb-0" style="font-size: 30px; color: #0a6e44;">{{ $jumlah_approve }}</h3>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endif

    @if(isset($feature['list nasabah']))
        <div class="col-lg-4 col-md-4 col-sm-4">
            <a href="/nasabah/list" class="card-link">
                <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center">
                            <img src="{{ asset('assets/images/new_features/list.png') }}" alt="Total Transaction"
                                class="mr-3" style="width: 50px">
                            <div class="text-left">
                                <p class="text-muted mt-2 mb-0">List Nasabah</p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endif
</div>

@endsection
