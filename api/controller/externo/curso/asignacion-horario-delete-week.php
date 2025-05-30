<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/ReservacionDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ExternoCursoAsignacionHorarioDeleteWeek extends BaseHandler
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
			if (!is_numeric($jsonData->cursoId)) {
				$errors[] = "CURSO_ID_EMPTY";
			}

			if (count($errors) > 0) {
				RestCommons::respondWithStatus(400, array(
					"errors" => $errors
				));
			}

			$reservacionDAO = new ReservacionDAO(ReservacionesBD::getInstance());

			$reservacionDAO->bajaSemanal($jsonData->year, $jsonData->week, $jsonData->labId, $jsonData->cursoId);

			RestCommons::respondWithStatus(204);
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('DELETE'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new ExternoCursoAsignacionHorarioDeleteWeek);
