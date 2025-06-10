<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/ReservacionDTO.php";

interface IReservacionDAO
{
	public function traslapos(int $horaIni, int $horaFin, string $fecha, int $labId): array;
	public function alta(string $horaIni, string $horaFin, string $fecha, int $profId, int $labId, int $grupoId, string $tipoGrupo): int;
	public function listadoPorProfesor(int $idProfesor): array;
	public function cancelar(int $idReservacion, int $idProfesor): bool;
	public function baja(int $idReservacion): bool;
	public function consultaWeek(int $year, int $week, int $labId): array;
	public function consultaExternoPerWeek(int $cursoId): array;
	public function consultaDay(string $fecha, array $labIds): array;
	public function bajaSemanal(int $year, int $week, int $labId, int $cursoId): bool;
	public function listadoPorFecha(array $fechas): array;
	public function listadoPorFechaYLaboratorio(array $fechas, int $idLab): array;
}
