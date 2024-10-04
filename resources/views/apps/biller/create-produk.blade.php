@extends('layouts.master')

@section('main-content')
    <div class="breadcrumb">
        <h1>Add New Produk Biller</h1>
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
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <p>{{ $message }}</p>
                    </div>
                @endif

                @if ($message = Session::get('failed'))
                    <div class="alert alert-danger">
                        <p>{{ $message }}</p>
                    </div>
                @endif
                    <form action="{{ route('store_produk') }}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Fitur</label>
                            <div class="col-sm-10">
                                <select name="screen" class="form-control">
                                    <option value="">Select Fitur</option>
                                    @foreach($screen as $item)
                                        <option value="{{ $item->screen_id }}" {{ old('screen_id') == $item->screen_id ? 'selected' : '' }}>
                                        {{ str_replace('Form Pilih', '', $item->screen_title) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nama Produk</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{{ old('nama_produk') }}" name="nama_produk" placeholder="Nama Produk" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12 text-right">
                                <a href="{{ route('list_produk') }}">
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
