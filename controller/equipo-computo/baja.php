<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/EquipoComputoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/EquipoComputoDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php";

class EquipoComputoBaja extends BaseHandler
{
	public function handle()
	{
		try {
			$jsonData = RestCommons::readJSON();
		
			if (!is_numeric($jsonData->eqcoIdEquipo)) {
				RestCommons::respondWithStatus(400, array(
					"errors" => ["ID_INVALID"]
				));
			}
		
			$equipoDAO = new EquipoComputoDAO(ReservacionesBD::getInstance());
		
			$equipoDTO = new EquipoComputoDTO;
			$equipoDTO->setEqcoIdEquipo($jsonData->eqcoIdEquipo);
		
			$equipoDAO->baja($equipoDTO);
		
			RestCommons::respondWithStatus(204);
		} catch (PDOException $e) {
			error_log($e->getMessage());
			RestCommons::respondWithStatus(400, array("error" => "INTEGRITY"));
		} catch (Exception $e) {
			error_log($e->getMessage());
			RestCommons::respondWithStatus(500);
		}
	}
}

(new RESTEndpointClient(array('DELETE'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new EquipoComputoBaja);