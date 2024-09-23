@extends('layouts.master')

@section('main-content')
<div class="breadcrumb">
    <h1>Create Request IMEI</h1>
</div>
<div class="separator-breadcrumb border-top"></div>

@if (session('status'))
<div class="alert alert-success">{{ session('status') }}</div>
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
                <form action="{{ route('imei.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="tid">TID</label>
                        <input type="text" name="tid" id="tid" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="mid">MID</label>
                        <input type="text" name="mid" id="mid" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="imei">IMEI</label>
                        <input type="text" name="imei" id="imei" class="form-control" required>
                    </div>

                    <!-- Hapus input status dari form -->
                    <!-- Status otomatis akan diset menjadi false -->

                    <div class="form-group row">
                        <div class="col-sm-12 text-right">
                            <a href="{{ route('imei_request') }}">
                                <button type="button" class="btn btn-danger">Back</button>
                            </a>
                            <button type="submit" class="btn btn-primary">Create IMEI</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
