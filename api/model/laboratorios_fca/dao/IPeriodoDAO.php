<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dto/PeriodoDTO.php";

interface IPeriodoDAO
{
	public function periodoPorFecha(string $fecha)/* : PeriodoDTO */;
}
