document.addEventListener("DOMContentLoaded", function () {
    const approveButtons = document.querySelectorAll(".btn-approve");

    approveButtons.forEach((button) => {
        button.addEventListener("click", function (event) {
            event.preventDefault();

            const form = button.closest("form");
            const ticketId = form.getAttribute("data-ticket-id");

            // Mostrar confirmación de SweetAlert2
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción aprobará el ticket seleccionado.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, aprobar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Aquí puedes agregar el código para aprobar el ticket
                    console.log("Ticket aprobado:", ticketId);
                    form.submit();
                }
            });
        });
    });
});
