<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/BitacoraDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioSesionDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ReportesBitacoraAltaObservacion extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();
			$jsonData->obs = trim($jsonData->obs);

			$errors = array();

			if (empty($jsonData->obs)) {
				$errors[] = "OBS_EMPTY";
			}
			if (!is_numeric($jsonData->detalleId)) {
				$errors[] = "DETALLE_ID_EMPTY";
			}

			if (count($errors) > 0) {
				RestCommons::respondWithStatus(400, array(
					"errors" => $errors
				));
			}

			$usuarioSesionDTO = RestCommons::getFromSession(RestCommons::USUARIO_SESION_DTO);
			$usuarioDTO = $usuarioSesionDTO->getObjetoUsuario();

			$obsDTO = new BitacoraObservacionDTO;
			$obsDTO->setBiobIdDetalle($jsonData->detalleId);
			$obsDTO->setBiobIdUsuario($usuarioDTO->getUsuaIdUsuario());
			$obsDTO->setBiobObservacion($jsonData->obs);

			$bitacoraDAO = new BitacoraDAO(ReservacionesBD::getInstance());

			RestCommons::respondWithStatus(200, array(
				"id" => $bitacoraDAO->altaObservacion($obsDTO)
			));
		} catch (Exception $e) {
			error_log($e->getMessage());
			RestCommons::respondWithStatus(500);
		}
	}
}

(new RESTEndpointClient(array('POST'), array(TipoUsuarioEnum::SUPERUSUARIO, TipoUsuarioEnum::TECNICO)))
	->execute(new ReportesBitacoraAltaObservacion);
