@extends('layouts.master')

@section('main-content')
<div class="breadcrumb">
    <h1>Edit Fee</h1>
</div>
<div class="separator-breadcrumb border-top"></div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('fee_update', ['meta_id' => $group->meta_id, 'service_id' => $group->service_id, 'seq' => $group->seq]) }}" method="POST">
                    @csrf
                    @method('POST')

                    <div class="form-group">
                        <label for="service_id">Service ID</label>
                        <input type="text" class="form-control" id="service_id" name="service_id" value="{{ $group->service_id }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="meta_id">Meta ID</label>
                        <input type="text" class="form-control" id="meta_id" name="meta_id" value="{{ $group->meta_id }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="influx">Influx</label>
                        <input type="text" class="form-control" id="influx" name="influx" value="{{ old('influx', $group->influx) }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="seq">Sequence</label>
                        <input type="text" class="form-control" id="seq" name="seq" value="{{ $group->seq }}" readonly>
                    </div>

                    <div class="form-group">
                        <label for="meta_default">Meta Default</label>
                        <input type="text" class="form-control" id="meta_default" name="meta_default" value="{{ old('meta_default', $group->meta_default) }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('fee') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
