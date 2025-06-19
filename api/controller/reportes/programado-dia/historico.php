<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/ReporteProgramadoDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ReportesProgramadoDiaHistorico extends BaseHandler
{
	public function handle()
	{
		try {
			$reporteId = $_GET["reporte-id"];

			if ($reporteId == null || empty($reporteId)) {
				RestCommons::respondWithStatus(400, array(
					"errors" => ["ID_EMPTY"]
				));
			}

			$reprDAO = new ReporteProgramadoDAO(ReservacionesBD::getInstance());

			RestCommons::respondWithStatus(200, $reprDAO->consultaHistorio($reporteId));
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('GET'), array(TipoUsuarioEnum::SUPERUSUARIO, TipoUsuarioEnum::TECNICO, TipoUsuarioEnum::SERVIDOR_SOCIAL)))
	->execute(new ReportesProgramadoDiaHistorico);
