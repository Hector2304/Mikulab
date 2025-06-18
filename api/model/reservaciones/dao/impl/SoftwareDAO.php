<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/ReservacionesBD.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dao/AbstractDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/ISoftwareDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/SoftwareDTO.php";

class SoftwareDAO extends AbstractDAO implements ISoftwareDAO
{
	protected $tabla = PG_SCHEMA . "software";
	protected $colId = "soft_id_software";
	protected $colNombre = "soft_nombre";

	protected $laboratorioSoftware = PG_SCHEMA . "laboratorio_software";
	protected $lasoIdSoftware = "laso_id_software";
	protected $lasoIdLaboratorio = "laso_id_laboratorio";

	public function __construct(IConexion $iConexion)
	{
		parent::__construct($iConexion);
	}

	public function alta(SoftwareDTO $softwareDTO): int
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"INSERT INTO " . $this->tabla .
					" (" . $this->colNombre . ")" .
					" VALUES (:nombre)"
			);

			$stmt->bindParam(":nombre", $softwareDTO->getSoftNombre(), PDO::PARAM_STR);

			if ($stmt->execute()) {
				return intval($conn->lastInsertId());
			}

			throw new Exception("No Insertado");
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function baja(SoftwareDTO $softwareDTO): bool
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"DELETE FROM " . $this->tabla .
					" WHERE "  . $this->colId . " = :id"
			);

			$stmt->bindParam(":id", $softwareDTO->getSoftIdSoftware(), PDO::PARAM_INT);

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function consultaAll(): array
	{
		try {
			$softwares = array();

			$conn = $this->conexion->getConexion();
			$stmt = $conn->query("SELECT * FROM " . $this->tabla . " ORDER BY " . $this->colNombre);
			$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if ($fetched) {
				foreach ($fetched as $f) {
					$software = new SoftwareDTO;
					$software->setSoftIdSoftware($f[$this->colId]);
					$software->setSoftNombre($f[$this->colNombre]);

					$softwares[] = $software;
				}
			}

			return $softwares;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function modificacion(SoftwareDTO $softwareDTO): bool
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"UPDATE " . $this->tabla .
					" SET "  . $this->colNombre . " = :nombre" .
					" WHERE "  . $this->colId . " = :id"
			);

			$stmt->bindParam(":nombre", $softwareDTO->getSoftNombre(), PDO::PARAM_STR);
			$stmt->bindParam(":id", $softwareDTO->getSoftIdSoftware(), PDO::PARAM_INT);

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function porLaboratorio(int $labId): array
	{
		try {
			$softwares = array();

			$conn = $this->conexion->getConexion();
			$stmt = $conn->prepare(
				"SELECT " . $this->lasoIdSoftware . " FROM " . $this->laboratorioSoftware
					. " WHERE " . $this->lasoIdLaboratorio . " = :labId"
			);

			$stmt->bindParam(":labId", $labId, PDO::PARAM_INT);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$softwares[] = array("lasoIdSoftware" => $f[$this->lasoIdSoftware]);
					}
				}
			}

			return $softwares;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function asignarALaboratorio(int $labId, int $softwareId): bool
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"INSERT INTO " . $this->laboratorioSoftware .
					" (" . $this->lasoIdLaboratorio . ", "  . $this->lasoIdSoftware .  ")" .
					" VALUES(:idLaboratorio, :idSoftware)"
			);

			$stmt->bindParam(":idLaboratorio", $labId, PDO::PARAM_INT);
			$stmt->bindParam(":idSoftware", $softwareId, PDO::PARAM_INT);

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function removerDeLaboratorio(int $labId, int $softwareId): bool
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"DELETE FROM " . $this->laboratorioSoftware .
					" WHERE " . $this->lasoIdLaboratorio . " = :idLaboratorio " .
					" AND " . $this->lasoIdSoftware . " = :idSoftware"
			);

			$stmt->bindParam(":idLaboratorio", $labId, PDO::PARAM_INT);
			$stmt->bindParam(":idSoftware", $softwareId, PDO::PARAM_INT);

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function  obtenerAsociacionCompleta(bool $porLaboratorio): array
	{
		try {
			$mapa = array();

			$conn = $this->conexion->getConexion();
			$stmt = $conn->query("SELECT * FROM " . $this->laboratorioSoftware);
			$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if ($fetched) {
				if ($porLaboratorio) {
					foreach ($fetched as $f) {
						$lab = "" . $f[$this->lasoIdLaboratorio];

                        if (!array_key_exists($lab, $mapa)) {
                            $mapa[$lab] = [];
                        }


                        $mapa[$lab][] = $f[$this->lasoIdSoftware];
					}
				}
			}

			return $mapa;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}
}
