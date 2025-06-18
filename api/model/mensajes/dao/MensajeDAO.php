<?php

class MensajeDAO {
    private $conn;

    public function __construct($conexionBD) {
        $this->conn = $conexionBD->getConexion();
    }

    public function insertar($datos) {
        $stmt = $this->conn->prepare("
            INSERT INTO mensaje (id_usuario, titulo, contenido, leido)
            VALUES (:id_usuario, :titulo, :contenido, false)
        ");

        $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
        $stmt->bindParam(':titulo', $datos['titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':contenido', $datos['contenido'], PDO::PARAM_STR);

        $resultado = $stmt->execute();

        if (!$resultado) {
            error_log("Error al insertar mensaje: " . implode(" | ", $stmt->errorInfo()));
        }

        return $resultado;
    }

    public function obtenerPorUsuario($idUsuario) {
        $stmt = $this->conn->prepare("
            SELECT * FROM mensaje
            WHERE id_usuario = :id_usuario
            ORDER BY fecha_envio DESC
        ");

        $stmt->bindParam(':id_usuario', $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marcarLeido($idMensaje) {
        $stmt = $this->conn->prepare("
            UPDATE mensaje SET leido = TRUE WHERE id_mensaje = :id
        ");
        $stmt->bindParam(':id', $idMensaje, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function marcarTodosComoLeidos($idUsuario)
    {
        $conn = $this->conexion->getConexion();
        $stmt = $conn->prepare("UPDATE mensaje SET leido = TRUE WHERE id_usuario = :id");
        $stmt->bindParam(":id", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function contarNoLeidos($idUsuario)
    {
        $conn = $this->conexion->getConexion();
        $stmt = $conn->prepare("SELECT COUNT(*) FROM mensaje WHERE id_usuario = :id AND leido = FALSE");
        $stmt->bindParam(":id", $idUsuario, PDO::PARAM_INT);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }


}
