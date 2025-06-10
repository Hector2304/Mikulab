<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/EquipoComputoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/EquipoComputoDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class EquipoComputoListado extends BaseHandler
{
	public function handle()
	{
		try {
			$equipoDAO = new EquipoComputoDAO(ReservacionesBD::getInstance());

			$equipos = array();

			foreach ($equipoDAO->consultaAll() as $equipoDTO) {
				$equipos[] = $equipoDTO->getObjectVars();
			}

			RestCommons::respondWithStatus(200, $equipos);
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('GET'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new EquipoComputoListado);
