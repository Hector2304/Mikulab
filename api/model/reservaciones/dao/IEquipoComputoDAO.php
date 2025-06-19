<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/EquipoComputoDTO.php";

interface IEquipoComputoDAO
{
	public function alta(EquipoComputoDTO $equipoComputoDTO): int;
	public function baja(EquipoComputoDTO $equipoComputoDTO): bool;
	public function consultaAll(): array;
	public function consultaPorLabId(int $labId): array;
	public function consultaSinLab(): array;
	public function modificacion(EquipoComputoDTO $equipoComputoDTO): bool;
	public function asignarLaboratorio(array $equiposIds, int $labId): bool;
	public function removerLaboratorio(array $equiposIds): bool;
}
