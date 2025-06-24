<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/ReservacionDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/impl/LaboratoriosDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dto/LaboratorioDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class AsignacionHorarioListado extends BaseHandler
{
	public function handle()
	{
		try {
			$cursoId = $_GET["curso-id"];
			
			if ($cursoId == null || empty($cursoId)) {
				RestCommons::respondWithStatus(400, array(
					"errors" => ["ID_EMPTY"]
				));
			}

			$reservacionDAO = new ReservacionDAO(ReservacionesBD::getInstance());

			$semanas = $reservacionDAO->consultaExternoPerWeek((int)$cursoId);

			if (count($semanas) > 0) {
				$labIds = array();

				foreach ($semanas as $week) {
					$labIds[] = $week['labId'];
				}
				
				$laboratoriosDAO = new LaboratoriosDAO(LaboratoriosFCABD::getInstance());
				$labs = $laboratoriosDAO->laboratoriosFCAIn($labIds);
				
				for ($i = 0; $i < count($semanas); $i++) {
					if ($labs[$semanas[$i]['labId']]) {
						$semanas[$i]['lab'] = $labs[$semanas[$i]['labId']]->getObjectVars();
					}
				}
			}

			RestCommons::respondWithStatus(200, $semanas);
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('GET'), array(TipoUsuarioEnum::SUPERUSUARIO)))
	->execute(new AsignacionHorarioListado);
