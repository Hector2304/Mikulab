<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/ReservacionesBD.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dao/AbstractDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/IBitacoraDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/BitacoraDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/BitacoraDetalleDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/BitacoraObservacionDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/BitacoraHistoricoDTO.php";

class BitacoraDAO extends AbstractDAO implements IBitacoraDAO
{
	protected $tablaBitacora = PG_SCHEMA . "bitacora";
	protected $colBitaIdBitacora = "bita_id_bitacora";
	protected $colBitaFecha = "bita_fecha";
	protected $colBitaTipo = "bita_tipo";
	protected $colBitaTipoLab = "bita_tipo_lab";

	protected $tablaBitacoraDetalle = PG_SCHEMA . "bitacora_detalle";
	protected $colBideIdDetalle = "bide_id_detalle";
	protected $colBideIdBitacora = "bide_id_bitacora";
	protected $colBideIdLaboratorio = "bide_id_laboratorio";
	protected $colBideMonitor = "bide_monitor";
	protected $colBideCpu = "bide_cpu";
	protected $colBideTeclado = "bide_teclado";
	protected $colBideMouse = "bide_mouse";
	protected $colBideVideoProyector = "bide_video_proyector";
	protected $colBideCableDport = "bide_cable_dport";
	protected $colBideControlCanon = "bide_control_canon";
	protected $colBideControlAire = "bide_control_aire";
	protected $colBideHoraApertura = "bide_hora_apertura";
	protected $colBideHoraCierre = "bide_hora_cierre";
	protected $colBideVigilante = "bide_vigilante";

	protected $tablaBitacoraObservacion = PG_SCHEMA . "bitacora_observacion";
	protected $colBiobIdObservacion = "biob_id_observacion";
	protected $colBiobIdDetalle = "biob_id_detalle";
	protected $colBiobIdUsuario = "biob_id_usuario";
	protected $colBiobObservacion = "biob_observacion";
	protected $colBiobFechaHora = "biob_fecha_hora";

	protected $tablaBitacoraHistorico = PG_SCHEMA . "bitacora_historico";
	protected $colBihiIdHistorico = "bihi_id_historico";
	protected $colBihiIdBitacora = "bihi_id_bitacora";
	protected $colBihiIdUsuario = "bihi_id_usuario";
	protected $colBihiFechaHora = "bihi_fecha_hora";
	protected $colBihiAccion = "bihi_accion";

