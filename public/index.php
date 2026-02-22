<?php
session_start();

// Obtener la vista solicitada
$vista = $_GET['vista'] ?? 'index';

// Rutas permitidas
$vistas_permitidas = ['index', 'listar'];

if (!in_array($vista, $vistas_permitidas)) {
    $vista = 'index';
}

// Para la vista listar, necesitamos obtener los usuarios
if ($vista === 'listar') {
    require_once __DIR__ . '/../config/database.php';
    require_once __DIR__ . '/../app/controllers/UsuarioController.php';
    
    $database = new Database();
    $db = $database->getConnection();
    $controller = new UsuarioController($db);
    $usuarios = $controller->obtenerTodos();
}

// Incluir la vista correspondiente
require_once __DIR__ . "/../app/views/{$vista}.php";
?>