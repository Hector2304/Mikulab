<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/HorarioBloqueadoDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class LaboratoriosBloqHorarioDeleteWeek extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();

			$errors = array();

			if (!is_numeric($jsonData->year)) {
				$errors[] = "YEAR_EMPTY";
			}
			if (!is_numeric($jsonData->week)) {
				$errors[] = "WEEK_EMPTY";
			}
			if (!is_numeric($jsonData->labId)) {
				$errors[] = "LAB_ID_EMPTY";
			}

			if (count($errors) > 0) {
				RestCommons::respondWithStatus(400, array(
					"errors" => $errors
				));
			}

			$bloqDAO = new HorarioBloqueadoDAO(ReservacionesBD::getInstance());

			$bloqDAO->bajaSemanal($jsonData->year, $jsonData->week, $jsonData->labId);

			RestCommons::respondWithStatus(204);
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('DELETE'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new LaboratoriosBloqHorarioDeleteWeek);
