<?php
session_start();
require_once 'config/database.php';

// Instancia de la base de datos
$database = new Database();
$db = $database->getConnection();

// Variables para mensajes
$mensaje = "";
$tipo_mensaje = "";

// ========== OPERACIONES CRUD ==========

// ELIMINAR usuario
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    try {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);
        
        $mensaje = "Usuario eliminado correctamente";
        $tipo_mensaje = "warning";
    } catch(PDOException $e) {
        $mensaje = "Error al eliminar: " . $e->getMessage();
        $tipo_mensaje = "danger";
    }
}

// LEER usuarios (para la tabla)
$sql = "SELECT * FROM usuarios ORDER BY creado_en DESC";
$stmt = $db->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://bootswatch.com/5/sketchy/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Lista de Usuarios - CRUD PHP</title>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <h1 class="text-center mb-4"> Lista de Usuarios</h1>
                
                <!-- Mensajes de alerta -->
                <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                    <?php echo $mensaje; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>
                
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            Usuarios Registrados (<?php echo count($usuarios); ?>)
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (count($usuarios) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="bg-dark text-white">
                                        <tr>
                                            <th class="bg-primary">ID</th>
                                            <th class="bg-primary">Nombre Completo</th>
                                            <th class="bg-primary">Email</th>
                                            <th class="bg-primary">Edad</th>
                                            <th class="bg-primary">Ciudad</th>
                                            <th class="bg-primary">Tipo</th>
                                            <th class="bg-primary">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td><?php echo $usuario['id']; ?></td>
                                            <td><?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?></td>
                                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                            <td><?php echo $usuario['edad']; ?></td>
                                            <td><?php echo $usuario['ciudad']; ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $usuario['tipo_usuario'] == 'Estudiante' ? 'info' : 'warning'; ?>">
                                                    <?php echo $usuario['tipo_usuario']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="index.php?editar=<?php echo $usuario['id']; ?>" 
                                                       class="btn btn-warning btn-sm px-3">
                                                        Editar
                                                    </a>
                                                    <a href="listar.php?eliminar=<?php echo $usuario['id']; ?>" 
                                                       class="btn btn-danger btn-sm px-3"
                                                       onclick="return confirm('¿Está seguro de eliminar este usuario?')">
                                                        Eliminar
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <div class="text-muted mb-3">
                                    <i class="bi bi-people display-1"></i>
                                </div>
                                <h4 class="text-muted">No hay usuarios registrados</h4>
                                <p class="text-muted">¡Agrega el primer usuario!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Enlace para agregar nuevo usuario -->
                <div class="text-center mt-4">
                    <a href="index.php" class="btn btn-outline-primary">
                         Agregar Nuevo Usuario
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-cierre de alertas después de 5 segundos
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>