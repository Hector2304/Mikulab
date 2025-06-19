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

// 🔧 Función de logging segura (solo escribe si la carpeta logs existe)
function logDepuracion($mensaje, $archivo = "clases_predeterminadas_debug.txt")
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
    $labId = isset($input['labId']) ? (int)$input['labId'] : null;

    $conn = LaboratoriosFCABD::getInstance()->getConexion();
    $stmt = $conn->prepare("SELECT * FROM periodo WHERE peri_estatus = 'A'");
    $stmt->execute();
    $periodos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $dao = new ReservacionDAO(ReservacionesBD::getInstance());

    $resumen = [];
    $total = 0;
    $todasLasClases = [];

    foreach ($periodos as $p) {
        $inicio = new DateTime($p['peri_fec_ini']);
        $fin = new DateTime($p['peri_fec_fin']);

        $inicio->modify('monday this week');
        $fin->modify('monday this week');

        while ($inicio <= $fin) {
            $year = (int)$inicio->format("o");
            $week = (int)$inicio->format("W");

            // ✅ Filtra por laboratorio si viene en el request
            $clases = $dao->consultarClasesPredeterminadasPorSemana($year, $week, $labId, false);

            // 🪵 Registro de depuración
            logDepuracion("🔢 Semana $week de $year: " . count($clases) . " clases");

            $resumen[] = "Semana $week de $year: " . count($clases);
            $total += count($clases);
            $todasLasClases = array_merge($todasLasClases, $clases);

            $inicio->modify('+1 week');
        }
    }

    header("Content-Type: application/json");
    echo json_encode([
        "mensaje" => "✅ Se detectaron $total clases en total",
        "resumen" => $resumen,
        "clases" => $todasLasClases
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
