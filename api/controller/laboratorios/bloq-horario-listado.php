<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/HorarioBloqueadoDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class LaboratoriosBloqHorariosListado extends BaseHandler
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

			$bloqDAO = new HorarioBloqueadoDAO(ReservacionesBD::getInstance());

			RestCommons::respondWithStatus(200, $bloqDAO->consultaAllPerWeek((int)$labId));
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('GET'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new LaboratoriosBloqHorariosListado);
