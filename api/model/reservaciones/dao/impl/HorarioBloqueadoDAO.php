<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/ReservacionesBD.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dao/AbstractDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/IHorarioBloqueadoDAO.php";

class HorarioBloqueadoDAO extends AbstractDAO implements IHorarioBloqueadoDAO
{
	protected $tabla = PG_SCHEMA . "horario_bloqueado";
	protected $colFecha = "hobl_fecha";
	protected $colIdHorario = "hobl_id_horario";
	protected $colIdLaboratorio = "hobl_id_laboratorio";
	protected $colMotivo = "hobl_motivo";

	public function __construct(IConexion $iConexion)
	{
		parent::__construct($iConexion);
	}

	public function altaIndividual(string $fecha, string $hora, int $labId, string $motivo = null): bool
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"WITH
				slct_horario AS (SELECT hora_id_horario FROM horario WHERE hora_ini = :hora AND hora_tipo = '1' LIMIT 1)
				INSERT INTO horario_bloqueado(hobl_id_horario, hobl_fecha, hobl_id_laboratorio, hobl_motivo)
				VALUES ((SELECT hora_id_horario FROM slct_horario), :fecha, :labId, :motivo)
				ON CONFLICT ON CONSTRAINT horario_bloqueado_pkey DO UPDATE SET hobl_motivo = EXCLUDED.hobl_motivo"
			);

			$stmt->bindValue(":hora", $hora, PDO::PARAM_STR);
			$stmt->bindValue(":fecha", $fecha, PDO::PARAM_STR);
			$stmt->bindValue(":labId", $labId, PDO::PARAM_INT);
			$stmt->bindValue(":motivo", $motivo, PDO::PARAM_STR);

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function bajaIndividual(string $fecha, string $hora, int $labId): bool
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"WITH
				slct_horario AS (SELECT hora_id_horario FROM horario WHERE hora_ini = :hora AND hora_tipo = '1' LIMIT 1)
				DELETE FROM horario_bloqueado
				WHERE hobl_id_horario = (SELECT hora_id_horario FROM slct_horario)
				AND hobl_fecha = :fecha
				AND hobl_id_laboratorio = :labId"
			);

			$stmt->bindValue(":hora", $hora, PDO::PARAM_STR);
			$stmt->bindValue(":fecha", $fecha, PDO::PARAM_STR);
			$stmt->bindValue(":labId", $labId, PDO::PARAM_INT);

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function consultaWeek(int $year, int $week, int $labId): array
	{
		try {
			$horarioSemana = array();

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare("SELECT hb.*, h.*, date_part('dow', hb.hobl_fecha::date) dow
				FROM horario_bloqueado hb
				JOIN horario h ON hb.hobl_id_horario = h.hora_id_horario
				WHERE hb.hobl_id_laboratorio = :labId
				AND date_part('year', hb.hobl_fecha::date) = :yyear
				AND date_part('week', hb.hobl_fecha::date) = :wweek");

			$stmt->bindParam(":labId", $labId, PDO::PARAM_INT);
			$stmt->bindParam(":yyear", $year, PDO::PARAM_INT);
			$stmt->bindParam(":wweek", $week, PDO::PARAM_INT);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$horarioSemana[] = array(
							"day" => $this->getDia($f['dow']),
							"hour" => ($f['hora_ini'] / 100),
							"motivo" => $f['hobl_motivo']
						);
					}
				}
			}

			return $horarioSemana;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function consultaAllPerWeek(int $labId): array
	{
		try {
			$semanas = array();

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare("SELECT
				date_part('year', hobl_fecha::date) AS _year,
				date_part('week', hobl_fecha::date) AS _week
				FROM horario_bloqueado
				WHERE hobl_id_laboratorio = :labId
				GROUP BY _year, _week
				ORDER BY _year DESC, _week DESC");

			$stmt->bindParam(":labId", $labId, PDO::PARAM_INT);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$semanas[] = array(
							"year" => $f['_year'],
							"week" => $f['_week']
						);
					}
				}
			}

			return $semanas;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function consultaDay(string $fecha, array $labIds): array
	{
		try {
			$horarioSemana = array();

			$in  = str_repeat('?,', count($labIds) - 1) . '?';

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare("SELECT hb.*, h.*, date_part('dow', hb.hobl_fecha::date) dow
				FROM horario_bloqueado hb
				JOIN horario h ON hb.hobl_id_horario = h.hora_id_horario
				WHERE hb.hobl_id_laboratorio IN (" . $in . ")
				AND hb.hobl_fecha = ?");

			$param = 0;

			for ($i = 0; $i < count($labIds); $i++) {
				$stmt->bindParam(++$param, $labIds[$i], PDO::PARAM_INT);
			}

			$stmt->bindParam(++$param, $fecha, PDO::PARAM_STR);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$horarioSemana[] = array(
							"hour" => ($f['hora_ini'] / 100),
							"labId" => $f['hobl_id_laboratorio']
						);
					}
				}
			}

			return $horarioSemana;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function bajaSemanal(int $year, int $week, int $labId): bool
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"DELETE FROM " . $this->tabla
					. " WHERE date_part('year', hobl_fecha::date) = :yyear"
					. " AND date_part('week', hobl_fecha::date) = :wweek"
					. " AND hobl_id_laboratorio = :labId"
			);

			$stmt->bindParam(":yyear", $year, PDO::PARAM_INT);
			$stmt->bindParam(":wweek", $week, PDO::PARAM_INT);
			$stmt->bindParam(":labId", $labId, PDO::PARAM_INT);

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function alta(array $daysHours, string $from, string $to, string $motivo = null, array $mapaHorarios): bool
	{
		try {
			$interval1D = new DateInterval('P1D');
			$dateFrom = $this->newDateTime($from);
			$dateTo = $this->newDateTime($to);

			if ($dateFrom > $dateTo) {
				$ftTemp = $dateFrom;
				$dateFrom = $dateTo;
				$dateTo = $ftTemp;
			}

			$_dFrom = $this->newDateTime($dateFrom->format('Y-m-d'));
			$totalHoras = 0;
			while ($_dFrom <= $dateTo) {
				$dia = $this->getDia($_dFrom->format('N'));
				$_dFrom->add($interval1D);

				if ($daysHours[$dia] != null) {
					$totalHoras += count($daysHours[$dia]);
				}
			}

			$select = "INSERT INTO " . $this->tabla . "("
				. $this->colFecha . ", " . $this->colIdHorario . ", " . $this->colMotivo
				. ") VALUES "
				. str_repeat('(?,?,?),', $totalHoras - 1) . '(?,?,?)'
				. " ON CONFLICT ON CONSTRAINT horario_bloqueado_pkey DO UPDATE SET hobl_motivo = EXCLUDED.hobl_motivo";

			$conn = $this->conexion->getConexion();
			$stmt = $conn->prepare($select);

			$paramIdx = 0;
			while ($dateFrom <= $dateTo) {
				$dia = $this->getDia($dateFrom->format('N'));
				$fecha = $dateFrom->format('Y-m-d');
				$dateFrom->add($interval1D);

				if ($daysHours[$dia] != null) {
					for ($j = 0; $j < count($daysHours[$dia]); $j++) {
						$stmt->bindValue(++$paramIdx, $fecha, PDO::PARAM_STR);
						$stmt->bindValue(++$paramIdx, $mapaHorarios[$daysHours[$dia][$j]], PDO::PARAM_INT);
						$stmt->bindValue(++$paramIdx, $motivo, PDO::PARAM_STR);
					}
				}
			}

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	private function newDateTime(string $date)
	{
		return new DateTime(
			// https://www.php.net/manual/en/datetime.format.php
			$date, // $dateTime->format('Y-m-d')
			new DateTimeZone('America/Mexico_City')
		);
	}

	private function getDia($numero)
	{
		switch ($numero) {
			case 1:
				return 'L';
			case 2:
				return 'M';
			case 3:
				return 'MC';
			case 4:
				return 'J';
			case 5:
				return 'V';
			case 6:
				return 'S';
			case 7:
				return 'D';
		}
	}
}
