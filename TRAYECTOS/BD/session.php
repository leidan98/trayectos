<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function verificarSesion() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: index.php");
        exit();
    }
}

function verificarAdmin() {
    if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] != '1') {
        header("Location: dashboard.php");
        exit();
    }
}

function limpiarSesion() {
    session_destroy();
    header("Location: index.php");
    exit();
}

 ?>