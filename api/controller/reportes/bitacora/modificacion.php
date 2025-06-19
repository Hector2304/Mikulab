<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/BitacoraDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioSesionDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ReportesBitacoraModificacion extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();

			$errors = array();

			if (!$jsonData->detalle || !is_array($jsonData->detalle) || count($jsonData->detalle) <= 0) {
				$errors[] = "DETALLE_EMPTY";
			}

			if (count($errors) > 0) {
				RestCommons::respondWithStatus(400, array(
					"errors" => $errors
				));
			}

			$bitacoraDAO = new BitacoraDAO(ReservacionesBD::getInstance());

			$bitacoraDAO->modificacionDetalle($jsonData->detalle);

			try {
				$usuarioSesionDTO = RestCommons::getFromSession(RestCommons::USUARIO_SESION_DTO);
				$usuarioDTO = $usuarioSesionDTO->getObjetoUsuario();

				$historicoDTO = new BitacoraHistoricoDTO;
				$historicoDTO->setBihiIdBitacora((int)$jsonData->detalle[0]->bideIdBitacora);
				$historicoDTO->setBihiIdUsuario($usuarioDTO->getUsuaIdUsuario());
				$historicoDTO->setBiobAccion("Modificado");

				$bitacoraDAO->altaHistorico($historicoDTO);
			} catch (Exception $ex) {
				error_log($ex->getMessage());
			}

			RestCommons::respondWithStatus(204);
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('POST'), array(TipoUsuarioEnum::SUPERUSUARIO, TipoUsuarioEnum::TECNICO)))
	->execute(new ReportesBitacoraModificacion);
