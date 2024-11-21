document.addEventListener("DOMContentLoaded", function () {
    const messages = document.getElementById("session-messages");

    // Verificar mensajes del backend
    const successMessage = messages.dataset.success;
    const errorMessage = messages.dataset.error;
    const validationErrors = messages.dataset.errors ? JSON.parse(messages.dataset.errors) : null;

    // Mostrar mensaje de éxito
    if (successMessage) {
        Swal.fire({
            icon: "success",
            title: "Éxito",
            text: successMessage,
            confirmButtonText: "Aceptar",
        });
    }

    // Mostrar mensaje de error general
    if (errorMessage) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: errorMessage,
            confirmButtonText: "Aceptar",
        });
    }

    // Mostrar errores de validación
    if (validationErrors) {
        let errorList = validationErrors.map(error => `<li>${error}</li>`).join("");

        Swal.fire({
            icon: "warning",
            title: "Errores de validación",
            html: `<ul style="text-align: left;">${errorList}</ul>`,
            confirmButtonText: "Corregir",
        });
    }
});
