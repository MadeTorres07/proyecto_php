<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/UsuarioController.php';

// Instancia de la base de datos y controlador
$database = new Database();
$db = $database->getConnection();
$controller = new UsuarioController($db);

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php?vista=index');
    exit();
}

// Procesar el guardado
$resultado = $controller->guardar($_POST);

// Guardar mensaje en sesión
$_SESSION['mensaje'] = $resultado['mensaje'];
$_SESSION['tipo_mensaje'] = $resultado['tipo'];

// Redirigir según el resultado
if ($resultado['success'] && isset($resultado['redirect'])) {
    header('Location: ' . $resultado['redirect']);
} else {
    // Si hay error, guardar datos para rellenar formulario
    $_SESSION['datos_form'] = $_POST;
    header('Location: index.php?vista=index');
}
exit();
?>