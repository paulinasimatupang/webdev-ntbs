@extends('layouts.master')

@section('main-content')
    <div class="breadcrumb">
        <h1>Detail Request</h1>
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
                                @foreach($assesmentDetails as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $detail->assesment->pertanyaan }}</td>
                                    <td>
                                        {{ $detail->poin != 0 ? 'Ya' : 'Tidak' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Total Poin</label>
                            <label class="col-sm-2 col-form-label">{{ $totalPoints ?? '-' }}</label>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Catatan</label>
                            <label class="col-sm-2 col-form-label">{{ $assesmentResult->catatan ?? 'Tidak ada catatan' }}</label>
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
                        <div id="map" style="height: 400px;"></div>
                        <div class="form-group row">
                            <div class="col-sm-12 text-right">
                                <button type="button" class="btn btn-secondary" id="prevBtn">Previous</button>
                                <button type="button" class="btn btn-primary" id="nextBtn2">Next</button>
                            </div>
                        </div>
                    </div>
                    
                    <div id="step3" style="display:none;">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label" for="file_ktp">KTP</label>
                            @if(isset($merchant) && $merchant->file_ktp)
                            <div class="mb-2">
                                <a href="{{ asset('uploads/' . $merchant->file_ktp) }}" target="_blank">Lihat File KTP</a>
                            </div>
                            @endif
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label" for="file_npwp">NPWP</label>
                            @if(isset($merchant) && $merchant->file_npwp)
                            <div class="mb-2">
                                <a href="{{ asset('uploads/' . $merchant->file_npwp) }}" target="_blank">Lihat File NPWP</a>
                            </div>
                            @endif
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label" for="foto_lokasi_usaha">Foto Lokasi Usaha</label>
                            @if(isset($merchant) && $merchant->foto_lokasi_usaha)
                            <div class="mb-2">
                                <a href="{{ asset('uploads/' . $merchant->foto_lokasi_usaha) }}" target="_blank">Lihat Foto Lokasi Usaha</a>
                            </div>
                            @endif
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12 text-right">
                                <button type="button" class="btn btn-secondary" id="prevBtn2">Previous</button>
                                <form id="actionForm" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="agen_id" value="{{ $merchant->id }}">
                                    <input type="hidden" name="action" id="formAction">
                                    <button type="button" id="reject" class="btn btn-danget">
                                        Reject
                                    </button>
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
    </div>
@endsection
@section('page-css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
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
<script>
    var latitude = {{ $merchant->latitude }};
    var longitude = {{ $merchant->longitude }};

    var map = L.map('map').setView([latitude, longitude], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    L.marker([latitude, longitude]).addTo(map)
        .bindPopup('Agen')
        .openPopup();
</script>
    <script>
        document.getElementById('approve').addEventListener('click', function() {
            if (confirm('Apakah Anda yakin akan mengaktivasi agen ini?')) {
                document.getElementById('formAction').value = 'activate';
                document.getElementById('actionForm').action = "{{ route('agen_activate', ['id' => $merchant->id]) }}";
                document.getElementById('actionForm').submit();
            }
        });document.getElementById('reject').addEventListener('click', function() {
            if (confirm('Apakah Anda yakin akan mengaktivasi agen ini?')) {
                document.getElementById('formAction').value = 'reject';
                document.getElementById('actionForm').action = "{{ route('agen_reject', ['id' => $merchant->id]) }}";
                document.getElementById('actionForm').submit();
            }
        });
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
    </script>
@endsection
