<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/IConexion.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/ReservacionesBD.php";

abstract class AbstractDAO
{
	protected $conexion;

	public function __construct(IConexion $iConexion)
	{
		$this->conexion = $iConexion;
	}
}
