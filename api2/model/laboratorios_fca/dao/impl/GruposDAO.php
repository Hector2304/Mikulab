<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/LaboratoriosFCABD.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dao/AbstractDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/IGruposDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dto/GrupoDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dto/GrupoPDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dto/GrupoOTDTO.php";

class GruposDAO extends AbstractDAO implements IGruposDAO
{

	public function __construct(IConexion $iConexion)
	{
		parent::__construct($iConexion);
	}

	/**
	 * Tabla: grupo
	 */
	public function gruposProfesor(int $idProfesor): array
	{
		try {
			$grupos = array();

			$conn = $this->conexion->getConexion();
			$stmt = $conn->prepare("SELECT g.*, c.carr_nombre, a.asig_id_asignatura, a.asig_nombre  FROM grupo g
				JOIN periodo p ON g.grup_id_periodo = p.peri_id_periodo AND p.peri_estatus = 'A' AND ((CURRENT_DATE BETWEEN p.peri_fec_ini AND p.peri_fec_fin) OR (CURRENT_DATE BETWEEN p.peri_fec_ini_inter AND p.peri_fec_fin_inter) OR (CURRENT_DATE BETWEEN p.peri_fec_ini_inter2 AND p.peri_fec_fin_inter2))
				JOIN carrera c ON g.grup_id_carrera = c.carr_id_carrera
				JOIN plan_asignatura pa ON g.grup_id_plan_asignatura = pa.plas_id_plan_asignatura
				JOIN asignatura a ON pa.plas_id_asignatura = a.asig_id_asignatura
				WHERE g.grup_id_profesor = :idProfesor
				AND g.grup_tipo = (CASE WHEN (CURRENT_DATE BETWEEN p.peri_fec_ini_inter AND p.peri_fec_fin_inter) OR (CURRENT_DATE BETWEEN p.peri_fec_ini_inter2 AND p.peri_fec_fin_inter2) THEN 'EX' ELSE 'OR' END)
				ORDER BY a.asig_nombre");

			$stmt->bindParam(":idProfesor", $idProfesor, PDO::PARAM_INT);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$grupo = new GrupoDTO;
						$grupo->setGrupAlumnosInscritos($f["grup_alumnos_inscritos"]);
						$grupo->setGrupClave($f["grup_clave"]);
						$grupo->setGrupIdGrupo($f["grup_id_grupo"]);
						$grupo->setGrupIdPeriodo($f["grup_id_periodo"]);
						$grupo->setGrupSemestre($f["grup_semestre"]);
						$grupo->setGrupIdProfesor($f["grup_id_profesor"]);
						$grupo->setCarrNombre($f["carr_nombre"]);
						$grupo->setAsigIdAsignatura($f["asig_id_asignatura"]);
						$grupo->setAsigNombre($f["asig_nombre"]);

						$grupos[] = $grupo;
					}
				}
			}

			return $grupos;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	/**
	 * Tabla: grupo_p
	 */
	public function grupos_pProfesor(int $idProfesor): array
	{
		try {
			$grupos = array();

			$conn = $this->conexion->getConexion();
			$stmt = $conn->prepare("SELECT g.*, ap.asig_id_asignatura_p, ap.asig_nombre_p, c.coor_nombre FROM grupo_p g
				JOIN periodo p ON g.gruo_id_periodo = p.peri_id_periodo AND p.peri_estatus = 'A' AND ((CURRENT_DATE BETWEEN p.peri_fec_ini AND p.peri_fec_fin) OR (CURRENT_DATE BETWEEN p.peri_fec_ini_inter AND p.peri_fec_fin_inter) OR (CURRENT_DATE BETWEEN p.peri_fec_ini_inter2 AND p.peri_fec_fin_inter2))
				JOIN plan_asignatura_orientacion pao ON g.gruo_id_plan_asig_orie = pao.paoc_id_plan_asig_orie
				JOIN asignatura_p ap ON pao.paoc_id_asignatura_p = ap.asig_id_asignatura_p
				JOIN coordinacion c ON pao.paoc_id_coordinacion_p = c.coor_id_coordinacion
				WHERE g.gruo_id_profesor = :idProfesor
				ORDER BY ap.asig_nombre_p");

			$stmt->bindParam(":idProfesor", $idProfesor, PDO::PARAM_INT);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$grupo = new GrupoPDTO;
						$grupo->setGruoAlumnosInscritos($f["gruo_alumnos_inscritos"]);
						$grupo->setGruoClave($f["gruo_clave"]);
						$grupo->setGruoIdGrupoP($f["gruo_id_grupo_p"]);
						$grupo->setGruoIdPeriodo($f["gruo_id_periodo"]);
						$grupo->setGruoIdProfesor($f["gruo_id_profesor"]);
						$grupo->setAsigIdAsignaturaP($f["asig_id_asignatura_p"]);
						$grupo->setAsigNombreP($f["asig_nombre_p"]);
						$grupo->setCoorNombre($f["coor_nombre"]);

						$grupos[] = $grupo;
					}
				}
			}

			return $grupos;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	/**
	 * Tabla: grupo_ot
	 */
	public function grupos_otProfesor(int $idProfesor): array
	{
		try {
			$grupos = array();

			$conn = $this->conexion->getConexion();
			$stmt = $conn->prepare("SELECT g.*, mo.moot_nombre, mo.moot_clave FROM grupo_ot g
				JOIN modulo_ot mo ON g.grot_id_modulo_ot = mo.moot_id_modulo_ot
				WHERE g.grot_id_profesor = :idProfesor
				AND CURRENT_DATE BETWEEN g.grot_fec_ini AND g.grot_fec_fin
				ORDER BY mo.moot_nombre");

			$stmt->bindParam(":idProfesor", $idProfesor, PDO::PARAM_INT);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$grupo = new GrupoOTDTO;
						$grupo->setGrotIdGrupoOt($f["grot_id_grupo_ot"]);
						$grupo->setGrotClave($f["grot_clave"]);
						$grupo->setGrotCupo($f["grot_cupo"]);
						$grupo->setGrotIdProfesor($f["grot_id_profesor"]);
						$grupo->setMootClave($f["moot_clave"]);
						$grupo->setMootNombre($f["moot_nombre"]);

						$grupos[] = $grupo;
					}
				}
			}

