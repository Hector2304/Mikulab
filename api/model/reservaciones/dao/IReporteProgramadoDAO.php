<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/ReporteProgramadoDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/ReporteProgramadoDetalleDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/ReporteProgramadoObservacionDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/ReporteProgramadoHistoricoDTO.php";

interface IReporteProgramadoDAO
{
	public function listado(): array;
	public function alta(ReporteProgramadoDTO $reprDTO): int;
	public function buscar(ReporteProgramadoDTO $reprDTO): int;
	public function altaDetalle(int $idReporte, array $detalle): bool;
	public function altaDetalleGruposFCA(int $idReporte, array $detalle): bool;
	public function consultaDetalle(int $idReporte): array;
	public function modificacionDetalle(array $detalle): bool;
	public function consultaObservaciones(int $idRepr): array;
	public function altaObservacion(ReporteProgramadoObservacionDTO $obsDTO): int;
	public function bajaObservacion(ReporteProgramadoObservacionDTO $obsDTO): bool;
	public function altaHistorico(ReporteProgramadoHistoricoDTO $hisoricoDTO): int;
	public function consultaHistorio(int $idRepr): array;
}
