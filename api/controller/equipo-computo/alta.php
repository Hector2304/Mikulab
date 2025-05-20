<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/EquipoComputoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/EquipoComputoDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php";

class EquipoComputoAlta extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();
			$jsonData->eqNombre = trim($jsonData->eqNombre);
			$jsonData->eqDescripcion = trim($jsonData->eqDescripcion);
			$jsonData->eqNumeroInventario = trim($jsonData->eqNumeroInventario);
			$jsonData->eqStatus = trim($jsonData->eqStatus);

			$errors = array();

			if (empty($jsonData->eqNombre)) {
				$errors[] = "NOMBRE_EMPTY";
			}
			if (empty($jsonData->eqDescripcion)) {
				$errors[] = "DESCRIPCION_EMPTY";
			}
			if (empty($jsonData->eqNumeroInventario)) {
				$errors[] = "NUMERO_INVENTARIO_EMPTY";
			}
			if (empty($jsonData->eqStatus)) {
				$errors[] = "STATUS_EMPTY";
			}

			if (count($errors) > 0) {
				RestCommons::respondWithStatus(400, array(
					"errors" => $errors
				));
			}

			$equipoDAO = new EquipoComputoDAO(ReservacionesBD::getInstance());

			$equipoDTO = new EquipoComputoDTO;
			$equipoDTO->setEqcoNombre($jsonData->eqNombre);
			$equipoDTO->setEqcoDescripcion($jsonData->eqDescripcion);
			$equipoDTO->setEqcoNumeroInventario($jsonData->eqNumeroInventario);
			$equipoDTO->setEqcoStatus($jsonData->eqStatus);

			$equipoDTO->setEqcoIdEquipo($equipoDAO->alta($equipoDTO));

			RestCommons::respondWithStatus(200, $equipoDTO->getObjectVars());
		} catch (Exception $e) {
			error_log($e->getMessage());
			RestCommons::respondWithStatus(500);
		}
	}
}

(new RESTEndpointClient(array('POST'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new EquipoComputoAlta);
