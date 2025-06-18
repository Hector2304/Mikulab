<?php

interface IHorarioBloqueadoDAO
{
	public function altaIndividual(string $fecha, string $hora, int $labId, string $motivo): bool;
	public function bajaIndividual(string $fecha, string $hora, int $labId): bool;
	public function consultaWeek(int $year, int $week, int $labId): array;
	public function consultaAllPerWeek(int $labId): array;
	public function consultaDay(string $fecha, array $labIds): array;
	public function bajaSemanal(int $year, int $week, int $labId): bool;

	public function alta(array $daysHours, string $from, string $to, string $motivo, array $mapaHorarios): bool;
}
