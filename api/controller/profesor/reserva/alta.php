<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/ReservacionDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/ReservacionDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioSesionDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dto/ProfesorDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ProfesorReservaAlta extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();

			$usuarioSesionDTO = RestCommons::getFromSession(RestCommons::USUARIO_SESION_DTO);
			$profesorDTO = $usuarioSesionDTO->getObjetoUsuario();

			$reservacionDAO = new ReservacionDAO(ReservacionesBD::getInstance());

			$traslapos = $reservacionDAO->traslapos((int)$jsonData->horaIni, (int)$jsonData->horaFin, $jsonData->fecha, $jsonData->labId);

			if (count($traslapos) > 0) {
				RestCommons::respondWithStatus(400, array("error" => "OVERLAP"));
				exit;
			}

			if ($jsonData->grupoId) {
				$reservacionDAO->alta($jsonData->horaIni, $jsonData->horaFin, $jsonData->fecha, $profesorDTO->getProfIdProfesor(), $jsonData->labId, $jsonData->grupoId, 'L');
				RestCommons::respondWithStatus(204);
			} else if ($jsonData->grupoIdP) {
				$reservacionDAO->alta($jsonData->horaIni, $jsonData->horaFin, $jsonData->fecha, $profesorDTO->getProfIdProfesor(), $jsonData->labId, $jsonData->grupoIdP, 'P');
				RestCommons::respondWithStatus(204);
			} else if ($jsonData->grupoIdOt) {
				$reservacionDAO->alta($jsonData->horaIni, $jsonData->horaFin, $jsonData->fecha, $profesorDTO->getProfIdProfesor(), $jsonData->labId, $jsonData->grupoIdOt, 'T');
				RestCommons::respondWithStatus(204);
			} else {
				RestCommons::respondWithStatus(400);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('POST'), array(TipoUsuarioEnum::PROFESOR)))
	->execute(new ProfesorReservaAlta);
