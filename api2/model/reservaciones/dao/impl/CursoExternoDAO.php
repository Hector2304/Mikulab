<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/ReservacionesBD.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dao/AbstractDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/ICursoExternoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/CursoExternoDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/InstructorExternoDTO.php";

class CursoExternoDAO extends AbstractDAO implements ICursoExternoDAO
{
	protected $tabla = PG_SCHEMA . "curso_externo";
	protected $colId = "cuex_id_curso_externo";
	protected $colClave = "cuex_clave";
	protected $colNombre = "cuex_nombre";
	protected $colAlumnosInscritos = "cuex_alumnos_inscritos";
	protected $colIdInstructor = "cuex_id_instructor_externo";

	protected $tablaInex = PG_SCHEMA . "instructor_externo";
	protected $colIdInex = "inex_id_instructor_externo";
	protected $colNombreInex = "inex_nombre";
	protected $colPaternoInex = "inex_apaterno";
	protected $colMaternoInex = "inex_amaterno";
	protected $colTelefonoInex = "inex_telefono";
	protected $colCorreoInex = "inex_correo";

	public function __construct(IConexion $iConexion)
	{
		parent::__construct($iConexion);
	}

	public function alta(CursoExternoDTO $cursoDTO): int
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"INSERT INTO " . $this->tabla
					. " ("
					. $this->colClave . ", "
					. $this->colNombre . ", "
					. $this->colAlumnosInscritos
					. ")" .
					" VALUES (:clave, :nombre, :alumnos)"
			);

			$stmt->bindParam(":clave", $cursoDTO->getCuexClave(), PDO::PARAM_STR);
			$stmt->bindParam(":nombre", $cursoDTO->getCuexNombre(), PDO::PARAM_STR);
			$stmt->bindParam(":alumnos", $cursoDTO->getCuexAlumnosInscritos(), PDO::PARAM_INT);

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
			$cursos = array();

			$conn = $this->conexion->getConexion();

			$stmt = $conn->query(
				"SELECT * FROM "
					. $this->tabla
					. " LEFT JOIN " . $this->tablaInex
					. " ON " . $this->tablaInex . "." . $this->colIdInex
					. " = " . $this->tabla . "." . $this->colIdInstructor
					. " ORDER BY "
					. $this->tabla . "." . $this->colNombre . " ASC"
			);

			$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if ($fetched) {
				foreach ($fetched as $f) {
					$curso = new CursoExternoDTO;
					$curso->setCuexIdCursoExterno($f[$this->colId]);
					$curso->setCuexClave($f[$this->colClave]);
					$curso->setCuexNombre($f[$this->colNombre]);
					$curso->setCuexAlumnosInscritos($f[$this->colAlumnosInscritos]);
					$curso->setCuexIdInstructorExterno($f[$this->colIdInstructor]);

					if ($curso->getCuexIdInstructorExterno() != null) {
						$instructor = new InstructorExternoDTO();
						$instructor->setInexIdInstructorExterno($f[$this->colIdInex]);
						$instructor->setInexNombre($f[$this->colNombreInex]);
						$instructor->setInexApaterno($f[$this->colPaternoInex]);
						$instructor->setInexAmaterno($f[$this->colMaternoInex]);
						$instructor->setInexTelefono($f[$this->colTelefonoInex]);
						$instructor->setInexCorreo($f[$this->colCorreoInex]);
						$curso->setInstructorExternoDTO($instructor);
					}

					$cursos[] = $curso;
				}
			}

			return $cursos;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function modificacion(CursoExternoDTO $cursoDTO): bool
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"UPDATE " . $this->tabla
					. " SET "
					. $this->colClave . " = :clave, "
					. $this->colNombre . " = :nombre, "
					. $this->colAlumnosInscritos . " = :alumnos"
					. " WHERE "  . $this->colId . " = :id"
			);

			$stmt->bindParam(":id", $cursoDTO->getCuexIdCursoExterno(), PDO::PARAM_INT);
			$stmt->bindParam(":clave", $cursoDTO->getCuexClave(), PDO::PARAM_STR);
			$stmt->bindParam(":nombre", $cursoDTO->getCuexNombre(), PDO::PARAM_STR);
			$stmt->bindParam(":alumnos", $cursoDTO->getCuexAlumnosInscritos(), PDO::PARAM_INT);

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function consultaPorInstructorId(int $insId): array
	{
		try {
			$cursos = array();

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"SELECT * FROM "
					. $this->tabla
					. " WHERE "
					. $this->colIdInstructor . " = :id"
					. " ORDER BY "
					. $this->colNombre . " ASC"
			);

			$stmt->bindParam(":id", $insId, PDO::PARAM_INT);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$curso = new CursoExternoDTO;
						$curso->setCuexIdCursoExterno($f[$this->colId]);
						$curso->setCuexClave($f[$this->colClave]);
						$curso->setCuexNombre($f[$this->colNombre]);
						$curso->setCuexAlumnosInscritos($f[$this->colAlumnosInscritos]);
						$curso->setCuexIdInstructorExterno($f[$this->colIdInstructor]);

						$cursos[] = $curso;
					}
				}
			}

			return $cursos;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function consultaSinInstructor(): array
	{
		try {
			$cursos = array();

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"SELECT * FROM "
					. $this->tabla
					. " WHERE "
					. $this->colIdInstructor . " IS NULL"
					. " ORDER BY "
					. $this->colNombre . " ASC"
			);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$curso = new CursoExternoDTO;
						$curso->setCuexIdCursoExterno($f[$this->colId]);
						$curso->setCuexClave($f[$this->colClave]);
						$curso->setCuexNombre($f[$this->colNombre]);
						$curso->setCuexAlumnosInscritos($f[$this->colAlumnosInscritos]);
						$curso->setCuexIdInstructorExterno($f[$this->colIdInstructor]);

						$cursos[] = $curso;
					}
				}
			}

			return $cursos;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function consultaInJoinInstructorMap(array $ids): array
	{
		try {
			$in  = str_repeat('?,', count($ids) - 1) . '?';

			$cursos = array();

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"SELECT * FROM "
					. $this->tabla
					. " LEFT JOIN " . $this->tablaInex . " ON " . $this->colIdInex . " = " . $this->colIdInstructor
					. " WHERE "
					. $this->colId . " IN (" . $in . ")"
			);

			$i = 0;
			foreach ($ids as $id) {
				$stmt->bindValue(++$i, $id, PDO::PARAM_INT);
			}

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$cursos[$f[$this->colId]] = array(
							"cuexIdCursoExterno" => $f[$this->colId],
							"cuexClave" => $f[$this->colClave],
							"cuexNombre" => $f[$this->colNombre],
							"cuexInstructor" => $f[$this->colIdInstructor] == null ? null : ($f[$this->colNombreInex] . " " . $f[$this->colPaternoInex] . " " . $f[$this->colMaternoInex])
						);
					}
				}
			}

			return $cursos;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function asignarInstructor(array $cursosIds, int $insId): bool
	{
		try {
			$conn = $this->conexion->getConexion();

			$in  = str_repeat('?,', count($cursosIds) - 1) . '?';

			$stmt = $conn->prepare(
				"UPDATE " . $this->tabla .
					" SET "  . $this->colIdInstructor . " = ?" .
					" WHERE " . $this->colId . " IN (" . $in . ")"
			);

			$stmt->bindParam(1, $insId, PDO::PARAM_INT);

			for ($i = 0; $i < count($cursosIds); $i++) {
				$stmt->bindParam($i + 2, $cursosIds[$i], PDO::PARAM_INT);
			}

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function removerInstructor(array $cursosIds): bool
	{
		try {
			$conn = $this->conexion->getConexion();

			$in  = str_repeat('?,', count($cursosIds) - 1) . '?';

			$stmt = $conn->prepare(
				"UPDATE " . $this->tabla .
					" SET "  . $this->colIdInstructor . " = NULL" .
					" WHERE " . $this->colId . " IN (" . $in . ")"
			);

			for ($i = 0; $i < count($cursosIds); $i++) {
				$stmt->bindParam($i + 1, $cursosIds[$i], PDO::PARAM_INT);
			}

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}
}
