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
                <!-- Konten detail merchant -->
                @csrf
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Kode Agen</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $merchant->mid }}</p>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Jenis Agen</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{ $merchant->option == 'Agen Individu' ? 'Agen Individu' : 'Agen Badan Hukum' }}</p>
                    </div>
                </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nomor Perjanjian Kerjasama</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $merchant->no_perjanjian_kerjasama }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nama Pemilik</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $merchant->name }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Jenis Kelamin</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    {{ $merchant->jenis_kelamin == 'Laki - Laki' ? 'Laki - Laki' : 'Perempuan' }}
                                </p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nomor Rekening</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $merchant->no }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nomor CIF</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $merchant->no_cif }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nomor KTP</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $merchant->no_ktp }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nomor NPWP</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $merchant->no_npwp }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nomor Telepon</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $merchant->no_telp }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nomor Handphone</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $merchant->phone }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $merchant->email }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Jenis Pekerjaan</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $merchant->pekerjaan }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Alamat</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $merchant->address }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">RT</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $merchant->rt }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">RW</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $merchant->rw }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Kelurahan</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $merchant->kelurahan }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Kecamatan</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $merchant->kecamatan }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Kota/Kabupaten</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $merchant->city }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Provinsi</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $merchant->provinsi }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Kode Pos</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $merchant->kode_pos }}</p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label" for="file_ktp">KTP</label>
                            @if(isset($merchant) && $merchant->file_ktp)
                            <div class="col-sm-10">
                                <a href="{{ asset('uploads/' . $merchant->file_ktp) }}" target="_blank">Lihat File NPWP</a>
                            </div>
                            @endif
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label" for="file_npwp">NPWP</label>
                            @if(isset($merchant) && $merchant->file_npwp)
                            <div class="col-sm-10">
                                <a href="{{ asset('uploads/' . $merchant->file_npwp) }}" target="_blank">Lihat File NPWP</a>
                            </div>
                            @endif
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label" for="foto_lokasi_usaha">Foto Lokasi Usaha</label>
                            @if(isset($merchant) && $merchant->foto_lokasi_usaha)
                            <div class="col-sm-10">
                                <a href="{{ asset('uploads/' . $merchant->foto_lokasi_usaha) }}" target="_blank">Lihat File NPWP</a>
                            </div>
                            @endif
                        </div>
                <div class="form-group row">
                    <div class="col-sm-12 text-right">
                        <a href="{{ route('agen_request') }}" class="btn btn-primary">Back</a>
                        <form id="actionForm" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="agen_id" value="{{ $merchant->id }}">
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
</div>

@endsection

@section('page-js')
<script>
document.getElementById('approve').addEventListener('click', function() {
    if (confirm('Apakah Anda yakin akan mengaktivasi agen ini?')) {
        document.getElementById('formAction').value = 'activate';
        document.getElementById('actionForm').action = "{{ route('agen_activate', ['id' => $merchant->id]) }}";
        document.getElementById('actionForm').submit();
    }
});
</script>
@endsection

@section('bottom-js')
<script src="{{ asset('assets/js/form.validation.script.js') }}"></script>
@endsection
