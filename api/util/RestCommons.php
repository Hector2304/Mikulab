<?php

class RestCommons
{
	/**
	 * CORS sólo en desarrollo
	 */
	public static function cors()
{
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    // Permitir origen específico (mejor que wildcard si usas credentials)
    if ($origin === 'http://localhost:3000') {
        header("Access-Control-Allow-Origin: $origin");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Accept, X-Requested-With, Authorization");
    }

    // Responder rápido a OPTIONS
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}


	/**
	 * 
	 */
	public static function getRequestMethod()
	{
		return $_SERVER['REQUEST_METHOD'];
	}

	/**
	 * Lee JSON desde el cuerpo de la petición.
	 * Si es requerido, regresa el status 400 Bad Request.
	 * https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/400
	 */
	public static function readJSON(bool $required = false)
	{
		$jsonRaw = file_get_contents('php://input');
		$jsonData = json_decode($jsonRaw);

		if ($required && !$jsonData) {
			RestCommons::respondWithStatus(400, array(
				"errors" => ["EMTPY_BODY"]
			));
			exit;
		}

		return $jsonData;
	}

	/**
	 * Regresa JSON en la petición a partir de un array asociativo.
	 * Agrega el header Content-Type y la configuración adecuada para JSON.
	 */
	public static function respondJSON(array $assocArray)
	{
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode($assocArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
		exit;
	}

	/**
	 * https://developer.mozilla.org/en-US/docs/Web/HTTP/Status
	 */
	public static function respondWithStatus(int $status, array $assocArray = null)
	{
		http_response_code($status);
		if (!is_null($assocArray)) {
			RestCommons::respondJSON($assocArray);
		}
		exit;
	}

	/**
	 * Nombre de objetos en sesión
	 */
	const USUARIO_SESION_DTO = 'USUARIO_SESION_DTO';

	/**
	 * Iniciar el uso de sesión.
	 */
	public static function startSession()
	{
		session_start();
	}

	/**
	 * Terminar el uso de sesión.
	 */
	public static function endSession()
	{
		session_destroy();
	}

	/**
	 * Agregar un atributo a la sesión.
	 */
	public static function setToSession(string $name, $any)
	{
		$_SESSION[$name] = $any;
	}

	/**
	 * Agregar un atributo a la sesión.
	 */
	public static function getFromSession(string $name)
	{
		return $_SESSION[$name];
	}

	/**
	 * Remover un atributo a la sesión.
	 */
	public static function removeFromSession(string $name)
	{
		unset($_SESSION[$name]);
	}

	/**
	 * Verificar existencia en sesión.
	 */
	public static function isSessionSet(string $name): bool
	{
		return isset($_SESSION[$name]);
	}

	/**
	 * JSON de usuario en sesión.
	 */
	public static function userSessionJSON(string $personName, string $userType, string $username): array
	{
		return array(
			"personName" => $personName,
			"userType" => $userType,
			"username" => $username,
		);
	}
}
