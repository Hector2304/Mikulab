<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioSesionDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/ReservacionesBD.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/mensajes/dao/MensajeDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";

$usuarioSesionDTO = RestCommons::getFromSession(RestCommons::USUARIO_SESION_DTO);
$idUsuario = $usuarioSesionDTO->getIdUsuario();

$mensajeDAO = new MensajeDAO(ReservacionesBD::getInstance());
$mensajes = $mensajeDAO->obtenerPorUsuario($idUsuario);

header('Content-Type: application/json');
echo json_encode($mensajes);
