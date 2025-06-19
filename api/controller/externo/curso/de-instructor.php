<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/CursoExternoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/CursoExternoDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php";

class ExternoCursoDeInstructor extends BaseHandler
{
	public function handle()
	{
		try {
			$insId = $_GET["instructor-id"];

			if ($insId == null || empty($insId)) {
				RestCommons::respondWithStatus(400, array(
					"errors" => ["ID_EMPTY"]
				));
			}

			$cursoDAO = new CursoExternoDAO(ReservacionesBD::getInstance());

			$cursos = array();

			foreach ($cursoDAO->consultaPorInstructorId((int)$insId) as $cursoDTO) {
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
	->execute(new ExternoCursoDeInstructor);
