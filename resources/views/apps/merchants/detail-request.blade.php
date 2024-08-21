@extends('layouts.master')
@section('main-content')
<div class="breadcrumb">
    <h1>Detail Merchant</h1>
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
                <form action="{{ route('merchant_activate', [$merchant->id]) }}" method="POST">
                    @csrf
                    @method('POST')

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Merchant ID</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{ $merchant->mid }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Nama Lengkap</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{ $merchant->name }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{ $merchant->email }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Nomor Handphone</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{ $merchant->phone }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Alamat</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{ $merchant->address }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Kota</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{ $merchant->city }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Nomor Rekening</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{ $merchant->no }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Nomor Registrasi</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{ $merchant->no_registrasi }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Nomor CIF</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{ $merchant->no_cif }}</p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Kode Cabang</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                                @switch($merchant->branchid)
                                @case('000') KANTOR PUSAT @break
                                @case('001') KC ISLAMIC CENTER @break
                                @case('002') KC SELONG PAHLAWAN @break
                                @case('003') KC PRAYA SUDIRMAN @break
                                @case('004') KC SUMBAWA DR. WAHIDIN @break
                                @case('005') KC BIMA SOEKARNO HATTA @break
                                @case('006') KCP MATARAM CAKRANEGARA @break
                                @case('007') KC DOMPU NUSANTARA @break
                                @case('008') KC TANJUNG @break
                                @case('009') KCP ALAS @break
                                @case('010') KCP NARMADA @break
                                @case('011') KCP AIKMEL @break
                                @case('012') KCP SWETA @break
                                @case('013') KC TENTE @break
                                @case('014') KCP BOLO @break
                                @case('015') KCP KOPANG @break
                                @case('016') KCP KERUAK @break
                                @case('017') KC TALIWANG SUDIRMAN @break
                                @case('018') KCP MANGGELEWA @break
                                @case('019') KCP SAPE @break
                                @case('020') KCP PLAMPANG @break
                                @case('021') KC SURABAYA DARMO @break
                                @case('022') KC GERUNG @break
                                @case('023') KCP LUNYUK @break
                                @case('024') KCP LOPOK @break
                                @case('025') KCP PEKAT @break
                                @case('026') KCP PRINGGABAYA @break
                                @case('027') KCP PAGESANGAN @break
                                @case('028') KCP Ampenan @break
                                @case('029') KCP Gunungsari @break
                                @case('030') KCP Mujur @break
                                @case('501') KC MASBAGIK @break
                                @case('502') KCP PASAR TALIWANG @break
                                @case('503') KCP KEMPO @break
                                @case('504') KC SRIWIJAYA MATARAM @break
                                @case('505') KCP PEMENANG @break
                                @case('506') KCP MALUK @break
                                @case('507') KCP KEDIRI @break
                                @case('508') KCP WAWO @break
                                @case('509') KCP UTAN @break
                                @case('510') KCP PUJUT @break
                                @endswitch
                            </p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Produk</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                                @if($merchant->kode_produk == '36')
                                BSA Laku Pandai
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">KTP</label>
                        <div class="col-sm-10">
                            @if(isset($merchant) && $merchant->file_ktp)
                            <div class="mb-2">
                                <a href="{{ asset('uploads/' . $merchant->file_ktp) }}" target="_blank">Lihat File KTP</a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Kartu Keluarga</label>
                        <div class="col-sm-10">
                            @if(isset($merchant) && $merchant->file_kk)
                            <div class="mb-2">
                                <a href="{{ asset('uploads/' . $merchant->file_kk) }}" target="_blank">Lihat File Kartu Keluarga</a>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">NPWP</label>
                        <div class="col-sm-10">
                            @if(isset($merchant) && $merchant->file_npwp)
                            <div class="mb-2">
                                <a href="{{ asset('uploads/' . $merchant->file_npwp) }}" target="_blank">Lihat File NPWP</a>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12 text-right">
                            <a href="{{ route('merchant') }}">
                                <button type="button" class="btn btn-primary">Back</button>
                            </a>
                            <button type="submit" class="btn btn-primary">Activate</button>
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