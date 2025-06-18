<?php

interface IConexion
{
	public function conectar();
	public function desconectar();
	public function getConexion();
}
