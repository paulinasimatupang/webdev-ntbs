@extends('layouts.master')

@section('main-content')
<div class="breadcrumb">
    <h1>Edit Permission</h1>
</div>
<div class="separator-breadcrumb border-top"></div>

@if(Session::has('status'))
<div class="alert alert-success" role="alert">
    <strong>Success:</strong> {{ Session::get('status') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if ($errors->any())
<ul class="alert alert-warning">
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
</ul>
@endif

<div class="row">
    <div class="col-md-12">
        <div class="card mb-5">
            <div class="card-body">
                <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Permission Name</label>
                        <div class="col-sm-10">
                            <input type="text" name="name" value="{{ old('name', $permission->name) }}" class="form-control" placeholder="Permission Name" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Feature</label>
                        <div class="col-sm-10">
                            <input type="text" name="feature" value="{{ old('feature', $permission->feature) }}" class="form-control" placeholder="Feature">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Feature Group</label>
                        <div class="col-sm-10">
                            <input type="text" name="feature_group" value="{{ old('feature_group', $permission->feature_group) }}" class="form-control" placeholder="Feature Group">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12 text-right">
                            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Back</a>
                            <button type="submit" class="btn btn-primary">Update</button>
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
