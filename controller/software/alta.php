<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/SoftwareDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/SoftwareDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php";

class SoftwareAlta extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();
			$jsonData->softNombre = trim($jsonData->softNombre);

			if (empty($jsonData->softNombre)) {
				RestCommons::respondWithStatus(400, array(
					"errors" => ["NOMBRE_EMPTY"]
				));
			}

			$softwareDAO = new SoftwareDAO(ReservacionesBD::getInstance());

			$softwareDTO = new SoftwareDTO;
			$softwareDTO->setSoftNombre($jsonData->softNombre);

			$softwareDTO->setSoftIdSoftware($softwareDAO->alta($softwareDTO));

			RestCommons::respondWithStatus(200, $softwareDTO->getObjectVars());
		} catch (Exception $e) {
			error_log($e->getMessage());
			RestCommons::respondWithStatus(500);
		}
	}
}

(new RESTEndpointClient(array('POST'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new SoftwareAlta);
