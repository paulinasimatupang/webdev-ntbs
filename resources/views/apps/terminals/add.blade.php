@extends('layouts.master')

@section('main-content')
    <div class="breadcrumb">
        <h1>Add Terminal</h1>
        <ul>
            <li><a href="">Selada</a></li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    @if(Session('error'))
        @foreach (Session('error') as $key => $item)
            <div class="alert alert-danger" role="alert">
                <strong class="text-capitalize">Error : </strong> {{ $item[0] }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endforeach
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-5">
                <div class="card-body">
                    <form action="{{ route('terminal_store') }}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Terminal Type</label>
                            <div class="col-sm-10">
                                <select name="terminal_type" class="form-control">
                                    <option value="">Select Terminal Type</option>
                                    @foreach($terminal_type as $item)
                                        <option value="{{ $item->terminal_type }}">{{ $item->terminal_type_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Terminal ID</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{{ old('terminal_id') }}" name="terminal_id" placeholder="Terminal ID" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Terminal IMEI</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{{ old('terminal_imei') }}" name="terminal_imei" placeholder="Terminal IMEI" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Terminal Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{{ old('terminal_name') }}" name="terminal_name" placeholder="Terminal Name" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Merchant ID</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{{ old('merchant_id') }}" name="merchant_id" placeholder="Merchant ID" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12 text-right">
                                <a href="{{ route('terminal') }}">
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
    <script src="{{ asset('assets/js/form.validation.script.js') }}"></script>
@endsection
