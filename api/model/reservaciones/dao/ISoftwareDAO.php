<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/SoftwareDTO.php";

interface ISoftwareDAO
{
	public function alta(SoftwareDTO $softwareDTO): int;
	public function baja(SoftwareDTO $softwareDTO): bool;
	public function consultaAll(): array;
	public function modificacion(SoftwareDTO $softwareDTO): bool;
	public function porLaboratorio(int $labId): array;
	public function asignarALaboratorio(int $labId, int $softwareId): bool;
	public function removerDeLaboratorio(int $labId, int $softwareId): bool;
	public function obtenerAsociacionCompleta(bool $porLaboratorio): array;
}
