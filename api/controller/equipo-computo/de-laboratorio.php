<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/EquipoComputoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/EquipoComputoDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php";

class EquipoComputoDeLaboratorio extends BaseHandler
{
	public function handle()
	{
		try {
			$labId = $_GET["lab-id"];

			if ($labId == null || empty($labId)) {
				RestCommons::respondWithStatus(400, array(
					"errors" => ["ID_EMPTY"]
				));
			}

			$equipoDAO = new EquipoComputoDAO(ReservacionesBD::getInstance());

			$equipos = array();

			foreach ($equipoDAO->consultaPorLabId((int)$labId) as $equipoDTO) {
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
	->execute(new EquipoComputoDeLaboratorio);
