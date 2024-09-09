@extends('layouts.master')

@section('main-content')
<div class="breadcrumb">
    <h1>Detail User</h1>
</div>
<div class="separator-breadcrumb border-top"></div>

@if(Session::has('error'))
    <div class="alert alert-danger" role="alert">
        <strong class="text-capitalize">Error: </strong> {{ Session::get('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="row">
    <div class="col-md-12">
        <div class="card mb-5">
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Nama Lengkap</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $user->fullname }}</p>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Username</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $user->username }}</p>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Role</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $user->role ? $user->role->name : '-' }}</p>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Cabang</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $user->branch ? $user->branch->branch_name : '-' }}</p>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12 text-right">
                        <a href="{{ route('users.list-request') }}" class="btn btn-primary">Back</a>
                        <form id="actionForm" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="agen_id" value="{{ $user->id }}">
                            <input type="hidden" name="action" id="formAction">

                            <button type="button" id="accept" class="btn btn-success">
                                Accept
                            </button>

                            <button type="button" id="reject" class="btn btn-danger">
                                Reject
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-js')
<script>
document.getElementById('accept').addEventListener('click', function() {
    if (confirm('Apakah Anda yakin akan menerima user ini?')) {
        document.getElementById('formAction').value = 'accept';
        document.getElementById('actionForm').action = "{{ route('users.accept', ['id' => $user->id]) }}";
        document.getElementById('actionForm').submit();
    }
});

document.getElementById('reject').addEventListener('click', function() {
    if (confirm('Apakah Anda yakin akan menolak agen ini?')) {
        document.getElementById('formAction').value = 'reject';
        document.getElementById('actionForm').action = "{{ route('users.reject', ['id' => $user->id]) }}";
        document.getElementById('actionForm').submit();
    }
});
</script>
@endsection

@section('bottom-js')
<script src="{{ asset('assets/js/form.validation.script.js') }}"></script>
@endsection
