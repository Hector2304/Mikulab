<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/impl/LaboratoriosDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dto/LaboratorioDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php";

class EquipoComputoLaboratorioGet extends BaseHandler
{
	public function handle()
	{
		try {
			$labId = $_GET["lab-id"];

			if ($labId == null || empty($labId)) {
				RestCommons::respondWithStatus(400, array(
					"errors" => ["ID_EMPTY"]
				));
			}

			$laboratoriosDAO = new LaboratoriosDAO(LaboratoriosFCABD::getInstance());

			$laboratorioDTO = $laboratoriosDAO->getLaboratorioPorId((int)$labId);

			if ($laboratorioDTO == null) {
				RestCommons::respondWithStatus(404, array("error" => "LABORATORIO_NOT_FOUND"));
			}

			RestCommons::respondWithStatus(200, $laboratorioDTO->getObjectVars());
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('GET'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new EquipoComputoLaboratorioGet);
