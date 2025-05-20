<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/ReservacionesBD.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dao/AbstractDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/IHorarioDAO.php";

class HorarioDAO extends AbstractDAO implements IHorarioDAO
{
	protected $tabla = PG_SCHEMA . "horario";
	protected $colId = "hora_id_horario";
	protected $colIni = "hora_ini";
	protected $colFin = "hora_fin";
	protected $colTipo = "hora_tipo";

	public function __construct(IConexion $iConexion)
	{
		parent::__construct($iConexion);
	}

	public function getAllIn(array $ids): array
	{
		try {
			$in  = str_repeat('?,', count($ids) - 1) . '?';

			$horarios = array();

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare("SELECT * FROM " . $this->tabla . " WHERE " . $this->colId . " IN (" . $in . ")");

			$i = 0;
			foreach ($ids as $id) {
				$stmt->bindValue(++$i, $id, PDO::PARAM_INT);
			}

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$horario = new HorarioDTO;
						$horario->setHoraIdHorario($f[$this->colId]);
						$horario->setHoraIni($f[$this->colIni]);
						$horario->setHoraFin($f[$this->colFin]);
						$horario->setHoraTipo($f[$this->colTipo]);

						$horarios[] = $horario;
					}
				}
			}

			return $horarios;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function getHoraIniIdTipoUno(): array
	{
		try {
			$mapa = array();

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"SELECT * FROM " . $this->tabla
					. " WHERE " . $this->colTipo . " = '1'"
			);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$mapa[((int)$f[$this->colIni]) / 100] = $f[$this->colId];
					}
				}
			}

			return $mapa;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function getIdsByTuples(array $tuples): array {
		try {
			$tin = "(?, ?)";
			$in  = str_repeat($tin . ',', count($tuples) - 1) . $tin;

			$ids = array();

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"SELECT * FROM " . $this->tabla
					. " WHERE (" . $this->colIni . ", " . $this->colFin . ")"
					. " IN (" . $in . ")"
			);

			$i = 0;
			foreach ($tuples as $t) {
				$stmt->bindValue(++$i, $t['horaIni'], PDO::PARAM_STR);
				$stmt->bindValue(++$i, $t['horaFin'], PDO::PARAM_STR);
			}

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$ids[] = $f[$this->colId];
					}
				}
			}

			return $ids;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

}
