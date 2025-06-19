<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/SoftwareDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class Laboratorios2Software extends BaseHandler
{
	public function handle()
	{
		try {
			$byLab = true;

			if ($_GET["by-lab"] != null) {
				$byLab = true;
			} else if ($_GET["by-software"] != null) {
				$byLab = false;
			}

			$softwareDAO = new SoftwareDAO(ReservacionesBD::getInstance());

			RestCommons::respondWithStatus(200, $softwareDAO->obtenerAsociacionCompleta($byLab));
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('GET'), array(TipoUsuarioEnum::ALL)))
	->execute(new Laboratorios2Software);
