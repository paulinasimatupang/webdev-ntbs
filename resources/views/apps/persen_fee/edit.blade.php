@extends('layouts.master')

@section('main-content')
<div class="breadcrumb">
    <h1>Edit Fee</h1>
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
        <div class="card">
            <div class="card-body">
                <form action="{{ route('persen_fee_update', ['id' => $group->id, 'penerima' => $group->penerima, 'persentase' => $group->persentase]) }}" method="POST">
                    @csrf
                    @method('post')
                    <div class="form-group">
                        <label for="meta_id">Peneriman</label>
                        <input type="text" class="form-control" id="penerima" name="penerima" value="{{ $group->penerima }}">
                    </div>

                    <div class="form-group">
                        <label for="meta_default">Persentase</label>
                        <input type="text" class="form-control" id="persentase" name="persentase" value="{{ $group->persentase }}">
                    </div>
                    <a href="{{ route('persen_fee') }}" class="btn btn-secondary">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection