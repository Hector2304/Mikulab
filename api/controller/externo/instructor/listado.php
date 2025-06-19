<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/InstructorExternoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/InstructorExternoDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ExternoInstructorListado extends BaseHandler
{
	public function handle()
	{
		try {
			$instructorDAO = new InstructorExternoDAO(ReservacionesBD::getInstance());

			$instructores = array();

			foreach ($instructorDAO->consultaAll() as $instructorDTO) {
				$instructores[] = $instructorDTO->getObjectVars();
			}

			RestCommons::respondWithStatus(200, $instructores);
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('GET'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new ExternoInstructorListado);
