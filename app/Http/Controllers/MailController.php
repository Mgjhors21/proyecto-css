<?php


namespace App\Http\Controllers;

use PhpOffice\PhpWord\PhpWord;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Mail\Notification;
use App\Models\Estudiante;
use App\Models\Ticket; // Asegúrate de importar el modelo correcto
use App\Models\Historial;
use Illuminate\Support\Facades\Log;


class MailController extends Controller
{
    public function enviarCarta(Request $request)
    {
        try {
            // Validación de existencia del ticket
            $request->validate(['ticket_id' => 'required|exists:tickets,id']);

            // Obtener el ticket con sus relaciones necesarias
            $ticket = Ticket::with([
                'estudiante',
                'ticketCursos'
            ])->find($request->ticket_id);

            // Guardar el historial solo si el ticket no tiene una entrada existente en el historial
            if (!$ticket->historial()->exists()) {
                $this->guardarEnHistorial($ticket);
            }

            // Crear archivo Word con información detallada
            $filePath = $this->crearDocumentoWordConInformacionCompleta($ticket);

            if (!$filePath || !file_exists($filePath)) {
                return redirect()->back()->with('error', 'El archivo no se ha creado correctamente.');
            }

            // Enviar el correo con el archivo adjunto
            Mail::to('cecd.soporte@gmail.com')->send(new Notification($filePath));

            // Eliminar el archivo temporal después de enviarlo
            unlink($filePath);

            Log::info("Ticket ID a eliminar: " . $ticket->id);

            // Gestionar historial y eliminación de ticket
            if ($ticket->historial) {
                Log::info("Historial encontrado, desasociando.");
                $ticket->historial()->update(['ticket_id' => null]);
            } else {
                Log::info("No se encontró historial asociado.");
            }

            $ticket->forceDelete();
            Log::info("Ticket eliminado.");

            return redirect()->route('solicitudes.carta')->with('success', 'La carta ha sido enviada con éxito.');
        } catch (\Exception $e) {
            Log::error("Error en enviarCarta: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al enviar la carta: ' . $e->getMessage());
        }
    }

    private function guardarEnHistorial($ticket)
    {
        // Crear una nueva entrada en el historial solo si no existe una
        $ticketHistorial = new Historial();
        $ticketHistorial->fill([
            'ticket_id' => $ticket->id,
            'numero_radicado_salida' => $ticket->numero_radicado_salida,
            'nombre' => $ticket->estudiante->name ?? 'Nombre no disponible',
            'apellido' => $ticket->estudiante->apellido ?? 'Apellido no disponible',
            'cod_alumno' => $ticket->estudiante->cod_alumno,
            'programa_academico' => $ticket->estudiante->programa_academico ?? 'programa no disponible',
            'numero_radicado' => $ticket->numero_radicado,
            'fecha_revision' => $ticket->fecha_revision ?? now(),
            'cursos' => json_encode($ticket->ticketCursos->where('estado_curso', 'aceptado')->pluck('curso_nombre')),
            'total_horas' => $ticket->ticketCursos->where('estado_curso', 'aceptado')->sum('curso_horas'),
            'estado' => 'enviado'
        ]);
        $ticketHistorial->save();
    }

    private function crearDocumentoWordConInformacionCompleta($ticket)
    {
        // Lógica para crear el documento Word
        $phpWord = new PhpWord();

        // Configuración de márgenes
        $sectionStyle = [
            'marginTop' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(1),  // 2.5 cm superior
            'marginBottom' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(1),  // 2.5 cm inferior
            'marginLeft' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(1),  // 2.5 cm izquierdo
            'marginRight' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(1)  // 2.5 cm derecho
        ];
        $section = $phpWord->addSection($sectionStyle);

        // Definir estilos
        $phpWord->addFontStyle('headerStyle', ['name' => 'Arial', 'size' => 14, 'bold' => true, 'color' => '000000']);  // Títulos en negrita
        $phpWord->addFontStyle('normalStyle', ['name' => 'Arial', 'size' => 12, 'color' => '000000']);  // Texto normal
        $phpWord->addFontStyle('boldStyle', ['name' => 'Arial', 'size' => 12, 'bold' => true, 'color' => '000000']);  // Negrita para encabezados y subtítulos
        $phpWord->addParagraphStyle('centerAlign', ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);  // Alineación centrada
        $phpWord->addParagraphStyle('rightAlign', ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);  // Alineación a la derecha
        $phpWord->addParagraphStyle('leftAlign', ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT]);  // Alineación a la izquierda
        $phpWord->addParagraphStyle('bothAlign', ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH]);  // Alineación justificada

