<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/ReservacionDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ExternoCursoAsignacionHorarioDelete extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();

			$reservacionDAO = new ReservacionDAO(ReservacionesBD::getInstance());

			$reservacionDAO->baja($jsonData->idReservacion);

			RestCommons::respondWithStatus(204);
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('DELETE'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new ExternoCursoAsignacionHorarioDelete);
