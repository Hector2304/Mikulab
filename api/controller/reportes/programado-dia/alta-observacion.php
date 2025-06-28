<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/ReporteProgramadoDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioSesionDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ReportesProgramadoDiaAltaObservacion extends BaseHandler
{
    public function handle()
    {
        try {
            $jsonData = RestCommons::readJSON();
            $jsonData->obs = trim($jsonData->obs);

            $errors = [];

            if (empty($jsonData->obs)) {
                $errors[] = "OBS_EMPTY";
            }
            if (!is_numeric($jsonData->detalleId)) {
                $errors[] = "DETALLE_ID_EMPTY";
            }

            if (count($errors) > 0) {
                RestCommons::respondWithStatus(400, [
                    "errors" => $errors
                ]);
            }

            $usuarioSesionDTO = RestCommons::getFromSession(RestCommons::USUARIO_SESION_DTO);
            $usuarioDTO = $usuarioSesionDTO->getObjetoUsuario();

            if (!$usuarioDTO || !$usuarioDTO->getUsuaIdUsuario()) {
                RestCommons::respondWithStatus(401, ["error" => "UNAUTHORIZED"]);
            }

            $obsDTO = new ReporteProgramadoObservacionDTO;
            $obsDTO->setRepoIdDetalle($jsonData->detalleId);
            $obsDTO->setRepoIdUsuario($usuarioDTO->getUsuaIdUsuario());
            $obsDTO->setRepoObservacion($jsonData->obs);

            $reprDAO = new ReporteProgramadoDAO(ReservacionesBD::getInstance());

            $obsDTO->setRepoIdObservacion($reprDAO->altaObservacion($obsDTO));
            $obsDTO->setRepoFecha(date("Y-m-d H:i:s"));
            $obsDTO->usuario = [
                "usuaIdUsuario" => $usuarioDTO->getUsuaIdUsuario(),
                "nombreCompleto" => $usuarioDTO->getNombreCompleto()
            ];

            RestCommons::respondWithStatus(200, array(
                "id" => $obsDTO->getRepoIdObservacion(),
                "repoIdDetalle" => $obsDTO->getRepoIdDetalle(),
                "repoIdUsuario" => $obsDTO->getRepoIdUsuario(),
                "repoObservacion" => $obsDTO->getRepoObservacion(),
                "repoFechaHora" => $obsDTO->getRepoFecha(),
                "usuario" => $obsDTO->usuario
            ));

        } catch (Exception $e) {
            error_log($e->getMessage());
            RestCommons::respondWithStatus(500);
        }
    }
}

(new RESTEndpointClient(['POST'], [
    TipoUsuarioEnum::SUPERUSUARIO,
    TipoUsuarioEnum::TECNICO,
    TipoUsuarioEnum::SERVIDOR_SOCIAL,
    TipoUsuarioEnum::VIGILANTE
]))->execute(new ReportesProgramadoDiaAltaObservacion);
