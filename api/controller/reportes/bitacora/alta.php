<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/BitacoraDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/impl/LaboratoriosDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioSesionDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ReportesBitacoraAlta extends BaseHandler
{
	public function handle()
	{
		$jsonData = RestCommons::readJSON();
		$jsonData->fecha = trim($jsonData->fecha);
		$jsonData->tipo = trim($jsonData->tipo);
		$jsonData->tipoLab = trim($jsonData->tipoLab);

		$errors = array();

		if (empty($jsonData->fecha)) {
			$errors[] = "FECHA_EMPTY";
		}
		if (empty($jsonData->tipo)) {
			$errors[] = "TIPO_EMPTY";
		}
		if (empty($jsonData->tipoLab)) {
			$errors[] = "TIPO_LAB_EMPTY";
		}

		if (count($errors) > 0) {
			RestCommons::respondWithStatus(400, array(
				"errors" => $errors
			));
		}

		$bitacoraDTO = new BitacoraDTO;
		$bitacoraDTO->setBitaFecha($jsonData->fecha);
		$bitacoraDTO->setBitaTipo($jsonData->tipo);
		$bitacoraDTO->setBitaTipoLab($jsonData->tipoLab);

		try {
			$bitacoraDAO = new BitacoraDAO(ReservacionesBD::getInstance());
			$bitacoraDTO->setBitaIdBitacora($bitacoraDAO->alta($bitacoraDTO));

			$laboratoriosDAO = new LaboratoriosDAO(LaboratoriosFCABD::getInstance());

			$bitacoraDAO->altaDetalle($bitacoraDTO->getBitaIdBitacora(), $laboratoriosDAO->laboratoriosFCAClaveStartsWith($bitacoraDTO->getBitaTipoLab()));

			try {
				$usuarioSesionDTO = RestCommons::getFromSession(RestCommons::USUARIO_SESION_DTO);
				$usuarioDTO = $usuarioSesionDTO->getObjetoUsuario();

				$historicoDTO = new BitacoraHistoricoDTO;
				$historicoDTO->setBihiIdBitacora($bitacoraDTO->getBitaIdBitacora());
				$historicoDTO->setBihiIdUsuario($usuarioDTO->getUsuaIdUsuario());
				$historicoDTO->setBiobAccion("Creado");

				$bitacoraDAO->altaHistorico($historicoDTO);
			} catch (Exception $ex) {
				error_log($ex->getMessage());
			}
		} catch (PDOException $e) {
			try {
				$bitacoraDAO = new BitacoraDAO(ReservacionesBD::getInstance());
				$bitacoraDTO->setBitaIdBitacora($bitacoraDAO->buscar($bitacoraDTO));
			} catch (Exception $ex) {
				error_log($ex->getMessage());
				RestCommons::respondWithStatus(500);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			RestCommons::respondWithStatus(500);
		}

		if ($bitacoraDTO->getBitaIdBitacora() > 0) {
			RestCommons::respondWithStatus(200, $bitacoraDTO->getObjectVars());
		} else {
			RestCommons::respondWithStatus(404);
		}
	}
}

(new RESTEndpointClient(array('POST'), array(TipoUsuarioEnum::SUPERUSUARIO, TipoUsuarioEnum::TECNICO)))
	->execute(new ReportesBitacoraAlta);
