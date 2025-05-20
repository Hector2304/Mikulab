<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/ReservacionesBD.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dao/AbstractDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/IInstructorExternoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/InstructorExternoDTO.php";

class InstructorExternoDAO extends AbstractDAO implements IInstructorExternoDAO
{
	protected $tabla = PG_SCHEMA . "instructor_externo";
	protected $colId = "inex_id_instructor_externo";
	protected $colNombre = "inex_nombre";
	protected $colPaterno = "inex_apaterno";
	protected $colMaterno = "inex_amaterno";
	protected $colTelefono = "inex_telefono";
	protected $colCorreo = "inex_correo";

	public function __construct(IConexion $iConexion)
	{
		parent::__construct($iConexion);
	}

	public function alta(InstructorExternoDTO $instructorDTO): int
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"INSERT INTO " . $this->tabla
					. " ("
					. $this->colNombre . ", "
					. $this->colPaterno . ", "
					. $this->colMaterno . ", "
					. $this->colTelefono . ", "
					. $this->colCorreo
					. ")" .
					" VALUES (:nombre, :ap, :am, :tel, :correo)"
			);

			$stmt->bindParam(":nombre", $instructorDTO->getInexNombre(), PDO::PARAM_STR);
			$stmt->bindParam(":ap", $instructorDTO->getInexApaterno(), PDO::PARAM_STR);
			$stmt->bindParam(":am", $instructorDTO->getInexAmaterno(), PDO::PARAM_STR);
			$stmt->bindParam(":tel", $instructorDTO->getInexTelefono(), PDO::PARAM_STR);
			$stmt->bindParam(":correo", $instructorDTO->getInexCorreo(), PDO::PARAM_STR);

			if ($stmt->execute()) {
				return intval($conn->lastInsertId());
			}

			throw new Exception("No Insertado");
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function consultaAll(): array
	{
		try {
			$instructores = array();

			$conn = $this->conexion->getConexion();

			$stmt = $conn->query(
				"SELECT * FROM "
				. $this->tabla
				. " ORDER BY "
				. $this->colPaterno . " ASC, "
				. $this->colMaterno . " ASC, "
				. $this->colNombre . " ASC"
			);

			$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if ($fetched) {
				foreach ($fetched as $f) {
					$instructor = new InstructorExternoDTO;
					$instructor->setInexIdInstructorExterno($f[$this->colId]);
					$instructor->setInexNombre($f[$this->colNombre]);
					$instructor->setInexApaterno($f[$this->colPaterno]);
					$instructor->setInexAmaterno($f[$this->colMaterno]);
					$instructor->setInexTelefono($f[$this->colTelefono]);
					$instructor->setInexCorreo($f[$this->colCorreo]);

					$instructores[] = $instructor;
				}
			}

			return $instructores;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function modificacion(InstructorExternoDTO $instructorDTO): bool
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"UPDATE " . $this->tabla
					. " SET "
					. $this->colNombre . " = :nombre, "
					. $this->colPaterno . " = :ap, "
					. $this->colMaterno . " = :am, "
					. $this->colTelefono . " = :tel, "
					. $this->colCorreo . " = :correo"
					. " WHERE "  . $this->colId . " = :id"
			);

			$stmt->bindParam(":id", $instructorDTO->getInexIdInstructorExterno(), PDO::PARAM_INT);
			$stmt->bindParam(":nombre", $instructorDTO->getInexNombre(), PDO::PARAM_STR);
			$stmt->bindParam(":ap", $instructorDTO->getInexApaterno(), PDO::PARAM_STR);
			$stmt->bindParam(":am", $instructorDTO->getInexAmaterno(), PDO::PARAM_STR);
			$stmt->bindParam(":tel", $instructorDTO->getInexTelefono(), PDO::PARAM_STR);
			$stmt->bindParam(":correo", $instructorDTO->getInexCorreo(), PDO::PARAM_STR);

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}
}
