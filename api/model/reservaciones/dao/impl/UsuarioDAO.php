<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/db/ReservacionesBD.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/dao/AbstractDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dao/IUsuarioDAO.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/api/model/reservaciones/dto/UsuarioDTO.php";

class UsuarioDAO extends AbstractDAO implements IUsuarioDAO
{
	protected $tablaUsuario = PG_SCHEMA . "usuario";
	protected $colUsuaIdUsuario = "usua_id_usuario";
	protected $colUsuaIdTipoUsuario = "usua_id_tipo_usuario";
	protected $colUsuaUsuario = "usua_usuario";
	protected $colUsuaContrasena = "usua_contrasena";
	protected $colUsuaNombre = "usua_nombre";
	protected $colUsuaApaterno = "usua_apaterno";
	protected $colUsuaAmaterno = "usua_amaterno";
	protected $colUsuaStatus = "usua_status";

	protected $tablaTipoUsuario = PG_SCHEMA . "tipo_usuario";
	protected $colTiusIdTipoUsuario = "tius_id_tipo_usuario";
	protected $colTiusNombre = "tius_nombre";

	public function __construct(IConexion $iConexion)
	{
		parent::__construct($iConexion);
	}

	public function inciarSesion(string $usuario)/* : UsuarioDTO */
	{
		try {
			$conn = $this->conexion->getConexion();
			$stmt = $conn->prepare("SELECT * FROM "
				. $this->tablaUsuario
				. " JOIN " . $this->tablaTipoUsuario
				. " ON "
				. $this->tablaTipoUsuario . "." . $this->colTiusIdTipoUsuario
				. " = "
				. $this->tablaUsuario . "." . $this->colUsuaIdTipoUsuario
				. " WHERE " . $this->tablaUsuario . "." . $this->colUsuaUsuario . " = :usuario");

			$stmt->bindParam(":usuario", $usuario, PDO::PARAM_STR);

			if ($stmt->execute()) {
				$fetched = $stmt->fetch(PDO::FETCH_ASSOC);

				if ($fetched) {
					$usuario = new UsuarioDTO;
					$usuario->setUsuaIdUsuario($fetched[$this->colUsuaIdUsuario]);
					$usuario->setUsuaIdTipoUsuario($fetched[$this->colUsuaIdTipoUsuario]);
					$usuario->setUsuaUsuario($fetched[$this->colUsuaUsuario]);
					$usuario->setUsuaContrasena($fetched[$this->colUsuaContrasena]);
					$usuario->setUsuaNombre($fetched[$this->colUsuaNombre]);
					$usuario->setUsuaApaterno($fetched[$this->colUsuaApaterno]);
					$usuario->setUsuaAmaterno($fetched[$this->colUsuaAmaterno]);
					$usuario->setUsuaStatus($fetched[$this->colUsuaStatus]);
					$usuario->setTiusNombre($fetched[$this->colTiusNombre]);

					return $usuario;
				}
			}

			return null;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function consultaAll(): array
	{
		try {
			$usuarios = array();

			$conn = $this->conexion->getConexion();
			$stmt = $conn->query(
				"SELECT * FROM "
					. $this->tablaUsuario
					. " JOIN " . $this->tablaTipoUsuario
					. " ON "
					. $this->tablaTipoUsuario . "." . $this->colTiusIdTipoUsuario
					. " = "
					. $this->tablaUsuario . "." . $this->colUsuaIdTipoUsuario
					. " ORDER BY "
					. $this->colUsuaUsuario . " ASC, "
					. $this->colUsuaApaterno . " ASC, "
					. $this->colUsuaAmaterno . " ASC, "
					. $this->colUsuaNombre . " ASC"
			);

			$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

			if ($fetched) {
				foreach ($fetched as $f) {
					$usuario = new UsuarioDTO;
					$usuario->setUsuaIdUsuario($f[$this->colUsuaIdUsuario]);
					$usuario->setUsuaIdTipoUsuario($f[$this->colUsuaIdTipoUsuario]);
					$usuario->setUsuaUsuario($f[$this->colUsuaUsuario]);
					$usuario->setUsuaContrasena($f[$this->colUsuaContrasena]);
					$usuario->setUsuaNombre($f[$this->colUsuaNombre]);
					$usuario->setUsuaApaterno($f[$this->colUsuaApaterno]);
					$usuario->setUsuaAmaterno($f[$this->colUsuaAmaterno]);
					$usuario->setUsuaStatus($f[$this->colUsuaStatus]);
					$usuario->setTiusNombre($f[$this->colTiusNombre]);

					$usuarios[] = $usuario;
				}
			}

			return $usuarios;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function consultaByTipo(string $tipo): array
	{
		try {
			$usuarios = array();

			$conn = $this->conexion->getConexion();
			$stmt = $conn->prepare(
				"SELECT * FROM "
					. $this->tablaUsuario
					. " JOIN " . $this->tablaTipoUsuario
					. " ON "
					. $this->tablaTipoUsuario . "." . $this->colTiusIdTipoUsuario
					. " = "
					. $this->tablaUsuario . "." . $this->colUsuaIdTipoUsuario
					. " WHERE " . $this->colTiusNombre . " = :tipo"
					. " AND " . $this->colUsuaStatus . " = 'A'"
					. " ORDER BY "
					. $this->colUsuaUsuario . " ASC, "
					. $this->colUsuaApaterno . " ASC, "
					. $this->colUsuaAmaterno . " ASC, "
					. $this->colUsuaNombre . " ASC"
			);

			$stmt->bindParam(":tipo", $tipo, PDO::PARAM_STR);

			if ($stmt->execute()) {
				$fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);

				if ($fetched) {
					foreach ($fetched as $f) {
						$usuario = new UsuarioDTO;
						$usuario->setUsuaIdUsuario($f[$this->colUsuaIdUsuario]);
						$usuario->setUsuaIdTipoUsuario($f[$this->colUsuaIdTipoUsuario]);
						$usuario->setUsuaUsuario($f[$this->colUsuaUsuario]);
						$usuario->setUsuaContrasena($f[$this->colUsuaContrasena]);
						$usuario->setUsuaNombre($f[$this->colUsuaNombre]);
						$usuario->setUsuaApaterno($f[$this->colUsuaApaterno]);
						$usuario->setUsuaAmaterno($f[$this->colUsuaAmaterno]);
						$usuario->setUsuaStatus($f[$this->colUsuaStatus]);
						$usuario->setTiusNombre($f[$this->colTiusNombre]);

						$usuarios[] = $usuario;
					}
				}
			}

			return $usuarios;
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function alta(UsuarioDTO $usuarioDTO): int
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"INSERT INTO " . $this->tablaUsuario
					. " ("
					. $this->colUsuaIdTipoUsuario . ", "
					. $this->colUsuaUsuario . ", "
					. $this->colUsuaContrasena . ", "
					. $this->colUsuaNombre . ", "
					. $this->colUsuaApaterno . ", "
					. $this->colUsuaAmaterno
					. ")" .
					" VALUES (:tipoUsuario, :usuario, :pass, :nombre, :ap, :am)"
			);

			$stmt->bindParam(":tipoUsuario", $usuarioDTO->getUsuaIdTipoUsuario(), PDO::PARAM_INT);
			$stmt->bindParam(":usuario", $usuarioDTO->getUsuaUsuario(), PDO::PARAM_STR);
			$stmt->bindParam(":pass", $usuarioDTO->getUsuaContrasena(), PDO::PARAM_STR);
			$stmt->bindParam(":nombre", $usuarioDTO->getUsuaNombre(), PDO::PARAM_STR);
			$stmt->bindParam(":ap", $usuarioDTO->getUsuaApaterno(), PDO::PARAM_STR);
			$stmt->bindParam(":am", $usuarioDTO->getUsuaAmaterno(), PDO::PARAM_STR);

			if ($stmt->execute()) {
				return intval($conn->lastInsertId());
			}

			throw new Exception("No Insertado");
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function modificacion(UsuarioDTO $usuarioDTO): bool
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"UPDATE " . $this->tablaUsuario .
					" SET "  .
					$this->colUsuaNombre . " = :nombre, " .
					$this->colUsuaApaterno . " = :ap, " .
					$this->colUsuaAmaterno . " = :am, " .
					$this->colUsuaIdTipoUsuario . " = :tipoUsuario" .
					" WHERE "  . $this->colUsuaIdUsuario . " = :id"
			);

			$stmt->bindParam(":nombre", $usuarioDTO->getUsuaNombre(), PDO::PARAM_STR);
			$stmt->bindParam(":ap", $usuarioDTO->getUsuaApaterno(), PDO::PARAM_STR);
			$stmt->bindParam(":am", $usuarioDTO->getUsuaAmaterno(), PDO::PARAM_STR);
			$stmt->bindParam(":tipoUsuario", $usuarioDTO->getUsuaIdTipoUsuario(), PDO::PARAM_INT);
			$stmt->bindParam(":id", $usuarioDTO->getUsuaIdUsuario(), PDO::PARAM_INT);

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function setContrasena(UsuarioDTO $usuarioDTO): bool
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"UPDATE " . $this->tablaUsuario .
					" SET "  .
					$this->colUsuaContrasena . " = :pass" .
					" WHERE "  . $this->colUsuaIdUsuario . " = :id"
			);

			$stmt->bindParam(":pass", $usuarioDTO->getUsuaContrasena(), PDO::PARAM_STR);
			$stmt->bindParam(":id", $usuarioDTO->getUsuaIdUsuario(), PDO::PARAM_INT);

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}

	public function setStatus(UsuarioDTO $usuarioDTO): bool
	{
		try {
			$conn = $this->conexion->getConexion();

			$stmt = $conn->prepare(
				"UPDATE " . $this->tablaUsuario .
					" SET "  .
					$this->colUsuaStatus . " = :status" .
					" WHERE "  . $this->colUsuaIdUsuario . " = :id"
			);

			$stmt->bindParam(":status", $usuarioDTO->getUsuaStatus(), PDO::PARAM_STR);
			$stmt->bindParam(":id", $usuarioDTO->getUsuaIdUsuario(), PDO::PARAM_INT);

			return $stmt->execute();
		} catch (Exception $e) {
			error_log($e->getMessage());
			throw $e;
		}
	}
}
