<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/impl/LaboratoriosDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ReservaDisponibilidad extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();

			$errors = array();

			if (!$jsonData->labIds || !is_array($jsonData->labIds)) {
				$errors[] = "LABIDS_EMPTY";
			}
			if (!$jsonData->dias || !is_array($jsonData->dias)) {
				$errors[] = "DIAS_EMPTY";
			}

			if (count($errors) > 0) {
				RestCommons::respondWithStatus(400, array(
					"errors" => $errors
				));
			}

			$laboratoriosDAO = new LaboratoriosDAO(LaboratoriosFCABD::getInstance());

			RestCommons::respondWithStatus(200, $laboratoriosDAO->getDisponibilidad($jsonData->dias, $jsonData->labIds));
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('POST'), array(TipoUsuarioEnum::ALL)))
	->execute(new ReservaDisponibilidad);
