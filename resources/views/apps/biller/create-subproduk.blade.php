@extends('layouts.master')

@section('main-content')
    <div class="breadcrumb">
        <h1>Tambah Sub Produk Biller</h1>
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
                    <form action="{{ route('store_sub_produk') }}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Provider</label>
                            <div class="col-sm-10">
                                <select name="comp_id" class="form-control">
                                    <option value="">Select Provider</option>
                                    @foreach($component as $item)
                                        <option value="{{ $item->comp_id }}" {{ old('comp_id') == $item->comp_id ? 'selected' : '' }}>
                                            {{ $item->comp_lbl }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Produk</label>
                            <div class="col-sm-10">
                                <select id="selectize-dropdown" name="items">
                                    <option value="">Select Produk</option>
                                    @foreach($component as $item)
                                        <optgroup label="{{ $item->comp_lbl }}">
                                            @foreach($item->comp_option as $option)
                                                <option value="{{ $option->opt_id }}" {{ old('items') == $option->opt_id ? 'selected' : '' }}>
                                                    {{ $option->opt_label }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                <small id="inputFormat" class="form-text text">
                                    Format penulisan untuk produk baru: <strong>Option Id - Nama Produk</strong>
                                </small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Meta ID</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{{ old('meta_id') }}" name="meta_id" placeholder="Meta ID" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Default Value</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{{ old('default_value') }}" name="default_value" placeholder="Default Value" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12 text-right">
                                <a href="{{ route('list_sub_produk') }}">
                                    <button type="button" class="btn btn-secondary">Kembali</button>
                                </a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-js')
<link href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"></script>
<script>
    $('#selectize-dropdown').selectize({
        create: true,
        sortField: 'text'
    });
</script>
@endsection

@section('bottom-js')
    <script src="{{ asset('assets/js/form.validation.script.js') }}"></script>
@endsection
