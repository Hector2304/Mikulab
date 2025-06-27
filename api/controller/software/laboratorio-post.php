<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/SoftwareDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/SoftwareDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php";

class SoftwareLaboratorioPost extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();

			if (!$jsonData->idLaboratorio) {
				RestCommons::respondWithStatus(400, array(
					"errors" => ["NOMBRE_EMPTY"]
				));
			}
			if (!$jsonData->idSoftware) {
				RestCommons::respondWithStatus(400, array(
					"errors" => ["ID_EMPTY"]
				));
			}

			$softwareDAO = new SoftwareDAO(ReservacionesBD::getInstance());

			$softwareDAO->asignarALaboratorio($jsonData->idLaboratorio, $jsonData->idSoftware);

			RestCommons::respondWithStatus(204);
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('POST'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new SoftwareLaboratorioPost);
