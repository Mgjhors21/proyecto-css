@extends('template')

@section('contenido')
    <link rel="stylesheet" href="{{ asset('css/edit.css') }}">

    <div class="card">
        <div class="card-header">
            <h1 class="page-title">EDITAR PERFIL</h1>

        </div>
        <form action="{{ route('usuarios.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="last_name" class="form-label">Apellido</label>
                    <input type="text" class="form-control" id="last_name" name="last_name"
                        value="{{ old('last_name', $user->last_name) }}" required>
                    @error('last_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="{{ old('email', $user->email) }}" pattern="[a-zA-Z0-9._%+-]+@uniautonoma\.edu\.co"
                        title="El correo debe terminar en @uniautonoma.edu.co" required>
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Teléfono</label>
                    <input type="text" class="form-control" id="phone" name="phone"
                        value="{{ old('phone', $user->phone) }}" minlength="9" maxlength="10" required pattern="\d{9,10}"
                        title="El teléfono debe contener entre 9 y 10 dígitos">
                    @error('phone')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña (Dejar en blanco para no cambiarla)</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" maxlength="8">
                        <button type="button" class="btn btn-outline-secondary toggle-password"
                            onclick="togglePasswordVisibility('password')">
                            👁️
                        </button>
                    </div>
                    @error('password')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 position-relative">
                    <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                            maxlength="8">
                        <button type="button" class="btn btn-outline-secondary toggle-password"
                            onclick="togglePasswordVisibility('password_confirmation')">
                            👁️
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="user_type" class="form-label">Rol</label>
                    <select name="user_type" id="user_type" class="form-control" required>
                        <option value="1" {{ $user->user_type == 1 ? 'selected' : '' }}>Administrador</option>
                        <option value="2" {{ $user->user_type == 2 ? 'selected' : '' }}>Coordinador</option>
                        <option value="3" {{ $user->user_type == 3 ? 'selected' : '' }}>Secretaria</option>
                        <option value="4" {{ $user->user_type == 4 ? 'selected' : '' }}>Estudiante</option>
                        <option value="5" {{ $user->user_type == 5 ? 'selected' : '' }}>Vicerrectoría</option>
                    </select>
                </div>


                <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                @if (session('success'))
                    <meta name="success-message" content="{{ session('success') }}">
                @endif
                <a href="{{ route('usuarios') }}" class="btn btn-secondary">Cancelar</a>

            </div>
        </form>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="{{ asset('js/edit.js') }}"></script> <!-- Llama al archivo JavaScript aquí -->
    </div>
@endsection
