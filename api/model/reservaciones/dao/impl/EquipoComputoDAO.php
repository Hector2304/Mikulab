<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/ReservacionesBD.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dao/AbstractDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/IEquipoComputoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/EquipoComputoDTO.php";

class EquipoComputoDAO extends AbstractDAO implements IEquipoComputoDAO
{
	protected $tabla = PG_SCHEMA . "equipo_computo";
	protected $colIdEquipo = "eqco_id_equipo";
	protected $colIdSalon = "eqco_id_salon";
	protected $colNombre = "eqco_nombre";
	protected $colDescripcion = "eqco_descripcion";
	protected $colNumeroInventario = "eqco_numero_inventario";
	protected $colStatus = "eqco_status";

	public function __construct(IConexion $iConexion)
	{
		parent::__construct($iConexion);
	}

	public function alta(EquipoComputoDTO $equipoComputoDTO): int
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"INSERT INTO " . $this->tabla .
					" (" .
					$this->colNombre . ", " .
					$this->colDescripcion . ", " .
					$this->colNumeroInventario . ", " .
					$this->colStatus .
					") " .
					" VALUES (:nombre, :descripcion, :numero_inventario, :status)"
			);

			$stmt->bindParam(":nombre", $equipoComputoDTO->getEqcoNombre(), PDO::PARAM_STR);
			$stmt->bindParam(":descripcion", $equipoComputoDTO->getEqcoDescripcion(), PDO::PARAM_STR);
			$stmt->bindParam(":numero_inventario", $equipoComputoDTO->getEqcoNumeroInventario(), PDO::PARAM_STR);
			$stmt->bindParam(":status", $equipoComputoDTO->getEqcoStatus(), PDO::PARAM_STR);

			if ($stmt->execute()) {
				return intval($conn->lastInsertId());
			}

			throw new Exception("No Insertado");
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function baja(EquipoComputoDTO $equipoComputoDTO): bool
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"DELETE FROM " . $this->tabla .
					" WHERE "  . $this->colIdEquipo . " = :id"
			);

			$stmt->bindParam(":id", $equipoComputoDTO->getEqcoIdEquipo(), PDO::PARAM_INT);

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function consultaAll(): array
	{
		try {
			$equipos = array();

			$conn = $this->conexion->getConexion();
			$stmt = $conn->query("SELECT * FROM " . $this->tabla . " ORDER BY " . $this->colNombre);
			$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if ($fetched) {
				foreach ($fetched as $f) {
					$equipo = new EquipoComputoDTO;
					$equipo->setEqcoIdEquipo($f[$this->colIdEquipo]);
					$equipo->setEqcoIdSalon($f[$this->colIdSalon]);
					$equipo->setEqcoNombre($f[$this->colNombre]);
					$equipo->setEqcoDescripcion($f[$this->colDescripcion]);
					$equipo->setEqcoNumeroInventario($f[$this->colNumeroInventario]);
					$equipo->setEqcoStatus($f[$this->colStatus]);

					$equipos[] = $equipo;
				}
			}

			return $equipos;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function consultaPorLabId(int $labId): array
	{
		try {
			$equipos = array();

			$conn = $this->conexion->getConexion();
			$stmt = $conn->prepare(
				"SELECT * FROM " . $this->tabla .
					" WHERE "  . $this->colIdSalon . " = :id" .
					" ORDER BY " . $this->colNombre
			);

			$stmt->bindParam(":id", $labId, PDO::PARAM_INT);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$equipo = new EquipoComputoDTO;
						$equipo->setEqcoIdEquipo($f[$this->colIdEquipo]);
						$equipo->setEqcoIdSalon($f[$this->colIdSalon]);
						$equipo->setEqcoNombre($f[$this->colNombre]);
						$equipo->setEqcoDescripcion($f[$this->colDescripcion]);
						$equipo->setEqcoNumeroInventario($f[$this->colNumeroInventario]);
						$equipo->setEqcoStatus($f[$this->colStatus]);

						$equipos[] = $equipo;
					}
				}
			}

			return $equipos;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function consultaSinLab(): array
	{
		try {
			$equipos = array();

			$conn = $this->conexion->getConexion();
			$stmt = $conn->prepare(
				"SELECT * FROM " . $this->tabla .
					" WHERE "  . $this->colIdSalon . " IS NULL" .
					" ORDER BY " . $this->colNombre
			);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$equipo = new EquipoComputoDTO;
						$equipo->setEqcoIdEquipo($f[$this->colIdEquipo]);
						$equipo->setEqcoIdSalon($f[$this->colIdSalon]);
						$equipo->setEqcoNombre($f[$this->colNombre]);
						$equipo->setEqcoDescripcion($f[$this->colDescripcion]);
						$equipo->setEqcoNumeroInventario($f[$this->colNumeroInventario]);
						$equipo->setEqcoStatus($f[$this->colStatus]);

						$equipos[] = $equipo;
					}
				}
			}

			return $equipos;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function modificacion(EquipoComputoDTO $equipoComputoDTO): bool
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"UPDATE " . $this->tabla .
					" SET "  .
					$this->colNombre . " = :nombre, " .
					$this->colDescripcion . " = :descripcion, " .
					$this->colNumeroInventario . " = :numero_inventario, " .
					$this->colStatus . " = :status" .
					" WHERE "  . $this->colIdEquipo . " = :id"
			);

			$stmt->bindParam(":nombre", $equipoComputoDTO->getEqcoNombre(), PDO::PARAM_STR);
			$stmt->bindParam(":descripcion", $equipoComputoDTO->getEqcoDescripcion(), PDO::PARAM_STR);
			$stmt->bindParam(":numero_inventario", $equipoComputoDTO->getEqcoNumeroInventario(), PDO::PARAM_STR);
			$stmt->bindParam(":status", $equipoComputoDTO->getEqcoStatus(), PDO::PARAM_STR);
			$stmt->bindParam(":id", $equipoComputoDTO->getEqcoIdEquipo(), PDO::PARAM_INT);

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function asignarLaboratorio(array $equiposIds, int $labId): bool
	{
		try {
			$conn = $this->conexion->getConexion();

			$in  = str_repeat('?,', count($equiposIds) - 1) . '?';

			$stmt = $conn->prepare(
				"UPDATE " . $this->tabla .
					" SET "  . $this->colIdSalon . " = ?" .
					" WHERE " . $this->colIdEquipo . " IN (" . $in . ")"
			);

			$stmt->bindParam(1, $labId, PDO::PARAM_INT);

			for ($i = 0; $i < count($equiposIds); $i++) {
				$stmt->bindParam($i + 2, $equiposIds[$i], PDO::PARAM_INT);
			}

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function removerLaboratorio(array $equiposIds): bool
	{
		try {
			$conn = $this->conexion->getConexion();

			$in  = str_repeat('?,', count($equiposIds) - 1) . '?';

			$stmt = $conn->prepare(
				"UPDATE " . $this->tabla .
					" SET "  . $this->colIdSalon . " = NULL" .
					" WHERE " . $this->colIdEquipo . " IN (" . $in . ")"
			);

			for ($i = 0; $i < count($equiposIds); $i++) {
				$stmt->bindParam($i + 1, $equiposIds[$i], PDO::PARAM_INT);
			}

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}
}
