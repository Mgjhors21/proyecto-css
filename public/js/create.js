function togglePasswordVisibility(id) {
    const passwordField = document.getElementById(id);

    // Alterna el tipo de 'password' a 'text' y viceversa
    passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
}


document.getElementById('email').addEventListener('input', function() {
    let email = this.value;
    let errorMessage = document.querySelector('#email + .text-danger');
    fetch(`/usuarios/check-email?email=${email}`)
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                if (!errorMessage) {
                    errorMessage = document.createElement('div');
                    errorMessage.classList.add('text-danger');
                    errorMessage.innerText = 'Correo duplicado. Por favor ingrese otro correo.';
                    document.getElementById('email').parentElement.appendChild(errorMessage);
                }
            } else {
                if (errorMessage) {
                    errorMessage.remove();
                }
            }
        });
});


document.addEventListener("DOMContentLoaded", function () {
    // Verifica si existe el mensaje de éxito en el DOM
    const successMessage = document.querySelector("meta[name='success-message']");

    if (successMessage) {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: successMessage.content,
            showConfirmButton: true,
            confirmButtonText: 'Aceptar'
        });
    }
});



