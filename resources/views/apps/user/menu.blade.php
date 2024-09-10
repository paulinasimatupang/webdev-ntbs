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
        <h1>User</h1>
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
        @foreach ($features as $mainFeature => $subFeatures)
            @if(isset($subFeatures['add user']))
            <div class="col-lg-4 col-md-4 col-sm-4">
                <a href="/users/create" class="card-link">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/new_features/add.png') }}" alt="Add User" class="mr-3" style="width: 50px">
                                <div class="text-left">
                                    <p class="text-muted mt-2 mb-0">Add User</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endif
            
            @if(isset($subFeatures['request user']))
            <div class="col-lg-4 col-md-4 col-sm-4">
                <a href="/users/request" class="card-link">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/new_features/request.png') }}" alt="Request User" class="mr-3" style="width: 50px">
                                <div class="text-left">
                                    <p class="text-muted mt-2 mb-0">Request User</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(isset($subFeatures['list user']))
            <div class="col-lg-4 col-md-4 col-sm-4">
                <a href="/users" class="card-link">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-2">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('assets/images/new_features/list.png') }}" alt="List User" class="mr-3" style="width: 50px">
                                <div class="text-left">
                                    <p class="text-muted mt-2 mb-0">List User</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endif
        @endforeach
    </div>
@endsection
