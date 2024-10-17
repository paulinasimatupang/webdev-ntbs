<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Selada</title>
        <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet">
        <link rel="stylesheet" href="{{asset('assets/styles/css/themes/lite-purple.min.css')}}">
        <link rel="stylesheet" href="{{asset('assets/styles/custom/bintang.css')}}">
    </head>
    <style>
    /* Warna default untuk tombol Sign In */
    .btn_selada {
        background-color: #0a6e44;
        color: white; /* Warna teks untuk kontras */
        border: none;
    }

    /* Warna saat tombol dihover atau diaktifkan */
    .btn_selada:hover,
    .btn_selada:focus {
        background-color: #efaf32;
        color: white; /* Warna teks saat dihover atau diaktifkan */
    }
    .invalid-feedback {
        display: block; /* Ensures each error message is on a new line */
        margin-top: 0.25rem; /* Adds a small margin for spacing */
        font-size: 0.875em; /* Adjust font size if necessary */
        color: #dc3545; /* Bootstrap's default danger color for errors */
    }

    .form-group {
        max-width: 350px; /* Adjust as needed to match your layout */
    }
</style>
    <body>
        <div class="auth-layout-wrap align-items-md-end mr-10" style="background-image: url({{asset('assets/images/icon_bintang/bg-ntbs.png')}})">
            <div class="auth-content width_signin m-0">
                <div style=" border-radius: 0px;" class="card o-hidden h-100vh border_radius_0 justify-content-center">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="p-4">
                                <div class="auth-logo text-center mb-4">
                                    <img class="logo_bintang_signin" src="{{asset('/assets/images/icon_signin/Logo-Bank-NTB-Syariah.png')}}" alt="">
                                </div>
                                @if(Session('error'))
                                    <div class="alert alert-danger" role="alert">
                                        {{ (Session('error')) }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif
                                <!-- <h1 class="mb-3 text-18">Sign In</h1> -->
                                <form method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input id="username"
                                            class="form-control form-control-rounded @error('username') is-invalid error @enderror"
                                            name="username" value="{{ old('username') }}" autocomplete="username"
                                            autofocus>
                                        @error('username')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <div class="input-group">
                                            <input id="password" type="password"
                                                class="form-control form-control-rounded @error('password') is-invalid @enderror"
                                                name="password" autocomplete="current-password">
                                            <div class="input-group-append">
                                                <span class="input-group-text" id="togglePassword" style="cursor: pointer;">
                                                    <i class="fa fa-eye-slash" id="eyeIcon"></i>
                                                </span>
                                            </div>
                                        </div>
                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <!-- <div class="form-group ">
                                        <div class="">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="remember"
                                                    id="remember" {{ old('remember') ? 'checked' : '' }}>

                                                <label class="form-check-label" for="remember">
                                                    {{ __('Remember Me') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div> -->

                                    <button class="btn btn-rounded btn-warning btn_selada btn-block mt-4 mb-3 p-2">Masuk</button>


                                </form>
                                @if (Route::has('password.request'))

              <!--                   <div class="mt-3 text-center">

                                    <a href="{{ route('password.request') }}" class="text-muted"><u>Forgot
                                            Password?</u></a>
                                </div> -->
                                @endif
                            </div>
                        </div>
                       
                    </div>
                </div>
            </div>
        </div>
        <script src="{{asset('assets/js/common-bundle-script.js')}}"></script>
        <script src="{{asset('assets/js/script.js')}}"></script>
    </body>
</html>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.add('fa-eye');
            eyeIcon.classList.remove('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.add('fa-eye-slash');
            eyeIcon.classList.remove('fa-eye');
        }
    });
</script>

