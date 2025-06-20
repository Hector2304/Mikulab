<?php
// CORS headers
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/ReservacionesBD.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['id_mensaje'])) {
        http_response_code(400);
        echo json_encode(["error" => "ID de mensaje no recibido"]);
        exit();
    }

    $idMensaje = (int)$input['id_mensaje'];
    $conn = ReservacionesBD::getInstance()->getConexion();

    $stmt = $conn->prepare("UPDATE mensaje SET leido = true WHERE id_mensaje = ?");
    $stmt->bindParam(1, $idMensaje, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo marcar como leído"]);
    }
}
