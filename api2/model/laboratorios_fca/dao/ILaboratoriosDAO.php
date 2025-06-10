<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dto/LaboratorioDTO.php";

interface ILaboratoriosDAO
{
	public function laboratoriosFCA(): array;
	public function laboratoriosFCAIn(array $in): array;
	public function laboratoriosFCAClaveStartsWith(string $with): array;
	public function getLaboratorioPorId(int $saloId)/* : LaboratorioDTO */;
	public function getDisponibilidad(array $diasSemana, array $labIds): array;
	public function getLaboratoriosMapPorId(array $labIds): array;
	public function getLaboratoriosGruposAlDia(array $diasSemana, int $idLab): array;
}
