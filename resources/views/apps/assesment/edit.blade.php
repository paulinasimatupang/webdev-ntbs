@extends('layouts.master')

@section('main-content')
    <div class="breadcrumb">
        <h1>Edit Pertanyaan Assesment</h1>
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
                    <form action="{{ route('assesment_update', $data->id) }}" method="POST">
                        @csrf
                        
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Pertanyaan</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{{ $data->pertanyaan }}" name="pertanyaan" placeholder="Pertanyaan" required>
                            @if ($errors->has('pertanyaan'))
                                <div class="text-danger mt-2">
                                    {{ $errors->first('pertanyaan') }}
                                </div>
                            @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Poin</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" value="{{ $data->poin }}" name="poin" placeholder="Poin" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12 text-right">
                                <a href="{{ route('assesment') }}" class="btn btn-secondary">Back</a>
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
