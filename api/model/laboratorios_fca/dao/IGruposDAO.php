<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dto/GrupoDTO.php";

interface IGruposDAO
{
	public function gruposProfesor(int $idProfesor): array;
	public function grupos_pProfesor(int $idProfesor): array;
	public function grupos_otProfesor(int $idProfesor): array;
	public function gruposPorIdMap(array $ids): array;
	public function grupos_pPorIdMap(array $ids): array;
	public function grupos_otPorIdMap(array $ids): array;
}
