@extends('layouts.master')

@section('main-content')
<div class="breadcrumb">
    <h1>Edit Parameterized</h1>
</div>
<div class="separator-breadcrumb border-top"></div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('update_produk', ['opt_id' => $data->opt_id, 'meta_id' => $data->meta_id]) }}" method="POST">
                    @csrf
                    @method('POST') <!-- Change to PUT for updates -->

                    <div class="form-group">
                        <label for="opt_id">Produk</label>
                        <input type="text" class="form-control" id="opt_id" name="opt_id" value="{{ $data->opt_id }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="meta_id">Meta ID</label>
                        <input type="text" class="form-control" id="meta_id" name="meta_id" value="{{ $data->meta_id }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="default_value">Default Value</label>
                        <input type="text" class="form-control" id="default_value" name="default_value" value="{{ $data->default_value }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('list_produk') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
