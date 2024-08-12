@extends('layouts.master')
@section('main-content')
    <div class="breadcrumb">
                <h1>Create Rekening</h1>
                <ul>
                    <li><a href="">Selada</a></li>
                   
                </ul>
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
                            <form action="{{route('merchant_store_rekening')}}" method="POST">
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Kode Cabang</label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" id="branchid" name="branchid" value="{{ old('branchid', session('branchid', null)) }}" placeholder="Kode Cabang" required3>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                                    <label class="col-sm-2 col-form-label">Nama Lengkap</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', session('nama_lengkap', null)) }}" placeholder="Nama Lengkap" required>
                                    </div>
                                </div>
                                <!-- <div class="form-group row">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                                    <label class="col-sm-2 col-form-label">NIK</label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" id="nik" name="nik" value="{{ old('nik', session('nik')) }}" placeholder="Nomor Identitas" >
                                    </div>
                                </div> -->
                                <div class="form-group row">
                                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                                    <label class="col-sm-2 col-form-label">Nomor CIF</label>
                                    <div class="col-sm-10">
                                        <input type="number" class="form-control" id="no_cif" name="no_cif" value="{{ old('no_cif', session('no_cif', null)) }}" placeholder="Nomor CIF" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Produk</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="kode_produk">
                                            <option value="36" {{ Request::old('kode_produk') == '36' ? 'selected' : '' }}>BSA Laku Pandai</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label">Nomor Registrasi</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" value="{{Request::old('no_registrasi')}}" name="no_registrasi" placeholder="Nomor Registrasi" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-12 text-right">
                                        <a href="{{route('merchant')}}">
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

    <script src="{{asset('assets/js/form.validation.script.js')}}"></script>

@endsection
