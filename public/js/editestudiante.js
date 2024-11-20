// edit.js
document.addEventListener('DOMContentLoaded', () => {
    const successMessage = document.querySelector('meta[name="success-message"]');
    if (successMessage) {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: successMessage.content,
            confirmButtonText: 'Aceptar'
        });
    }
});
