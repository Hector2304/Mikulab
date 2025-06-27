<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/reservaciones_config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/IConexion.php";

class ReservacionesBD implements IConexion
{
	private static $instance;

	// Singleton
	public static function getInstance()
	{
		if (!ReservacionesBD::$instance instanceof self) {
			ReservacionesBD::$instance = new self();
		}
		return ReservacionesBD::$instance;
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
					"host=" . PG_HOST .
					";port=" . PG_PORT .
					";dbname=" . PG_DB_NAME,
				PG_USER,
				PG_PASS,
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

/*
class ReservacionesBD implements IConexion
{
	private static $instance;

	// Singleton
	public static function getInstance()
	{
		if (!ReservacionesBD::$instance instanceof self) {
			ReservacionesBD::$instance = new self();
		}
		return ReservacionesBD::$instance;
	}

	private $pgConn;

	private function __construct()
	{
		$this->conectar();
	}
	private function __clone()
	{
	}

	public function conectar()
	{
		try {
			$this->pgConn = @pg_connect(
				"host=" . PG_HOST .
					" port=" . PG_PORT .
					" dbname=" . PG_DB_NAME .
					" user=" . PG_USER .
					" password=" . PG_PASS
			);

			return $this->pgConn;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function desconectar()
	{
		try {
			if (isset($this->pgConn)) {
				@pg_close($this->pgConn);
			}
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function getConexion()
	{
		return $this->pgConn;
	}

	public function __destruct()
	{
		$this->desconectar();
	}
}
*/