<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/impl/GruposDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioSesionDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dto/ProfesorDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php';

class ProfesorGrupos extends BaseHandler
{
	public function handle()
	{
		try {
			$usuarioSesionDTO = RestCommons::getFromSession(RestCommons::USUARIO_SESION_DTO);
			$profesorDTO = $usuarioSesionDTO->getObjetoUsuario();

			$gruposDAO = new GruposDAO(LaboratoriosFCABD::getInstance());

			$todosLosGrupos = array();
			$grupos = array();
			$gruposP = array();
			$gruposOT = array();

			foreach ($gruposDAO->gruposProfesor($profesorDTO->getProfIdProfesor()) as $grupoDTO) {
				$grupos[] = $grupoDTO->getObjectVars();
			}

			foreach ($gruposDAO->grupos_pProfesor($profesorDTO->getProfIdProfesor()) as $grupoDTO) {
				$gruposP[] = $grupoDTO->getObjectVars();
			}

			foreach ($gruposDAO->grupos_otProfesor($profesorDTO->getProfIdProfesor()) as $grupoDTO) {
				$gruposOT[] = $grupoDTO->getObjectVars();
			}

			$todosLosGrupos['grupos'] = $grupos;
			$todosLosGrupos['gruposP'] = $gruposP;
			$todosLosGrupos['gruposOT'] = $gruposOT;

			RestCommons::respondWithStatus(200, $todosLosGrupos);
		} catch (Exception $e) {
			error_log($e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('GET'), array(TipoUsuarioEnum::PROFESOR)))
	->execute(new ProfesorGrupos);
