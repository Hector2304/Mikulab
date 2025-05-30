<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/ReporteProgramadoDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ReportesProgramadoDiaListado extends BaseHandler
{
	public function handle()
	{
		try {
			$reprDAO = new ReporteProgramadoDAO(ReservacionesBD::getInstance());

			$reportes = array();

			foreach ($reprDAO->listado() as $bitacora) {
				$reportes[] = $bitacora->getObjectVars();
			}

			RestCommons::respondWithStatus(200, $reportes);
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('GET'), array(TipoUsuarioEnum::SUPERUSUARIO, TipoUsuarioEnum::TECNICO, TipoUsuarioEnum::SERVIDOR_SOCIAL, TipoUsuarioEnum::VIGILANTE)))
	->execute(new ReportesProgramadoDiaListado);

