@extends('layouts.master')
@section('main-content')
<div class="breadcrumb">
    <h1>Edit Agen</h1>
    <!-- <ul>
        <li><a href="">Selada</a></li>
    </ul> -->
</div>
<div class="separator-breadcrumb border-top"></div>

<div class="row">
    <div class="col-md-12">
        <div class="card mb-5">
            <div class="card-body">
                   <div class="form-group row">
                        <label class="col-sm-2 col-form-label">Jenis Agen</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                                {{ $merchant->jenis_agen == 'Agen Individu' ? 'Agen Individu' : 'Agen Badan Hukum' }}
                            </p>
                        </div>
                    </div>

                    <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Nomor Perjanjian Kerjasama</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $merchant->no_perjanjian_kerjasama }}</p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Kode Agen</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">{{ $merchant->mid }}</p>
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
                                <div class="col-sm-12">
                                    <div id="map-container">
                                        <div id="map"></div>
                                    </div>
                                </div>
                            </div>

                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label" for="file_ktp">KTP</label>
                        <div class="col-sm-10">
                            @if(isset($merchant) && $merchant->file_ktp)
                            <div class="mb-2">
                                <a href="{{ asset('uploads/' . $merchant->file_ktp) }}" target="_blank">Lihat File KTP</a>
                            </div>
                            @else
                            <div class="mb-2">
                                <p>File Tidak Ditemukan</a>
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
                            @else
                            <div class="mb-2">
                                <p>File Tidak Ditemukan</a>
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
                            @else
                            <div class="mb-2">
                                <p>File Tidak Ditemukan</a>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="form-group row">
                    <div class="col-sm-12 text-right">
                    <a href="{{route('agen_list')}}">
                                            <button type="button" class="btn btn-primary">Kembali</button>
                                        </a>
                        <form id="actionForm" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="agen_id" value="{{ $merchant->id }}">
                            <input type="hidden" name="action" id="formAction">

                            @if($merchant->status_agen == 1)
                                <button type="button" id="deactive" class="btn btn-danger">
                                    Nonaktifkan
                                </button>
                                <button type="button" id="activate" class="btn btn-success" disabled>
                                    Aktifkan
                                </button>
                                <p class="d-inline-block ml-2">Agen Aktif</p>
                            @elseif($merchant->status_agen == 2)
                                <button type="button" id="deactive" class="btn btn-danger" disabled>
                                    Nonaktifkan
                                </button>
                                <p class="d-inline-block mr-2">Agen Nonaktif</p>
                                <button type="button" id="activate" class="btn btn-success">
                                    Aktifkan
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
        </div>
    </div>
</div>
@endsection


@section('page-css')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <style>
        #map-container {
            width: 100%;
            height: 500px;
            position: relative;
            margin-bottom: 20px;
        }
        #map {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
        }
        .leaflet-control-geocoder {
            width: 100%;
            max-width: none;
            margin-top: 10px;
        }
        .leaflet-control-geocoder-form input {
            width: calc(100% - 30px);
        }

        .step-indicator {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .step {
            width: 40px;
            height: 40px;
            line-height: 40px;
            background-color: #f1f1f1;
            border: 1px solid #ddd;
            border-radius: 50%;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
        }
        .step.active {
            background-color: #007bff;
            color: white;
        }
    </style>
@endsection

@section('bottom-js')
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script>
        document.getElementById('activate').addEventListener('click', function() {
            if (confirm('Apakah Anda yakin akan mengaktifkan agen ini?')) {
                document.getElementById('formAction').value = 'activate';
                document.getElementById('actionForm').action = "{{ route('agen_activate', ['id' => $merchant->id]) }}";
                document.getElementById('actionForm').submit();
            }
        });document.getElementById('deactive').addEventListener('click', function() {
            if (confirm('Apakah Anda yakin akan menonaktifkan agen ini?')) {
                document.getElementById('formAction').value = 'deactive';
                document.getElementById('actionForm').action = "{{ route('agen_deactivate', ['id' => $merchant->id]) }}";
                document.getElementById('actionForm').submit();
            }
        });
        document.addEventListener('DOMContentLoaded', function() {
            var map = L.map('map', {
                center: [-2.5489, 118.0149],
                zoom: 8,
                minZoom: 5,
                maxBounds: [
                    [-11.0, 94.0], 
                    [6.0, 141.0] 
                ],
                maxBoundsViscosity: 1.0
            });

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            var marker;

            var geocoder = L.Control.geocoder({
                defaultMarkGeocode: false,
                placeholder: 'Cari lokasi...',
                errorMessage: 'Tidak ditemukan.',
                suggestMinLength: 3,
                suggestTimeout: 250,
                queryMinLength: 1
            })
            .on('markgeocode', function(e) {
                var latlng = e.geocode.center;
                setMarkerAndView(latlng);
            })
            .addTo(map);

            function setMarkerAndView(latlng) {
                if (marker) {
                    map.removeLayer(marker);
                }
                marker = L.marker(latlng).addTo(map);
                map.setView(latlng, 14);
                
                document.getElementById('latitude').value = latlng.lat.toFixed(6);
                document.getElementById('longitude').value = latlng.lng.toFixed(6);
            }

            map.on('click', function(e) {
                setMarkerAndView(e.latlng);
            });

            setTimeout(function() {
                map.invalidateSize();
            }, 100);

            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    setTimeout(function() {
                        map.invalidateSize();
                    }, 100);
                }
            });
        });
    </script>
@endsection
