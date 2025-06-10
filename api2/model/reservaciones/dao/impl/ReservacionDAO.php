<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/ReservacionesBD.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dao/AbstractDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/IReservacionDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/ReservacionDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/LaboratoriosFCABD.php";

class ReservacionDAO extends AbstractDAO implements IReservacionDAO
{
    protected $tablaReservacion = PG_SCHEMA . "reservacion";
    protected $colReseIdReservacion = "rese_id_reservacion";
    protected $colReseIdLaboratorio = "rese_id_laboratorio";
    protected $colReseFecha = "rese_fecha";
    protected $colReseIdHorario = "rese_id_horario";
    protected $colReseReservadoEn = "rese_reservado_en";
    protected $colReseReservadoPor = "rese_reservado_por";
    protected $colReseIdGrupo = "rese_id_grupo";
    protected $colReseTipoGrupo = "rese_tipo_grupo";
    protected $colReseStatus = "rese_status";

    public function __construct(IConexion $iConexion)
    {
        parent::__construct($iConexion);
    }

    public function traslapos(int $horaIni, int $horaFin, string $fecha, int $labId): array {
        try {
            $listado = array();

            $conn = $this->conexion->getConexion();
            $stmt = $conn->prepare("WITH overlapping AS (
					SELECT h.hora_ini, h.hora_fin,
					(LEAST(h.hora_fin::integer, :horaFin) - GREATEST(h.hora_ini::integer, :horaIni)) AS ammount
					FROM reservacion r
					JOIN horario h ON r.rese_id_horario = h.hora_id_horario
					WHERE r.rese_fecha = :fecha AND r.rese_id_laboratorio = :idLab AND r.rese_status = 'A')
				SELECT * FROM overlapping WHERE ammount > 0");

            $stmt->bindParam(":horaFin", $horaFin, PDO::PARAM_INT);
            $stmt->bindParam(":horaIni", $horaIni, PDO::PARAM_INT);
            $stmt->bindParam(":fecha", $fecha, PDO::PARAM_STR);
            $stmt->bindParam(":idLab", $labId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($fetched) {
                    foreach ($fetched as $f) {
                        $listado[] = array(
                            "horaIni" => $f['hora_ini'],
                            "horaFin" => $f['hora_fin'],
                            "ammount" => $f['ammount']
                        );
                    }
                }
            }

            return $listado;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function alta(string $horaIni, string $horaFin, string $fecha, int $profId = null, int $labId, int $grupoId = null, string $tipoGrupo = null): int
    {
        try {
            $conn = $this->conexion->getConexion();

            $query = "WITH
			slct_horario AS (SELECT hora_id_horario FROM horario WHERE hora_ini = :hora_ini AND hora_fin = :hora_fin LIMIT 1)
			INSERT INTO reservacion(rese_id_horario, rese_id_laboratorio, rese_fecha, rese_reservado_por, rese_id_grupo, rese_tipo_grupo, rese_status)
			VALUES ((SELECT hora_id_horario FROM slct_horario), :lab_id, :fecha, :prof_id, :grupo_id, :tipo_grupo, 'A')";

            $stmt = $conn->prepare($query);

            $stmt->bindParam(":hora_ini", $horaIni, PDO::PARAM_STR);
            $stmt->bindParam(":hora_fin", $horaFin, PDO::PARAM_STR);
            $stmt->bindParam(":lab_id", $labId, PDO::PARAM_INT);
            $stmt->bindParam(":fecha", $fecha, PDO::PARAM_STR);
            $stmt->bindParam(":prof_id", $profId, PDO::PARAM_INT);
            $stmt->bindParam(":grupo_id", $grupoId, PDO::PARAM_INT);
            $stmt->bindParam(":tipo_grupo", $tipoGrupo, PDO::PARAM_STR);

            if ($stmt->execute()) {
                return intval($conn->lastInsertId());
            }

            throw new Exception("No Insertado");
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function listadoPorProfesor(int $idProfesor): array
    {
        try {
            $listado = array();

            $conn = $this->conexion->getConexion();
            $stmt = $conn->prepare(
                "SELECT * FROM " . $this->tablaReservacion .
                " JOIN horario h ON " . $this->tablaReservacion . "." . $this->colReseIdHorario . " = h.hora_id_horario " .
                " WHERE "  . $this->colReseReservadoPor . " = :id_profesor" .
                " ORDER BY " . $this->colReseFecha . " DESC"
            );

            $stmt->bindParam(":id_profesor", $idProfesor, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($fetched) {
                    foreach ($fetched as $f) {
                        $reserva = new ReservacionDTO;
                        $reserva->setReseFecha($f[$this->colReseFecha]);
                        $reserva->setReseIdGrupo($f[$this->colReseIdGrupo]);
                        $reserva->setReseIdHorario($f[$this->colReseIdHorario]);
                        $reserva->setReseIdLaboratorio($f[$this->colReseIdLaboratorio]);
                        $reserva->setReseIdReservacion($f[$this->colReseIdReservacion]);
                        $reserva->setReseReservadoEn($f[$this->colReseReservadoEn]);
                        $reserva->setReseReservadoPor($f[$this->colReseReservadoPor]);
                        $reserva->setReseStatus($f[$this->colReseStatus]);
                        $reserva->setReseTipoGrupo($f[$this->colReseTipoGrupo]);
                        $reserva->setHoraIni($f['hora_ini']);
                        $reserva->setHoraFin($f['hora_fin']);

                        $listado[] = $reserva;
                    }
                }
            }

            return $listado;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function cancelar(int $idReservacion, int $idProfesor = null): bool
    {
        try {
            $conn = $this->conexion->getConexion();

            $query = "UPDATE " . $this->tablaReservacion .
                " SET "  . $this->colReseStatus . " = 'C'" .
                " WHERE "  . $this->colReseIdReservacion . " = :idReservacion";

            if ($idProfesor !== null) {
                $query .= " AND "  . $this->colReseReservadoPor . " = :idProfesor";
            }

            $stmt = $conn->prepare($query);
            $stmt->bindParam(":idReservacion", $idReservacion, PDO::PARAM_INT);

            if ($idProfesor !== null) {
                $stmt->bindParam(":idProfesor", $idProfesor, PDO::PARAM_INT);
            }

            return $stmt->execute();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }


    public function baja(int $idReservacion): bool
    {
        try {
            $conn = $this->conexion->getConexion();

            $stmt = $conn->prepare(
                "DELETE FROM " . $this->tablaReservacion .
                " WHERE "  . $this->colReseIdReservacion . " = :idReservacion" .
                " AND "  . $this->colReseReservadoPor . " IS NULL"
            );

            $stmt->bindParam(":idReservacion", $idReservacion, PDO::PARAM_INT);

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

            $stmt = $conn->prepare("SELECT r.*, h.*, date_part('dow', r.rese_fecha::date) dow
				FROM reservacion r
				JOIN horario h ON r.rese_id_horario = h.hora_id_horario
				WHERE r.rese_id_laboratorio = :labId
				AND r.rese_status = 'A'
				AND date_part('year', r.rese_fecha::date) = :yyear
				AND date_part('week', r.rese_fecha::date) = :wweek");

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
                            "end" => ($f['hora_fin'] / 100),
                            "tipoGrupo" => $f['rese_tipo_grupo'],
                            "idGrupo" => $f['rese_id_grupo'],
                            "idReservacion" => $f['rese_id_reservacion']
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

    public function consultaExternoPerWeek(int $cursoId): array
    {
        try {
            $semanas = array();

            $conn = $this->conexion->getConexion();

            $stmt = $conn->prepare("SELECT
				date_part('year', rese_fecha::date) AS _year,
				date_part('week', rese_fecha::date) AS _week,
				rese_id_laboratorio
				FROM reservacion
				WHERE rese_tipo_grupo = 'E'
				AND rese_id_grupo = :cursoId
				GROUP BY _year, _week, rese_id_laboratorio
				ORDER BY _year DESC, _week DESC, rese_id_laboratorio ASC");

            $stmt->bindParam(":cursoId", $cursoId, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($fetched) {
                    foreach ($fetched as $f) {
                        $semanas[] = array(
                            "year" => $f['_year'],
                            "week" => $f['_week'],
                            "labId" => $f['rese_id_laboratorio']
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

            $stmt = $conn->prepare("SELECT r.*, h.*
				FROM reservacion r
				JOIN horario h ON r.rese_id_horario = h.hora_id_horario
				WHERE r.rese_id_laboratorio IN (" . $in . ")
				AND r.rese_fecha = ?");

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
                            "end" => ($f['hora_fin'] / 100),
                            "labId" => $f['rese_id_laboratorio']
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

    public function bajaSemanal(int $year, int $week, int $labId, int $cursoId): bool
    {
        try {
            $conn = $this->conexion->getConexion();

            $stmt = $conn->prepare(
                "DELETE FROM reservacion
				WHERE date_part('year', rese_fecha::date) = :yyear
				AND date_part('week', rese_fecha::date) = :wweek
				AND rese_id_laboratorio = :labId
				AND rese_id_grupo = :cursoId
				AND rese_tipo_grupo = 'E'"
            );

            $stmt->bindParam(":yyear", $year, PDO::PARAM_INT);
            $stmt->bindParam(":wweek", $week, PDO::PARAM_INT);
            $stmt->bindParam(":labId", $labId, PDO::PARAM_INT);
            $stmt->bindParam(":cursoId", $cursoId, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function listadoPorFecha(array $fechas): array
    {
        try {
            $listado = array();

            $in  = str_repeat('?,', count($fechas) - 1) . '?';

            $conn = $this->conexion->getConexion();
            $stmt = $conn->prepare(
                "SELECT * FROM " . $this->tablaReservacion .
                " JOIN horario h ON " . $this->tablaReservacion . "." . $this->colReseIdHorario . " = h.hora_id_horario " .
                " WHERE "  . $this->colReseFecha . " IN (" . $in . ")" .
                " AND "  . $this->colReseStatus . " = 'A'" .
                " ORDER BY " . $this->colReseFecha . " DESC"
            );

            $param = 0;
            for ($i = 0; $i < count($fechas); $i++) {
                $stmt->bindValue(++$param, $fechas[$i], PDO::PARAM_STR);
            }

            if ($stmt->execute()) {
                $fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($fetched) {
                    foreach ($fetched as $f) {
                        $reserva = new ReservacionDTO;
                        $reserva->setReseFecha($f[$this->colReseFecha]);
                        $reserva->setReseIdGrupo($f[$this->colReseIdGrupo]);
                        $reserva->setReseIdHorario($f[$this->colReseIdHorario]);
                        $reserva->setReseIdLaboratorio($f[$this->colReseIdLaboratorio]);
                        $reserva->setReseIdReservacion($f[$this->colReseIdReservacion]);
                        $reserva->setReseReservadoEn($f[$this->colReseReservadoEn]);
                        $reserva->setReseReservadoPor($f[$this->colReseReservadoPor]);
                        $reserva->setReseStatus($f[$this->colReseStatus]);
                        $reserva->setReseTipoGrupo($f[$this->colReseTipoGrupo]);
                        $reserva->setHoraIni($f['hora_ini']);
                        $reserva->setHoraFin($f['hora_fin']);

                        $listado[] = $reserva;
                    }
                }
            }

            return $listado;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function listadoPorFechaYLaboratorio(array $fechas, int $idLab): array {
        try {
            $listado = array();

            $in  = str_repeat('?,', count($fechas) - 1) . '?';

            $conn = $this->conexion->getConexion();
            $stmt = $conn->prepare(
                "SELECT * FROM " . $this->tablaReservacion .
                " JOIN horario h ON " . $this->tablaReservacion . "." . $this->colReseIdHorario . " = h.hora_id_horario " .
                " WHERE "  . $this->colReseFecha . " IN (" . $in . ")" .
                " AND "  . $this->colReseStatus . " = 'A'" .
                " AND "  . $this->colReseIdLaboratorio . " = ?" .
                " ORDER BY " . $this->colReseFecha . " DESC"
            );

            $param = 0;
            for ($i = 0; $i < count($fechas); $i++) {
                $stmt->bindValue(++$param, $fechas[$i], PDO::PARAM_STR);
            }

            $stmt->bindValue(++$param, $idLab, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($fetched) {
                    foreach ($fetched as $f) {
                        $reserva = new ReservacionDTO;
                        $reserva->setReseFecha($f[$this->colReseFecha]);
                        $reserva->setReseIdGrupo($f[$this->colReseIdGrupo]);
                        $reserva->setReseIdHorario($f[$this->colReseIdHorario]);
                        $reserva->setReseIdLaboratorio($f[$this->colReseIdLaboratorio]);
                        $reserva->setReseIdReservacion($f[$this->colReseIdReservacion]);
                        $reserva->setReseReservadoEn($f[$this->colReseReservadoEn]);
                        $reserva->setReseReservadoPor($f[$this->colReseReservadoPor]);
                        $reserva->setReseStatus($f[$this->colReseStatus]);
                        $reserva->setReseTipoGrupo($f[$this->colReseTipoGrupo]);
                        $reserva->setHoraIni($f['hora_ini']);
                        $reserva->setHoraFin($f['hora_fin']);

                        $listado[] = $reserva;
                    }
                }
            }

            return $listado;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

   public function importarClasesPredeterminadas(string $fechaInicio, string $fechaFin): int
{
    try {
        $connInterna = $this->conexion->getConexion();
        $connExterna = LaboratoriosFCABD::getInstance()->getConexion();

        // 🚀 Traer todo desde la externa, incluyendo el ID real del salón (laboratorio)
        $stmt = $connExterna->prepare("
            SELECT 
                hg.hogr_id_grupo,
                g.grup_id_profesor,
                hg.hogr_id_horario,
                hg.hogr_id_dia,
                s.salo_id_salon AS lab_id,
                h.hora_ini,
                h.hora_fin
            FROM horario_grupo hg
            JOIN grupo g ON g.grup_id_grupo = hg.hogr_id_grupo
            JOIN salon s ON s.salo_id_salon = hg.hogr_id_salon
            JOIN horario h ON h.hora_id_horario = hg.hogr_id_horario
            WHERE s.salo_tipo = 'L' AND s.salo_estado = 'A' AND hg.hogr_id_salon IS NOT NULL
        ");
        $stmt->execute();
        $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $insertadas = 0;
        $fecha = new DateTime($fechaInicio);
        $fin = new DateTime($fechaFin);

        while ($fecha <= $fin) {
            $semanaBase = clone $fecha;
            $semanaBase->modify('monday this week');

            foreach ($clases as $clase) {
                $diaOffset = intval($clase['hogr_id_dia']) - 1;
                $fechaClase = (clone $semanaBase)->modify("+$diaOffset days");
                $fechaStr = $fechaClase->format('Y-m-d');

                // 🕓 Buscar ID de horario en la BD interna
                $horaStmt = $connInterna->prepare("
                    SELECT hora_id_horario FROM horario 
                    WHERE hora_ini = :ini AND hora_fin = :fin 
                    LIMIT 1
                ");
                $horaStmt->bindParam(":ini", $clase['hora_ini'], PDO::PARAM_STR);
                $horaStmt->bindParam(":fin", $clase['hora_fin'], PDO::PARAM_STR);
                $horaStmt->execute();
                $hora = $horaStmt->fetch(PDO::FETCH_ASSOC);
                if (!$hora) continue;

                $idHorario = $hora['hora_id_horario'];
                $labId = $clase['lab_id']; // 💡 Ya viene de la externa

                // 🔒 Validar que no exista ya la reservación
                $checkStmt = $connInterna->prepare("
                    SELECT 1 FROM reservacion 
                    WHERE rese_id_laboratorio = :lab 
                    AND rese_fecha = :fecha 
                    AND rese_id_horario = :horario
                ");
                $checkStmt->bindParam(":lab", $labId, PDO::PARAM_INT);
                $checkStmt->bindParam(":fecha", $fechaStr, PDO::PARAM_STR);
                $checkStmt->bindParam(":horario", $idHorario, PDO::PARAM_INT);
                $checkStmt->execute();
                if ($checkStmt->fetch()) continue;

                // 📝 Insertar reservación
                $insertStmt = $connInterna->prepare("
                    INSERT INTO reservacion (
                        rese_id_laboratorio,
                        rese_fecha,
                        rese_id_horario,
                        rese_reservado_por,
                        rese_id_grupo,
                        rese_tipo_grupo,
                        rese_status
                    ) VALUES (
                        :lab, :fecha, :horario, :prof, :grupo, 'L', 'A'
                    )
                ");
                $insertStmt->bindParam(":lab", $labId, PDO::PARAM_INT);
                $insertStmt->bindParam(":fecha", $fechaStr, PDO::PARAM_STR);
                $insertStmt->bindParam(":horario", $idHorario, PDO::PARAM_INT);
                $insertStmt->bindParam(":prof", $clase['grup_id_profesor'], PDO::PARAM_INT);
                $insertStmt->bindParam(":grupo", $clase['hogr_id_grupo'], PDO::PARAM_INT);

                if ($insertStmt->execute()) {
                    $insertadas++;
                }
            }

            $fecha->modify('+7 days');
        }

        return $insertadas;
    } catch (Exception $e) {
        error_log($e->getMessage());
        throw $e;
    }


}


}
