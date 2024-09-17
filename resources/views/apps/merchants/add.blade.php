@extends('layouts.master')

@section('main-content')
    <div class="breadcrumb">
        <h1>Add Agen</h1>
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
                    <div class="step-indicator">
                        <div class="step active" id="stepIndicator1">1</div>
                        <div class="step" id="stepIndicator2">2</div>
                        <div class="step" id="stepIndicator3">3</div>
                    </div>
                    <br>

                    <form action="{{ route('agen_store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div id="step1">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Pertanyaan</th>
                                        <th>Jawaban</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assessments->slice(0, 5) as $index => $assessment)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $assessment->pertanyaan }}</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="answer[{{ $assessment->id }}]" id="yes_{{ $assessment->id }}" value="yes" required>
                                                <label class="form-check-label" for="yes_{{ $assessment->id }}">Ya</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="answer[{{ $assessment->id }}]" id="no_{{ $assessment->id }}" value="no" required>
                                                <label class="form-check-label" for="no_{{ $assessment->id }}">Tidak</label>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Catatan</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="catatan" placeholder="Catatan">{{ old('catatan', session('catatan', null)) }}</textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12 text-right">
                                    <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
                                </div>
                            </div>
                        </div>

                        <div id="step2" style="display:none;">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Jenis Agen</label>
                                <div class="col-sm-10">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="jenis_agen" id="option1" value="Agen Individu">
                                        <label class="form-check-label" for="option1">Agen Individu</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="jenis_agen" id="option2" value="Agen Badan Hukum">
                                        <label class="form-check-label" for="option2">Agen Badan Hukum</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor Perjanjian Kerjasama</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="no_perjanjian_kerjasama" value="{{ old('no_perjanjian_kerjasama', session('no_perjanjian_kerjasama', null)) }}" placeholder="Nomor Perjanjian Kerjasama" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nama Pemilik</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="fullname" value="{{ old('fullname', session('fullname', null)) }}" placeholder="Nama Lengkap" disbaled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Jenis Kelamin</label>
                                <div class="col-sm-10">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="option1" value="Laki - Laki">
                                        <label class="form-check-label" for="option1">Laki - Laki</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="option2" value="perempuan">
                                        <label class="form-check-label" for="option2">Perempuan</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor Rekening</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="no" value="{{ old('no', session('no', null)) }}" placeholder="Nomor Rekening">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor CIF</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="no_cif" value="{{ old('no_cif', session('no_cif', null)) }}" placeholder="Nomor CIF">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor KTP</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="no_ktp" value="{{ old('no_ktp', session('no_ktp', null)) }}" placeholder="Nomor KTP">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor NPWP</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="no_npwp" value="{{ old('no_npwp', session('no_npwp', null)) }}" placeholder="Nomor NPWP">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor Telepon</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="no_telp" value="{{ old('no_telp', session('no_telp', null)) }}" placeholder="Nomor Telepon">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor Handphone</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="phone" value="{{ old('phone', session('phone', null)) }}" placeholder="Nomor Telepon">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" name="email" value="{{ old('email', session('email', null)) }}" placeholder="Email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Jenis Pekerjaan</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="pekerjaan" value="{{ old('pekerjaan', session('pekerjaan', null)) }}" placeholder="Jenis Pekerjaan">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Alamat</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="address" value="{{ old('address', session('address', null)) }}" placeholder="Alamat">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">RT</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="rt" value="{{ old('rt', session('rt', null)) }}" placeholder="RT">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">RW</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="rw" value="{{ old('rw', session('rw', null)) }}" placeholder="RW">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Kelurahan</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="kelurahan" value="{{ old('kelurahan', session('kelurahan', null)) }}" placeholder="Kelurahan">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Kecamatan</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="kecamatan" value="{{ old('kecamatan', session('kecamatan', null)) }}" placeholder="Kecamatan">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Kota/Kabupaten</label>
                                <div class="col-sm-10">
                                    <select class="form-control" name="city">
                                        <option value="">Pilih Kota/Kabupaten</option>
                                        @if($kota_kabupaten && count($kota_kabupaten) > 0)
                                            @foreach($kota_kabupaten as $item)
                                                <option value="{{ $item->opt_label }}" {{ old('city', session('city', null)) == $item->opt_label ? 'selected' : '' }}>
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
                                                <option value="{{ $item->opt_label }}" {{ old('provinsi', session('provinsi', null)) == $item->opt_label ? 'selected' : '' }}>
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
                                    <input type="text" class="form-control" name="kode_pos" value="{{ old('kode_pos', session('kode_pos', null)) }}" placeholder="Kode Pos">
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
                                <label class="col-sm-2 col-form-label" for="latitude">Latitude:</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="latitude" name="latitude" readonly>
                                </div>
                                <label class="col-sm-2 col-form-label" for="longitude">Longitude:</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="longitude" name="longitude" readonly>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12 text-right">
                                    <button type="button" class="btn btn-secondary" id="prevBtn">Previous</button>
                                    <button type="button" class="btn btn-primary" id="nextBtn2">Next</button>
                                </div>
                            </div>
                        </div>
                        
                        <div id="step3" style="display:none;">

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="file_ktp">Upload KTP</label>
                                <div class="col-sm-10">
                                    <input type="file" class="form-control" name="file_ktp" id="file_ktp" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="file_npwp">Upload NPWP</label>
                                <div class="col-sm-10">
                                    <input type="file" class="form-control" name="file_npwp" id="file_npwp">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="foto_lokasi_usaha">Upload Foto Lokasi Usaha</label>
                                <div class="col-sm-10">
                                    <input type="file" class="form-control" name="foto_lokasi_usaha" id="foto_lokasi_usaha" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12 text-right">
                                    <button type="button" class="btn btn-secondary" id="prevBtn2">Previous</button>
                                    <button type="submit" class="btn btn-success">Save</button>
                                </div>
                            </div>
                        </div>

                    </form>
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
        document.getElementById('nextBtn').addEventListener('click', function () {
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
            document.getElementById('step3').style.display = 'none';
            document.getElementById('stepIndicator1').classList.remove('active');
            document.getElementById('stepIndicator2').classList.add('active');
            document.getElementById('stepIndicator3').classList.remove('active');
        });

        document.getElementById('nextBtn2').addEventListener('click', function () {
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'none';
            document.getElementById('step3').style.display = 'block';
            document.getElementById('stepIndicator1').classList.remove('active');
            document.getElementById('stepIndicator2').classList.remove('active');
            document.getElementById('stepIndicator3').classList.add('active');
        });

        document.getElementById('prevBtn').addEventListener('click', function () {
            document.getElementById('step1').style.display = 'block';
            document.getElementById('step2').style.display = 'none';
            document.getElementById('step3').style.display = 'none';
            document.getElementById('stepIndicator1').classList.add('active');
            document.getElementById('stepIndicator2').classList.remove('active');
        });

        document.getElementById('prevBtn2').addEventListener('click', function () {
            document.getElementById('step1').style.display = 'none';
            document.getElementById('step2').style.display = 'block';
            document.getElementById('step3').style.display = 'none';
            document.getElementById('stepIndicator1').classList.remove('active');
            document.getElementById('stepIndicator2').classList.add('active');
            document.getElementById('stepIndicator3').classList.remove('active');
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
