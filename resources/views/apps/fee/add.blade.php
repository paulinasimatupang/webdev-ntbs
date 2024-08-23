@extends('layouts.master')

@section('main-content')
    <div class="breadcrumb">
        <h1>Add Service Meta</h1>
        <ul>
            <li><a href="">Selada</a></li>
        </ul>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    @if(Session('error'))
        <div class="alert alert-danger" role="alert">
            <strong class="text-capitalize">Error: </strong> {{ Session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-5">
                <div class="card-body">
                    <form action="{{ route('fee_store') }}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Service ID</label>
                            <div class="col-sm-10">
                                <select name="service_id" class="form-control">
                                    <option value="">Select Service ID</option>
                                    @foreach($service as $item)
                                        <option value="{{ $item->service_id }}">{{ $item->service_id }} - {{ $item->service_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Service Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{{ old('service_name') }}" name="service_name" placeholder="Service Name" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Meta ID</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{{ old('meta_id') }}" name="meta_id" placeholder="Meta ID" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Meta Default</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{{ old('meta_default') }}" name="meta_default" placeholder="Meta Default" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Influx</label>
                            <div class="col-sm-10">
                                <select id="influx" name="influx" class="form-control">
                                    <option value="3">3</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12 text-right">
                                <a href="{{ route('fee') }}">
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
