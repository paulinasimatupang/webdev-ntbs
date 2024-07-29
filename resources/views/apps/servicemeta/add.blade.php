@extends('layouts.master')

@section('page-css')
    <link rel="stylesheet" href="{{ asset('assets/styles/vendor/pickadate/classic.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/styles/vendor/pickadate/classic.date.css') }}">
@endsection

@section('main-content')
    <div class="container">
        <h1>Add New Service Meta</h1>
        <form action="{{ route('servicemeta_store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="service_id">Service ID</label>
                <input type="text" id="service_id" name="service_id" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="seq">Sequence</label>
                <input type="number" id="seq" name="seq" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="meta_type_id">Meta Type ID</label>
                <input type="text" id="meta_type_id" name="meta_type_id" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="meta_default">Meta Default</label>
                <textarea id="meta_default" name="meta_default" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label for="influx">Influx</label>
                <select id="influx" name="influx" class="form-control">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
@endsection
