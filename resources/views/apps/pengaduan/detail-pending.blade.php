@extends('layouts.master')
@section('main-content')
<div class="breadcrumb">
    <h1>Detail Calon Nasabah</h1>
</div>
<div class="separator-breadcrumb border-top"></div>

@if(Session::has('error'))
@foreach (Session::get('error') as $key => $item)
<div class="alert alert-danger" role="alert">
    <strong class="text-capitalize">Error: </strong> {{ $item[0] }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endforeach
@endif

<div class="row">
    <div class="col-md-12">
        <div class="card mb-5">
            <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Nama Agen</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{ $data->merchant->name }}</p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Kategori</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{ $data->kategori }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Deskripsi</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{ $data->deskripsi }}</p>
                        </div>
                    </div>

                    <div class="col-sm-12 d-flex justify-content-end">
                        <a href="{{ route('list_pending') }}" class="btn btn-primary mr-2">Back</a>
                        <form id="actionForm" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="data_id" value="{{ $data->id }}">
                            <input type="hidden" name="action" id="formAction">
                            <button type="button" id="approve" class="btn btn-success">
                                Approve
                            </button>
                        </form>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
document.getElementById('approve').addEventListener('click', function() {
    if (confirm('Apakah anda yakin akan menerima data ini?')) {
        document.getElementById('formAction').value = 'activate';
        document.getElementById('actionForm').action = "{{ route('pengaduan_process', ['id' => $data->id]) }}";
        document.getElementById('actionForm').submit();
    }
});
</script>
@endsection

@section('bottom-js')
<script src="{{ asset('assets/js/form.validation.script.js') }}"></script>
@endsection
