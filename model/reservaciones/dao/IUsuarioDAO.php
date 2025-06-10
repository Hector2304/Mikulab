<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioDTO.php";

interface IUsuarioDAO
{
	public function inciarSesion(string $usuario)/* : UsuarioDTO */;
	public function consultaAll(): array;
	public function consultaByTipo(string $tipo): array;
	public function alta(UsuarioDTO $usuarioDTO): int;
	public function modificacion(UsuarioDTO $usuarioDTO): bool;
	public function setContrasena(UsuarioDTO $usuarioDTO): bool;
	public function setStatus(UsuarioDTO $usuarioDTO): bool;
}
