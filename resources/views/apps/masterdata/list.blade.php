@extends('layouts.master')

@section('page-css')
<link rel="stylesheet" href="{{ asset('assets/styles/vendor/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/styles/vendor/pickadate/classic.css') }}">
<link rel="stylesheet" href="{{ asset('assets/styles/vendor/pickadate/classic.date.css') }}">
@endsection

@php
    $featureService = new \App\Services\FeatureService();
    $feature = $featureService->getFeatureItems();
@endphp

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
    <h1>Master Data</h1>
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="row mb-4">
    @if(isset($feature['fee']))
    <div class="col-lg-4 col-md-4 col-sm-4">
        <a href="/fee" class="card-link">
            <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/new_features/fee.png') }}" alt="Service Meta" class="mr-3" style="width: 50px">
                        <div class="text-left">
                            <p class="text-muted mt-2 mb-0">Fee Transaksi</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endif

    @if(isset($feature['persen fee']))
    <div class="col-lg-4 col-md-4 col-sm-4">
        <a href="/persen_fee" class="card-link">
            <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/new_features/fee_persen.png') }}" alt="Service Meta" class="mr-3" style="width: 50px">
                        <div class="text-left">
                            <p class="text-muted mt-2 mb-0">Persentase Fee</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endif

    @if(isset($feature['masterdata parameter']))
    <div class="col-lg-4 col-md-4 col-sm-4">
        <a href="/masterdata/parameter" class="card-link">
            <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/new_features/parameter.png') }}" alt="Service Meta" class="mr-3" style="width: 50px">
                        <div class="text-left">
                            <p class="text-muted mt-2 mb-0">Parameter Nilai</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endif

    @if(isset($feature['role']))
    <div class="col-lg-4 col-md-4 col-sm-4">
        <a href="/roles" class="card-link">
            <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/new_features/permission.png') }}" alt="Service Meta" class="mr-3" style="width: 50px">
                        <div class="text-left">
                            <p class="text-muted mt-2 mb-0">Role Akses</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endif

    @if(isset($feature['user']))
    <div class="col-lg-4 col-md-4 col-sm-4">
        <a href="/users/menu" class="card-link">
            <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/new_features/user.png') }}" alt="Service Meta" class="mr-3" style="width: 50px">
                        <div class="text-left">
                            <p class="text-muted mt-2 mb-0">User Portal</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endif
    
    @if(isset($feature['cabang']))
    <div class="col-lg-4 col-md-4 col-sm-4">
        <a href="/branch" class="card-link">
            <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/new_features/branch.png') }}" alt="Service" class="mr-3" style="width: 50px">
                        <div class="text-left">
                            <p class="text-muted mt-2 mb-0">Cabang Bank</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endif

    @if(isset($feature['assesment']))
    <div class="col-lg-4 col-md-4 col-sm-4">
        <a href="/assesment" class="card-link">
            <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/images/new_features/assesment.png') }}" alt="Service Meta" class="mr-3" style="width: 50px">
                        <div class="text-left">
                            <p class="text-muted mt-2 mb-0">Assesment Due Diligence</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    @endif
</div>
@endsection