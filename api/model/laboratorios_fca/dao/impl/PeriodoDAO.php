<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/LaboratoriosFCABD.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dao/AbstractDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/IPeriodoDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dto/PeriodoDTO.php";

class PeriodoDAO extends AbstractDAO implements IPeriodoDAO
{

	public function __construct(IConexion $iConexion)
	{
		parent::__construct($iConexion);
	}

	/**
	 * 
	 */
	public function periodoPorFecha(string $fecha)/* : ProfesorDTO */
	{
		try {
			$conn = $this->conexion->getConexion();
			$stmt = $conn->prepare("SELECT * FROM periodo
				WHERE
				(:fecha BETWEEN peri_fec_ini AND peri_fec_fin)
				OR
				(:fecha_inter BETWEEN peri_fec_ini_inter AND peri_fec_fin_inter)
				OR
				(:fecha_inter2 BETWEEN peri_fec_ini_inter2 AND peri_fec_fin_inter2)
				LIMIT 1");

				$stmt->bindValue(":fecha", $fecha, PDO::PARAM_STR);
				$stmt->bindValue(":fecha_inter", $fecha, PDO::PARAM_STR);
				$stmt->bindValue(":fecha_inter2", $fecha, PDO::PARAM_STR);

			if ($stmt->execute()) {
				$fetched = $stmt->fetch(PDO::FETCH_ASSOC);

				if ($fetched) {
					$periodo = new PeriodoDTO;
					$periodo->setPeriIdPeriodo($fetched["peri_id_periodo"]);

					return $periodo;
				}
			}

			return null;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

}
/*SELECT
	p.peri_id_periodo, p.peri_estatus,
	p.peri_fec_ini, p.peri_fec_fin,
	p.peri_fec_ini_inter, peri_fec_fin_inter,
	p.peri_fec_ini_inter2, peri_fec_fin_inter2
FROM periodo p
WHERE p.peri_estatus = 'A'
AND (
	(CURRENT_DATE BETWEEN p.peri_fec_ini AND p.peri_fec_fin)
	OR
	(CURRENT_DATE BETWEEN p.peri_fec_ini_inter AND p.peri_fec_fin_inter)
	OR
	(CURRENT_DATE BETWEEN p.peri_fec_ini_inter2 AND p.peri_fec_fin_inter2)
)
*/