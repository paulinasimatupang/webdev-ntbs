@extends('layouts.master')
@section('main-content')
<div class="breadcrumb">
    <h1>Edit Merchant</h1>
    <!-- <ul>
        <li><a href="">Selada</a></li>
    </ul> -->
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-5">
            <div class="card-body">
                <form action="{{ route('agen_update', [$merchant->id]) }}" method="POST">
                    @csrf
                    @method('POST')

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Jenis Agen</label>
                        <div class="col-sm-10">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_agen" id="option1" value="Agen Individu" 
                                    {{ old('jenis_agen', $merchant->jenis_agen) == 'Agen Individu' ? 'checked' : '' }}>
                                <label class="form-check-label" for="option1">Agen Individu</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_agen" id="option2" value="Agen Badan Hukum" 
                                    {{ old('jenis_agen', $merchant->jenis_agen) == 'Agen Badan Hukum' ? 'checked' : '' }}>
                                <label class="form-check-label" for="option2">Agen Badan Hukum</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Nomor Perjanjian Kerjasama</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="no_perjanjian_kerjasama" value="{{ $merchant->no_perjanjian_kerjasama }}" placeholder="Nomor Perjanjian Kerjasama" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Merchant ID</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ $merchant->mid }}" name="mid" placeholder="Merchant ID" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Nama Pemilik</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="name" name="name" value="{{ $merchant->name }}" placeholder="Nama Lengkap" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Jenis Kelamin</label>
                        <div class="col-sm-10">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_kelamin" id="option1" value="Laki - Laki" 
                                    {{ old('jenis_kelamin', $merchant->jenis_kelamin) == 'Laki - Laki' ? 'checked' : '' }}>
                                <label class="form-check-label" for="option1">Laki - Laki</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jenis_kelamin" id="option2" value="Perempuan" 
                                    {{ old('jenis_kelamin', $merchant->jenis_kelamin) == 'Perempuan' ? 'checked' : '' }}>
                                <label class="form-check-label" for="option2">Perempuan</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Nomor Rekening</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="no" name="no" value="{{ $merchant->no }}" placeholder="Nomor Rekening" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Nomor CIF</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ $merchant->no_cif }}" name="no_cif" placeholder="Nomor CIF" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Nomor KTP</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="no_ktp" name="no_ktp" value="{{ $merchant->no_ktp }}" placeholder="Nomor KTP" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Nomor NPWP</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ $merchant->no_npwp }}" name="no_npwp" placeholder="Nomor NPWP">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Nomor Telepon</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ $merchant->no_telp }}" name="no_telp" placeholder="Nomor Telepon" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Nomor Handphone</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ $merchant->phone }}" name="phone" placeholder="Phone" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control" value="{{ $merchant->email }}" name="email" placeholder="Email" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Jenis Pekerjaan</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ $merchant->pekerjaan }}" name="pekerjaan" placeholder="Jenis Pekerjaan" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Alamat</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ $merchant->address }}" name="address" placeholder="Address" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">RT</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ $merchant->rt }}" name="rt" placeholder="RT" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">RW</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ $merchant->rw }}" name="rw" placeholder="RW" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Kelurahan</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ $merchant->kelurahan }}" name="kelurahan" placeholder="Kelurahan" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Kecamatan</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ $merchant->kecamatan }}" name="kecamatan" placeholder="Kecamatan" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Kota/Kabupaten</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="city">
                                <option value="">Pilih Kota/Kabupaten</option>
                                @if($kota_kabupaten && count($kota_kabupaten) > 0)
                                    @foreach($kota_kabupaten as $item)
                                        <option value="{{ $item->opt_label }}" 
                                                {{ old('city', $merchant->city) == $item->opt_label ? 'selected' : '' }}>
                                            {{ $item->opt_label }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="">Data Kota/Kabupaten tidak tersedia</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Provinsi</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="provinsi">
                                <option value="">Pilih Provinsi</option>
                                @if($provinsi && count($provinsi) > 0)
                                    @foreach($provinsi as $item)
                                        <option value="{{ $item->opt_label }}" 
                                                {{ old('provinsi', $merchant->provinsi) == $item->opt_label ? 'selected' : '' }}>
                                            {{ $item->opt_label }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="">Data Provinsi tidak tersedia</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Kode Pos</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="{{ $merchant->kode_pos }}" name="kode_pos" placeholder="Kode Pos" required>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="file_ktp">KTP</label>
                        <div class="col-sm-10">
                            @if(isset($merchant) && $merchant->file_ktp)
                            <div class="mb-2">
                                <a href="{{ asset('uploads/' . $merchant->file_ktp) }}" target="_blank">Lihat File KTP</a>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="file_npwp">NPWP</label>
                        <div class="col-sm-10">
                            @if(isset($merchant) && $merchant->file_npwp)
                            <div class="mb-2">
                                <a href="{{ asset('uploads/' . $merchant->file_npwp) }}" target="_blank">Lihat File NPWP</a>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="foto_lokasi_usaha">Lokasi Usaha</label>
                        <div class="col-sm-10">
                            @if(isset($merchant) && $merchant->foto_lokasi_usaha)
                            <div class="mb-2">
                                <a href="{{ asset('uploads/' . $merchant->foto_lokasi_usaha) }}" target="_blank">Lihat Foto Lokasi Usaha</a>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Activate Merchant</label>
                        <div class="col-sm-10">
                            <button type="button" id="activateMerchantBtn" class="btn btn-success" {{ $merchant->status_agen == 1 ? 'disabled' : '' }}>
                                {{ $merchant->status_agen == 1 ? 'Activated' : 'Activate' }}
                            </button>
                            @if ($merchant->status_agen == 1)
                            <label class="col-sm-10 col-form-label">Merchant Aktif</label>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Deactivate Merchant</label>
                        <div class="col-sm-10">
                            <button type="button" id="deactivateMerchantBtn" class="btn btn-primary" {{ $merchant->status_agen == 2 ? 'disabled' : '' }}>
                                {{ $merchant->status_agen == 2 ? 'Deactivated' : 'Deactivate' }}
                            </button>
                            @if ($merchant->status_agen == 2)
                            <label class="col-sm-10 col-form-label">Merchant Tidak aktif</label>
                            @endif
                        </div>
                    </div>

                    <script>
                        document.getElementById('activateMerchantBtn').addEventListener('click', function() {
                            if (confirm('Apakah Anda yakin akan mengaktivasi agen ini?')) {
                                fetch("{{ route('agen_activate', [$merchant->id]) }}", {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                        },
                                        body: JSON.stringify({
                                            action: 'activate'
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            alert('Merchant has been activated.');
                                            location.reload(); // Reload the page or redirect to merchant list
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                    });
                            }
                        });

                        document.getElementById('deactivateMerchantBtn').addEventListener('click', function() {
                            if (confirm('Apakah Anda yakin akan menonaftifkan agen ini?')) {
                                fetch("{{ route('agen_deactivate', [$merchant->id]) }}", {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                                        },
                                        body: JSON.stringify({
                                            action: 'deactivate'
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            alert('Merchant has been deactivated.');
                                            location.reload(); // Reload the page or redirect to merchant list
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                    });
                            }
                        });
                    </script>

                    <div class="form-group row">
                        <div class="col-sm-12 text-right">
                            <a href="{{route('agen_list')}}">
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