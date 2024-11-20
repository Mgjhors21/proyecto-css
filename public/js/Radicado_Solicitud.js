document.addEventListener('DOMContentLoaded', function () {
    // Alerta de confirmación para el botón "Guardar"
    document.querySelectorAll('.tickets-table form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevenir el envío inicial del formulario

            Swal.fire({
                title: '¿Estás seguro de guardar el número de radicado?',
                text: 'Este cambio se guardará permanentemente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit(); // Enviar el formulario si se confirma
                    Swal.fire(
                        'Guardado!',
                        'El número de radicado ha sido guardado correctamente.',
                        'success'
                    );
                }
            });
        });
    });

    // Función de búsqueda por ID de Ticket
    const searchInput = document.getElementById('search-ticket');
    const searchButton = document.getElementById('search-btn');

    searchButton.addEventListener('click', function () {
        const searchValue = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll('.tickets-table tbody tr');

        if (searchValue === '') {
            // Si el campo de búsqueda está vacío, mostrar todas las filas
            rows.forEach(row => {
                row.style.display = '';
            });
        } else {
            // Filtrar las filas según el valor de búsqueda
            rows.forEach(row => {
                const ticketIdCell = row.querySelector('td:first-child');
                const ticketId = ticketIdCell.textContent.trim().toLowerCase();

                if (ticketId.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    });
});
