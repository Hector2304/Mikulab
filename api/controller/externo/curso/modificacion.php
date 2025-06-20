<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/CursoExternoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/CursoExternoDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php";

class ExternoCursoModificacion extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();
			$jsonData->nombre = trim($jsonData->nombre);
			$jsonData->clave = trim($jsonData->clave);

			$errors = array();

			if (empty($jsonData->clave)) {
				$errors[] = "CLAVE_EMPTY";
			}
			if (empty($jsonData->nombre)) {
				$errors[] = "NOMBRE_EMPTY";
			}
			if (!is_numeric($jsonData->alumnos)) {
				$errors[] = "ALUMNOS_EMPTY";
			}
			if (!$jsonData->id) {
				$errors[] = "ID_EMPTY";
			}

			if (count($errors) > 0) {
				RestCommons::respondWithStatus(400, array(
					"errors" => $errors
				));
			}

			$cursoDAO = new CursoExternoDAO(ReservacionesBD::getInstance());

			$cursoDTO = new CursoExternoDTO;
			$cursoDTO->setCuexIdCursoExterno($jsonData->id);
			$cursoDTO->setCuexClave($jsonData->clave);
			$cursoDTO->setCuexNombre($jsonData->nombre);
			$cursoDTO->setCuexAlumnosInscritos($jsonData->alumnos);

			$cursoDAO->modificacion($cursoDTO);

			RestCommons::respondWithStatus(204);
		} catch (Exception $e) {
			error_log($e->getMessage());
			RestCommons::respondWithStatus(500);
		}
	}
}

(new RESTEndpointClient(array('PATCH'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new ExternoCursoModificacion);
