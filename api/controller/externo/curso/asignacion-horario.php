<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/ReservacionDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ExternoCursoAsignacionHorario extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();

			$reservacionDAO = new ReservacionDAO(ReservacionesBD::getInstance());

			RestCommons::respondWithStatus(200, array("idReservacion" => $reservacionDAO->alta($jsonData->horaIni, $jsonData->horaFin, $jsonData->fecha, null, $jsonData->labId, $jsonData->cursoId, 'E')));
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('POST'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new ExternoCursoAsignacionHorario);
