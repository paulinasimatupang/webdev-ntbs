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
                    <div class="step-container">
                        <div class="step active" id="stepIndicator1">1</div>
                        <div class="step-description">Assesment Due Diligence</div>
                    </div>
                    <div class="step-container">
                        <div class="step" id="stepIndicator2">2</div>
                        <div class="step-description">Profil Agen</div>
                    </div>
                    <div class="step-container">
                        <div class="step" id="stepIndicator3">3</div>
                        <div class="step-description">Upload File</div>
                    </div>
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
                                    @foreach($assessments as $index => $assessment)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $assessment->pertanyaan }}</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="answer[{{ $assessment->id }}]" id="yes_{{ $assessment->id }}" value="yes" {{ old('answer.' . $assessment->id) == 'yes' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="yes_{{ $assessment->id }}">Ya</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="answer[{{ $assessment->id }}]" id="no_{{ $assessment->id }}" value="no" {{ old('answer.' . $assessment->id) == 'no' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="no_{{ $assessment->id }}">Tidak</label>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Total Poin:</label>
                                <div class="col-sm-10">
                                    <h5 id="totalPoints">0</h5>
                                </div>
                            </div>
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
                                        <input class="form-check-input" type="radio" name="jenis_agen" id="option1" value="Agen Individu" {{ old('jenis_agen') == 'Agen Individu' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="option1">Agen Individu</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="jenis_agen" id="option2" value="Agen Badan Hukum" {{ old('jenis_agen') == 'Agen Badan Hukum' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="option2">Agen Badan Hukum</label>
                                    </div>
                                    @error('jenis_agen')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor Perjanjian Kerjasama</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('no_perjanjian_kerjasama') is-invalid @enderror" name="no_perjanjian_kerjasama" value="{{ old('no_perjanjian_kerjasama', session('no_perjanjian_kerjasama', null)) }}" placeholder="Nomor Perjanjian Kerjasama">
                                    @error('no_perjanjian_kerjasama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nama Pemilik</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('fullname') is-invalid @enderror" name="fullname" value="{{ old('fullname', session('fullname', null)) }}" placeholder="Nama Lengkap" {{ old('fullname') || session('fullname') ? 'readonly' : '' }}>
                                    @error('fullname')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Jenis Kelamin</label>
                                <div class="col-sm-10">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="gender1" value="Laki - Laki" {{ old('jenis_kelamin') == 'Laki - Laki' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gender1">Laki - Laki</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="jenis_kelamin" id="gender2" value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="gender2">Perempuan</label>
                                    </div>
                                    @error('jenis_kelamin')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor Rekening</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('no') is-invalid @enderror" name="no" value="{{ old('no', session('no', null)) }}" placeholder="Nomor Rekening" {{  old('no') || session('no') ? 'readonly' : '' }}>
                                    @error('no')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor CIF</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('no_cif') is-invalid @enderror" name="no_cif" value="{{ old('no_cif', session('no_cif', null)) }}" placeholder="Nomor CIF" {{  old('no_cif') || session('no_cif') ? 'readonly' : '' }}>
                                    @error('no_cif')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">NIK</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('no_ktp') is-invalid @enderror" name="no_ktp" value="{{ old('no_ktp', session('no_ktp', null)) }}" placeholder="NIK" {{  old('no_ktp') || session('no_ktp') ? 'readonly' : '' }}>
                                    @error('no_ktp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor NPWP</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('no_npwp') is-invalid @enderror" name="no_npwp" value="{{ old('no_npwp', session('no_npwp', null)) }}" placeholder="Nomor NPWP" {{  old('no_npwp') || session('no_npwp') ? 'readonly' : '' }}>
                                    @error('no_npwp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor Telepon</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('no_telp') is-invalid @enderror" name="no_telp" value="{{ old('no_telp', session('no_telp', null)) }}" placeholder="Nomor Telepon" {{  old('no_telp') || session('no_telp') ? 'readonly' : '' }}>
                                    @error('no_telp')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor Handphone</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', session('phone', null)) }}" placeholder="Nomor Handphone" {{  old('phone') ||  session('phone') ? 'readonly' : '' }}>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', session('email', null)) }}" placeholder="Email">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Jenis Pekerjaan</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('pekerjaan') is-invalid @enderror" name="pekerjaan" value="{{ old('pekerjaan', session('pekerjaan', null)) }}" placeholder="Jenis Pekerjaan">
                                    @error('pekerjaan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Alamat</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address', session('address', null)) }}" placeholder="Alamat" {{  old('address') || session('address') ? 'readonly' : '' }}>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">RT</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control @error('rt') is-invalid @enderror" name="rt" value="{{ old('rt', session('rt', null)) }}" placeholder="RT">
                                    @error('rt')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">RW</label>
                                <div class="col-sm-10">
                                    <input type="number" class="form-control @error('rw') is-invalid @enderror" name="rw" value="{{ old('rw', session('rw', null)) }}" placeholder="RW">
                                    @error('rw')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Kelurahan</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('kelurahan') is-invalid @enderror" name="kelurahan" value="{{ old('kelurahan', session('kelurahan', null)) }}" placeholder="Kelurahan">
                                    @error('kelurahan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Kecamatan</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control @error('kecamatan') is-invalid @enderror" name="kecamatan" value="{{ old('kecamatan', session('kecamatan', null)) }}" placeholder="Kecamatan">
                                    @error('kecamatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                    <input type="text" class="form-control @error('kode_pos') is-invalid @enderror" name="kode_pos" value="{{ old('kode_pos', session('kode_pos', null)) }}" placeholder="Kode Pos" {{  old('kode_pos') || session('kode_pos') ? 'readonly' : '' }}>
                                    @error('kode_pos')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                    <input type="text" class="form-control" id="latitude" name="latitude" value="{{ old('latitude') }}" readonly>
                                </div>
                                <label class="col-sm-2 col-form-label" for="longitude">Longitude:</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control" id="longitude" name="longitude" value="{{ old('longitude') }}" readonly>
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
                                    <input type="file" class="form-control @error('file_ktp') is-invalid @enderror" name="file_ktp" id="file_ktp">
                                    @error('file_ktp')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="file_npwp">Upload NPWP</label>
                                <div class="col-sm-10">
                                    <input type="file" class="form-control @error('file_npwp') is-invalid @enderror" name="file_npwp" id="file_npwp">
                                    @error('file_npwp')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="foto_lokasi_usaha">Upload Foto Lokasi Usaha</label>
                                <div class="col-sm-10">
                                    <input type="file" class="form-control @error('foto_lokasi_usaha') is-invalid @enderror" name="foto_lokasi_usaha" id="foto_lokasi_usaha">
                                    @error('foto_lokasi_usaha')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
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
    .step-container {
        display: flex;
        flex-direction: column;
        align-items: center;
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
    .step-description {
        width: 100px;
        text-align: center;
        margin-top: 5px;
        font-size: 12px;
        color: #666;
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
            const totalPoints = parseInt(document.getElementById('totalPoints').textContent);
            const requiredPoints = 50; 
            if (totalPoints < requiredPoints) {
                alert(`Total poin harus mencapai minimal ${requiredPoints} untuk melanjutkan.`);
                return;
            }
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

        document.querySelectorAll('input[type="radio"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                updateScore();
            });
        });

        function updateScore() {
            let totalPoints = 0;

            @foreach($assessments as $assessment)
                const answer{{ $assessment->id }} = document.querySelector('input[name="answer[{{ $assessment->id }}]"]:checked');
                if (answer{{ $assessment->id }} && answer{{ $assessment->id }}.value === 'yes') {
                    totalPoints += {{ $assessment->poin }};
                }
            @endforeach
            document.getElementById('totalPoints').textContent = totalPoints;
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            updateScore();
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
                var lat = e.latlng.lat;
                var lng = e.latlng.lng;
                
                // Menyimpan koordinat ke input
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;

                // Jika marker sudah ada, hapus yang lama
                if (marker) {
                    map.removeLayer(marker);
                }

                // Menambahkan marker baru di lokasi yang diklik
                marker = L.marker([lat, lng]).addTo(map);
            });

            @if(old('latitude') && old('longitude'))
                L.marker([{{ old('latitude') }}, {{ old('longitude') }}]).addTo(map);
            @endif

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
