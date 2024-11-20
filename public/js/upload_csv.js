// Inicializar tooltips de Bootstrap
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

document.addEventListener("DOMContentLoaded", function () {
    const migrateButton = document.getElementById("migrateButton");
    const migrateForm = document.getElementById("migrateForm");

    migrateForm.addEventListener("submit", function (e) {
        e.preventDefault(); // Prevenir el envío del formulario por defecto

        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción migrará a todos los estudiantes como usuarios.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, migrar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                migrateForm.submit(); // Solo se envía el formulario si se confirma
            }
        });
    });
});

// Gestión del archivo seleccionado
document.getElementById('file').addEventListener('change', function (e) {
    const fileName = e.target.files[0]?.name || 'No hay archivo seleccionado';
    document.getElementById('fileSelected').textContent = fileName;

    const fileWrapper = e.target.closest('.file-upload-wrapper');
    fileWrapper.classList.add('file-selected');
});

document.getElementById('search').addEventListener('input', function (e) {
    const searchTerm = e.target.value.toLowerCase().trim();
    const tbody = document.getElementById('studentTableBody');
    const rows = tbody.getElementsByTagName('tr');
    let hasResults = false;

    Array.from(rows).forEach(row => {
        // Obtener el contenido de la primera columna (Cod Alumno)
        const codAlumno = row.cells[0].textContent.toLowerCase().trim();

        // Comprobar si el código del alumno contiene el término de búsqueda
        if (codAlumno.includes(searchTerm)) {
            row.style.display = '';
            hasResults = true;

            // Resaltar solo el código del alumno
            const codAlumnoCell = row.cells[0];
            if (searchTerm) {
                const regex = new RegExp(`(${searchTerm})`, 'gi');
                codAlumnoCell.innerHTML = codAlumnoCell.textContent.replace(
                    regex,
                    '<span class="highlight">$1</span>'
                );
            } else {
                codAlumnoCell.innerHTML = codAlumnoCell.textContent;
            }
        } else {
            row.style.display = 'none';
        }


        // Mostrar/ocultar mensaje de no resultados
        const noResults = document.getElementById('noResults');
        if (noResults) {
            noResults.classList.toggle('d-none', hasResults);
        }

        // Si no hay resultados, y el mensaje no existe, crear el mensaje
        if (!hasResults && !noResults.classList.contains('d-none')) {
            noResults.classList.remove('d-none');
        } else if (hasResults && !noResults.classList.contains('d-none')) {
            noResults.classList.add('d-none');
        }
    });


    Array.from(rows).forEach(row => {
        // Obtener específicamente el contenido de la primera columna (Cod Alumno)
        const codAlumno = row.cells[0].textContent.toLowerCase().trim();

        // Comprobar si el código del alumno contiene el término de búsqueda
        if (codAlumno.includes(searchTerm)) {
            row.style.display = '';
            hasResults = true;

            // Resaltar solo el código del alumno
            const codAlumnoCell = row.cells[0];
            if (searchTerm) {
                const regex = new RegExp(`(${searchTerm})`, 'gi');
                codAlumnoCell.innerHTML = codAlumnoCell.textContent.replace(
                    regex,
                    '<span class="highlight">$1</span>'
                );
            } else {
                codAlumnoCell.innerHTML = codAlumnoCell.textContent;
            }
        } else {
            row.style.display = 'none';
        }
    });

    // Mostrar/ocultar mensaje de no resultados
    const noResults = document.getElementById('noResults');
    if (noResults) {
        noResults.classList.toggle('d-none', hasResults);
    }

    // Si no hay resultados y el mensaje no existe, crearlo
    if (!hasResults && !noResults) {
        const message = document.createElement('div');
        message.id = 'noResults';
        message.className = 'text-center py-4';
        message.innerHTML = `
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <p class="text-muted">No se encontraron resultados para "${searchTerm}"</p>
        `;
        tbody.parentNode.parentNode.appendChild(message);
    }
});

// Ordenamiento de columnas
document.querySelectorAll('.sortable').forEach(header => {
    header.addEventListener('click', function () {
        const column = this.dataset.sort;
        const tbody = document.getElementById('studentTableBody');
        const rows = Array.from(tbody.getElementsByTagName('tr'));

        const isAscending = this.classList.toggle('asc');

        rows.sort((a, b) => {
            const aVal = a.querySelector(`td:nth-child(${Array.from(this.parentNode.children).indexOf(this) + 1})`).textContent;
            const bVal = b.querySelector(`td:nth-child(${Array.from(this.parentNode.children).indexOf(this) + 1})`).textContent;

            return isAscending ? aVal.localeCompare(bVal) : bVal.localeCompare(aVal);
        });

        document.querySelectorAll('.sortable i').forEach(icon => {
            icon.className = 'fas fa-sort me-2';
        });

        this.querySelector('i').className = `fas fa-sort-${isAscending ? 'up' : 'down'} me-2`;

        rows.forEach(row => tbody.appendChild(row));
    });
});


// Copiar número de teléfono al hacer click
document.querySelectorAll('.phone-number').forEach(phone => {
    phone.addEventListener('click', function () {
        const number = this.textContent.trim();
        navigator.clipboard.writeText(number).then(() => {
            const tooltip = bootstrap.Tooltip.getInstance(this);
            this.setAttribute('data-bs-original-title', '¡Copiado!');
            tooltip.show();

            setTimeout(() => {
                this.setAttribute('data-bs-original-title', 'Click para copiar');
            }, 1500);
        });
    });
});

// Efecto hover en filas
document.querySelectorAll('#studentsTable tbody tr').forEach(row => {
    row.addEventListener('mouseenter', function () {
        this.classList.add('animate__animated', 'animate__pulse');
    });

    row.addEventListener('mouseleave', function () {
        this.classList.remove('animate__animated', 'animate__pulse');
    });
});

// Animación de carga durante el submit
document.getElementById('importForm').addEventListener('submit', function () {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Importando...';
    submitBtn.disabled = true;
});

// Estilos dinámicos
const style = document.createElement('style');
style.textContent = `
    .highlight {
        background-color: #fff3cd;
        padding: 2px;
        border-radius: 3px;
        font-weight: bold;
    }

    .file-selected .form-control {
        border-color: #198754;
    }

    .sortable {
        cursor: pointer;
        user-select: none;
    }

    .sortable:hover {
        background-color: #f8f9fa;
    }

    .email-link {
        color: #0d6efd;
        transition: color 0.3s;
    }

    .email-link:hover {
        color: #0a58ca;
    }

    .table-container::-webkit-scrollbar {
        width: 8px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    #search {
        border-color: #dee2e6;
        transition: all 0.3s ease;
    }

    #search:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
`;
document.head.appendChild(style);



