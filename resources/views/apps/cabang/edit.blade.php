@extends('layouts.master')

@section('main-content')
    <div class="breadcrumb">
        <h1>Edit Cabang</h1>
        <!-- <ul>
            <li><a href="">Selada</a></li>
        </ul> -->
    </div>
    <div class="separator-breadcrumb border-top"></div>

    @if(Session('error'))
        <div class="alert alert-danger" role="alert">
            <strong class="text-capitalize">Error : </strong> {{ Session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-5">
                <div class="card-body">
                    <form action="{{ route('branch_update', [$branch->branch_id]) }}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Kode Cabang</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{{ $branch->branch_code }}" name="kode_cabang" placeholder="Kode Cabang" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nama Cabang</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" value="{{ $branch->branch_name }}" name="nama_cabang" placeholder="Nama Cabang" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12 text-right">
                                <a href="{{ route('branch') }}">
                                    <button type="button" class="btn btn-danger">Kembali</button>
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
