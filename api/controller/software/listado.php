<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/SoftwareDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/SoftwareDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php";

class SoftwareListado extends BaseHandler
{
	public function handle()
	{
		try {
			$softwareDAO = new SoftwareDAO(ReservacionesBD::getInstance());

			$softwares = array();

			foreach ($softwareDAO->consultaAll() as $softwareDTO) {
				$softwares[] = $softwareDTO->getObjectVars();
			}

			RestCommons::respondWithStatus(200, $softwares);
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('GET'), array(TipoUsuarioEnum::SUPERUSUARIO, TipoUsuarioEnum::PROFESOR)))
	->execute(new SoftwareListado);
