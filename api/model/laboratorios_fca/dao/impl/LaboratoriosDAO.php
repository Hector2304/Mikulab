<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/LaboratoriosFCABD.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dao/AbstractDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/ILaboratoriosDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dto/LaboratorioDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/util/Util.php';

class LaboratoriosDAO extends AbstractDAO implements ILaboratoriosDAO
{

	public function __construct(IConexion $iConexion)
	{
		parent::__construct($iConexion);
	}

	/**
	 * Tabla: salon
	 * 
	 * Los laboratorios se identifican por:
	 * tipo: L (laboratorio)
	 * estado: A (activo)
	 * clave comienza por: LAB o K
	 */
	public function laboratoriosFCA(): array
	{
		try {
			$laboratorios = array();

			$conn = $this->conexion->getConexion();
			$stmt = $conn->prepare("SELECT * FROM salon
				WHERE salo_estado = 'A'
				AND salo_tipo = 'L'
				AND (salo_clave LIKE 'LAB%' OR salo_clave LIKE 'K%')");

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$laboratorio = new LaboratorioDTO;
						$laboratorio->setSaloIdSalon($f["salo_id_salon"]);
						$laboratorio->setSaloClave($f["salo_clave"]);
						$laboratorio->setSaloUbicacion($f["salo_ubicacion"]);
						$laboratorio->setSaloCupo($f["salo_cupo"]);

						$laboratorios[] = $laboratorio;
					}
				}
			}