        // Agregar el logo
        $header = $section->addHeader();
        $logoPath = public_path('images/logos autonoma_1.png');
        if (file_exists($logoPath)) {
            $header->addImage(
                $logoPath,
                [
                    'width' => 100,
                    'height' => 100,
                    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
                ]
            );
        } else {
            $header->addText('Logo no disponible', 'boldStyle', 'centerAlign');
        }

        // Encabezado de documento
        $section->addText('RC', 'boldStyle');
        $section->addText($ticket->numero_radicado_salida, 'normalStyle');
        $section->addTextBreak();

        // Información del estudiante
        $section->addText('Estudiante:', 'boldStyle');
        $section->addText($ticket->estudiante->name . ' ' . $ticket->estudiante->apellido, 'normalStyle');
        $section->addText('Codigo Estudiante:', 'boldStyle');
        $section->addText($ticket->estudiante->cod_alumno, 'normalStyle');
        $section->addText('Programa:', 'boldStyle');
        $section->addText($ticket->estudiante->programa_academico, 'normalStyle');
        $section->addText('Asunto: Radicado No. ' . $ticket->numero_radicado . ' – Validación Horas Seminario y Horas Cursos de Extensión.', 'normalStyle');

        $section->addTextBreak();
        $section->addText('Cordial Saludo,', 'normalStyle');

        // Contenido principal
        $section->addText('En respuesta a la comunicación radicada el día ' . $ticket->fecha_revision . ', con el número de referencia, en donde solicita la validación de las horas de seminario y cursos de extensión para optar su título profesional, le informamos que su solicitud ha sido aprobada.', 'normalStyle');
        $section->addTextBreak();

        // Tabla de Seminarios de Actualización
        if ($ticket->ticketCursos->where('estado_curso', 'aceptado')->whereNotNull('curso_seminario_id')->count() > 0) {
            $section->addText('1) Validación 96 Horas Seminario de Actualización:', 'boldStyle');
            $table1 = $section->addTable('tableStyle');
            $table1->addRow();
            $table1->addCell(6000, ['bgColor' => '1F497D'])->addText('Nombre del Seminario', 'boldStyle', ['alignment' => 'center']);
            $table1->addCell(3000, ['bgColor' => '1F497D'])->addText('Número de Horas', 'boldStyle', ['alignment' => 'center']);

            $totalHorasSeminario = 0;
            foreach ($ticket->ticketCursos as $curso) {
                if (strtolower($curso->estado_curso) === 'aceptado' && !is_null($curso->curso_seminario_id)) {
                    $table1->addRow();
                    $table1->addCell(6000)->addText($curso->curso_nombre, 'normalStyle');
                    $table1->addCell(3000)->addText($curso->curso_horas, 'normalStyle');
                    $totalHorasSeminario += $curso->curso_horas;
                }
            }

            $table1->addRow();
            $table1->addCell(6000)->addText('Total:', 'boldStyle', 'rightAlign');
            $table1->addCell(3000)->addText($totalHorasSeminario, 'boldStyle');
            $section->addTextBreak();
        }

        // Tabla de Cursos de Extensión
        if ($ticket->ticketCursos->where('estado_curso', 'aceptado')->whereNotNull('curso_extension_id')->count() > 0) {
            $section->addText('2) Validación 40 Horas Cursos de Extensión:', 'boldStyle');
            $table2 = $section->addTable('tableStyle');
            $table2->addRow();
            $table2->addCell(6000, ['bgColor' => '1F497D'])->addText('Nombre del Curso', 'boldStyle', ['alignment' => 'center']);
            $table2->addCell(3000, ['bgColor' => '1F497D'])->addText('Número de Horas', 'boldStyle', ['alignment' => 'center']);

            $totalHorasExtension = 0;
            foreach ($ticket->ticketCursos as $curso) {
                if (strtolower($curso->estado_curso) === 'aceptado' && !is_null($curso->curso_extension_id)) {
                    $table2->addRow();
                    $table2->addCell(6000)->addText($curso->curso_nombre, 'normalStyle');
                    $table2->addCell(3000)->addText($curso->curso_horas, 'normalStyle');
                    $totalHorasExtension += $curso->curso_horas;
                }
            }

            $table2->addRow();
            $table2->addCell(6000)->addText('Total:', 'boldStyle', 'rightAlign');
            $table2->addCell(3000)->addText($totalHorasExtension, 'boldStyle');
            $section->addTextBreak();
        }

