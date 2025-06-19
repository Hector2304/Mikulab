<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/BitacoraDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/BitacoraDetalleDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/BitacoraObservacionDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/BitacoraHistoricoDTO.php";

interface IBitacoraDAO
{
	public function listado(): array;
	public function alta(BitacoraDTO $BitacoraDTO): int;
	public function buscar(BitacoraDTO $BitacoraDTO): int;
	public function altaDetalle(int $idBitacora, array $labs): bool;
	public function consultaDetalle(int $idBitacora): array;
	public function modificacionDetalle(array $detalle): bool;
	public function consultaObservaciones(int $idBitacora): array;
	public function altaObservacion(BitacoraObservacionDTO $BitacoraDTO): int;
	public function bajaObservacion(BitacoraObservacionDTO $BitacoraDTO): bool;
	public function altaHistorico(BitacoraHistoricoDTO $BitacoraDTO): int;
	public function consultaHistorio(int $idBitacora): array;
}
