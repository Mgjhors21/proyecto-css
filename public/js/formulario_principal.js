// Esperar a que el DOM esté listo
document.addEventListener('DOMContentLoaded', function () {
    // Alerta de confirmación para el botón "Eliminar"
    document.querySelectorAll('.delete-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevenir el envío del formulario

            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Este registro se eliminará permanentemente',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Enviar el formulario si se confirma la eliminación

                    // Mostrar alerta de éxito después de la eliminación
                    Swal.fire({
                        title: '¡Eliminado!',
                        text: 'El registro ha sido eliminado correctamente.',
                        icon: 'success',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        });
    });

    // Alerta de éxito para el botón "Guardar" en el formulario principal
    const mainForm = document.querySelector('.main-form');
    if (mainForm) {
        mainForm.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevenir el envío inicial del formulario

            Swal.fire({
                title: '¿Deseas guardar los cambios?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    mainForm.submit(); // Enviar el formulario si se confirma el guardado

                    // Mostrar alerta de éxito después de guardar los cambios
                    Swal.fire({
                        title: '¡Guardado!',
                        text: 'Los cambios se han guardado correctamente.',
                        icon: 'success',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        });
    }

    // Alerta de confirmación para el formulario de creación de tickets
    const ticketForm = document.querySelector('.form-ticket');
    if (ticketForm) {
        ticketForm.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevenir el envío inicial del formulario

            Swal.fire({
                title: '¿Estás seguro de que deseas crear un ticket?',
                text: 'Se generará un nuevo ticket de solicitud.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Crear Ticket',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    ticketForm.submit(); // Enviar el formulario si se confirma la creación del ticket

                    // Mostrar alerta de éxito después de crear el ticket
                    Swal.fire({
                        title: '¡Ticket Creado!',
                        text: 'El ticket de solicitud ha sido creado correctamente.',
                        icon: 'success',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });
        });
    }
});


$(document).ready(function() {
    // Cuando se cambie la facultad
    $('#Facultad').change(function() {
        var facultadId = $(this).val();  // Obtener el id de la facultad seleccionada

        if (facultadId) {
            // Realizar la solicitud AJAX para obtener los programas
            $.ajax({
                url: '/programas/' + facultadId,  // La ruta definida para obtener los programas
                type: 'GET',
                success: function(data) {
                    // Limpiar el select de programas
                    $('#programa_academico').empty().append('<option value="">Seleccione un programa</option>');

                    // Añadir los programas recibidos al select
                    $.each(data, function(index, programa) {
                        $('#programa_academico').append('<option value="' + programa.id + '">' + programa.nombre_programa + '</option>');
                    });
                },
                error: function() {
                    alert('Hubo un error al cargar los programas');
                }
            });
        } else {
            // Limpiar el select de programas si no hay facultad seleccionada
            $('#programa_academico').empty().append('<option value="">Seleccione un programa</option>');
        }
    });
});
