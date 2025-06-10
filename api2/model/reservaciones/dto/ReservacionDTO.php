<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";

class ReservacionDTO extends AbstractDTO
{
	protected $reseIdReservacion; // int
	protected $reseIdLaboratorio; // int
	protected $reseFecha; // string
	protected $reseIdHorario; // int
	protected $horaIni; // string
	protected $horaFin; // string
	protected $reseReservadoPor; // int
	protected $reseReservadoEn; // string
	protected $reseIdGrupo; // int
	protected $reseTipoGrupo; // string
	protected $reseStatus; // string

	const GRUPO_LICENCIATURA = 'L';
	const GRUPO_POSGRADO = 'P';
	const GRUPO_OPCION_TITULACION = 'T';
	const GRUPO_EXTERNO = 'E';

	public function getReseIdReservacion() //: int
	{
		return $this->reseIdReservacion;
	}

	public function setReseIdReservacion(int $reseIdReservacion = null)
	{
		$this->reseIdReservacion = $reseIdReservacion;
	}

	public function getReseIdLaboratorio() //: int
	{
		return $this->reseIdLaboratorio;
	}

	public function setReseIdLaboratorio(int $reseIdLaboratorio = null)
	{
		$this->reseIdLaboratorio = $reseIdLaboratorio;
	}

	public function getReseFecha() //: string
	{
		return $this->reseFecha;
	}

	public function setReseFecha(string $reseFecha = null)
	{
		$this->reseFecha = $reseFecha;
	}

	public function getReseIdHorario() //: int
	{
		return $this->reseIdHorario;
	}

	public function setReseIdHorario(int $reseIdHorario = null)
	{
		$this->reseIdHorario = $reseIdHorario;
	}

	public function getReseReservadoEn() //: string
	{
		return $this->reseReservadoEn;
	}

	public function setReseReservadoEn(string $reseReservadoEn = null)
	{
		$this->reseReservadoEn = $reseReservadoEn;
	}

	public function getReseReservadoPor() //: int
	{
		return $this->reseReservadoPor;
	}

	public function setReseReservadoPor(int $reseReservadoPor = null)
	{
		$this->reseReservadoPor = $reseReservadoPor;
	}

	public function getReseIdGrupo() //: int
	{
		return $this->reseIdGrupo;
	}

	public function setReseIdGrupo(int $reseIdGrupo = null)
	{
		$this->reseIdGrupo = $reseIdGrupo;
	}

	public function getReseTipoGrupo() //: string
	{
		return $this->reseTipoGrupo;
	}

	public function setReseTipoGrupo(string $reseTipoGrupo = null)
	{
		$this->reseTipoGrupo = $reseTipoGrupo;
	}

	public function getReseStatus() //: string
	{
		return $this->reseStatus;
	}

	public function setReseStatus(string $reseStatus = null)
	{
		$this->reseStatus = $reseStatus;
	}

	public function getHoraIni() //: string
	{
		return $this->horaIni;
	}

	public function setHoraIni(string $horaIni = null)
	{
		$this->horaIni = $horaIni;
	}

	public function getHoraFin() //: string
	{
		return $this->horaFin;
	}

	public function setHoraFin(string $horaFin = null)
	{
		$this->horaFin = $horaFin;
	}
}
