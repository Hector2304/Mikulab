<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/ReservacionesBD.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dao/AbstractDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/IReporteProgramadoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/ReporteProgramadoDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/ReporteProgramadoDetalleDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/ReporteProgramadoObservacionDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/ReporteProgramadoHistoricoDTO.php";

class ReporteProgramadoDAO extends AbstractDAO implements IReporteProgramadoDAO
{
	protected $tablaReporteProgramado = PG_SCHEMA . "reporte_programado";
	protected $colReprIdReporteProgramado = "repr_id_reporte_programado";
	protected $colReprFecha = "repr_fecha";

	protected $tablaReporteProgramadoDetalle = PG_SCHEMA . "reporte_programado_detalle";
	protected $colRepdIdDetalle = "repd_id_detalle";
	protected $colRepdIdReporteProgramado = "repd_id_reporte_programado";
	protected $colRepdIdLaboratorio = "repd_id_laboratorio";
	protected $colRepdIdGrupo = "repd_id_grupo";
	protected $colRepdTipoGrupo = "repd_tipo_grupo";
	protected $colRepdIdHorario = "repd_id_horario";
	protected $colRepdHoraApertura = "repd_hora_apertura";
	protected $colRepdAsistencia = "repd_asistencia";

	protected $tablaReporteProgramadoObservacion = PG_SCHEMA . "reporte_programado_observacion";
	protected $colRepoIdObservacion = "repo_id_observacion";
	protected $colRepoIdDetalle = "repo_id_detalle";
	protected $colRepoIdUsuario = "repo_id_usuario";
	protected $colRepoObservacion = "repo_observacion";
	protected $colRepoFechaHora = "repo_fecha_hora";

	protected $tablaReporteProgramadoHistorico = PG_SCHEMA . "reporte_programado_historico";
	protected $colRephIdHistorico = "reph_id_historico";
	protected $colRephIdProgramado = "reph_id_programado";
	protected $colRephIdUsuario = "reph_id_usuario";
	protected $colRephFechaHora = "reph_fecha_hora";
	protected $colRephAccion = "reph_accion";

