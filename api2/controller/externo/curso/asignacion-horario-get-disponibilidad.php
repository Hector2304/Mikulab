<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/ReservacionDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ExternoCursoAsignacionHorarioGet extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();
			$jsonData->fecha = trim($jsonData->fecha);

			$errors = array();

			if (empty($jsonData->fecha)) {
				$errors[] = "FECHA_EMPTY";
			}
			if (!$jsonData->labIds || !is_array($jsonData->labIds)) {
				$errors[] = "LABIDS_EMPTY";
			}

			if (count($errors) > 0) {
				RestCommons::respondWithStatus(400, array(
					"errors" => $errors
				));
			}

			$reservacionDAO = new ReservacionDAO(ReservacionesBD::getInstance());

			RestCommons::respondWithStatus(200, $reservacionDAO->consultaDay($jsonData->fecha, $jsonData->labIds));
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('POST'), array(TipoUsuarioEnum::ALL)))
	->execute(new ExternoCursoAsignacionHorarioGet);
