function togglePasswordVisibility(id) {
    const passwordField = document.getElementById(id);

    // Alterna el tipo de 'password' a 'text' y viceversa
    passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
}


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
