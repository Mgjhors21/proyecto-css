@extends('template')

@section('contenido')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="container p-4 animate__animated animate__fadeIn">
        <div class="card shadow-lg">
            <div class="card-header bg-white border-0">
                <div class="d-flex align-items-center">
                    <i class="fas fa-file-import fs-3 me-3 text-primary"></i>
                    <h1 class="h3 mb-0">Importar Estudiantes desde CSV</h1>
                </div>
            </div>

            <div class="card-body">
                <!-- Formulario de importación -->
                <div class="card bg-light border-0 mb-4">
                    <div class="card-body">
                        <form action="{{ route('estudiantes.import') }}" method="POST" enctype="multipart/form-data"
                            id="importForm">
                            @csrf
                            <div class="row align-items-end">
                                <div class="col-md-8">
                                    <div class="file-upload-wrapper">
                                        <label for="file" class="form-label">
                                            <i class="fas fa-cloud-upload-alt me-2"></i>Seleccionar archivo CSV:
                                        </label>
                                        <div class="input-group">
                                            <input type="file" class="form-control" name="file" id="file"
                                                required accept=".csv" data-browse="Seleccionar">
                                            <span class="input-group-text" id="fileSelected">No hay archivo
                                                seleccionado</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-file-import me-2"></i>Importar
                                        </button>
                                        <a href="{{ route('usuarios') }}" class="btn btn-secondary btn-lg">
                                            <i class="fas fa-times me-2"></i>Cancelar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Formulario para migrar estudiantes a usuarios -->
                <div class="card bg-light border-0 mb-4">
                    <div class="card-body">
                        <form id="migrateForm" action="{{ route('usuarios.migrarEstudiantes') }}" method="POST">
                            @csrf
                            <button id="migrateButton" type="submit" class="btn btn-primary">Migrar Estudiantes a
                                Usuarios</button>
                        </form>
                    </div>
                </div>


                <!-- Lista de estudiantes -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-users me-2 text-primary"></i>
                                    <h5 class="mb-0">Lista de Estudiantes</h5>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="search-wrapper">
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent">
                                            <i class="fas fa-search text-primary"></i>
                                        </span>
                                        <input type="text" id="search" class="form-control"
                                            placeholder="Buscar por Cod Alumno">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-container" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-hover mb-0" id="studentsTable">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th class="sortable" data-sort="cod_alumno">
                                            <i class="fas fa-sort me-2"></i>Cod Alumno
                                        </th>
                                        <th class="sortable" data-sort="documento">
                                            <i class="fas fa-sort me-2"></i>Documento
                                        </th>
                                        <th class="sortable" data-sort="nombres">
                                            <i class="fas fa-sort me-2"></i>Nombres
                                        </th>
                                        <th class="sortable" data-sort="semestre">
                                            <i class="fas fa-sort me-2"></i>Semestre
                                        </th>
                                        <th>Teléfonos</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody id="studentTableBody">
                                    @foreach ($estudiantes as $estudiante)
                                        <tr class="animate__animated animate__fadeIn">
                                            <td>{{ $estudiante->cod_alumno }}</td>
                                            <td>{{ $estudiante->documento }}</td>
                                            <td>{{ $estudiante->nombres }}</td>
                                            <td>{{ $estudiante->semestre }}</td>
                                            <td>
                                                <span class="phone-number" data-bs-toggle="tooltip"
                                                    title="Click para copiar">
                                                    {{ $estudiante->telefonos }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="mailto:{{ $estudiante->email }}"
                                                    class="text-decoration-none email-link">
                                                    {{ $estudiante->email }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div id="noResults" class="text-center py-4 d-none">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No se encontraron resultados</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('js/upload_csv.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Aceptar'
                }).then(() => {
                    window.location.href = "{{ route('users.upload_csv') }}";
                });
            @elseif ($errors->any())
                let errorMessages = '';
                @foreach ($errors->all() as $error)
                    errorMessages += "{{ $error }}\n";
                @endforeach
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessages,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Aceptar'
                });
            @endif
        });
    </script>
@endsection
