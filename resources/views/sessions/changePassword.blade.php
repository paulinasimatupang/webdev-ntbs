@extends('layouts.master')

@section('main-content')
    <div class="breadcrumb">
        <h1>Ganti Kata Sandi</h1>
    </div>
    <div class="separator-breadcrumb border-top"></div>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-5">
                <div class="card-body">
                    <form action="{{ route('change_password_store') }}" method="POST">
                        @csrf

                        <!-- Password Lama dengan Ikon Mata -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Password Lama</label>
                            <div class="col-sm-10 input-group">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" placeholder="Masukkan password lama" value="{{ old('current_password') }}">
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePassword('current_password', 'current_password_eye')">
                                        <i class="fa fa-eye-slash" id="current_password_eye"></i>
                                    </span>
                                </div>
                                @error('current_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Password Baru dengan Ikon Mata -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Password Baru</label>
                            <div class="col-sm-10 input-group">
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" placeholder="Masukkan password baru" value="{{ old('new_password') }}">
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePassword('new_password', 'new_password_eye')">
                                        <i class="fa fa-eye-slash" id="new_password_eye"></i>
                                    </span>
                                </div>
                                @error('new_password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Konfirmasi Password Baru dengan Ikon Mata -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Konfirmasi Password Baru</label>
                            <div class="col-sm-10 input-group">
                                <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" id="new_password_confirmation" name="new_password_confirmation" placeholder="Konfirmasi password baru" value="{{ old('new_password_confirmation') }}">
                                <div class="input-group-append">
                                    <span class="input-group-text" onclick="togglePassword('new_password_confirmation', 'confirm_password_eye')">
                                        <i class="fa fa-eye-slash" id="confirm_password_eye"></i>
                                    </span>
                                </div>
                                @error('new_password_confirmation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <!-- Tombol Simpan -->
                        <div class="form-group row">
                            <div class="col-sm-12 text-right">
                                <a href="{{ route('landing') }}">
                                    <button type="button" class="btn btn-danger">Kembali</button>
                                </a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('bottom-js')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        // Fungsi untuk toggle visibilitas password
        function togglePassword(inputId, eyeIconId) {
            var inputField = document.getElementById(inputId);
            var eyeIcon = document.getElementById(eyeIconId);
            if (inputField.type === "password") {
                inputField.type = "text";
                eyeIcon.classList.add("fa-eye");
                eyeIcon.classList.remove("fa-eye-slash");
            } else {
                inputField.type = "password";
                eyeIcon.classList.add("fa-eye-slash");
                eyeIcon.classList.remove("fa-eye");
            }
        }
    </script>
@endsection
