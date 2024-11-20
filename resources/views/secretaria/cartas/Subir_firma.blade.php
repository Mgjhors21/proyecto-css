@extends('template')

@section('titulo', 'Subir Firma Digital')

@section('contenido')

    <link rel="stylesheet" href="{{ asset('css/subir_firma.css') }}">

    <div class="card">
        <div class="subir-container p-4 bg-light border rounded">
            <h2 class="text-center mb-4">Subir Firma Digital</h2>
            <form action="{{ route('subir.firma') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="firma" class="form-label">Selecciona una firma digital (.png):</label>
                    <input type="file" name="firma" id="firma" accept="image/png" class="form-control" required>
                </div>
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">Cargar Firma</button>
                </div>
            </form>
        </div>
    </div>

    @if (session('success'))
        <script>
            Swal.fire({
                title: 'Éxito!',
                text: 'Firma cargada correctamente',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                title: 'Error',
                text: 'Hubo un problema al cargar la firma. Intenta nuevamente.',
                icon: 'error',
                confirmButtonText: 'Aceptar'
            });
        </script>
    @endif

@endsection
