<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/EquipoComputoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/EquipoComputoDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php";

class EquipoComputoModificacion extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();
			$jsonData->eqcoNombre = trim($jsonData->eqcoNombre);
			$jsonData->eqcoDescripcion = trim($jsonData->eqcoDescripcion);
			$jsonData->eqcoNumeroInventario = trim($jsonData->eqcoNumeroInventario);
			$jsonData->eqcoStatus = trim($jsonData->eqcoStatus);

			$errors = array();

			if (!is_numeric($jsonData->eqcoIdEquipo)) {
				$errors[] = "ID_INVALID";
			}
			if (empty($jsonData->eqcoNombre)) {
				$errors[] = "NOMBRE_EMPTY";
			}
			if (empty($jsonData->eqcoDescripcion)) {
				$errors[] = "DESCRIPCION_EMPTY";
			}
			if (empty($jsonData->eqcoNumeroInventario)) {
				$errors[] = "NUMERO_INVENTARIO_EMPTY";
			}
			if (empty($jsonData->eqcoStatus)) {
				$errors[] = "STATUS_EMPTY";
			}

			if (count($errors) > 0) {
				RestCommons::respondWithStatus(400, array(
					"errors" => $errors
				));
			}

			$equipoDAO = new EquipoComputoDAO(ReservacionesBD::getInstance());

			$equipoDTO = new EquipoComputoDTO;
			$equipoDTO->setEqcoIdEquipo($jsonData->eqcoIdEquipo);
			$equipoDTO->setEqcoNombre($jsonData->eqcoNombre);
			$equipoDTO->setEqcoDescripcion($jsonData->eqcoDescripcion);
			$equipoDTO->setEqcoNumeroInventario($jsonData->eqcoNumeroInventario);
			$equipoDTO->setEqcoStatus($jsonData->eqcoStatus);

			$equipoDAO->modificacion($equipoDTO);

			RestCommons::respondWithStatus(204);
		} catch (Exception $e) {
			error_log($e->getMessage());
			RestCommons::respondWithStatus(500);
		}
	}
}

(new RESTEndpointClient(array('PATCH'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new EquipoComputoModificacion);
