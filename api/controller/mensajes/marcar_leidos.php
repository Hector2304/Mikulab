<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioSesionDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/ReservacionesBD.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/mensajes/dao/MensajeDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";

// CORS
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Leer sesión
$usuarioSesionDTO = RestCommons::getFromSession(RestCommons::USUARIO_SESION_DTO);
$idUsuario = $usuarioSesionDTO->getObjetoUsuario()->getProfIdProfesor();

$mensajeDAO = new MensajeDAO(ReservacionesBD::getInstance());
$mensajeDAO->marcarTodosComoLeidos($idUsuario); // 👈 Nuevo método

header('Content-Type: application/json');
echo json_encode(["ok" => true]);
