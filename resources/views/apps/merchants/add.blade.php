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
                            @foreach($assessments as $index => $assessment)
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">{{ $assessment->pertanyaan }}</label>
                                <div class="col-sm-10">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="answer[{{ $index }}]" id="yes_{{ $index }}" value="yes" required>
                                        <label class="form-check-label" for="yes_{{ $index }}">Ya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="answer[{{ $index }}]" id="no_{{ $index }}" value="no" required>
                                        <label class="form-check-label" for="no_{{ $index }}">Tidak</label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                <div class="col-sm-12 text-right">
                                    <button type="button" class="btn btn-primary" id="nextBtn">Next</button>
                                </div>
                            </div>
                            </div>
                            @endforeach
                        </div>
                        <div id="step2" style="display:none;">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Jenis Agen</label>
                                <div class="col-sm-10">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="option" id="option1" value="option1">
                                        <label class="form-check-label" for="option1">Agen Individu</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="option" id="option2" value="option2">
                                        <label class="form-check-label" for="option2">Agen Badan Hukum</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nama Pemilik</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="fullname" name="fullname" value="{{ old('fullname', session('fullname', null)) }}" placeholder="Nama Lengkap" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor Rekening</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="no" name="no" value="{{ old('no', session('no', null)) }}" placeholder="Nomor Rekening" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor CIF</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{{ old('no_cif', session('no_cif', null)) }}" name="no_cif" placeholder="Nomor CIF" disabled>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor KTP</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" value="{{ old('email', session('email', null)) }}" name="email" placeholder="Nomor KTP" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor NPWP</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" value="{{ old('email', session('email', null)) }}" name="email" placeholder="Nomor NPWP" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor Telepon</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" value="{{ old('email', session('email', null)) }}" name="email" placeholder="Nomor Telepon" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor Handphone</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{{ old('phone', session('phone', null)) }}" name="phone" placeholder="Nomor Handphone" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" value="{{ old('email', session('email', null)) }}" name="email" placeholder="Email" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Jenis Pekerjaan</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" value="{{ old('email', session('email', null)) }}" name="email" placeholder="Jenis Pekerjaan" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Alamat</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{{ old('address', session('address', null)) }}" name="address" placeholder="Alamat" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">RT</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{{old('city', session('city', null))}}" name="city" placeholder="RT" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">RW</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{{old('city', session('city', null))}}" name="city" placeholder="RW" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Kelurahan</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{{old('city', session('city', null))}}" name="city" placeholder="Kelurahan" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Kecamatan</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{{old('city', session('city', null))}}" name="city" placeholder="Kecamatan" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Kota/Kabupaten</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{{old('city', session('city', null))}}" name="city" placeholder="Kota/Kabupaten" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Provinsi</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{{old('city', session('city', null))}}" name="city" placeholder="Provinsi" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Kode Pos</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{{old('city', session('city', null))}}" name="city" placeholder="Kode Pos" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-12 text-right">
                                    <button type="button" class="btn btn-primary" id="prevBtn">Previous</button>
                                    <button type="button" class="btn btn-primary" id="nextBtn2">Next</button>
                                </div>
                            </div>
                        </div>
                        
                        <div id="step3" style="display:none;">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor KTP</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="no_ktp" placeholder="Nomor KTP" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor NPWP</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="no_npwp" placeholder="Nomor NPWP" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nomor Telepon</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="no_telepon" placeholder="Nomor Telepon" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="file_ktp">Upload KTP</label>
                                <div class="col-sm-10">
                                    <input type="file" class="form-control" name="file_ktp" id="file_ktp" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label" for="file_npwp">Upload NPWP</label>
                                <div class="col-sm-10">
                                    <input type="file" class="form-control" name="file_npwp" id="file_npwp" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-12 text-right">
                                    <button type="button" class="btn btn-primary" id="prevBtn2">Previous</button>
                                    <button type="submit" class="btn btn-primary">Save</button>
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
            document.getElementById('step3').style.display = 'block';
            document.getElementById('stepIndicator1').classList.add('active');
            document.getElementById('stepIndicator2').classList.remove('active');
        });

        
    </script>
@endsection
