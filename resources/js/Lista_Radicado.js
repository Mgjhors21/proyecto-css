document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Función para mostrar notificaciones
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} notification`;
        notification.textContent = message;
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '1000';
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Función para guardar el radicado
    async function saveRadicado(ticketId, numeroRadicado, button) {
        try {
            // Mostrar estado de carga
            const originalText = button.textContent;
            button.textContent = 'Guardando...';
            button.disabled = true;

            // Realizar la petición
            const response = await fetch(`/vicerrectora/updateRadicado/${ticketId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ numero_radicado: numeroRadicado })
            });

            const data = await response.json();

            if (response.ok) {
                showNotification('Radicado guardado correctamente', 'success');

                // Actualizar el valor del input y añadir clase de éxito
                const input = document.getElementById(`radicado_${ticketId}`);
                if (input) {
                    input.classList.add('is-valid');
                    setTimeout(() => {
                        input.classList.remove('is-valid');
                    }, 2000);
                }
            } else {
                throw new Error(data.message || 'Error al guardar el radicado');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification(error.message, 'danger');
        } finally {
            // Restaurar el botón
            button.textContent = originalText;
            button.disabled = false;
        }
    }

    // Agregar listeners a los botones
    document.querySelectorAll('.btnGuardarRadicado').forEach(button => {
        button.addEventListener('click', function () {
            const ticketId = this.dataset.ticketId;
            const input = document.getElementById(`radicado_${ticketId}`);

            if (!input) {
                showNotification('Error: No se encontró el campo de radicado', 'danger');
                return;
            }

            const numeroRadicado = input.value.trim();

            if (!numeroRadicado) {
                showNotification('Por favor, ingrese un número de radicado', 'warning');
                input.focus();
                return;
            }

            saveRadicado(ticketId, numeroRadicado, this);
        });
    });
});