			return $grupos;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	/**
	 * Tabla: grupo
	 */
	public function gruposPorIdMap(array $ids): array
	{
		try {
			$uniqueIds = array_unique($ids, SORT_NUMERIC);
			$in  = str_repeat('?,', count($uniqueIds) - 1) . '?';

			$grupos = array();

			$conn = $this->conexion->getConexion();
			$stmt = $conn->prepare("SELECT g.*, c.carr_nombre, a.asig_id_asignatura, a.asig_nombre  FROM grupo g
				JOIN carrera c ON g.grup_id_carrera = c.carr_id_carrera
				JOIN plan_asignatura pa ON g.grup_id_plan_asignatura = pa.plas_id_plan_asignatura
				JOIN asignatura a ON pa.plas_id_asignatura = a.asig_id_asignatura
				WHERE g.grup_id_grupo IN (" . $in . ")");

			$i = 0;
			foreach ($uniqueIds as $id) {
				$stmt->bindValue(++$i, $id, PDO::PARAM_INT);
			}

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$grupo = new GrupoDTO;
						$grupo->setGrupAlumnosInscritos($f["grup_alumnos_inscritos"]);
						$grupo->setGrupClave($f["grup_clave"]);
						$grupo->setGrupIdGrupo($f["grup_id_grupo"]);
						$grupo->setGrupIdPeriodo($f["grup_id_periodo"]);
						$grupo->setGrupSemestre($f["grup_semestre"]);
						$grupo->setGrupIdProfesor($f["grup_id_profesor"]);
						$grupo->setCarrNombre($f["carr_nombre"]);
						$grupo->setAsigIdAsignatura($f["asig_id_asignatura"]);
						$grupo->setAsigNombre($f["asig_nombre"]);

						$grupos[$grupo->getGrupIdGrupo()] = $grupo->getObjectVars();
					}
				}
			}

			return $grupos;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	/**
	 * Tabla: grupo_p
	 */
	public function grupos_pPorIdMap(array $ids): array
	{
		try {
			$uniqueIds = array_unique($ids, SORT_NUMERIC);
			$in  = str_repeat('?,', count($uniqueIds) - 1) . '?';

			$grupos = array();

			$conn = $this->conexion->getConexion();
			$stmt = $conn->prepare("SELECT g.*, ap.asig_id_asignatura_p, ap.asig_nombre_p, c.coor_nombre FROM grupo_p g
				JOIN plan_asignatura_orientacion pao ON g.gruo_id_plan_asig_orie = pao.paoc_id_plan_asig_orie
				JOIN asignatura_p ap ON pao.paoc_id_asignatura_p = ap.asig_id_asignatura_p
				JOIN coordinacion c ON pao.paoc_id_coordinacion_p = c.coor_id_coordinacion
				WHERE g.gruo_id_grupo_p IN (" . $in . ")");

			$i = 0;
			foreach ($uniqueIds as $id) {
				$stmt->bindValue(++$i, $id, PDO::PARAM_INT);
			}

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$grupo = new GrupoPDTO;
						$grupo->setGruoAlumnosInscritos($f["gruo_alumnos_inscritos"]);
						$grupo->setGruoClave($f["gruo_clave"]);
						$grupo->setGruoIdGrupoP($f["gruo_id_grupo_p"]);
						$grupo->setGruoIdPeriodo($f["gruo_id_periodo"]);
						$grupo->setGruoIdProfesor($f["gruo_id_profesor"]);
						$grupo->setAsigIdAsignaturaP($f["asig_id_asignatura_p"]);
						$grupo->setAsigNombreP($f["asig_nombre_p"]);
						$grupo->setCoorNombre($f["coor_nombre"]);

						$grupos[$grupo->getGruoIdGrupoP()] = $grupo->getObjectVars();
					}
				}
			}

			return $grupos;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	/**
	 * Tabla: grupo_ot
	 */
	public function grupos_otPorIdMap(array $ids): array
	{
		try {
			$uniqueIds = array_unique($ids, SORT_NUMERIC);
			$in  = str_repeat('?,', count($uniqueIds) - 1) . '?';

			$grupos = array();

			$conn = $this->conexion->getConexion();
			$stmt = $conn->prepare("SELECT g.*, mo.moot_nombre, mo.moot_clave FROM grupo_ot g
				JOIN modulo_ot mo ON g.grot_id_modulo_ot = mo.moot_id_modulo_ot
				WHERE g.grot_id_grupo_ot IN (" . $in . ")");

			$i = 0;
			foreach ($uniqueIds as $id) {
				$stmt->bindValue(++$i, $id, PDO::PARAM_INT);
			}

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$grupo = new GrupoOTDTO;
						$grupo->setGrotIdGrupoOt($f["grot_id_grupo_ot"]);
						$grupo->setGrotClave($f["grot_clave"]);
						$grupo->setGrotCupo($f["grot_cupo"]);
						$grupo->setGrotIdProfesor($f["grot_id_profesor"]);
						$grupo->setMootClave($f["moot_clave"]);
						$grupo->setMootNombre($f["moot_nombre"]);

						$grupos[$grupo->getGrotIdGrupoOt()] = $grupo->getObjectVars();
					}
				}
			}

			return $grupos;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

}
