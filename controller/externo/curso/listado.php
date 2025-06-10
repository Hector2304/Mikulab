<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/CursoExternoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/CursoExternoDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ExternoCursoListado extends BaseHandler
{
	public function handle()
	{
		try {
			$cursoDAO = new CursoExternoDAO(ReservacionesBD::getInstance());

			$cursos = array();

			foreach ($cursoDAO->consultaAll() as $cursoDTO) {
				$cursos[] = $cursoDTO->getObjectVars();
			}

			RestCommons::respondWithStatus(200, $cursos);
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('GET'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new ExternoCursoListado);
