document.addEventListener('DOMContentLoaded', () => {
    // Verificar si hay un mensaje de éxito en la sesión
    const successMessage = document.getElementById('success-message')?.textContent;

    if (successMessage) {
        Swal.fire({
            icon: 'success',
            title: '¡Eliminado!',
            text: successMessage,
            showConfirmButton: false,
            timer: 2500
        });
    }

    const deleteButtons = document.querySelectorAll('.btn-danger');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault(); // Prevenir envío del formulario

            const form = this.closest('form');

            // Confirmación con SweetAlert
            Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esta acción!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
