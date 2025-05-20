<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/ReporteProgramadoDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioSesionDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ReportesProgramadoDiaModificacion extends BaseHandler
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

			$reprDAO = new ReporteProgramadoDAO(ReservacionesBD::getInstance());

			$reprDAO->modificacionDetalle($jsonData->detalle);

			try {
				$usuarioSesionDTO = RestCommons::getFromSession(RestCommons::USUARIO_SESION_DTO);
				$usuarioDTO = $usuarioSesionDTO->getObjetoUsuario();

				$historicoDTO = new ReporteProgramadoHistoricoDTO;
				$historicoDTO->setRephIdProgramado((int)$jsonData->detalle[0]->repdIdReporteProgramado);
				$historicoDTO->setRephIdUsuario($usuarioDTO->getUsuaIdUsuario());
				$historicoDTO->setRephAccion("Modificado");

				$reprDAO->altaHistorico($historicoDTO);
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

(new RESTEndpointClient(array('POST'), array(TipoUsuarioEnum::SUPERUSUARIO, TipoUsuarioEnum::TECNICO, TipoUsuarioEnum::SERVIDOR_SOCIAL)))
	->execute(new ReportesProgramadoDiaModificacion);
