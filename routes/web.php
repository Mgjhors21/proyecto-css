<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\SecretariaController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\FacultadController;
use App\Http\Controllers\CursoController;


use App\Http\Controllers\UserController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Aquí es donde puedes registrar las rutas web para tu aplicación. Estas
| rutas son cargadas por el RouteServiceProvider y todas se asignarán
| al grupo "web" de middleware. Haz algo grandioso.
*/

// Ruta para la página de login
Route::get('/', function () {
    return view('auth.login');
});

// Rutas de autenticación (login, registro, recuperación de contraseña)
Auth::routes();

// Rutas protegidas por el middleware de autenticación
Route::middleware(['auth'])->group(function () {

    // Rutas de Gestion de Usuarios
    Route::prefix('usuarios')->group(function () {
        Route::get('gestion_user', [UsuariosController::class, 'usuarios'])->name('gestion.user');
        Route::get('/crear', [UsuariosController::class, 'create'])->name('usuarios.create'); // Formulario de creación
        Route::post('/', [UsuariosController::class, 'store'])->name('usuarios.store'); // Guardar nuevo usuario
        Route::get('/{id}/editar', [UsuariosController::class, 'edit'])->name('usuarios.edit'); // Formulario de edición
        Route::put('/{id}', [UsuariosController::class, 'updateUsuario'])->name('usuarios.update'); // Actualizar usuario
        Route::delete('/{id}', [UsuariosController::class, 'destroy'])->name('usuarios.destroy'); // Eliminar usuario
        Route::get('/check-email', [UsuariosController::class, 'checkEmail'])->name('usuarios.checkEmail');
    });



    // Rutas de bienvenida y certificados
    Route::get('/welcome', [WelcomeController::class, 'welcome'])->name('welcome');

    // Ruta para verificar el inicio de sesión
    Route::post('/verifi', [LoginController::class, 'verifi'])->name('verifi');

    // Rutas de verificación de email
    Route::get('email/verify', [VerificationController::class, 'show'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('email/resend', [VerificationController::class, 'resend'])->name('verification.resend');

    // Rutas para solicitudes
    Route::prefix('solicitud')->group(function () {
        // Rutas Secretaria:
        Route::get('/solicitudes_lista', [SecretariaController::class, 'listarTickets'])->name('solicitudes_lista');
        Route::post('/ticket/aprobar/{ticketId}', [SecretariaController::class, 'aprobarSolicitud'])->name('ticket.aprobar');
        Route::post('/tickets/{ticketId}/rechazar', [SecretariaController::class, 'rechazarSolicitud'])->name('solicitud.rechazar');
        Route::get('/secretaria/solicitudes/{id}', [SecretariaController::class, 'verDetalles'])->name('solicitud.detalles');
        Route::post('/cursos/{id}/rechazar/{tipoCurso}', [SecretariaController::class, 'rechazarCurso'])->name('curso.rechazar');
        Route::get('/historial', [SecretariaController::class, 'Historial'])->name('historial');
        Route::post('/curso/{id}/aprobar', [SecretariaController::class, 'aprobarCurso'])->name('curso.aprobar');

        // Rutas de Facultad
        Route::get('facultades', [FacultadController::class, 'index'])->name('facultades.index');
        Route::get('facultades/create', [FacultadController::class, 'create'])->name('facultades.create');
        Route::post('facultades', [FacultadController::class, 'store'])->name('facultades.store');
        Route::get('facultades/{id}', [FacultadController::class, 'show'])->name('facultades.show');
        Route::get('facultades/{id}/edit', [FacultadController::class, 'edit'])->name('facultades.edit');
        Route::put('facultades/{id}', [FacultadController::class, 'update'])->name('facultades.update');
        Route::delete('facultades/{id}', [FacultadController::class, 'destroy'])->name('facultades.destroy');
        Route::get('/facultades/{id}/programas', [FacultadController::class, 'obtenerProgramas'])->name('facultades.programas');


        // Rutas de Programa
        Route::get('facultades/{facultad}/programas/create', [ProgramaController::class, 'create'])->name('programas.create');
        Route::post('facultades/{facultad}/programas', [ProgramaController::class, 'store'])->name('programas.store');
        Route::delete('/programas/{id}', [ProgramaController::class, 'destroy'])->name('programas.destroy');

        // Rutas para Curso
        Route::get('/solicitud/curso/registrar/{categoria}', [CursoController::class, 'mostrarFormulario'])->name('curso.registrar');
        Route::get('/curso/obtener-cursos', [CursoController::class, 'obtenerCursosUsuario'])->name('curso.obtener');
        Route::get('/curso/crear/{categoria}', [CursoController::class, 'crear'])->name('curso.crear');
        Route::post('/curso/guardar', [CursoController::class, 'guardar'])->name('curso.guardar');
        Route::delete('/curso/eliminar/{id}', [CursoController::class, 'eliminar'])->name('curso.eliminar');
        Route::post('/solicitud/users/cursos/horas', [CursoController::class, 'guardarHoras'])->name('cursos.horas.guardar');
        Route::delete('users/cursos/horas/{id}', [CursoController::class, 'eliminarHoras'])->name('cursos.horas.eliminar');
        Route::get('/horas-cursos', [CursoController::class, 'mostrarHoras'])->name('horas.cursos');

        // Rutas para tickets
        Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
        Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
        Route::post('/tickets/crear', [TicketController::class, 'crearTicket'])->name('tickets.crear');
        Route::delete('/cursos/{id}', [TicketController::class, 'eliminarCurso'])->name('cursos.eliminar');
        Route::delete('/solicitudes/{id}', [TicketController::class, 'eliminar'])->name('solicitud.eliminar');
        Route::get('/solicitudes', [TicketController::class, 'solicitudes'])->name('solicitudes');
        Route::get('/solicitudes', [TicketController::class, 'solicitudes'])->name('solicitudes.carta');
        Route::get('/view-carta', [TicketController::class, 'viewCarta'])->name('viewCarta');
        Route::get('/tickets/{id}/detalles', [TicketController::class, 'detallesCurso'])->name('tickets.show');
        Route::put('/radicado-salida/{id}', [TicketController::class, 'updateRadicadoSalida'])->name('updateRadicadoSalida');
    });
    // Rutas para usuarios
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UsuariosController::class, 'usuarios'])->name('usuarios');
        Route::put('/{id}', [UsuariosController::class, 'updateUsuario'])->name('usuarios.update');
        Route::delete('/{id}', [UsuariosController::class, 'destroy'])->name('usuarios.destroy');
        Route::post('/usuarios/migrar-estudiantes', [UsuariosController::class, 'migrarEstudiantesAUsuarios'])->name('usuarios.migrarEstudiantes');
    });

    // Rutas para EstudianteController
    Route::get('/principal', [EstudianteController::class, 'mostrarPrincipalForm'])->name('principal.form');
    Route::get('/estudiante', [EstudianteController::class, 'mostrarPrincipalForm'])->name('estudiante');
    Route::get('/programas/{facultadId}', [EstudianteController::class, 'obtenerProgramasPorFacultad'])->name('programas.obtener');
    Route::get('/estudiante/formulario', [EstudianteController::class, 'mostrarFormulario'])->name('estudiante.formulario');
    Route::post('/estudiante/guardar/{id}', [EstudianteController::class, 'guardarEstudiante'])->name('estudiante.guardar');
    Route::get('/estudiantes/upload', [EstudianteController::class, 'showUploadForm'])->name('estudiantes.upload');
    Route::post('/estudiantes/import', [EstudianteController::class, 'import'])->name('estudiantes.import');
    Route::get('/users/upload-csv', [EstudianteController::class, 'showUploadForm'])->name('users.upload_csv');
    Route::put('/solicitud/{id}/actualizar-radicado', [EstudianteController::class, 'actualizarRadicado'])->name('actualizarRadicado');
    Route::get('/usuarios/crear-institucion', [EstudianteController::class, 'createInstitucion'])->name('institucion.create');
    Route::post('/usuarios/crear-institucion', [EstudianteController::class, 'storeInstitucion'])->name('institucion.store');
    Route::get('/institucion/{id}/edit', [EstudianteController::class, 'editInstitucion'])->name('institucion.edit');
    Route::delete('/institucion/{id}', [EstudianteController::class, 'destroyInstitucion'])->name('institucion.destroy');
    Route::put('/estudiantes/{id}/actualizar', [EstudianteController::class, 'actualizar'])->name('estudiante.actualizar');




    //Ruta de EMAIL
    Route::get('/certificado', [MailController::class, 'enviarEmail'])->name('viewCarta');
    Route::post('/email', [MailController::class, 'enviarcarta'])->name('carta');
    Route::post('/emamilrechazo', [MailController::class, 'enviarcartaRechazo'])->name('cartarechazo');
    Route::post('/subir-firma', [MailController::class, 'subirFirma'])->name('subir.firma');
    Route::get('/subir-firma', function () {
        return view('secretaria.cartas.Subir_firma');
    })->name('subir.firma');
});


// Rutas de recuperación de contraseña
// Elimina o comenta las siguientes líneas, ya que Auth::routes() maneja esto
// Route::get('password/email', [ForgotPasswordController::class, 'showLinkRequestFormDos'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');



// Ruta para verificar el inicio de sesión
Route::post('/verifi', [LoginController::class, 'verifi'])->name('verifi');
