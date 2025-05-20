<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/HorarioDTO.php";

interface IHorarioDAO
{
	public function getAllIn(array $ids): array;
	public function getHoraIniIdTipoUno(): array;
	public function getIdsByTuples(array $tuples): array;
}