	public function listado(): array
	{
		try {
			$listado = array();

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"SELECT * FROM "
					. $this->tablaBitacora
					. " ORDER BY "
					. $this->colBitaFecha . " DESC"
			);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$bitacora = new BitacoraDTO;
						$bitacora->setBitaIdBitacora($f[$this->colBitaIdBitacora]);
						$bitacora->setBitaFecha($f[$this->colBitaFecha]);
						$bitacora->setBitaTipo($f[$this->colBitaTipo]);
						$bitacora->setBitaTipoLab($f[$this->colBitaTipoLab]);

						$listado[] = $bitacora;
					}
				}
			}

			return $listado;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function alta(BitacoraDTO $BitacoraDTO): int
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"INSERT INTO " . $this->tablaBitacora
					. " ("
					. $this->colBitaFecha . ", "
					. $this->colBitaTipo . ", "
					. $this->colBitaTipoLab
					. ")" .
					" VALUES (:fecha, :tipo, :tipoLab)"
			);

			$stmt->bindParam(":fecha", $BitacoraDTO->getBitaFecha(), PDO::PARAM_STR);
			$stmt->bindParam(":tipo", $BitacoraDTO->getBitaTipo(), PDO::PARAM_STR);
			$stmt->bindParam(":tipoLab", $BitacoraDTO->getBitaTipoLab(), PDO::PARAM_STR);

			if ($stmt->execute()) {
				return intval($conn->lastInsertId());
			}

			throw new Exception("No Insertado");
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function buscar(BitacoraDTO $BitacoraDTO): int
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"SELECT * FROM " . $this->tablaBitacora
					. " WHERE "
					. $this->colBitaFecha . " = :fecha"
					. " AND "
					. $this->colBitaTipo . " = :tipo"
			);

			$stmt->bindParam(":fecha", $BitacoraDTO->getBitaFecha(), PDO::PARAM_STR);
			$stmt->bindParam(":tipo", $BitacoraDTO->getBitaTipo(), PDO::PARAM_STR);

			if ($stmt->execute()) {
				$fetched = $stmt->fetch(PDO::FETCH_ASSOC);

				if ($fetched) {
					return $fetched[$this->colBitaIdBitacora];
				}
			}

			return -1;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function altaDetalle(int $idBitacora, array $labs): bool
	{
		try {
			$vp = "(?, ?, ?, ?, ?, ?, 1, 1, 1, 1, '06:30', '22:30')";

			$values  = str_repeat($vp . ",", count($labs) - 1) . $vp;

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"INSERT INTO bitacora_detalle(
					bide_id_bitacora, bide_id_laboratorio,
					bide_monitor, bide_cpu, bide_teclado, bide_mouse,
					bide_video_proyector, bide_cable_dport, bide_control_canon, bide_control_aire,
					bide_hora_apertura, bide_hora_cierre)
					VALUES " . $values
			);

			$params = 0;

			for ($i = 0; $i < count($labs); $i++) {
				$stmt->bindValue(++$params, $idBitacora, PDO::PARAM_INT);
				$stmt->bindValue(++$params, $labs[$i]->getSaloIdSalon(), PDO::PARAM_INT);

				$cupo = $labs[$i]->getSaloCupo() != null ? $labs[$i]->getSaloCupo() : 1;

				$stmt->bindValue(++$params, $cupo, PDO::PARAM_INT);
				$stmt->bindValue(++$params, $cupo, PDO::PARAM_INT);
				$stmt->bindValue(++$params, $cupo, PDO::PARAM_INT);
				$stmt->bindValue(++$params, $cupo, PDO::PARAM_INT);
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

	public function consultaDetalle(int $idBitacora): array
	{
		try {
			$listado = array();

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"SELECT * FROM "
					. $this->tablaBitacoraDetalle
					. " WHERE " . $this->colBideIdBitacora . " = :idBitacora"
					. " ORDER BY "
					. $this->colBideIdLaboratorio
			);

			$stmt->bindParam(":idBitacora", $idBitacora, PDO::PARAM_INT);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$detalle = new BitacoraDetalleDTO;
						$detalle->setBideIdDetalle($f[$this->colBideIdDetalle]);
						$detalle->setBideIdBitacora($f[$this->colBideIdBitacora]);
						$detalle->setBideIdLaboratorio($f[$this->colBideIdLaboratorio]);
						$detalle->setBideMonitor($f[$this->colBideMonitor]);
						$detalle->setBideCpu($f[$this->colBideCpu]);
						$detalle->setBideTeclado($f[$this->colBideTeclado]);
						$detalle->setBideMouse($f[$this->colBideMouse]);
						$detalle->setBideVideoProyector($f[$this->colBideVideoProyector]);
						$detalle->setBideCableDport($f[$this->colBideCableDport]);
						$detalle->setBideControlCanon($f[$this->colBideControlCanon]);
						$detalle->setBideControlAire($f[$this->colBideControlAire]);
						$detalle->setBideHoraApertura($f[$this->colBideHoraApertura]);
						$detalle->setBideHoraCierre($f[$this->colBideHoraCierre]);
						$detalle->setBideVigilante($f[$this->colBideVigilante]);

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
			$vp = "(?,?,?,?,?,?,?,?,?,?,?,?)";
			$values  = str_repeat($vp . ",", count($detalle) - 1) . $vp;

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"UPDATE bitacora_detalle AS bcd SET
					bide_monitor = unv.bide_monitor::integer,
					bide_cpu = unv.bide_cpu::integer,
					bide_teclado = unv.bide_teclado::integer,
					bide_mouse = unv.bide_mouse::integer,
					bide_video_proyector = unv.bide_video_proyector::integer,
					bide_cable_dport = unv.bide_cable_dport::integer,
					bide_control_canon = unv.bide_control_canon::integer,
					bide_control_aire = unv.bide_control_aire::integer,
					bide_hora_apertura = unv.bide_hora_apertura,
					bide_hora_cierre = unv.bide_hora_cierre,
					bide_vigilante = unv.bide_vigilante::integer
					FROM (VALUES " . $values . ") AS unv(bide_id_detalle, bide_monitor, bide_cpu,
					bide_teclado, bide_mouse, bide_video_proyector,
					bide_cable_dport, bide_control_canon, bide_control_aire,
					bide_hora_apertura, bide_hora_cierre, bide_vigilante)
					WHERE unv.bide_id_detalle::integer = bcd.bide_id_detalle;"
			);

			$params = 0;

			for ($i = 0; $i < count($detalle); $i++) {
				$stmt->bindValue(++$params, $detalle[$i]->bideIdDetalle, PDO::PARAM_INT);
				$stmt->bindValue(++$params, $detalle[$i]->bideMonitor, PDO::PARAM_INT);
				$stmt->bindValue(++$params, $detalle[$i]->bideCpu, PDO::PARAM_INT);
				$stmt->bindValue(++$params, $detalle[$i]->bideTeclado, PDO::PARAM_INT);
				$stmt->bindValue(++$params, $detalle[$i]->bideMouse, PDO::PARAM_INT);
				$stmt->bindValue(++$params, $detalle[$i]->bideVideoProyector, PDO::PARAM_INT);
				$stmt->bindValue(++$params, $detalle[$i]->bideCableDport, PDO::PARAM_INT);
				$stmt->bindValue(++$params, $detalle[$i]->bideControlCanon, PDO::PARAM_INT);
				$stmt->bindValue(++$params, $detalle[$i]->bideControlAire, PDO::PARAM_INT);
				$stmt->bindValue(++$params, $detalle[$i]->bideHoraApertura, PDO::PARAM_STR);
				$stmt->bindValue(++$params, $detalle[$i]->bideHoraCierre, PDO::PARAM_STR);
				$stmt->bindValue(++$params, $detalle[$i]->bideVigilante, PDO::PARAM_INT);
			}

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function consultaObservaciones(int $idBitacora): array
	{
		try {
			$mapa = array();

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"SELECT bco.*, u.*,
				to_char(timezone('America/Mexico_City', bco.biob_fecha_hora),'YYYY-MM-DD HH24:MI:SS') cdmx_date
				FROM bitacora_observacion bco
				LEFT JOIN usuario u ON u.usua_id_usuario = bco.biob_id_usuario
				LEFT JOIN bitacora_detalle bcd ON bcd.bide_id_detalle = bco.biob_id_detalle
				LEFT JOIN bitacora bc ON bc.bita_id_bitacora = bcd.bide_id_bitacora
				WHERE bc.bita_id_bitacora = :idBitacora
				ORDER BY bco.biob_fecha_hora ASC"
			);

			$stmt->bindParam(":idBitacora", $idBitacora, PDO::PARAM_INT);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$detalleId = "" . $f["biob_id_detalle"];

						if (!is_array($mapa[$detalleId])) {
							$mapa[$detalleId] = array();
						}

						$mapa[$detalleId][] = array(
							"id" => $f["biob_id_observacion"],
							"observacion" => $f["biob_observacion"],
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

	public function altaObservacion(BitacoraObservacionDTO $BitacoraDTO): int
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"INSERT INTO "
					. $this->tablaBitacoraObservacion . " ("
					. $this->colBiobIdDetalle . ","
					. $this->colBiobIdUsuario . ","
					. $this->colBiobObservacion
					. ") VALUES (:idDetalle, :idUsuario, :observacion)"
			);

			$params = 0;

			$stmt->bindParam(":idDetalle", $BitacoraDTO->getBiobIdDetalle(), PDO::PARAM_INT);
			$stmt->bindParam(":idUsuario", $BitacoraDTO->getBiobIdUsuario(), PDO::PARAM_INT);
			$stmt->bindParam(":observacion", $BitacoraDTO->getBiobObservacion(), PDO::PARAM_STR);

			if ($stmt->execute()) {
				return intval($conn->lastInsertId());
			}

			throw new Exception("No Insertado");
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function bajaObservacion(BitacoraObservacionDTO $BitacoraDTO): bool
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"DELETE FROM " . $this->tablaBitacoraObservacion
					. " WHERE " . $this->colBiobIdObservacion . " = :id"
					. " AND " . $this->colBiobIdUsuario . " = :idUsuario"
			);

			$stmt->bindParam(":id", $BitacoraDTO->getBiobIdObservacion(), PDO::PARAM_INT);
			$stmt->bindParam(":idUsuario", $BitacoraDTO->getBiobIdUsuario(), PDO::PARAM_INT);

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function altaHistorico(BitacoraHistoricoDTO $BitacoraDTO): int
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"INSERT INTO " . $this->tablaBitacoraHistorico
					. " ("
					. $this->colBihiIdBitacora . ", "
					. $this->colBihiIdUsuario . ", "
					. $this->colBihiAccion
					. ")" .
					" VALUES (:idBitacora, :idUsuario, :accion)"
			);

			$stmt->bindParam(":idBitacora", $BitacoraDTO->getBihiIdBitacora(), PDO::PARAM_INT);
			$stmt->bindParam(":idUsuario", $BitacoraDTO->getBihiIdUsuario(), PDO::PARAM_INT);
			$stmt->bindParam(":accion", $BitacoraDTO->getBiobAccion(), PDO::PARAM_STR);

			if ($stmt->execute()) {
				return intval($conn->lastInsertId());
			}

			throw new Exception("No Insertado");
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function consultaHistorio(int $idBitacora): array
	{
		try {
			$listado = array();

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"SELECT bh.*, u.*,
				to_char(timezone('America/Mexico_City', bh.bihi_fecha_hora),'YYYY-MM-DD HH24:MI:SS') cdmx_date
				FROM bitacora_historico bh
				LEFT JOIN usuario u ON u.usua_id_usuario = bh.bihi_id_usuario
				LEFT JOIN bitacora bc ON bc.bita_id_bitacora = bh.bihi_id_bitacora
				WHERE bc.bita_id_bitacora = :idBitacora
				ORDER BY bh.bihi_fecha_hora ASC"
			);

			$stmt->bindParam(":idBitacora", $idBitacora, PDO::PARAM_INT);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$listado[] = array(
							"accion" => $f["bihi_accion"],
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
