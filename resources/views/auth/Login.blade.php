@extends('layouts.app')


<!DOCTYPE html>
<html lang="es">
@section('content')

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>{{ __('Inicio De Sesion') }}</title>



        <!-- CSS Files -->

        <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    </head>

    <body>
        <div class="login-container">
            <div class="card login-card">
                <div class="card-header">
                    {{ __('Bienvenido al Sistema de Certificación de Cursos de Extensión') }}
                </div>
                <div class="logo-form-container">
                    <div class="logo-container">
                        <img src="https://moodle.uniautonoma.edu.co/pluginfile.php/1/core_admin/logo/0x200/1724602779/logo-quimera-transparente.png"
                            alt="Logo">
                    </div>
                    <div class="form-container">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="email">{{ __('Email Address') }}</label>
                                <input id="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="password">{{ __('Password') }}</label>
                                <input id="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" name="password"
                                    maxlength="10" required autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <div class="custom-control custom-checkbox">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary btn-block">
                                    {{ __('Login') }}
                                </button>
                                @if (Route::has('password.request'))
                                    <div class="text-center mt-3">
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <script>
            function showAlert(message) {
                alert(message);
            }

            @if (session('alert'))
                showAlert('{{ session('alert') }}');
            @endif
        </script>
    </body>
@endsection

</html>