        // Mensaje final
        $section->addText(
            'En cualquier caso, la decanatura está a su disposición para orientar o aclarar cualquier duda adicional que le pueda surgir.',
            'normalStyle',
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH]
        );

        // Espaciado entre texto y firma
        $section->addTextBreak();

        // Firma dinámica o línea
        $firmaPath = public_path('storage/firmas/firma_decano_1.png'); // Ajustar la ruta si es diferente
        if (file_exists($firmaPath)) {
            $section->addImage(
                $firmaPath,
                [
                    'width' => 150,
                    'height' => 50,
                    'alignment' => 'leftAlign'
                ]
            );
        } else {
            // Línea de firma si no existe la imagen
            $section->addText('____________________________', 'normalStyle', 'leftAlign');
        }

        // Texto debajo de la firma
        $section->addText('Decano Facultad de Ingeniería y Ciencias Naturales', 'normalStyle', ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);

        // Pie de página
        $footer = $section->addFooter();
        $footerStyle = ['size' => 11];
        $centeredStyle = ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER];

        $footer->addText('Lic. De Funcionamiento: 12321 de 1979. Resolución MEN Nº. 677 de 2003. Código SNIES: 2849', $footerStyle, $centeredStyle);
        $footer->addText('Sede principal - Calle 5 N° 3 - 85 Barrio Centro.', $footerStyle, $centeredStyle);
        $footer->addText('PBX: 602 8222295 - WhatsApp 314 639 54 95 - 320 575 04 64 A.A. 043 Popayán - Cauca - Colombia.', $footerStyle, $centeredStyle);
        $footer->addText('www.uniautonoma.edu.co - Email: recepción@uniautonoma.edu.co', $footerStyle, $centeredStyle);

        // Guardar el archivo
        $fileName = 'carta_respuesta_' . $ticket->id . '.docx';
        $filePath = storage_path($fileName);
        $phpWord->save($filePath, 'Word2007');

        // Verificar si el archivo fue creado
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'El archivo no se ha creado correctamente.');
        }

        return $filePath;
    }

    public function enviarcartaRechazo(Request $request)
    {
        try {
            // Validación de existencia del ticket
            $request->validate(['ticket_id' => 'required|exists:tickets,id']);

            // Obtener el ticket con sus relaciones necesarias
            $ticket = Ticket::with([
                'estudiante',
                'ticketCursos'
            ])->find($request->ticket_id);

            // Guardar el historial solo si el ticket no tiene una entrada existente en el historial
            if (!$ticket->historial()->exists()) {
                $this->guardarEnHistorial($ticket);
            }

            // Crear archivo Word de rechazo
            $filePath = $this->crearDocumentoWordRechazo($ticket);

            if (!$filePath || !file_exists($filePath)) {
                return redirect()->back()->with('error', 'El archivo no se ha creado correctamente.');
            }

            // Enviar el correo con el archivo adjunto
            Mail::to('cecd.soporte@gmail.com')->send(new Notification($filePath));

            // Eliminar el archivo temporal después de enviarlo
            unlink($filePath);

            Log::info("Ticket ID a eliminar: " . $ticket->id);

            // Verificar si el historial existe
            if ($ticket->historial) {
                Log::info("Historial encontrado, desasociando.");
                $ticket->historial()->update(['ticket_id' => null]);
            } else {
                Log::info("No se encontró historial asociado.");
            }

            // Intentar eliminar el ticket
            $ticket->forceDelete();
            Log::info("Ticket eliminado."); // Usamos forceDelete() para eliminar sin restricciones

            return redirect()->route('solicitudes.carta')->with('success', 'La carta de rechazo ha sido enviada con éxito.');
        } catch (\Exception $e) {
            // Capturar cualquier error y mostrarlo
            Log::error("Error en enviarcartaRechazo: " . $e->getMessage());
            return redirect()->back()->with('error', 'Ocurrió un error al enviar la carta de rechazo.');
        }
    }

    private function crearDocumentoWordRechazo($ticket)
    {
        // Lógica para crear el documento Word de rechazo
        $phpWord = new PhpWord();
        // Configuración de márgenes
        $sectionStyle = [
            'marginTop' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(1),
            'marginBottom' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(1),
            'marginLeft' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(1),
            'marginRight' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(1)
        ];
        $section = $phpWord->addSection($sectionStyle);

        // Definir estilos
        $phpWord->addFontStyle('headerStyle', ['name' => 'Arial', 'size' => 16, 'bold' => true, 'color' => '000000']);
        $phpWord->addFontStyle('normalStyle', ['name' => 'Arial', 'size' => 12, 'color' => '000000']);
        $phpWord->addFontStyle('boldStyle', ['name' => 'Arial', 'size' => 12, 'bold' => true, 'color' => '000000']);
        $phpWord->addParagraphStyle('rightAlign', ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);  // Alineación a la derecha
        $phpWord->addParagraphStyle('leftAlign', ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::LEFT]);  // Alineación a la izquierda
        $phpWord->addParagraphStyle('centerAlign', ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);  // Alineación centrada
        $phpWord->addTableStyle('tableStyle', [
            'borderSize' => 6,
            'borderColor' => '999999',
            'cellMargin' => 50,
            'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER
        ]);

        // Agregar el logo
        $header = $section->addHeader();
        $logoPath = public_path('images/logos autonoma_1.png');

        if (file_exists($logoPath)) {
            $header->addImage(
                $logoPath,
                [
                    'width' => 100,
                    'height' => 100,
                    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER
                ]
            );
        } else {
            $header->addText('Logo no disponible', 'boldStyle', 'centerAlign');
        }

        $section->addTextBreak();

        // Información del estudiante
        $section->addText('Estudiante:', 'boldStyle');
        $section->addText($ticket->estudiante->name . ' ' . $ticket->estudiante->apellido, 'normalStyle');
        $section->addText('Codigo Estudiante:', 'boldStyle');
        $section->addText($ticket->estudiante->cod_alumno, 'normalStyle');
        $section->addText('Programa:', 'boldStyle');
        $section->addText($ticket->estudiante->programa_academico, 'normalStyle');
        $section->addText('Asunto: Radicado No. ' . $ticket->numero_radicado . ' – Validación Horas Seminario y Horas Cursos de Extensión.', 'normalStyle');

        $section->addTextBreak();
        $section->addText('Cordial Saludo,', 'normalStyle');

        // Contenido principal
        $section->addText('En respuesta a la comunicación radicada el día ' . $ticket->fecha_revision . ', le informamos que, lamentablemente, su solicitud de validación de horas de seminario y cursos de extensión ha sido rechazada.', 'normalStyle');
        $section->addTextBreak();

        // Tabla de Seminarios de Actualización (Si aplica)
        if ($ticket->ticketCursos->where('estado_curso', 'rechazado')->whereNotNull('curso_seminario_id')->count() > 0) {
            $section->addText('1) Rechazo de Seminarios de Actualización:', 'boldStyle');
            $table1 = $section->addTable('tableStyle');
            $table1->addRow();
            $table1->addCell(6000, ['bgColor' => '1F497D'])->addText('Nombre del Seminario', 'boldStyle', ['alignment' => 'center']);
            $table1->addCell(3000, ['bgColor' => '1F497D'])->addText('Número de Horas', 'boldStyle', ['alignment' => 'center']);

            foreach ($ticket->ticketCursos as $curso) {
                if (strtolower($curso->estado_curso) === 'rechazado' && !is_null($curso->curso_seminario_id)) {
                    $table1->addRow();
                    $table1->addCell(6000)->addText($curso->curso_nombre, 'normalStyle');
                    $table1->addCell(3000)->addText($curso->curso_horas, 'normalStyle');
                }
            }

            $section->addTextBreak();
        }

        // Tabla de Cursos de Extensión (Si aplica)
        if ($ticket->ticketCursos->where('estado_curso', 'rechazado')->whereNotNull('curso_extension_id')->count() > 0) {
            $section->addText('2) Rechazo de Cursos de Extensión:', 'boldStyle');
            $table2 = $section->addTable('tableStyle');
            $table2->addRow();
            $table2->addCell(6000, ['bgColor' => 'FF0000'])->addText('Nombre del Curso', 'boldStyle', ['alignment' => 'center']);
            $table2->addCell(3000, ['bgColor' => 'FF0000'])->addText('Número de Horas', 'boldStyle', ['alignment' => 'center']);

            foreach ($ticket->ticketCursos as $curso) {
                if (strtolower($curso->estado_curso) === 'rechazado' && !is_null($curso->curso_extension_id)) {
                    $table2->addRow();
                    $table2->addCell(6000)->addText($curso->curso_nombre, 'normalStyle');
                    $table2->addCell(3000)->addText($curso->curso_horas, 'normalStyle');
                }
            }

            $section->addTextBreak();
        }

        // Mensaje final
        $section->addText(
            'En cualquier caso, la decanatura está a su disposición para orientar o aclarar cualquier duda adicional que le pueda surgir.',
            'normalStyle',
            ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH]
        );

        // Espaciado entre texto y firma
        $section->addTextBreak();


        // Firma dinámica o línea
        $firmaPath = public_path('storage/firmas/firma_decano_1.png'); // Ajustar la ruta si es diferente
        if (file_exists($firmaPath)) {
            $section->addImage(
                $firmaPath,
                [
                    'width' => 150,
                    'height' => 50,
                    'alignment' => 'leftAlign'
                ]
            );
        } else {
            // Línea de firma si no existe la imagen
            $section->addText('____________________________', 'normalStyle', 'leftAlign');
        }

        // Texto debajo de la firma
        $section->addText('Decano Facultad de Ingeniería y Ciencias Naturales', 'normalStyle', ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        // Pie de página
        $footer = $section->addFooter();
        $footerStyle = ['size' => 11];
        $centeredStyle = ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER];

        $footer->addText('Lic. De Funcionamiento: 12321 de 1979. Resolución MEN Nº. 677 de 2003. Código SNIES 12345.', $footerStyle, $centeredStyle);
        $footer->addText('Carrera No. 16 - Ciudad Universitaria.', $footerStyle, $centeredStyle);
        $footer->addText('Tel. (2) 32132 – Bogotá, Colombia.', $footerStyle, $centeredStyle);

        // Guardar el archivo
        $filename = 'Carta_Rechazo_' . $ticket->estudiante->cod_alumno . '.docx';
        $filePath = public_path('storage/' . $filename);

        $phpWord->save($filePath, 'Word2007');

        return $filePath;
    }

    public function enviarEmail(Request $request)
    {
        // Obtener el ticket según el ID pasado en el formulario
        $ticket = Ticket::findOrFail($request->ticket_id);

        // Recuperar la firma desde la sesión
        $firmaPath = session('firmaPath', null);


        // Verificar el estado y redirigir a la carta correspondiente
        if ($ticket->estado_ticket === 'aprobado') {
            return view('secretaria.cartas.aprobada', compact('ticket', 'firmaPath'));
        } elseif ($ticket->estado_ticket === 'rechazado') {
            return view('secretaria.cartas.rechazada', compact('ticket', 'firmaPath'));
        } else {
            return redirect()->back()->with('error', 'Estado de la solicitud no válido');
        }
    }

    public function subirFirma(Request $request)
    {
        Log::info('Iniciando proceso de carga de firma.');

        try {
            // Validar que el archivo sea una imagen permitida (JPEG o PNG)
            $request->validate([
                'firma' => 'required|image|mimes:jpeg,png|max:2048',
            ]);
            Log::info('Validación completada.');
        } catch (\Exception $e) {
            Log::error('Error en la validación:', ['mensaje' => $e->getMessage()]);
            return redirect()->back()->with('error', 'El archivo no cumple con los requisitos. Asegúrate de subir una imagen válida.');
        }

        if ($request->hasFile('firma')) {
            $file = $request->file('firma');

            // Loguear información del archivo recibido
            Log::info('Archivo recibido:', [
                'nombre' => $file->getClientOriginalName(),
                'tamaño' => $file->getSize(),
                'mime' => $file->getMimeType(),
            ]);

            // Subir la imagen y asegurar extensión correcta
            $extension = $file->getClientOriginalExtension(); // Obtener extensión del archivo
            $directory = public_path('storage/firmas'); // Ruta al directorio donde se almacenan las firmas

            // Asegurarse de que el directorio exista
            if (!file_exists($directory)) {
                mkdir($directory, 0775, true); // Crear el directorio si no existe
            }

            // Buscar los archivos existentes en el directorio que coincidan con el patrón "firma_decano"
            $files = glob($directory . '/firma_decano_*.{jpeg,png}', GLOB_BRACE);

            // Determinar el número para la nueva firma
            $nextNumber = count($files) + 1;

            // Generar el nuevo nombre del archivo
            $fileName = "firma_decano_{$nextNumber}." . $extension;

            // Guardar el archivo en el directorio público
            $path = $file->storeAs('firmas', $fileName, 'public');

            // Loguear información del archivo almacenado
            Log::info('Archivo almacenado en:', ['path' => $path]);

            // Guardar la ruta en la sesión
            session(['firmaPath' => $path]);

            return redirect()->back()->with('success', 'Firma digital cargada correctamente.');
        } else {
            Log::error('No se recibió ningún archivo en la solicitud.');
            return redirect()->back()->with('error', 'No se pudo cargar la firma. Por favor, intenta nuevamente.');
        }
    }
}
