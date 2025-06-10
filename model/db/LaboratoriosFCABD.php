<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/laboratorios_fca_config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/IConexion.php";

class LaboratoriosFCABD implements IConexion
{
	private static $instance;

	// Singleton
	public static function getInstance()
	{
		if (!LaboratoriosFCABD::$instance instanceof self) {
			LaboratoriosFCABD::$instance = new self();
		}
		return LaboratoriosFCABD::$instance;
	}
	private function __clone()
	{
	}
	private function __construct()
	{
		$this->conectar();
	}

	private $conexion;

	public function conectar()
	{
		try {
			$this->conexion = new PDO(
				"pgsql:" .
					"host=" . LABO_BD_HOST .
					";port=" . LABO_BD_PORT .
					";dbname=" . LABO_BD_DB_NAME,
				LABO_BD_USER,
				LABO_BD_PASS,
				[
					PDO::ATTR_CASE => PDO::CASE_NATURAL,
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL
				]
			);

			return $this->conexion;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function desconectar()
	{
		$this->conexion = null;
	}

	public function getConexion()
	{
		return $this->conexion;
	}

	public function __destruct()
	{
		$this->desconectar();
	}
}
