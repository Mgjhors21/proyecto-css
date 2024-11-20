document.addEventListener('DOMContentLoaded', function () {
    // Función para mostrar alertas con SweetAlert2
    function mostrarAlerta(title, icon, text) {
        Swal.fire({
            title: title,
            icon: icon,
            text: text,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Aceptar'
        });
    }

    // Función para manejar la confirmación de acciones
    function confirmarAccion(title, text, icon, confirmButtonText, callback) {
        Swal.fire({
            title: title,
            text: text,
            icon: icon,
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmButtonText,
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                callback();
            }
        });
    }

    // Manejador para aprobar tickets
    document.querySelectorAll('.btn-aprobar').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const form = this.closest('form');

            confirmarAccion(
                '¿Aprobar ticket?',
                '¿Estás seguro de que deseas aprobar este ticket?',
                'question',
                'Sí, aprobar',
                () => form.submit()
            );
        });
    });

    // Manejador para aprobar cursos individuales
    document.querySelectorAll('.btn-aprobar-curso').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const form = this.closest('form');
            const cursoId = this.getAttribute('data-curso-id');

            confirmarAccion(
                '¿Aprobar curso?',
                '¿Estás seguro de que deseas aprobar este curso?',
                'question',
                'Sí, aprobar',
                () => form.submit()
            );
        });
    });

    // Manejador para rechazar tickets
    document.querySelectorAll('.btn-rechazar').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const form = this.closest('form');

            confirmarAccion(
                '¿Rechazar ticket?',
                '¿Estás seguro de que deseas rechazar este ticket? agrega un comentario en el Curso por el cual se rechaza el ticket.',
                'warning',
                'Sí, rechazar',
                () => form.submit()
            );
        });
    });


    function rechazarCurso(cursoId) {
        Swal.fire({
            title: 'Agregar razón por la cual rechazas este curso:',
            input: 'textarea',
            inputPlaceholder: 'Escribe tu razón aquí...',
            showCancelButton: true,
            confirmButtonText: 'Rechazar',
            cancelButtonText: 'Cancelar',
            preConfirm: (reason) => {
                if (!reason) {
                    Swal.showValidationMessage('Por favor ingresa una razón.');
                }
                return reason;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Si se confirma, enviar el formulario
                const form = document.getElementById('form-rechazar-' + cursoId);
                form.insertAdjacentHTML('beforeend',
                    `<input type="hidden" name="curso_descripcion" value="${result.value}">`);
                form.submit();
            }
        });
    }

    // Manejador de eventos para el botón de rechazar curso
    document.querySelectorAll('.btn-rechazar-curso').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const cursoId = this.getAttribute('data-curso-id');
            rechazarCurso(cursoId);
        });
    });



    // Manejador de respuestas del servidor
    function manejarRespuestaServidor(response) {
        if (response.success) {
            mostrarAlerta('¡Éxito!', 'success', response.message);
            // Recargar la página después de 1.5 segundos
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            mostrarAlerta('Error', 'error', response.message || 'Ha ocurrido un error');
        }
    }

    // Función para manejar errores de red
    function manejarError(error) {
        console.error('Error:', error);
        mostrarAlerta('Error', 'error', 'Ha ocurrido un error de conexión');
    }

    // Inicialización de tooltips si los usas
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
