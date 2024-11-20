@extends('template')

@section('titulo', 'Gestión de Usuarios')

@section('contenido')



    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">

    <body>
        <div class="content-inner">
            <div class="page-header">
                <h2>Gestión de Usuarios</h2>

                <!-- Barra de búsqueda -->
                <form action="{{ route('usuarios') }}" method="GET" class="mb-3">
                    <div class="input-group">
                        <input type="text" id="searchInput" name="search" class="form-control"
                            placeholder="Buscar por nombre o correo" value="{{ request('search') }}" onkeyup="buscar()">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">Buscar</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Tabla de usuarios -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table table-bordered">
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Apellido</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->codigo_estudiante }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>{{ $user->user_type }}</td>
                                <td class="actions">
                                    <a href="{{ route('usuarios.edit', $user->id) }}"
                                        class="btn btn-sm btn-warning">Editar</a>
                                    <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST"
                                        class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger delete-button">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
                <!-- Paginación -->
                <div class="d-flex justify-content-center">
                    {{ $users->links() }}
                </div>

                <!-- Botón para crear un nuevo usuario -->
                <div class="text-center mt-3">
                    <a href="{{ route('usuarios.create') }}" class="btn btn-success">Crear nuevo usuario</a>

                    <!-- Botón para añadir CSV -->
                    <a href="{{ route('users.upload_csv') }}" class="btn btn-info">Añadir CSV</a>
                </div>
            </div>

            <!-- SweetAlert JS -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script src="{{ asset('js/users.js') }}"></script>
        </div>
    </body>

@endsection
