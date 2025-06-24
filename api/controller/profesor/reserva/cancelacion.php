<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/ReservacionDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/ReservacionDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioSesionDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dto/ProfesorDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ProfesorReservaAlta extends BaseHandler
{
    public function handle()
    {
        try {
            $jsonData = RestCommons::readJSON();
            $usuarioSesionDTO = RestCommons::getFromSession(RestCommons::USUARIO_SESION_DTO);
            $reservacionDAO = new ReservacionDAO(ReservacionesBD::getInstance());

            if (!isset($jsonData->id)) {
                http_response_code(400);
                echo json_encode(["success" => false]);
                return;
            }

            $idReservacion = intval($jsonData->id);
            $tipoUsuario = $usuarioSesionDTO->getTipoUsuario();

            ob_start(); // Captura "__POPUP__"
            if ($tipoUsuario === TipoUsuarioEnum::SUPERUSUARIO) {
                $success = $reservacionDAO->cancelar($idReservacion, null);
            } else {
                $profesorDTO = $usuarioSesionDTO->getObjetoUsuario();
                $idProfesor = $profesorDTO->getProfIdProfesor();
                $success = $reservacionDAO->cancelar($idReservacion, $idProfesor);
            }
            $salida = ob_get_clean();

            header("Content-Type: application/json");
            if ($success) {
                echo json_encode([
                    "success" => true,
                    "popup" => strpos($salida, "__POPUP__") !== false
                ]);
            } else {
                http_response_code(400);
                echo json_encode(["success" => false]);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            http_response_code(500);
            echo json_encode(["success" => false, "error" => "Error interno"]);
        }
    }
}


// Permitimos acceso tanto a PROFESOR como SUPERUSUARIO
(new RESTEndpointClient(['PATCH'], [TipoUsuarioEnum::PROFESOR, TipoUsuarioEnum::SUPERUSUARIO]))
    ->execute(new ProfesorReservaAlta);

