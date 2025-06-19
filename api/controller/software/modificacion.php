<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/SoftwareDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/SoftwareDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php";

class SoftwareModificacion extends BaseHandler
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
			if (!$jsonData->softIdSoftware) {
				RestCommons::respondWithStatus(400, array(
					"errors" => ["ID_EMPTY"]
				));
			}

			$softwareDAO = new SoftwareDAO(ReservacionesBD::getInstance());

			$softwareDTO = new SoftwareDTO;
			$softwareDTO->setSoftIdSoftware($jsonData->softIdSoftware);
			$softwareDTO->setSoftNombre($jsonData->softNombre);

			$softwareDAO->modificacion($softwareDTO);

			RestCommons::respondWithStatus(204);
		} catch (Exception $e) {
			error_log($e->getMessage());
			RestCommons::respondWithStatus(500);
		}
	}
}

(new RESTEndpointClient(array('PATCH'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new SoftwareModificacion);
