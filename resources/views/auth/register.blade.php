@extends('layouts.app')

@section('content')
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>{{ __('Registro') }}</title>

        <!-- Core JS Files -->
        <script src="{{ asset('atlantis/assets/js/core/jquery.3.2.1.min.js') }}"></script>
        <script src="{{ asset('atlantis/assets/js/core/popper.min.js') }}"></script>
        <script src="{{ asset('atlantis/assets/js/core/bootstrap.min.js') }}"></script>

        <!-- CSS Files -->
        <link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
        <link href="{{ asset('atlantis/assets/css/atlantis.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/register.css') }}">

    </head>

    <body>
        <div class="register-container">
            <div class="register-card">
                <div class="card-header">
                    {{ __('Registro para Certificado de Cursos de Extensión') }}
                </div>
                <div class="logo-form-container">
                    <div class="logo-container">
                        <img src="https://moodle.uniautonoma.edu.co/pluginfile.php/1/core_admin/logo/0x200/1724602779/logo-quimera-transparente.png"
                            alt="Logo">
                    </div>
                    <div class="form-container">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="name">{{ __('Name') }}</label>
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="email">{{ __('Email Address') }}</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="password">{{ __('Password') }}</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="password-confirm">{{ __('Confirm Password') }}</label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary btn-block">
                                    {{ __('Register') }}
                                </button>
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

    </html>
@endsection
