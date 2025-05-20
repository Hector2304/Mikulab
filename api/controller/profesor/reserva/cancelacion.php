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
                RestCommons::respondWithStatus(400);
                return;
            }

            $idReservacion = intval($jsonData->id);
            $tipoUsuario = $usuarioSesionDTO->getTipoUsuario();

            // Si es superusuario, no se valida ID de profesor
            if ($tipoUsuario === TipoUsuarioEnum::SUPERUSUARIO) {
                $success = $reservacionDAO->cancelar($idReservacion, null);
            } else {
                $profesorDTO = $usuarioSesionDTO->getObjetoUsuario();
                $idProfesor = $profesorDTO->getProfIdProfesor();
                $success = $reservacionDAO->cancelar($idReservacion, $idProfesor);
            }

            if ($success) {
                RestCommons::respondWithStatus(204);
            } else {
                RestCommons::respondWithStatus(400);
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            http_response_code(500);
        }
    }
}

// Permitimos acceso tanto a PROFESOR como SUPERUSUARIO
(new RESTEndpointClient(['PATCH'], [TipoUsuarioEnum::PROFESOR, TipoUsuarioEnum::SUPERUSUARIO]))
    ->execute(new ProfesorReservaAlta);
