<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/CursoExternoDTO.php";

interface ICursoExternoDAO
{
	public function alta(CursoExternoDTO $cursoDTO): int;
	public function consultaAll(): array;
	public function modificacion(CursoExternoDTO $cursoDTO): bool;
	public function consultaPorInstructorId(int $insId): array;
	public function consultaSinInstructor(): array;
	public function consultaInJoinInstructorMap(array $ids): array;
	public function asignarInstructor(array $cursosIds, int $insId): bool;
	public function removerInstructor(array $cursosIds): bool;
}
