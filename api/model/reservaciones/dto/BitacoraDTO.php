<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/BitacoraDetalleDTO.php";

class BitacoraDTO extends AbstractDTO
{
	protected $bitaIdBitacora; // int
	protected $bitaFecha; // string
	protected $bitaTipo; // string
	protected $bitaTipoLab; // string

	public function getBitaIdBitacora() //: int
	{
		return $this->bitaIdBitacora;
	}

	public function setBitaIdBitacora(string $bitaIdBitacora = null)
	{
		$this->bitaIdBitacora = $bitaIdBitacora;
	}

	public function getBitaFecha() //: string
	{
		return $this->bitaFecha;
	}

	public function setBitaFecha(string $bitaFecha = null)
	{
		$this->bitaFecha = $bitaFecha;
	}

	public function getBitaTipo() //: string
	{
		return $this->bitaTipo;
	}

	public function setBitaTipo(string $bitaTipo = null)
	{
		$this->bitaTipo = $bitaTipo;
	}

	public function getBitaTipoLab() //: string
	{
		return $this->bitaTipoLab;
	}

	public function setBitaTipoLab(string $bitaTipoLab = null)
	{
		$this->bitaTipoLab = $bitaTipoLab;
	}
}
