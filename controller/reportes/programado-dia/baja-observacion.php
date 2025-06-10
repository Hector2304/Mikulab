<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/ReporteProgramadoDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioSesionDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ReportesProgramadoDiaBajaObservacion extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();

			$errors = array();

			if (!is_numeric($jsonData->id)) {
				$errors[] = "DETALLE_ID_EMPTY";
			}

			if (count($errors) > 0) {
				RestCommons::respondWithStatus(400, array(
					"errors" => $errors
				));
			}

			$usuarioSesionDTO = RestCommons::getFromSession(RestCommons::USUARIO_SESION_DTO);
			$usuarioDTO = $usuarioSesionDTO->getObjetoUsuario();

			$obsDTO = new ReporteProgramadoObservacionDTO;
			$obsDTO->setRepoIdObservacion($jsonData->id);
			$obsDTO->setRepoIdUsuario($usuarioDTO->getUsuaIdUsuario());

			$reprDAO = new ReporteProgramadoDAO(ReservacionesBD::getInstance());

			$reprDAO->bajaObservacion($obsDTO);

			RestCommons::respondWithStatus(204);
		} catch (Exception $e) {
			error_log($e->getMessage());
			RestCommons::respondWithStatus(500);
		}
	}
}

(new RESTEndpointClient(array('DELETE'), array(TipoUsuarioEnum::SUPERUSUARIO, TipoUsuarioEnum::TECNICO, TipoUsuarioEnum::SERVIDOR_SOCIAL)))
	->execute(new ReportesProgramadoDiaBajaObservacion);
