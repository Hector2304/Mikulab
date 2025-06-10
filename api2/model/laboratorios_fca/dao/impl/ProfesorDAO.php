<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/LaboratoriosFCABD.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dao/AbstractDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dao/IProfesorDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/laboratorios_fca/dto/ProfesorDTO.php";

class ProfesorDAO extends AbstractDAO implements IProfesorDAO
{

	public function __construct(IConexion $iConexion)
	{
		parent::__construct($iConexion);
	}

	/**
	 * 
	 */
	public function inciarSesion(string $usuario, string $contrasena)/* : ProfesorDTO */
	{
		try {
			$conn = $this->conexion->getConexion();
			$stmt = $conn->prepare("SELECT pr.prof_id_profesor, pr.prof_num_unam,
				pe.pers_nombre, pe.pers_apaterno, pe.pers_amaterno
				FROM profesor pr
				JOIN persona pe ON pr.prof_id_persona = pe.pers_id_persona 
				WHERE pr.prof_num_unam = :usuario
				AND pe.pers_rfc = :contrasena");

			$stmt->bindParam(":usuario", $usuario, PDO::PARAM_STR);
			$stmt->bindParam(":contrasena", $contrasena, PDO::PARAM_STR);

			if ($stmt->execute()) {
				$fetched = $stmt->fetch(PDO::FETCH_ASSOC);

				if ($fetched) {
					$profesor = new ProfesorDTO;
					$profesor->setProfIdProfesor($fetched["prof_id_profesor"]);
					$profesor->setProfNumUnam($fetched["prof_num_unam"]);
					$profesor->setNombre(
						$fetched["pers_nombre"] .
							" " .
							$fetched["pers_apaterno"] .
							" " .
							$fetched["pers_amaterno"]
					);

					return $profesor;
				}
			}

			return null;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function profesoresMap(array $ids): array {
		try {
			$uniqueIds = array_unique($ids, SORT_NUMERIC);
			$in  = str_repeat('?,', count($uniqueIds) - 1) . '?';

			$profes = array();

			$conn = $this->conexion->getConexion();
			$stmt = $conn->prepare("SELECT pr.prof_id_profesor, pe.pers_nombre, pe.pers_apaterno, pe.pers_amaterno
				FROM profesor pr
				JOIN persona pe ON pr.prof_id_persona = pe.pers_id_persona 
				WHERE pr.prof_id_profesor IN (" . $in . ")");

			$i = 0;
			foreach ($uniqueIds as $id) {
				$stmt->bindValue(++$i, $id, PDO::PARAM_INT);
			}

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$profesor = new ProfesorDTO;
						$profesor->setProfIdProfesor($f["prof_id_profesor"]);
						$profesor->setNombre(
							$f["pers_nombre"] .
								" " .
								$f["pers_apaterno"] .
								" " .
								$f["pers_amaterno"]
						);

						$profes[$profesor->getProfIdProfesor()] = $profesor->getObjectVars();
					}
				}
			}

			return $profes;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

    public function porId(int $idProfesor)
    {
        try {
            $conn = $this->conexion->getConexion();
            $stmt = $conn->prepare("
            SELECT pr.prof_num_trabajador, pr.prof_correo,
                   pe.pers_nombre, pe.pers_apaterno, pe.pers_amaterno
            FROM profesor pr
            JOIN persona pe ON pr.prof_id_persona = pe.pers_id_persona
            WHERE pr.prof_id_profesor = :id
        ");

            $stmt->bindParam(":id", $idProfesor, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $f = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($f) {
                    $profesor = new ProfesorDTO();
                    $profesor->setNumTrabajador($f['prof_num_trabajador']);

                    $nombre = new Name();
                    $nombre->setNombreDePila($f['pers_nombre']);
                    $nombre->setPrimerApellido($f['pers_apaterno']);
                    $nombre->setSegundoApellido($f['pers_amaterno']);
                    $profesor->setNombre($nombre);

                    $profesor->setCorreoElectronico($f['prof_correo']);

                    return $profesor;
                }
            }

            return null;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }


}
