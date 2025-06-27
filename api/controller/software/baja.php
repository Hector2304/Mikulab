<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/SoftwareDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/SoftwareDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php";

class SoftwareBaja extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();

			if (!is_numeric($jsonData->softIdSoftware)) {
				RestCommons::respondWithStatus(400, array(
					"errors" => ["ID_INVALID"]
				));
			}

			$softwareDAO = new SoftwareDAO(ReservacionesBD::getInstance());

			$softwareDTO = new SoftwareDTO;
			$softwareDTO->setSoftIdSoftware($jsonData->softIdSoftware);

			$softwareDAO->baja($softwareDTO);

			RestCommons::respondWithStatus(204);
		} catch (PDOException $e) {
			error_log($e->getMessage());
			RestCommons::respondWithStatus(400, array("error" => "INTEGRITY"));
		} catch (Exception $e) {
			error_log($e->getMessage());
			RestCommons::respondWithStatus(500);
		}
	}
}

(new RESTEndpointClient(array('DELETE'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new SoftwareBaja);
