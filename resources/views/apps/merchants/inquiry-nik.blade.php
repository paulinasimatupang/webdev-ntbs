@extends('layouts.master')
@section('main-content')
    <div class="breadcrumb">
                <h1>Add Merchant</h1>
                <ul>
                    <li><a href="">Selada</a></li>
                   
                </ul>
            </div>
            <div class="separator-breadcrumb border-top"></div>

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-5">
                        <div class="card-body">
                            <form action="{{route('merchant_store_inquiry_nik')}}" method="POST">
                                <div class="form-group row">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                                    <label class="col-sm-2 col-form-label">NIK</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{Request::old('nik')}}" name="nik" placeholder="NIK" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12 text-right">
                                        <a href="{{route('merchant')}}">
                                            <button type="button" class="btn btn-primary">Back</button>
                                        </a>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


@endsection

@section('page-js')

@endsection

@section('bottom-js')

    <script src="{{asset('assets/js/form.validation.script.js')}}"></script>

@endsection
