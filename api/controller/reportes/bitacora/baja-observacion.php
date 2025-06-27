<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/BitacoraDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioSesionDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ReportesBitacoraBajaObservacion extends BaseHandler
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

			$obsDTO = new BitacoraObservacionDTO;
			$obsDTO->setBiobIdObservacion($jsonData->id);
			$obsDTO->setBiobIdUsuario($usuarioDTO->getUsuaIdUsuario());

			$bitacoraDAO = new BitacoraDAO(ReservacionesBD::getInstance());

			$bitacoraDAO->bajaObservacion($obsDTO);

			RestCommons::respondWithStatus(204);
		} catch (Exception $e) {
			error_log($e->getMessage());
			RestCommons::respondWithStatus(500);
		}
	}
}

(new RESTEndpointClient(array('DELETE'), array(TipoUsuarioEnum::SUPERUSUARIO, TipoUsuarioEnum::TECNICO)))
	->execute(new ReportesBitacoraBajaObservacion);
