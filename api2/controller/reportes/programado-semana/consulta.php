<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/CursoExternoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/ReporteProgramadoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/ReservacionDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/impl/GruposDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/impl/LaboratoriosDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/impl/ProfesorDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ReportesProgramadoSemanaConsulta extends BaseHandler
{
	public function handle()
	{
		try {
			$from = $_GET["from"];
			$to = $_GET["to"];
			$labId = $_GET["lab-id"];

			if ($from == null || empty($from)) {
				RestCommons::respondWithStatus(400, array(
					"errors" => ["FROM_EMPTY"]
				));
			}

			if ($to == null || empty($to)) {
				RestCommons::respondWithStatus(400, array(
					"errors" => ["TO_EMPTY"]
				));
			}

			if (!is_numeric($labId)) {
				RestCommons::respondWithStatus(400, array(
					"errors" => ["LAB_EMPTY"]
				));
			}

			$desdeHasta = array();

			$interval1D = new DateInterval('P1D');
			$dateFrom = $this->newDateTime($from);
			$dateTo = $this->newDateTime($to);

			if ($dateFrom > $dateTo) {
				$ftTemp = $dateFrom;
				$dateFrom = $dateTo;
				$dateTo = $ftTemp;
			}

			$_dFrom = $this->newDateTime($dateFrom->format('Y-m-d'));
			while ($_dFrom <= $dateTo) {
				$desdeHasta[] = $_dFrom->format('Y-m-d');
				$_dFrom->add($interval1D);
			}

			$grlIds = array();
			$grpIds = array();
			$grtIds = array();
			$greIds = array();
			$profIds = array();

			$response = array();
			$gruposE = array();
			$gruposL = array();
			$gruposP = array();
			$gruposT = array();
			$profes = array();
			$reservas = array();

			$reservaDAO = new ReservacionDAO(ReservacionesBD::getInstance());
			foreach ($reservaDAO->listadoPorFechaYLaboratorio($desdeHasta, $labId) as $r) {
				switch ($r->getReseTipoGrupo()) {
					case "E":
						$greIds[] = $r->getReseIdGrupo();
						break;
					case "L":
						$grlIds[] = $r->getReseIdGrupo();
						break;
					case "P":
						$grpIds[] = $r->getReseIdGrupo();
						break;
					case "T":
						$grtIds[] = $r->getReseIdGrupo();
						break;
				}

				$reservas[] = $r->getObjectVars();
			}

			$cuexDAO = new CursoExternoDAO(ReservacionesBD::getInstance());
			if (count($greIds) > 0) {
				$gruposE = $cuexDAO->consultaInJoinInstructorMap($greIds);
			}

			$laboratoriosDAO = new LaboratoriosDAO(LaboratoriosFCABD::getInstance());
			$labGrupos = $laboratoriosDAO->getLaboratoriosGruposAlDia(array(1,2,3,4,5,6), (int)$labId);

			foreach ($labGrupos as $lg) {
				switch ($lg['tipoGrupo']) {
					case "L":
						$grlIds[] = $lg['idGrupo'];
						break;
					case "P":
						$grpIds[] = $lg['idGrupo'];
						break;
					case "T":
						$grtIds[] = $lg['idGrupo'];
						break;
				}
			}

			$gruposDAO = new GruposDAO(LaboratoriosFCABD::getInstance());
			if (count($grlIds) > 0) {
				$gruposL = $gruposDAO->gruposPorIdMap($grlIds);
				foreach ($gruposL as $dto) {
					$profIds[] = $dto['grupIdProfesor'];
				}
			}
			if (count($grpIds) > 0) {
				$gruposP = $gruposDAO->grupos_pPorIdMap($grpIds);
				foreach ($gruposP as $dto) {
					$profIds[] = $dto['gruoIdProfesor'];
				}
			}
			if (count($grtIds) > 0) {
				$gruposT = $gruposDAO->grupos_otPorIdMap($grtIds);
				foreach ($gruposT as $dto) {
					$profIds[] = $dto['grotIdProfesor'];
				}
			}

			$profesDAO = new ProfesorDAO(LaboratoriosFCABD::getInstance());
			if (count($profIds) > 0) {
				$profes = $profesDAO->profesoresMap($profIds);
			}

			$response['gruposE'] = $gruposE;
			$response['gruposL'] = $gruposL;
			$response['gruposP'] = $gruposP;
			$response['gruposT'] = $gruposT;
			$response['profes'] = $profes;
			$response['labGrupos'] = $labGrupos;
			$response['reservas'] = $reservas;

			RestCommons::respondWithStatus(200, $response);
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}

	private function newDateTime(string $date)
	{
		return new DateTime(
			// https://www.php.net/manual/en/datetime.format.php
			$date, // $dateTime->format('Y-m-d')
			new DateTimeZone('America/Mexico_City')
		);
	}
}

(new RESTEndpointClient(array('POST'), array(TipoUsuarioEnum::SUPERUSUARIO, TipoUsuarioEnum::TECNICO, TipoUsuarioEnum::SERVIDOR_SOCIAL)))
	->execute(new ReportesProgramadoSemanaConsulta);
