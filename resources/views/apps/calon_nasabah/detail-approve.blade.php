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
                @foreach([
                    'NIK' => $nasabah->no_identitas,
                    'Nama Lengkap' => $nasabah->nama_lengkap,
                    'Nama Alias' => $nasabah->nama_alias,
                    'Ibu Kandung' => $nasabah->ibu_kandung,
                    'Tempat Lahir' => $nasabah->tempat_lahir,
                    'Tanggal Lahir' => $nasabah->tgl_lahir,
                    'Jenis Kelamin' => $jenis_kelamin->where('seq', $nasabah->jenis_kelamin)->first()->opt_label ?? '',
                    'Agama' => $agama->where('seq', $nasabah->agama)->first()->opt_label ?? '',
                    'Alamat' => $nasabah->alamat,
                    'RT' => $nasabah->rt,
                    'RW' => $nasabah->rw,
                    'Kecamatan' => $nasabah->kecamatan,
                    'Kelurahan' => $nasabah->kelurahan,
                    'Kabupaten/Kota' => $kab_kota->where('seq', $nasabah->kab_kota)->first()->opt_label ?? $nasabah->kab_kota,
                    'Provinsi' => $provinsi->where('seq', $nasabah->provinsi)->first()->opt_label ?? '',
                    'Kode POS' => $nasabah->kode_pos,
                    'Nomor Telepon' => $nasabah->no_telp,
                    'Nomor HP' => $nasabah->no_hp,
                    'Status Penduduk' => $status_penduduk->where('seq', $nasabah->status_penduduk)->first()->opt_label ?? '',
                    'Kewarganegaraan' => $kewarganegaraan->where('seq', $nasabah->kewarganegaraan)->first()->opt_label ?? '',
                    'Nomor NPWP' => $nasabah->npwp,
                    'Jenis Identitas' => $jenis_identitas->where('seq', $nasabah->jenis_identitas)->first()->opt_label ?? '',
                    'Golongan Darah' => $golongan_darah->where('seq', $nasabah->golongan_darah)->first()->opt_label ?? '',
                    'Expired Identitas' => $nasabah->expired_identitas,
                    'Pendidikan Terakhir' => $pendidikan_terakhir->where('seq', $nasabah->pendidikan_terakhir)->first()->opt_label ?? '',
                    'Email' => $nasabah->email
                ] as $label => $value)
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">{{ $label }}</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">{{ $value }}</p>
                        </div>
                    </div>
                @endforeach

                <div class="col-sm-12 d-flex justify-content-end">
                    <a href="{{ route('nasabah_request') }}" class="btn btn-primary mr-2">Back</a>
                    <form id="actionForm" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="nasabah_id" value="{{ $nasabah->id }}">
                        <input type="hidden" name="action" id="formAction">

                        <button type="button" id="activateMerchantBtn" class="btn btn-success">
                            Approve
                        </button>

                        <button type="button" id="rejectMerchantBtn" class="btn btn-danger ml-2">
                            Reject
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
document.getElementById('activateMerchantBtn').addEventListener('click', function() {
    if (confirm('Are you sure you want to activate this merchant?')) {
        document.getElementById('formAction').value = 'activate';
        document.getElementById('actionForm').action = "{{ route('nasabah_approve', ['id' => $nasabah->id]) }}";
        document.getElementById('actionForm').submit();
    }
});

document.getElementById('rejectMerchantBtn').addEventListener('click', function() {
    if (confirm('Are you sure you want to reject this merchant?')) {
        document.getElementById('formAction').value = 'reject';
        document.getElementById('actionForm').action = "{{ route('nasabah_reject', ['id' => $nasabah->id]) }}";
        document.getElementById('actionForm').submit();
    }
});
</script>
@endsection

@section('bottom-js')
<script src="{{ asset('assets/js/form.validation.script.js') }}"></script>
@endsection
