<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/HorarioBloqueadoDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class LaboratoriosLaboratoriosBloqHorario extends BaseHandler
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
			if (!is_numeric($jsonData->hora)) {
				$errors[] = "HORA_EMPTY";
			}
			if (!is_numeric($jsonData->labId)) {
				$errors[] = "LAB_ID_EMPTY";
			}

			if (count($errors) > 0) {
				RestCommons::respondWithStatus(400, array(
					"errors" => $errors
				));
			}

			if (strlen('' . $jsonData->hora) < 2) {
				$jsonData->hora = '0' . $jsonData->hora . '00';
			} else {
				$jsonData->hora = '' . $jsonData->hora . '00';
			}

			$bloqDAO = new HorarioBloqueadoDAO(ReservacionesBD::getInstance());

			$bloqDAO->altaIndividual($jsonData->fecha, $jsonData->hora, $jsonData->labId, $jsonData->motivo);

			RestCommons::respondWithStatus(204);
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('POST'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new LaboratoriosLaboratoriosBloqHorario);
