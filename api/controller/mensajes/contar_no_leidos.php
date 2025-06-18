<?php
// CORS
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioSesionDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/ReservacionesBD.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/mensajes/dao/MensajeDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";

// Validar que la sesión existe (una sola vez basta bb 😅)
if (!isset($_SESSION[RestCommons::USUARIO_SESION_DTO])) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "Sesión no iniciada"]);
    exit();
}

$usuarioSesionDTO = RestCommons::getFromSession(RestCommons::USUARIO_SESION_DTO);

if (!$usuarioSesionDTO) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "Sesión no iniciada"]);
    exit();
}

// Obtener ID del usuario (profesor)
$idUsuario = $usuarioSesionDTO->getObjetoUsuario()->getProfIdProfesor();

// Contar mensajes no leídos
$mensajeDAO = new MensajeDAO(ReservacionesBD::getInstance());
$total = $mensajeDAO->contarNoLeidos($idUsuario);

header('Content-Type: application/json');
echo json_encode(["total" => $total], JSON_UNESCAPED_UNICODE);
