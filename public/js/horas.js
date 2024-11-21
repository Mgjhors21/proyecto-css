// Código para manejar la eliminación con confirmación utilizando SweetAlert2
document.querySelectorAll('.delete-form').forEach(function (form) {
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás revertir esta acción.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // Si el usuario confirma, se envía el formulario
            }
        });
    });
});
