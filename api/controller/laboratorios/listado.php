<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/impl/LaboratoriosDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dto/LaboratorioDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class LaboratoriosListado extends BaseHandler
{
	public function handle()
	{
		try {
			$laboratoriosDAO = new LaboratoriosDAO(LaboratoriosFCABD::getInstance());

			$laboratorios = array();

			foreach ($laboratoriosDAO->laboratoriosFCA() as $laboratorioDTO) {
				$laboratorios[] = $laboratorioDTO->getObjectVars();
			}

			RestCommons::respondWithStatus(200, $laboratorios);
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('GET'), array(TipoUsuarioEnum::ALL)))
	->execute(new LaboratoriosListado);
