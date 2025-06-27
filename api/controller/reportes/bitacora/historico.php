<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/BitacoraDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ReportesBitacoraHistorico extends BaseHandler
{
	public function handle()
	{
		try {
			$bitacoraId = $_GET["bitacora-id"];

			if ($bitacoraId == null || empty($bitacoraId)) {
				RestCommons::respondWithStatus(400, array(
					"errors" => ["ID_EMPTY"]
				));
			}

			$BitacoraDAO = new BitacoraDAO(ReservacionesBD::getInstance());

			RestCommons::respondWithStatus(200, $BitacoraDAO->consultaHistorio($bitacoraId));
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('GET'), array(TipoUsuarioEnum::SUPERUSUARIO, TipoUsuarioEnum::TECNICO)))
	->execute(new ReportesBitacoraHistorico);
