<?php
ini_set("log_errors", 1);
ini_set("error_log", "C:/xampp/htdocs/api/logs/debug_sesion.log");

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/RestCommons.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/impl/ProfesorDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/impl/UsuarioDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioSesionDTO.php";

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/util/design-patterns/chain-of-responsibility/clients/RESTEndpointClient.php";
RestCommons::cors();


class Sesion extends BaseHandler
{
	public function handle()
	{
		try {
			error_log("===> Inicio del handler de sesión");
			RestCommons::startSession();

			if (RestCommons::getRequestMethod() == 'GET') {
				error_log("===> Método GET detectado");
				if (RestCommons::isSessionSet(RestCommons::USUARIO_SESION_DTO)) {
					error_log("===> Sesión encontrada");
					$usuarioSesionDTO = RestCommons::getFromSession(RestCommons::USUARIO_SESION_DTO);

					if ($usuarioSesionDTO->getTipoUsuario() == TipoUsuarioEnum::PROFESOR) {
						$profesorDTO = $usuarioSesionDTO->getObjetoUsuario();
						error_log("===> Usuario es profesor: " . $profesorDTO->getNombre());

						RestCommons::respondWithStatus(
							200,
							RestCommons::userSessionJSON(
								$profesorDTO->getNombre(),
								TipoUsuarioEnum::PROFESOR,
								$profesorDTO->getProfNumUnam()
							)
						);
					} else {
						$usuarioDTO = $usuarioSesionDTO->getObjetoUsuario();
						error_log("===> Usuario es general: " . $usuarioDTO->getUsuaUsuario());

						RestCommons::respondWithStatus(
							200,
							RestCommons::userSessionJSON(
								$usuarioDTO->getUsuaNombre() . " " . $usuarioDTO->getUsuaApaterno() . " " . $usuarioDTO->getUsuaAmaterno(),
								$usuarioDTO->getTiusNombre(),
								$usuarioDTO->getUsuaUsuario()
							)
						);
					}
				} else {
					error_log("===> No hay sesión activa");
					RestCommons::respondWithStatus(401);
				}
			}

			else if (RestCommons::getRequestMethod() == 'POST') {
				error_log("===> Método POST detectado");

				if (RestCommons::isSessionSet(RestCommons::USUARIO_SESION_DTO)) {
	RestCommons::removeFromSession(RestCommons::USUARIO_SESION_DTO);
}


				$jsonData = RestCommons::readJSON();
				error_log("===> JSON recibido: " . json_encode($jsonData));

				$usuarioDAO = new UsuarioDAO(ReservacionesBD::getInstance());
				error_log("===> UsuarioDAO instanciado");

				$usuarioDTO = $usuarioDAO->inciarSesion($jsonData->usuario);
				error_log("===> Usuario encontrado: " . ($usuarioDTO ? "sí" : "no"));

				if ($usuarioDTO != null) {
					error_log("===> Tipo usuario: " . $usuarioDTO->getTiusNombre());

					if ($usuarioDTO->getTiusNombre() == TipoUsuarioEnum::VIGILANTE) {
						error_log("===> Usuario es VIGILANTE. Acceso denegado.");
						RestCommons::respondWithStatus(401);
						exit;
					}

				if ($usuarioDTO->getUsuaStatus() == 'A' && $jsonData->contrasena === $usuarioDTO->getUsuaContrasena()) {
						error_log("===> Contraseña válida. Usuario activo.");
						$usuarioSesionDTO = new UsuarioSesionDTO;
						$usuarioSesionDTO->setObjetoUsuario($usuarioDTO);
						$usuarioSesionDTO->setTipoUsuario($usuarioDTO->getTiusNombre());

						RestCommons::setToSession(RestCommons::USUARIO_SESION_DTO, $usuarioSesionDTO);
						RestCommons::respondWithStatus(
							200,
							RestCommons::userSessionJSON(
								$usuarioDTO->getUsuaNombre() . " " . $usuarioDTO->getUsuaApaterno() . " " . $usuarioDTO->getUsuaAmaterno(),
								$usuarioDTO->getTiusNombre(),
								$usuarioDTO->getUsuaUsuario()
							)
						);
					} else {
						error_log("===> Contraseña inválida o usuario inactivo");
						RestCommons::respondWithStatus(401);
						exit;
					}
				} else {
					error_log("===> Usuario no encontrado. Intentando como profesor...");
					$profesorDAO = new ProfesorDAO(LaboratoriosFCABD::getInstance());
					error_log("===> ProfesorDAO instanciado");

					$profesorDTO = $profesorDAO->inciarSesion($jsonData->usuario, $jsonData->contrasena);
					error_log("===> Profesor encontrado: " . ($profesorDTO ? "sí" : "no"));

					if ($profesorDTO == null) {
						error_log("===> Profesor no encontrado");
						RestCommons::respondWithStatus(401);
						exit;
					}

					error_log("===> Login exitoso como profesor: " . $profesorDTO->getNombre());

					$usuarioSesionDTO = new UsuarioSesionDTO;
					$usuarioSesionDTO->setObjetoUsuario($profesorDTO);
					$usuarioSesionDTO->setTipoUsuario(TipoUsuarioEnum::PROFESOR);

					RestCommons::setToSession(RestCommons::USUARIO_SESION_DTO, $usuarioSesionDTO);
					RestCommons::respondWithStatus(
						200,
						RestCommons::userSessionJSON(
							$profesorDTO->getNombre(),
							TipoUsuarioEnum::PROFESOR,
							$profesorDTO->getProfNumUnam()
						)
					);
				}
			}

			else {
				error_log("===> Método DELETE detectado. Cerrando sesión.");
				RestCommons::removeFromSession(RestCommons::USUARIO_SESION_DTO);
				RestCommons::endSession();
				RestCommons::respondWithStatus(200);
			}
		} catch (Exception $e) {
			error_log("===> EXCEPCIÓN: " . $e->getMessage());
			http_response_code(500);
		}
	}
}

(new RESTEndpointClient(array('GET', 'POST', 'DELETE')))
	->execute(new Sesion);
