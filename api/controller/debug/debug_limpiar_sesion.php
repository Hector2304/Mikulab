<?php
session_start();

if (isset($_SESSION['USUARIO_SESION_DTO'])) {
    unset($_SESSION['USUARIO_SESION_DTO']);
    echo "✅ USUARIO_SESION_DTO eliminado correctamente.";
} else {
    echo "ℹ️ No existía USUARIO_SESION_DTO en esta sesión.";
}
