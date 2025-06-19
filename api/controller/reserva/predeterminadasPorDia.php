<?php
$frontendOrigin = "http://localhost:3000";

header("Access-Control-Allow-Origin: $frontendOrigin");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 🔧 Logging solo si hay carpeta logs
function logDepuracion($mensaje, $archivo = "clases_predeterminadas_dia.txt")
{
    $logDir = $_SERVER['DOCUMENT_ROOT'] . "/logs";
    if (is_dir($logDir)) {
        $logPath = $logDir . "/" . $archivo;
        $timestamp = date("[Y-m-d H:i:s]");
        @file_put_contents($logPath, "$timestamp $mensaje\n", FILE_APPEND);
    }
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/ReservacionesBD.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/ReservacionDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/LaboratoriosFCABD.php";

try {
    $input = json_decode(file_get_contents("php://input"), true);
    if (!isset($input['fecha'])) {
        throw new Exception("Falta parámetro 'fecha'");
    }

    $fecha = new DateTime($input['fecha']); // ej: 2025-06-19
    $year = (int)$fecha->format("o");
    $week = (int)$fecha->format("W");
    $dia = (int)$fecha->format("N"); // 1 = lunes ... 7 = domingo

    $labId = isset($input['labId']) ? (int)$input['labId'] : null;

    $dao = new ReservacionDAO(ReservacionesBD::getInstance());
    $clases = $dao->consultarClasesPredeterminadasPorSemana($year, $week, $labId, false);

    // 🔍 Filtrar por día exacto
    $filtradas = array_filter($clases, function ($clase) use ($dia) {
        return (int)$clase['hogr_id_dia'] === $dia;
    });

    logDepuracion("📅 Día {$input['fecha']} ($dia) → " . count($filtradas) . " clases filtradas");

    header("Content-Type: application/json");
    echo json_encode([
        "mensaje" => "✅ Clases filtradas para {$input['fecha']}",
        "clases" => array_values($filtradas)
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
