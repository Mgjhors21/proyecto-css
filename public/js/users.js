
document.querySelectorAll('.delete-button').forEach(button => {
    button.addEventListener('click', function() {
        const form = this.closest('.delete-form');
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Este usuario será eliminado permanentemente!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminarlo!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});


function buscar() {
    var input = document.getElementById('searchInput');
    var filtro = input.value.toUpperCase(); // Convertir filtro a mayúsculas
    var tablas = document.querySelectorAll('.table-responsive table'); // Obtener todas las tablas dentro de contenedores .table-responsive

    tablas.forEach(function(tabla) {
        var tbody = tabla.getElementsByTagName('tbody')[0];
        var filas = tbody.getElementsByTagName('tr'); // Obtener todas las filas de la tabla
        for (var i = 0; i < filas.length; i++) {
            var datos = filas[i].getElementsByTagName('td');
            var coincide = false;
            for (var j = 0; j < datos.length; j++) {
                var dato = datos[j].innerText.toUpperCase(); // Convertir dato a mayúsculas
                // Verificar si la búsqueda coincide con el nombre o el correo (que normalmente están en las columnas 1 y 2)
                if (j === 0 || j === 2) { // Columna 1: Nombre, Columna 2: Email
                    if (dato.includes(filtro)) {
                        coincide = true;
                        break;
                    }
                }
            }
            filas[i].style.display = coincide ? '' : 'none';
        }
    });
}
