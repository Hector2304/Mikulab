<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/fpdf/fpdf.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/model/db/ReservacionesBD.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/model/db/LaboratoriosFCABD.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/model/reservaciones/dao/impl/ReservacionDAO.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/model/laboratorios_fca/dao/impl/GruposDAO.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/model/laboratorios_fca/dao/impl/LaboratoriosDAO.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/model/laboratorios_fca/dao/impl/ProfesorDAO.php';


if (!isset($_POST['id']) || empty($_POST['id'])) {
    die("ID no recibido.");
}

$id = $_POST['id'];

$reservacionDAO = new ReservacionDAO(ReservacionesBD::getInstance());
$gruposDAO = new GruposDAO(LaboratoriosFCABD::getInstance());
$laboratoriosDAO = new LaboratoriosDAO(LaboratoriosFCABD::getInstance());
$profesorDAO = new ProfesorDAO(LaboratoriosFCABD::getInstance());


$reservacion = $reservacionDAO->porId($id);
if (!$reservacion) {
    die("Reservación no encontrada.");
}

$tipoGrupo = $reservacion->getReseTipoGrupo();
$idGrupo = $reservacion->getReseIdGrupo();

$grupo = [];
if ($tipoGrupo === 'L') {
    $grupo = $gruposDAO->gruposPorIdMap([$idGrupo])[$idGrupo] ?? [];
} elseif ($tipoGrupo === 'P') {
    $grupo = $gruposDAO->grupos_pPorIdMap([$idGrupo])[$idGrupo] ?? [];
} elseif ($tipoGrupo === 'T') {
    $grupo = $gruposDAO->grupos_otPorIdMap([$idGrupo])[$idGrupo] ?? [];
}
if (empty($grupo)) {
    die("Datos del grupo no encontrados.");
}

$idLab = $reservacion->getReseIdLaboratorio();
$laboratorio = $laboratoriosDAO->getLaboratoriosMapPorId([$idLab])[$idLab] ?? [];
if (empty($laboratorio)) {
    die("Datos del laboratorio no encontrados.");
}

$idProfesor = $grupo['grupIdProfesor'] ?? $grupo['gruoIdProfesor'] ?? $grupo['grotIdProfesor'] ?? null;

$profesorData = null;
if ($idProfesor) {
    $mapaProfesores = $profesorDAO->profesoresMap([$idProfesor]);
    $profesorData = $mapaProfesores[$idProfesor] ?? null;
}



// Función para título de sección
function tituloSeccion($titulo, $pdf) {
    $pdf->SetFillColor(200, 220, 255);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, utf8_decode(" $titulo"), 0, 1, 'L', true);
    $pdf->Ln(2);
}

// Función para fila
function fila($label, $valor, $pdf) {
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(55, 8, utf8_decode($label), 0, 0);
    $pdf->SetFont('Arial', '', 11);
    $pdf->Cell(0, 8, utf8_decode($valor), 0, 1);
}

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetMargins(20, 20, 20);

// Logo (opcional)
// $pdf->Image($_SERVER['DOCUMENT_ROOT'] . '/ruta/logo.png', 160, 10, 30);

$pdf->SetFillColor(230, 230, 250);
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 12, utf8_decode('Comprobante de Reservación de Laboratorio'), 0, 1, 'C', true);
$pdf->Ln(10);


if ($profesorData) {
    tituloSeccion('Datos del Profesor', $pdf);
    fila('Nombre:', $profesorData['nombre'] ?? 'Desconocido', $pdf);
    $pdf->Ln(5);
}






// Sección: Datos del Grupo
tituloSeccion('Datos del Grupo / Asignatura', $pdf);
fila('Grupo:', $grupo['grupClave'] ?? $grupo['gruoClave'] ?? $grupo['grotClave'] ?? '', $pdf);
fila('Asignatura:', $grupo['asigNombre'] ?? $grupo['asigNombreP'] ?? $grupo['mootNombre'] ?? '', $pdf);
fila('Clave Asignatura:', $grupo['asigIdAsignatura'] ?? $grupo['asigIdAsignaturaP'] ?? $grupo['mootClave'] ?? '', $pdf);
fila('Semestre:', $grupo['grupSemestre'] ?? '', $pdf);
fila('Periodo:', $grupo['grupIdPeriodo'] ?? $grupo['gruoIdPeriodo'] ?? '', $pdf);
fila('Carrera / Coordinación:', $grupo['carrNombre'] ?? $grupo['coorNombre'] ?? '', $pdf);

// Sección: Laboratorio
$pdf->Ln(5);
tituloSeccion('Datos del Laboratorio', $pdf);
fila('Laboratorio:', $laboratorio['saloClave'] ?? '', $pdf);
fila('Ubicación:', $laboratorio['saloUbicacion'] ?? '', $pdf);
fila('Cupo:', $laboratorio['saloCupo'] ?? '', $pdf);

// Sección: Reservación
$pdf->Ln(5);
tituloSeccion('Detalles de la Reservación', $pdf);
$fecha = date('d M Y', strtotime($reservacion->getReseFecha()));
function formatearHora($horaEntera) {
    // Si es 700 → 07:00, si es 1300 → 13:00
    $hora = floor($horaEntera / 100);
    $minuto = $horaEntera % 100;
    return sprintf('%02d:%02d', $hora, $minuto);
}

$horaInicio = formatearHora($reservacion->getHoraIni());
$horaFin = formatearHora($reservacion->getHoraFin());
$horario = "$horaInicio - $horaFin";
$horario = "$horaInicio - $horaFin";

fila('Fecha:', $fecha, $pdf);
fila('Horario:', $horario, $pdf);
fila('Estatus:', $reservacion->getReseStatus() === 'A' ? 'Activa' : 'Cancelada', $pdf);

// Pie de página
$pdf->Ln(12);
$pdf->SetFont('Arial', 'I', 10);
$pdf->MultiCell(0, 6, utf8_decode("Este documento fue generado automáticamente por el sistema de reservaciones del Centro de Informática.\nPara más información, comuníquese con el administrador del sistema o escriba a no_se@gmail"), 0, 'C');

$pdf->Output('I', "comprobante_reservacion_$id.pdf");