<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/UsuarioController.php';

// Instancia de la base de datos y controlador
$database = new Database();
$db = $database->getConnection();
$controller = new UsuarioController($db);

// Verificar que tenga ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['mensaje'] = "ID de usuario no válido";
    $_SESSION['tipo_mensaje'] = "danger";
    header('Location: index.php?vista=listar');
    exit();
}

// Procesar eliminación
$id = $_GET['id'];
$resultado = $controller->eliminar($id);

// Guardar mensaje en sesión
$_SESSION['mensaje'] = $resultado['mensaje'];
$_SESSION['tipo_mensaje'] = $resultado['tipo'];

// Redirigir a la lista
header('Location: index.php?vista=listar');
exit();
?>