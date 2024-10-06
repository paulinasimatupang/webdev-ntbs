@extends('layouts.master')

@section('main-content')
<div class="breadcrumb">
    <h1>Edit Parameter</h1>
</div>
<div class="separator-breadcrumb border-top"></div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('masterdata_update_parameter', ['id' => $data->comp_id]) }}" method="POST">
                    @csrf
                    @method('POST')

                    <div class="form-group">
                        <label for="comp_id">Fitur</label>
                        <input type="text" class="form-control" id="comp_id" name="comp_id" value="{{ $data->comp_lbl }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="comp_act">Nilai</label>
                        <input type="text" class="form-control" id="comp_act" name="comp_act" value="{{ old('comp_act', $data->comp_act) }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('masterdata_list_parameter') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
