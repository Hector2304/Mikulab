<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/CursoExternoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/HorarioDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/ReporteProgramadoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/impl/GruposDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/impl/LaboratoriosDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/impl/ProfesorDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ReportesProgramadoDiaConsulta extends BaseHandler
{
	public function handle()
	{
		try {
			$reprId = $_GET["reporte-id"];

			if ($reprId == null || empty($reprId)) {
				RestCommons::respondWithStatus(400, array(
					"errors" => ["ID_EMPTY"]
				));
			}

			$labIds = array();
			$horIds = array();
			$grlIds = array();
			$grpIds = array();
			$grtIds = array();
			$greIds = array();
			$profIds = array();

			$response = array();
			$detalle = array();
			$labs = array();
			$horarios = array();
			$gruposE = array();
			$gruposL = array();
			$gruposP = array();
			$gruposT = array();
			$profes = array();

			$reprDAO = new ReporteProgramadoDAO(ReservacionesBD::getInstance());

			$observaciones = $reprDAO->consultaObservaciones((int)$reprId);

			foreach ($reprDAO->consultaDetalle((int)$reprId) as $dto) {
				$detalle[] = $dto->getObjectVars();
				$labIds[] = $dto->getRepdIdLaboratorio();
				$horIds[] = $dto->getRepdIdHorario();

				switch ($dto->getRepdTipoGrupo()) {
					case "E":
						$greIds[] = $dto->getRepdIdGrupo();
						break;
					case "L":
						$grlIds[] = $dto->getRepdIdGrupo();
						break;
					case "P":
						$grpIds[] = $dto->getRepdIdGrupo();
						break;
					case "T":
						$grtIds[] = $dto->getRepdIdGrupo();
						break;
				}
			}

			$cuexDAO = new CursoExternoDAO(ReservacionesBD::getInstance());
			if (count($greIds) > 0) {
				$gruposE = $cuexDAO->consultaInJoinInstructorMap($greIds);
			}

			$horarioDAO = new HorarioDAO(ReservacionesBD::getInstance());
			if (count($horIds) > 0) {
				foreach ($horarioDAO->getAllIn($horIds) as $dto) {
					$h = $dto->getObjectVars();
					$ini = ((int)$dto->getHoraIni()) / 100;
					$fin = ((int)$dto->getHoraFin()) / 100;
					$h['formatted'] = ($ini < 10 ? "0" : "") . $ini . ":00 - " . ($fin < 10 ? "0" : "") . $fin  . ":00";
					$horarios[$dto->getHoraIdHorario()] = $h;
				}
			}

			$laboratoriosDAO = new LaboratoriosDAO(LaboratoriosFCABD::getInstance());
			if (count($labIds) > 0) {
				foreach ($laboratoriosDAO->laboratoriosFCAIn($labIds) as $dto) {
					$labs[$dto->getSaloIdSalon()] = $dto->getObjectVars();
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

			$response['observaciones'] = $observaciones;
			$response['detalle'] = $detalle;
			$response['labs'] = $labs;
			$response['horarios'] = $horarios;
			$response['gruposE'] = $gruposE;
			$response['gruposL'] = $gruposL;
			$response['gruposP'] = $gruposP;
			$response['gruposT'] = $gruposT;
			$response['profes'] = $profes;

			RestCommons::respondWithStatus(200, $response);
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('GET'), array(TipoUsuarioEnum::SUPERUSUARIO, TipoUsuarioEnum::TECNICO, TipoUsuarioEnum::SERVIDOR_SOCIAL)))
	->execute(new ReportesProgramadoDiaConsulta);
