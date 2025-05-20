<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/ReporteProgramadoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/ReservacionDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/impl/LaboratoriosDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioSesionDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ReportesProgramadoDiaAlta extends BaseHandler
{
	public function handle()
	{
		$jsonData = RestCommons::readJSON();
		$jsonData->fecha = trim($jsonData->fecha);

		$errors = array();

		if (empty($jsonData->fecha)) {
			$errors[] = "FECHA_EMPTY";
		}

		if (count($errors) > 0) {
			RestCommons::respondWithStatus(400, array(
				"errors" => $errors
			));
		}

		$reprDTO = new ReporteProgramadoDTO;
		$reprDTO->setReprFecha($jsonData->fecha);

		try {
			$reprDAO = new ReporteProgramadoDAO(ReservacionesBD::getInstance());
			$reprDTO->setReprIdReporteProgramado($reprDAO->alta($reprDTO));

			$reservaDAO = new ReservacionDAO(ReservacionesBD::getInstance());
			$reservas = $reservaDAO->listadoPorFecha(array($jsonData->fecha));

			$detalleArray = array();

			foreach ($reservas as $r) {
				$detalleDTO = new ReporteProgramadoDetalleDTO;
				$detalleDTO->setRepdIdLaboratorio($r->getReseIdLaboratorio());
				$detalleDTO->setRepdIdGrupo($r->getReseIdGrupo());
				$detalleDTO->setRepdTipoGrupo($r->getReseTipoGrupo());
				$detalleDTO->setRepdIdHorario($r->getReseIdHorario());

				$detalleArray[] = $detalleDTO;
			}

			if (count($detalleArray) > 0) {
				$reprDAO->altaDetalle($reprDTO->getReprIdReporteProgramado(), $detalleArray);
			}

			$laboratoriosDAO = new LaboratoriosDAO(LaboratoriosFCABD::getInstance());
			$labGrupos = $laboratoriosDAO->getLaboratoriosGruposAlDia(array((new DateTime($jsonData->fecha, new DateTimeZone('America/Mexico_City')))->format('N')), null);

			$reprDAO->altaDetalleGruposFCA($reprDTO->getReprIdReporteProgramado(), $labGrupos);

			try {
				$usuarioSesionDTO = RestCommons::getFromSession(RestCommons::USUARIO_SESION_DTO);
				$usuarioDTO = $usuarioSesionDTO->getObjetoUsuario();

				$historicoDTO = new ReporteProgramadoHistoricoDTO;
				$historicoDTO->setRephIdProgramado($reprDTO->getReprIdReporteProgramado());
				$historicoDTO->setRephIdUsuario($usuarioDTO->getUsuaIdUsuario());
				$historicoDTO->setRephAccion("Creado");

				$reprDAO->altaHistorico($historicoDTO);
			} catch (Exception $ex) {
				error_log($ex->getMessage());
			}
		} catch (PDOException $e) {
			try {
				$reprDAO = new ReporteProgramadoDAO(ReservacionesBD::getInstance());
				$reprDTO->setReprIdReporteProgramado($reprDAO->buscar($reprDTO));
			} catch (Exception $ex) {
				error_log($ex->getMessage());
				RestCommons::respondWithStatus(500);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			RestCommons::respondWithStatus(500);
		}

		if ($reprDTO->getReprIdReporteProgramado() > 0) {
			RestCommons::respondWithStatus(200, $reprDTO->getObjectVars());
		} else {
			RestCommons::respondWithStatus(404);
		}
	}
}

(new RESTEndpointClient(array('POST'), array(TipoUsuarioEnum::SUPERUSUARIO, TipoUsuarioEnum::TECNICO, TipoUsuarioEnum::SERVIDOR_SOCIAL)))
	->execute(new ReportesProgramadoDiaAlta);
