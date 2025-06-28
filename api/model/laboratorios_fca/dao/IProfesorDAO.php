<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dto/ProfesorDTO.php";

interface IProfesorDAO
{
	public function inciarSesion(string $usuario, string $contrasena)/* : ProfesorDTO */;
	public function profesoresMap(array $ids): array;
}
