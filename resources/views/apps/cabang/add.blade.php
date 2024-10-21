@extends('layouts.master')

@section('main-content')
    <div class="breadcrumb">
        <h1>Tambah Cabang</h1>
        <!-- <ul>
            <li><a href="#">Selada</a></li>
        </ul> -->
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
                    <form action="{{ route('branch_store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Kode Cabang</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="kode_cabang" name="kode_cabang" value="{{ old('kode_cabang') }}" placeholder="Kode Cabang" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nama Cabang</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{{ old('nama_cabang') }}" name="nama_cabang" placeholder="Nama Cabang" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12 text-right">
                                <a href="{{ route('branch') }}">
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
@endsection

@section('bottom-js')
    <script src="{{ asset('assets/js/form.validation.script.js') }}"></script>
@endsection
