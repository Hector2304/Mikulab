<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/BitacoraDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ReportesBitacoraListado extends BaseHandler
{
	public function handle()
	{
		try {
			$BitacoraDAO = new BitacoraDAO(ReservacionesBD::getInstance());

			$bitacoras = array();

			foreach ($BitacoraDAO->listado() as $bitacora) {
				$bitacoras[] = $bitacora->getObjectVars();
			}

			RestCommons::respondWithStatus(200, $bitacoras);
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('GET'), array(TipoUsuarioEnum::SUPERUSUARIO, TipoUsuarioEnum::TECNICO)))
	->execute(new ReportesBitacoraListado);
