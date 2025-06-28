<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/EquipoComputoDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php";

class EquipoComputoAsignacionLaboratorio extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();

			if (count($jsonData->seleccionar) == 0 && count($jsonData->deseleccionar) == 0) {
				RestCommons::respondWithStatus(400, array(
					"errors" => ["EMPTY"]
				));
			}

			if (!is_numeric($jsonData->saloIdSalon)) {
				RestCommons::respondWithStatus(400, array(
					"errors" => ["ID_INVALID"]
				));
			}

			$equipoDAO = new EquipoComputoDAO(ReservacionesBD::getInstance());

			if (count($jsonData->seleccionar) > 0) {
				$equipoDAO->asignarLaboratorio($jsonData->seleccionar, $jsonData->saloIdSalon);
			}

			if (count($jsonData->deseleccionar) > 0) {
				$equipoDAO->removerLaboratorio($jsonData->deseleccionar, $jsonData->saloIdSalon);
			}

			RestCommons::respondWithStatus(204);
		} catch (Exception $e) {
			error_log($e->getMessage());
			RestCommons::respondWithStatus(500);
		}
	}
}

(new RESTEndpointClient(array('PATCH'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new EquipoComputoAsignacionLaboratorio);
