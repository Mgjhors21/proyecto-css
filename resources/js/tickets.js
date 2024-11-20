// tickets.js

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-aceptar').forEach(button => {
        button.addEventListener('click', function () {
            const ticketId = this.closest('tr').getAttribute('data-ticket-id');
            fetch(`/secretaria/ticket/aceptar/${ticketId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({})
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('tr').querySelector('.status-badge').classList.add('status-aprobado');
                        this.closest('tr').querySelector('.status-badge').textContent = 'Aprobado';
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });

    document.querySelectorAll('.btn-rechazar').forEach(button => {
        button.addEventListener('click', function () {
            const ticketId = this.closest('tr').getAttribute('data-ticket-id');
            fetch(`/secretaria/ticket/rechazar/${ticketId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({})
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('tr').querySelector('.status-badge').classList.add('status-rechazado');
                        this.closest('tr').querySelector('.status-badge').textContent = 'Rechazado';
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });
});
