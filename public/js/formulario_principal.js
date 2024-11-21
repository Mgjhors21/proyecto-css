document.addEventListener('DOMContentLoaded', function () {
    const horasErrorElement = document.getElementById('horas-error');
    const successMessageElement = document.getElementById('ticket-creado');
    const ticketForm = document.querySelector('.form-ticket');

    // Mostrar alerta de error si existe un problema con las horas
    if (horasErrorElement) {
        const horasTotalesSeminarios = horasErrorElement.getAttribute('data-horas-totales-seminarios');
        const horasTotalesExtension = horasErrorElement.getAttribute('data-horas-totales-extension');
        const horasMinimasSeminarios = horasErrorElement.getAttribute('data-horas-minimas-seminarios');
        const horasMinimasExtension = horasErrorElement.getAttribute('data-horas-minimas-extension');

        Swal.fire({
            title: 'Requisitos de Horas Incumplidos',
            html: `
                <div class="text-left">
                    <div class="mb-4">
                        <h3 class="font-semibold text-lg mb-2 text-blue-600">📚 Horas de Seminarios</h3>
                        <div class="flex justify-between">
                            <span>Horas actuales:</span>
                            <span class="${horasTotalesSeminarios < horasMinimasSeminarios ? 'text-red-500 font-bold' : 'text-green-500'}">
                                ${horasTotalesSeminarios} hrs
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span>Horas mínimas requeridas:</span>
                            <span class="text-blue-500">${horasMinimasSeminarios} hrs</span>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-lg mb-2 text-blue-600">🎓 Horas de Extensión</h3>
                        <div class="flex justify-between">
                            <span>Horas actuales:</span>
                            <span class="${horasTotalesExtension < horasMinimasExtension ? 'text-red-500 font-bold' : 'text-green-500'}">
                                ${horasTotalesExtension} hrs
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span>Horas mínimas requeridas:</span>
                            <span class="text-blue-500">${horasMinimasExtension} hrs</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-600 italic">
                    💡 No se cumple con las horas mínimas requeridas para crear un ticket.
                </div>
            `,
            icon: 'warning',
            confirmButtonColor: '#3085d6',
            customClass: {
                popup: 'rounded-lg shadow-xl',
                title: 'text-2xl font-bold text-gray-800',
                content: 'text-left'
            }
        });
    }

    // Manejar el envío del formulario de ticket
    if (ticketForm) {
        ticketForm.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevenir envío inicial

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
                    ticketForm.submit();
                }
            });
        });
    }

    // Mostrar alerta de éxito si el ticket se creó correctamente
    if (successMessageElement) {
        const message = successMessageElement.getAttribute('data-message');
        Swal.fire({
            title: '¡Éxito!',
            text: message,
            icon: 'success',
            confirmButtonColor: '#3085d6'
        });
    }
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
});

$(document).ready(function () {
    // Cuando se cambie la facultad
    $('#Facultad').change(function () {
        var facultadId = $(this).val();  // Obtener el id de la facultad seleccionada

        if (facultadId) {
            // Realizar la solicitud AJAX para obtener los programas
            $.ajax({
                url: '/programas/' + facultadId,  // La ruta definida para obtener los programas
                type: 'GET',
                success: function (data) {
                    // Limpiar el select de programas
                    $('#programa_academico').empty().append('<option value="">Seleccione un programa</option>');

                    // Añadir los programas recibidos al select
                    $.each(data, function (index, programa) {
                        $('#programa_academico').append('<option value="' + programa.id + '">' + programa.nombre_programa + '</option>');
                    });
                },
                error: function () {
                    alert('Hubo un error al cargar los programas');
                }
            });
        } else {
            // Limpiar el select de programas si no hay facultad seleccionada
            $('#programa_academico').empty().append('<option value="">Seleccione un programa</option>');
        }
    });

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
});


$(document).ready(function () {
    // Cuando se cambie la facultad
    $('#Facultad').change(function () {
        var facultadId = $(this).val();  // Obtener el id de la facultad seleccionada

        if (facultadId) {
            // Realizar la solicitud AJAX para obtener los programas
            $.ajax({
                url: '/programas/' + facultadId,  // La ruta definida para obtener los programas
                type: 'GET',
                success: function (data) {
                    // Limpiar el select de programas
                    $('#programa_academico').empty().append('<option value="">Seleccione un programa</option>');

                    // Añadir los programas recibidos al select
                    $.each(data, function (index, programa) {
                        $('#programa_academico').append('<option value="' + programa.id + '">' + programa.nombre_programa + '</option>');
                    });
                },
                error: function () {
                    alert('Hubo un error al cargar los programas');
                }
            });
        } else {
            // Limpiar el select de programas si no hay facultad seleccionada
            $('#programa_academico').empty().append('<option value="">Seleccione un programa</option>');
        }
    });
});
