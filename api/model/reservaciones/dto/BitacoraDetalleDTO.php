<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dto/AbstractDTO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/BitacoraObservacionDTO.php";

class BitacoraDetalleDTO extends AbstractDTO
{
	protected $bideIdDetalle; // int
	protected $bideIdBitacora; // int
	protected $bideIdLaboratorio; // int
	protected $bideMonitor; // int
	protected $bideCpu; // int
	protected $bideTeclado; // int
	protected $bideMouse; // int
	protected $bideVideoProyector; // int
	protected $bideCableDport; // int
	protected $bideControlCanon; // int
	protected $bideControlAire; // int
	protected $bideHoraApertura; // string
	protected $bideHoraCierre; // string
	protected $bideVigilante; // int

	public function getBideIdDetalle()
	{
		return $this->bideIdDetalle;
	}

	public function setBideIdDetalle(int $bideIdDetalle = null)
	{
		$this->bideIdDetalle = $bideIdDetalle;
	}

	public function getBideIdBitacora()
	{
		return $this->bideIdBitacora;
	}

	public function setBideIdBitacora(int $bideIdBitacora = null)
	{
		$this->bideIdBitacora = $bideIdBitacora;
	}

	public function getBideIdLaboratorio()
	{
		return $this->bideIdLaboratorio;
	}

	public function setBideIdLaboratorio(int $bideIdLaboratorio = null)
	{
		$this->bideIdLaboratorio = $bideIdLaboratorio;
	}

	public function getBideMonitor()
	{
		return $this->bideMonitor;
	}

	public function setBideMonitor(int $bideMonitor = null)
	{
		$this->bideMonitor = $bideMonitor;
	}

	public function getBideCpu()
	{
		return $this->bideCpu;
	}

	public function setBideCpu(int $bideCpu = null)
	{
		$this->bideCpu = $bideCpu;
	}

	public function getBideTeclado()
	{
		return $this->bideTeclado;
	}

	public function setBideTeclado(int $bideTeclado = null)
	{
		$this->bideTeclado = $bideTeclado;
	}

	public function getBideMouse()
	{
		return $this->bideMouse;
	}

	public function setBideMouse(int $bideMouse = null)
	{
		$this->bideMouse = $bideMouse;
	}

	public function getBideVideoProyector()
	{
		return $this->bideVideoProyector;
	}

	public function setBideVideoProyector(int $bideVideoProyector = null)
	{
		$this->bideVideoProyector = $bideVideoProyector;
	}

	public function getBideCableDport()
	{
		return $this->bideCableDport;
	}

	public function setBideCableDport(int $bideCableDport = null)
	{
		$this->bideCableDport = $bideCableDport;
	}

	public function getBideControlCanon()
	{
		return $this->bideControlCanon;
	}

	public function setBideControlCanon(int $bideControlCanon = null)
	{
		$this->bideControlCanon = $bideControlCanon;
	}

	public function getBideControlAire()
	{
		return $this->bideControlAire;
	}

	public function setBideControlAire(int $bideControlAire = null)
	{
		$this->bideControlAire = $bideControlAire;
	}

	public function getBideHoraApertura()
	{
		return $this->bideHoraApertura;
	}

	public function setBideHoraApertura(string $bideHoraApertura = null)
	{
		$this->bideHoraApertura = $bideHoraApertura;
	}

	public function getBideHoraCierre()
	{
		return $this->bideHoraCierre;
	}

	public function setBideHoraCierre(string $bideHoraCierre = null)
	{
		$this->bideHoraCierre = $bideHoraCierre;
	}

	public function getBideVigilante()
	{
		return $this->bideVigilante;
	}

	public function setBideVigilante(int $bideVigilante = null)
	{
		$this->bideVigilante = $bideVigilante;
	}
}