	public function listado(): array
	{
		try {
			$listado = array();

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"SELECT * FROM "
					. $this->tablaReporteProgramado
					. " ORDER BY "
					. $this->colReprFecha . " DESC"
			);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$repr = new ReporteProgramadoDTO;
						$repr->setReprIdReporteProgramado($f[$this->colReprIdReporteProgramado]);
						$repr->setReprFecha($f[$this->colReprFecha]);

						$listado[] = $repr;
					}
				}
			}

			return $listado;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function alta(ReporteProgramadoDTO $reprDTO): int
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"INSERT INTO " . $this->tablaReporteProgramado
					. " ("
					. $this->colReprFecha
					. ")" .
					" VALUES (:fecha)"
			);

			$stmt->bindParam(":fecha", $reprDTO->getReprFecha(), PDO::PARAM_STR);

			if ($stmt->execute()) {
				return intval($conn->lastInsertId());
			}

			throw new Exception("No Insertado");
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function buscar(ReporteProgramadoDTO $reprDTO): int
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"SELECT * FROM " . $this->tablaReporteProgramado
					. " WHERE "
					. $this->colReprFecha . " = :fecha"
			);

			$stmt->bindParam(":fecha", $reprDTO->getReprFecha(), PDO::PARAM_STR);

			if ($stmt->execute()) {
				$fetched = $stmt->fetch(PDO::FETCH_ASSOC);

				if ($fetched) {
					return $fetched[$this->colReprIdReporteProgramado];
				}
			}

			return -1;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function altaDetalle(int $idReporte, array $detalle): bool
	{
		try {
			$vp = "(?, ?, ?, ?, ?, '06:30')";

			$values  = str_repeat($vp . ",", count($detalle) - 1) . $vp;

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"INSERT INTO reporte_programado_detalle(
					repd_id_reporte_programado,
					repd_id_laboratorio,
					repd_id_grupo,
					repd_tipo_grupo,
					repd_id_horario,
					repd_hora_apertura)
					VALUES " . $values
			);

			$params = 0;

			for ($i = 0; $i < count($detalle); $i++) {
				$stmt->bindValue(++$params, $idReporte, PDO::PARAM_INT);
				$stmt->bindValue(++$params, $detalle[$i]->getRepdIdLaboratorio(), PDO::PARAM_INT);
				$stmt->bindValue(++$params, $detalle[$i]->getRepdIdGrupo(), PDO::PARAM_INT);
				$stmt->bindValue(++$params, $detalle[$i]->getRepdTipoGrupo(), PDO::PARAM_STR);
				$stmt->bindValue(++$params, $detalle[$i]->getRepdIdHorario(), PDO::PARAM_INT);
			}

			if ($stmt->execute()) {
				return intval($conn->lastInsertId());
			}

			throw new Exception("No Insertado");
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function altaDetalleGruposFCA(int $idReporte, array $detalle): bool
	{
		try {
			$vp = "(?, ?, ?, ?, COALESCE((SELECT hora_id_horario FROM horario WHERE hora_ini = ? AND hora_fin = ? LIMIT 1), 1), '06:30')";

			$values  = str_repeat($vp . ",", count($detalle) - 1) . $vp;

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"INSERT INTO reporte_programado_detalle(
					repd_id_reporte_programado,
					repd_id_laboratorio,
					repd_id_grupo,
					repd_tipo_grupo,
					repd_id_horario,
					repd_hora_apertura)
					VALUES " . $values
			);

			$params = 0;

			for ($i = 0; $i < count($detalle); $i++) {
				$stmt->bindValue(++$params, $idReporte, PDO::PARAM_INT);
				$stmt->bindValue(++$params, $detalle[$i]['idSalon'], PDO::PARAM_INT);
				$stmt->bindValue(++$params, $detalle[$i]['idGrupo'], PDO::PARAM_INT);
				$stmt->bindValue(++$params, $detalle[$i]['tipoGrupo'], PDO::PARAM_STR);
				$stmt->bindValue(++$params, $detalle[$i]['horaIni'], PDO::PARAM_STR);
				$stmt->bindValue(++$params, $detalle[$i]['horaFin'], PDO::PARAM_STR);
			}

			if ($stmt->execute()) {
				return intval($conn->lastInsertId());
			}

			throw new Exception("No Insertado");
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function consultaDetalle(int $idReporte): array
	{
		try {
			$listado = array();

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"SELECT * FROM "
					. $this->tablaReporteProgramadoDetalle
					. " WHERE " . $this->colRepdIdReporteProgramado . " = :idReporte"
					. " ORDER BY "
					. $this->colRepdIdLaboratorio . ", "
					. $this->colRepdIdHorario
			);

			$stmt->bindParam(":idReporte", $idReporte, PDO::PARAM_INT);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$detalle = new ReporteProgramadoDetalleDTO;
						$detalle->setRepdIdDetalle($f[$this->colRepdIdDetalle]);
						$detalle->setRepdIdReporteProgramado($f[$this->colRepdIdReporteProgramado]);
						$detalle->setRepdIdLaboratorio($f[$this->colRepdIdLaboratorio]);
						$detalle->setRepdIdGrupo($f[$this->colRepdIdGrupo]);
						$detalle->setRepdTipoGrupo($f[$this->colRepdTipoGrupo]);
						$detalle->setRepdIdHorario($f[$this->colRepdIdHorario]);
						$detalle->setRepdHoraApertura($f[$this->colRepdHoraApertura]);
						$detalle->setRepdAsistencia($f[$this->colRepdAsistencia]);

						$listado[] = $detalle;
					}
				}
			}

			return $listado;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function modificacionDetalle(array $detalle): bool
	{
		try {
			$vp = "(?,?,?)";
			$values  = str_repeat($vp . ",", count($detalle) - 1) . $vp;

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"UPDATE reporte_programado_detalle AS rpd SET
					repd_hora_apertura = unv.repd_hora_apertura,
					repd_asistencia = unv.repd_asistencia::boolean
					FROM (VALUES " . $values . ") AS unv(repd_id_detalle, repd_hora_apertura, repd_asistencia)
					WHERE unv.repd_id_detalle::integer = rpd.repd_id_detalle;"
			);

			$params = 0;

			for ($i = 0; $i < count($detalle); $i++) {
				$stmt->bindValue(++$params, $detalle[$i]->repdIdDetalle, PDO::PARAM_INT);
				$stmt->bindValue(++$params, $detalle[$i]->repdHoraApertura, PDO::PARAM_STR);
				$stmt->bindValue(++$params, $detalle[$i]->repdAsistencia, PDO::PARAM_BOOL);
			}

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function consultaObservaciones(int $idRepr): array
	{
		try {
			$mapa = array();

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"SELECT bco.*, u.*,
				to_char(timezone('America/Mexico_City', bco.repo_fecha_hora),'YYYY-MM-DD HH24:MI:SS') cdmx_date
				FROM reporte_programado_observacion bco
				LEFT JOIN usuario u ON u.usua_id_usuario = bco.repo_id_usuario
				LEFT JOIN reporte_programado_detalle bcd ON bcd.repd_id_detalle = bco.repo_id_detalle
				LEFT JOIN reporte_programado bc ON bc.repr_id_reporte_programado = bcd.repd_id_reporte_programado
				WHERE bc.repr_id_reporte_programado = :idRepr
				ORDER BY bco.repo_fecha_hora ASC"
			);

			$stmt->bindParam(":idRepr", $idRepr, PDO::PARAM_INT);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$detalleId = "" . $f["repo_id_detalle"];

						if (!is_array($mapa[$detalleId])) {
							$mapa[$detalleId] = array();
						}

						$mapa[$detalleId][] = array(
							"id" => $f["repo_id_observacion"],
							"observacion" => $f["repo_observacion"],
							"fecha" => $f["cdmx_date"],
							"usuario" => $f["usua_nombre"] . " " . $f["usua_apaterno"] . " " . $f["usua_amaterno"],
							"username" => $f["usua_usuario"]
						);
					}
				}
			}

			return $mapa;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function altaObservacion(ReporteProgramadoObservacionDTO $obsDTO): int
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"INSERT INTO "
					. $this->tablaReporteProgramadoObservacion . " ("
					. $this->colRepoIdDetalle . ","
					. $this->colRepoIdUsuario . ","
					. $this->colRepoObservacion
					. ") VALUES (:idDetalle, :idUsuario, :observacion)"
			);

			$stmt->bindParam(":idDetalle", $obsDTO->getRepoIdDetalle(), PDO::PARAM_INT);
			$stmt->bindParam(":idUsuario", $obsDTO->getRepoIdUsuario(), PDO::PARAM_INT);
			$stmt->bindParam(":observacion", $obsDTO->getRepoObservacion(), PDO::PARAM_STR);

			if ($stmt->execute()) {
				return intval($conn->lastInsertId());
			}

			throw new Exception("No Insertado");
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function bajaObservacion(ReporteProgramadoObservacionDTO $obsDTO): bool
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"DELETE FROM " . $this->tablaReporteProgramadoObservacion
					. " WHERE " . $this->colRepoIdObservacion . " = :id"
					. " AND " . $this->colRepoIdUsuario . " = :idUsuario"
			);

			$stmt->bindParam(":id", $obsDTO->getRepoIdObservacion(), PDO::PARAM_INT);
			$stmt->bindParam(":idUsuario", $obsDTO->getRepoIdUsuario(), PDO::PARAM_INT);

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function altaHistorico(ReporteProgramadoHistoricoDTO $hisoricoDTO): int
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"INSERT INTO " . $this->tablaReporteProgramadoHistorico
					. " ("
					. $this->colRephIdProgramado . ", "
					. $this->colRephIdUsuario . ", "
					. $this->colRephAccion
					. ")" .
					" VALUES (:idProgramado, :idUsuario, :accion)"
			);

			$stmt->bindParam(":idProgramado", $hisoricoDTO->getRephIdProgramado(), PDO::PARAM_INT);
			$stmt->bindParam(":idUsuario", $hisoricoDTO->getRephIdUsuario(), PDO::PARAM_INT);
			$stmt->bindParam(":accion", $hisoricoDTO->getRephAccion(), PDO::PARAM_STR);

			if ($stmt->execute()) {
				return intval($conn->lastInsertId());
			}

			throw new Exception("No Insertado");
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function consultaHistorio(int $idRepr): array
	{
		try {
			$listado = array();

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"SELECT bh.*, u.*,
				to_char(timezone('America/Mexico_City', bh.reph_fecha_hora),'YYYY-MM-DD HH24:MI:SS') cdmx_date
				FROM reporte_programado_historico bh
				LEFT JOIN usuario u ON u.usua_id_usuario = bh.reph_id_usuario
				WHERE bh.reph_id_programado = :idRepr
				ORDER BY bh.reph_fecha_hora ASC"
			);

			$stmt->bindParam(":idRepr", $idRepr, PDO::PARAM_INT);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$listado[] = array(
							"accion" => $f["reph_accion"],
							"fecha" => $f["cdmx_date"],
							"usuario" => $f["usua_nombre"] . " " . $f["usua_apaterno"] . " " . $f["usua_amaterno"],
							"username" => $f["usua_usuario"]
						);
					}
				}
			}

			return $listado;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}
}
