<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/ReservacionDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/ReservacionDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioSesionDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dto/ProfesorDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/impl/LaboratoriosDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/impl/GruposDAO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ProfesorReservaListado extends BaseHandler
{
	public function handle()
	{
		try {
			$usuarioSesionDTO = RestCommons::getFromSession(RestCommons::USUARIO_SESION_DTO);
			$profesorDTO = $usuarioSesionDTO->getObjetoUsuario();

			$labIds = array();
			$gruposLIds = array();
			$gruposPIds = array();
			$gruposTIds = array();

			$response = array();
			$reservaciones = array();

			$reservacionDAO = new ReservacionDAO(ReservacionesBD::getInstance());
			foreach ($reservacionDAO->listadoPorProfesor($profesorDTO->getProfIdProfesor()) as $reservacionDTO) {
				$reservaciones[] = $reservacionDTO->getObjectVars();
				$labIds[] = $reservacionDTO->getReseIdLaboratorio();

				if ($reservacionDTO->getReseTipoGrupo() == ReservacionDTO::GRUPO_LICENCIATURA) {
					$gruposLIds[] = $reservacionDTO->getReseIdGrupo();
				} else if ($reservacionDTO->getReseTipoGrupo() == ReservacionDTO::GRUPO_POSGRADO) {
					$gruposPIds[] = $reservacionDTO->getReseIdGrupo();
				} else if ($reservacionDTO->getReseTipoGrupo() == ReservacionDTO::GRUPO_OPCION_TITULACION) {
					$gruposTIds[] = $reservacionDTO->getReseIdGrupo();
				}
			}
			$response['reservaciones'] = $reservaciones;

			$laboratoriosDAO = new LaboratoriosDAO(LaboratoriosFCABD::getInstance());
			$response['laboratorios'] = $laboratoriosDAO->getLaboratoriosMapPorId($labIds);

			$gruposDAO = new GruposDAO(LaboratoriosFCABD::getInstance());
			if (count($gruposLIds) > 0) {
				$response['gruposL'] = $gruposDAO->gruposPorIdMap($gruposLIds);
			}
			if (count($gruposPIds) > 0) {
				$response['gruposP'] = $gruposDAO->grupos_pPorIdMap($gruposPIds);
			}
			if (count($gruposTIds) > 0) {
				$response['gruposT'] = $gruposDAO->grupos_otPorIdMap($gruposTIds);
			}

			RestCommons::respondWithStatus(200, $response);
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('GET'), array(TipoUsuarioEnum::PROFESOR)))
	->execute(new ProfesorReservaListado);
