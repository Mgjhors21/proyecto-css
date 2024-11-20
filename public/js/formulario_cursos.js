// public/js/formulario_cursos.js

document.addEventListener('DOMContentLoaded', function () {
    const institucionSelect = document.getElementById('institucion_select');
    const otraInstitucionInput = document.getElementById('otra_institucion');
    const formulario = document.getElementById('cursoForm');

    // Mostrar u ocultar el campo de "Otra Institución" según la selección
    function toggleOtraInstitucion() {
        if (institucionSelect.value === 'Otro') {
            otraInstitucionInput.style.display = 'block';
        } else {
            otraInstitucionInput.style.display = 'none';
            otraInstitucionInput.value = '';
        }
    }

    // Verificar al cargar la página
    toggleOtraInstitucion();

    // Cambiar visibilidad cuando cambia la selección
    institucionSelect.addEventListener('change', toggleOtraInstitucion);

    // Validación del formulario
    function validarFormulario() {
        const lugarCertificado = document.getElementById('lugar_certificado').value.trim();
        const horas = document.getElementById('horas').value;
        const archivoPDF = document.getElementById('archivo').files[0];
        const tipoCurso = document.getElementById('tipo_curso').value;

        // Validación de los campos
        if (!lugarCertificado) {
            mostrarError('¡Ups!', 'El lugar del certificado no puede estar vacío 📝');
            return false;
        }

        if (!horas || horas <= 0) {
            mostrarError('¡Atención!', 'El número de horas debe ser mayor a 0 ⏰');
            return false;
        }

        if (!archivoPDF) {
            mostrarError('¡Falta algo!', 'No olvides subir el certificado en PDF 📄');
            return false;
        }

        if (archivoPDF && !archivoPDF.type.includes('pdf')) {
            mostrarError('¡Formato incorrecto!', 'El archivo debe ser un PDF 📋');
            return false;
        }

        if (institucionSelect.value === 'Otro' && !otraInstitucionInput.value.trim()) {
            mostrarError('¡Campo requerido!', 'Por favor, especifica el nombre de la institución 🏛️');
            return false;
        }

        if (!['seminario', 'extension'].includes(tipoCurso)) {
            mostrarError('¡Tipo inválido!', 'Selecciona un tipo de curso válido.');
            return false;
        }

        return true;
    }

    // Funciones para mostrar alertas estilizadas
    function mostrarError(titulo, mensaje) {
        Swal.fire({
            icon: 'error',
            title: titulo,
            text: mensaje,
            confirmButtonColor: '#d33',
            background: '#fff',
            showClass: {
                popup: 'animate_animated animate_fadeInDown'
            },
            hideClass: {
                popup: 'animate_animated animate_fadeOutUp'
            }
        });
    }

    function mostrarCargando() {
        Swal.fire({
            title: 'Guardando el curso...',
            html: 'Por favor espera un momento <b></b>',
            timer: 2000,
            timerProgressBar: true,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    function mostrarExito() {
        Swal.fire({
            icon: 'success',
            title: '¡Registro Exitoso! 🎉',
            text: 'El curso ha sido registrado correctamente',
            confirmButtonColor: '#28a745',
            background: '#fff',
            showClass: {
                popup: 'animate_animated animate_fadeInDown'
            },
            hideClass: {
                popup: 'animate_animated animate_fadeOutUp'
            },
            footer: '<span style="color: #28a745">¡Gracias por registrar tu curso!</span>'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '/principal';
            }
        });
    }

    // Manejo del envío del formulario
    formulario.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!validarFormulario()) {
            return;
        }

        mostrarCargando();

        const formData = new FormData(this);
        const url = this.getAttribute('action');

        // Obtener el token CSRF del formulario
        const csrfToken = document.querySelector('input[name="_token"]').value;

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin'
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errors => {
                        throw new Error(Object.values(errors.errors).flat().join(', '));
                    });
                }
                return response.json();
            })
            .then(data => {
                mostrarExito();
                setTimeout(() => {
                    window.location.href = '/principal';
                }, 2000);
            })
            .catch(error => {
                mostrarError('¡Error!', error.message || 'Ocurrió un problema al guardar el curso');
            });
    });
});
