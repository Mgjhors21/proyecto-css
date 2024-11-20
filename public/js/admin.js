// FUNCION PARA VER LOS ERRORES DE LAS CONSULTAS MEDIANTE AJAX
function imprimirError(jqXHR, textStatus, errorThrown) {
    if (jqXHR.status === 0) {

        console.log('Not connect: Verify Network.');

    } else if (jqXHR.status == 404) {

        console.log('Requested page not found [404]');

    } else if (jqXHR.status == 500) {

        console.log('Internal Server Error [500].');

    } else if (textStatus === 'parsererror') {

        console.log('Requested JSON parse failed.');

    } else if (textStatus === 'timeout') {

        console.log('Time out error.');

    } else if (textStatus === 'abort') {

        console.log('Ajax request aborted.');

    } else {

        console.log('Uncaught Error: ' + jqXHR.responseText);

    }
}

/*Validacion de formulario*/
(function () {
    'use strict';
    window.addEventListener('load', function () {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function (form) {
            form.addEventListener('submit', function (event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