			return $laboratorios;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function laboratoriosFCAIn(array $labIds): array
	{
		try {
			$laboratorios = array();

			$in  = str_repeat('?,', count($labIds) - 1) . '?';

			$conn = $this->conexion->getConexion();
			$stmt = $conn->prepare("SELECT * FROM salon WHERE salo_id_salon IN (" . $in . ")");

			$i = 0;
			foreach ($labIds as $id) {
				$stmt->bindValue(++$i, $id, PDO::PARAM_INT);
			}

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$laboratorio = new LaboratorioDTO;
						$laboratorio->setSaloIdSalon($f["salo_id_salon"]);
						$laboratorio->setSaloClave($f["salo_clave"]);
						$laboratorio->setSaloUbicacion($f["salo_ubicacion"]);
						$laboratorio->setSaloCupo($f["salo_cupo"]);

						$laboratorios[$laboratorio->getSaloIdSalon()] = $laboratorio;
					}
				}
			}

			return $laboratorios;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function laboratoriosFCAClaveStartsWith(string $with): array
	{
		try {
			$laboratorios = array();

			$conn = $this->conexion->getConexion();
			$stmt = $conn->prepare("SELECT * FROM salon
				WHERE salo_estado = 'A'
				AND salo_tipo = 'L'
				AND salo_clave LIKE '" . $with . "%'");

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$laboratorio = new LaboratorioDTO;
						$laboratorio->setSaloIdSalon($f["salo_id_salon"]);
						$laboratorio->setSaloClave($f["salo_clave"]);
						$laboratorio->setSaloUbicacion($f["salo_ubicacion"]);
						$laboratorio->setSaloCupo($f["salo_cupo"]);

						$laboratorios[] = $laboratorio;
					}
				}
			}

			return $laboratorios;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function getLaboratorioPorId(int $saloId)/* : LaboratorioDTO */
	{
		try {
			$conn = $this->conexion->getConexion();
			$stmt = $conn->prepare("SELECT * FROM salon WHERE salo_id_salon = :id");

			$stmt->bindParam(":id", $saloId, PDO::PARAM_STR);

			if ($stmt->execute()) {
				$fetched = $stmt->fetch(PDO::FETCH_ASSOC);

				if ($fetched) {
					$laboratorio = new LaboratorioDTO;
					$laboratorio->setSaloIdSalon($fetched["salo_id_salon"]);
					$laboratorio->setSaloClave($fetched["salo_clave"]);
					$laboratorio->setSaloUbicacion($fetched["salo_ubicacion"]);
					$laboratorio->setSaloCupo($fetched["salo_cupo"]);

					return $laboratorio;
				}
			}

			return null;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function getDisponibilidad(array $diasSemana, array $labIds): array
	{
		try {
			$disponibilidad = array();

			$conn = $this->conexion->getConexion();

			$inDias  = str_repeat('?,', count($diasSemana) - 1) . '?';
			$inLabs  = str_repeat('?,', count($labIds) - 1) . '?';

			$stmt = $conn->prepare(
				"SELECT hg.hogr_id_salon AS id_salon, h.hora_ini, h.hora_fin, hg.hogr_id_dia AS num_dia FROM horario_grupo hg
				JOIN grupo g ON hg.hogr_id_grupo = g.grup_id_grupo
				JOIN periodo p ON g.grup_id_periodo = p.peri_id_periodo AND p.peri_estatus = 'A' AND ((CURRENT_DATE BETWEEN p.peri_fec_ini AND p.peri_fec_fin) OR (CURRENT_DATE BETWEEN p.peri_fec_ini_inter AND p.peri_fec_fin_inter) OR (CURRENT_DATE BETWEEN p.peri_fec_ini_inter2 AND p.peri_fec_fin_inter2))
				JOIN horario h ON h.hora_id_horario = hg.hogr_id_horario
				WHERE hg.hogr_id_dia IN (" . $inDias . ") AND hg.hogr_id_salon IN (" . $inLabs . ")
				AND g.grup_tipo = (CASE WHEN (CURRENT_DATE BETWEEN p.peri_fec_ini_inter AND p.peri_fec_fin_inter) OR (CURRENT_DATE BETWEEN p.peri_fec_ini_inter2 AND p.peri_fec_fin_inter2) THEN 'EX' ELSE 'OR' END)
				UNION ALL
				SELECT hgp.hogp_id_salon AS id_salon, h.hora_ini, h.hora_fin, hgp.hogp_id_dia AS num_dia FROM horario_grupo_p hgp 
				JOIN grupo_p gp ON hgp.hogp_id_grupo_p = gp.gruo_id_grupo_p 
				JOIN periodo p ON gp.gruo_id_periodo = p.peri_id_periodo AND p.peri_estatus = 'A' AND ((CURRENT_DATE BETWEEN p.peri_fec_ini AND p.peri_fec_fin) OR (CURRENT_DATE BETWEEN p.peri_fec_ini_inter AND p.peri_fec_fin_inter) OR (CURRENT_DATE BETWEEN p.peri_fec_ini_inter2 AND p.peri_fec_fin_inter2))
				JOIN horario h ON h.hora_id_horario = hgp.hogp_id_horario
				WHERE hgp.hogp_id_dia IN (" . $inDias . ") AND hgp.hogp_id_salon IN (" . $inLabs . ")
				UNION ALL
				SELECT hgo.hogo_id_salon AS id_salon, h.hora_ini, h.hora_fin, hgo.hogo_id_dia AS num_dia FROM horario_grupo_ot hgo
				JOIN grupo_ot gt ON hgo.hogo_id_grupo_ot = gt.grot_id_grupo_ot
				JOIN horario h ON h.hora_id_horario = hgo.hogo_id_horario
				WHERE CURRENT_DATE BETWEEN gt.grot_fec_ini AND gt.grot_fec_fin
				AND hgo.hogo_id_dia IN (" . $inDias . ") AND hgo.hogo_id_salon IN (" . $inLabs . ")"
			);

			$paramIndex = 0;

			for ($a = 0; $a < 3; $a++) {
				for ($i = 0; $i < count($diasSemana); $i++) {
					$stmt->bindParam(++$paramIndex, $diasSemana[$i], PDO::PARAM_INT);
				}
				for ($i = 0; $i < count($labIds); $i++) {
					$stmt->bindParam(++$paramIndex, $labIds[$i], PDO::PARAM_INT);
				}
			}

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$lab = "" . $f["id_salon"];

						if (!is_array($disponibilidad[$lab])) {
							$disponibilidad[$lab] = array();
						}

						$disponibilidad[$lab][] = array(
							"horaIni" => $f["hora_ini"],
							"horaFin" => $f["hora_fin"],
							"numDia" => $f["num_dia"]
						);
					}
				}
			}

			return $disponibilidad;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function getLaboratoriosMapPorId(array $labIds): array
	{
		try {
			$uniqueLabIds = array_unique($labIds, SORT_NUMERIC);
			$in  = str_repeat('?,', count($uniqueLabIds) - 1) . '?';

			$laboratorios = array();

			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare("SELECT * FROM salon WHERE salo_id_salon IN (" . $in . ")");

			$i = 0;
			foreach ($uniqueLabIds as $id) {
				$stmt->bindValue(++$i, $id, PDO::PARAM_INT);
			}

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$laboratorio = new LaboratorioDTO;
						$laboratorio->setSaloIdSalon($f["salo_id_salon"]);
						$laboratorio->setSaloClave($f["salo_clave"]);
						$laboratorio->setSaloUbicacion($f["salo_ubicacion"]);
						$laboratorio->setSaloCupo($f["salo_cupo"]);

						$laboratorios[$laboratorio->getSaloIdSalon()] = $laboratorio->getObjectVars();
					}
				}
			}

			return $laboratorios;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function getLaboratoriosGruposAlDia(array $diasSemana, int $idLab = null): array
	{
		try {
			$labsGrups = array();

			$conn = $this->conexion->getConexion();

			$inDias  = str_repeat('?,', count($diasSemana) - 1) . '?';

			$filtroLabs = "";

			if ($idLab == null) {
				$filtroLabs = "s.salo_id_salon AND s.salo_estado = 'A' AND s.salo_tipo = 'L' AND (s.salo_clave LIKE 'LAB%' OR s.salo_clave LIKE 'K%')";
			} else {
				$filtroLabs = "s.salo_id_salon AND s.salo_id_salon = ?";
			}

			$stmt = $conn->prepare(
				"SELECT hg.hogr_id_salon AS id_salon, h.hora_ini, h.hora_fin, g.grup_id_grupo AS id_grupo, 'L' AS tipo_grupo, hg.hogr_id_dia AS id_dia FROM horario_grupo hg
				JOIN grupo g ON hg.hogr_id_grupo = g.grup_id_grupo
				JOIN periodo p ON g.grup_id_periodo = p.peri_id_periodo AND p.peri_estatus = 'A' AND ((CURRENT_DATE BETWEEN p.peri_fec_ini AND p.peri_fec_fin) OR (CURRENT_DATE BETWEEN p.peri_fec_ini_inter AND p.peri_fec_fin_inter) OR (CURRENT_DATE BETWEEN p.peri_fec_ini_inter2 AND p.peri_fec_fin_inter2))
				JOIN horario h ON h.hora_id_horario = hg.hogr_id_horario
				JOIN salon s ON hg.hogr_id_salon = " . $filtroLabs . "
				WHERE hg.hogr_id_dia IN (" . $inDias . ")
				AND g.grup_tipo = (CASE WHEN (CURRENT_DATE BETWEEN p.peri_fec_ini_inter AND p.peri_fec_fin_inter) OR (CURRENT_DATE BETWEEN p.peri_fec_ini_inter2 AND p.peri_fec_fin_inter2) THEN 'EX' ELSE 'OR' END)
				UNION ALL
				SELECT hgp.hogp_id_salon AS id_salon, h.hora_ini, h.hora_fin, gp.gruo_id_grupo_p AS id_grupo, 'P' AS tipo_grupo, hgp.hogp_id_dia AS id_dia
				FROM horario_grupo_p hgp 
				JOIN grupo_p gp ON hgp.hogp_id_grupo_p = gp.gruo_id_grupo_p 
				JOIN periodo p ON gp.gruo_id_periodo = p.peri_id_periodo AND p.peri_estatus = 'A' AND ((CURRENT_DATE BETWEEN p.peri_fec_ini AND p.peri_fec_fin) OR (CURRENT_DATE BETWEEN p.peri_fec_ini_inter AND p.peri_fec_fin_inter) OR (CURRENT_DATE BETWEEN p.peri_fec_ini_inter2 AND p.peri_fec_fin_inter2))
				JOIN horario h ON h.hora_id_horario = hgp.hogp_id_horario
				JOIN salon s ON hgp.hogp_id_salon = " . $filtroLabs . "
				WHERE hgp.hogp_id_dia IN (" . $inDias . ")
				UNION ALL
				SELECT hgo.hogo_id_salon AS id_salon, h.hora_ini, h.hora_fin, gt.grot_id_grupo_ot AS id_grupo, 'T' AS tipo_grupo, hgo.hogo_id_dia AS id_dia
				FROM horario_grupo_ot hgo
				JOIN grupo_ot gt ON hgo.hogo_id_grupo_ot = gt.grot_id_grupo_ot
				JOIN horario h ON h.hora_id_horario = hgo.hogo_id_horario
				JOIN salon s ON hgo.hogo_id_salon = " . $filtroLabs . "
				WHERE CURRENT_DATE BETWEEN gt.grot_fec_ini AND gt.grot_fec_fin
				AND hgo.hogo_id_dia IN (" . $inDias . ")"
			);

			$paramIndex = 0;

			for ($a = 0; $a < 3; $a++) {
				if ($idLab != null) {
					$stmt->bindParam(++$paramIndex, $idLab, PDO::PARAM_INT);
				}

				for ($i = 0; $i < count($diasSemana); $i++) {
					$stmt->bindParam(++$paramIndex, $diasSemana[$i], PDO::PARAM_INT);
				}
			}

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$labsGrups[] = array(
							"idSalon" => $f["id_salon"],
							"horaIni" => $f["hora_ini"],
							"horaFin" => $f["hora_fin"],
							"idGrupo" => $f["id_grupo"],
							"tipoGrupo" => $f["tipo_grupo"],
							"idDia" => $f["id_dia"]
						);
					}
				}
			}

			return $labsGrups;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}
}
