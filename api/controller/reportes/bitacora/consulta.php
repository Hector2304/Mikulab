<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/BitacoraDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/impl/LaboratoriosDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ReportesBitacoraConsulta extends BaseHandler
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

			$labIds = array();

			$response = array();
			$detalle = array();
			$labs = array();

			$bitacoraDAO = new BitacoraDAO(ReservacionesBD::getInstance());

			$observaciones = $bitacoraDAO->consultaObservaciones((int)$bitacoraId);

			foreach ($bitacoraDAO->consultaDetalle((int)$bitacoraId) as $dto) {
				$detalle[] = $dto->getObjectVars();
				$labIds[] = $dto->getBideIdLaboratorio();
			}

			$laboratoriosDAO = new LaboratoriosDAO(LaboratoriosFCABD::getInstance());

			foreach ($laboratoriosDAO->laboratoriosFCAIn($labIds) as $dto) {
				$labs[$dto->getSaloIdSalon()] = $dto->getObjectVars();
			}

			$response['observaciones'] = $observaciones;
			$response['detalle'] = $detalle;
			$response['labs'] = $labs;

			RestCommons::respondWithStatus(200, $response);
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('GET'), array(TipoUsuarioEnum::SUPERUSUARIO, TipoUsuarioEnum::TECNICO)))
	->execute(new ReportesBitacoraConsulta);
