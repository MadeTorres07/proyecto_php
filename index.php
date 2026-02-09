<?php
session_start();
require_once 'config/database.php';

// Instancia de la base de datos
$database = new Database();
$db = $database->getConnection();

// Variables para mensajes
$mensaje = "";
$tipo_mensaje = ""; // success, danger, warning, info

// Variables del formulario
$id = $nombre = $apellido = $edad = $email = $ciudad = $tipo_usuario = "";
$modo_edicion = false;

// ========== OPERACIONES CRUD ==========

// CREAR o ACTUALIZAR usuario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['guardar'])) {
        $nombre = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        $edad = trim($_POST['edad']);
        $email = trim($_POST['email']);
        $ciudad = trim($_POST['ciudad']);
        $tipo_usuario = $_POST['tipo_usuario'];
        
        // Validaciones básicas
        if (empty($nombre) || empty($apellido) || empty($email)) {
            $mensaje = "Todos los campos son obligatorios";
            $tipo_mensaje = "danger";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensaje = "El email no es válido";
            $tipo_mensaje = "danger";
        } else {
            try {
                // Si hay un ID, es actualización
                if (!empty($_POST['id'])) {
                    $id = $_POST['id'];
                    $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, edad = ?, 
                            email = ?, ciudad = ?, tipo_usuario = ? WHERE id = ?";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$nombre, $apellido, $edad, $email, $ciudad, $tipo_usuario, $id]);
                    
                    $mensaje = "Usuario actualizado correctamente";
                    $tipo_mensaje = "success";
                } else {
                    // Es creación
                    $sql = "INSERT INTO usuarios (nombre, apellido, edad, email, ciudad, tipo_usuario) 
                            VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$nombre, $apellido, $edad, $email, $ciudad, $tipo_usuario]);
                    
                    $mensaje = "Usuario creado correctamente";
                    $tipo_mensaje = "success";
                    
                    // Limpiar formulario después de crear
                    $nombre = $apellido = $edad = $email = $ciudad = "";
                    $tipo_usuario = "Estudiante";
                }
            } catch(PDOException $e) {
                if ($e->getCode() == 23000) { // Error de duplicado
                    $mensaje = "El email ya está registrado";
                } else {
                    $mensaje = "Error: " . $e->getMessage();
                }
                $tipo_mensaje = "danger";
            }
        }
    }
}

// EDITAR usuario (cargar datos en formulario)
if (isset($_GET['editar'])) {
    $id = $_GET['editar'];
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario) {
        $modo_edicion = true;
        $nombre = $usuario['nombre'];
        $apellido = $usuario['apellido'];
        $edad = $usuario['edad'];
        $email = $usuario['email'];
        $ciudad = $usuario['ciudad'];
        $tipo_usuario = $usuario['tipo_usuario'];
    }
}

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
    <link rel="stylesheet" href="https://bootswatch.com/5/morph/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>CRUD PHP + MySQL</title>
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4"> Registro de Usuarios</h1>
        
        <!-- Mensajes de alerta -->
        <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
            <?php echo $mensaje; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <!-- Formulario -->
            <div class="col-lg-5">
                <div class="card shadow">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <?php echo $modo_edicion ? ' Editar Usuario' : ' Nuevo Usuario'; ?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label">Nombre *</label>
                                    <input type="text" name="nombre" class="form-control" 
                                           value="<?php echo htmlspecialchars($nombre); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="apellido" class="form-label">Apellido *</label>
                                    <input type="text" name="apellido" class="form-control" 
                                           value="<?php echo htmlspecialchars($apellido); ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edad" class="form-label">Edad *</label>
                                    <input type="number" name="edad" class="form-control" 
                                           value="<?php echo $edad; ?>" min="1" max="120" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" name="email" class="form-control" 
                                           value="<?php echo htmlspecialchars($email); ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="ciudad" class="form-label">Ciudad *</label>
                                <select class="form-select" name="ciudad" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Bogotá" <?php echo ($ciudad == 'Bogotá') ? 'selected' : ''; ?>>Bogotá</option>
                                    <option value="Medellín" <?php echo ($ciudad == 'Medellín') ? 'selected' : ''; ?>>Medellín</option>
                                    <option value="Cali" <?php echo ($ciudad == 'Cali') ? 'selected' : ''; ?>>Cali</option>
                                    <option value="Barranquilla" <?php echo ($ciudad == 'Barranquilla') ? 'selected' : ''; ?>>Barranquilla</option>
                                    <option value="Cartagena" <?php echo ($ciudad == 'Cartagena') ? 'selected' : ''; ?>>Cartagena</option>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Tipo de Usuario *</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipo_usuario" 
                                           id="estudiante" value="Estudiante" 
                                           <?php echo ($tipo_usuario == 'Estudiante' || empty($tipo_usuario)) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="estudiante">
                                        Estudiante
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipo_usuario" 
                                           id="profesor" value="Profesor" 
                                           <?php echo ($tipo_usuario == 'Profesor') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="profesor">
                                        Profesor
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <?php if ($modo_edicion): ?>
                                    <a href="index.php" class="btn btn-secondary me-md-2">Cancelar</a>
                                <?php endif; ?>
                                <button type="submit" name="guardar" class="btn btn-primary">
                                    <?php echo $modo_edicion ? 'Actualizar' : 'Guardar'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Tabla de usuarios -->
            <div class="col-lg-7">
                <div class="card shadow">
                    <div class="card-header">
                        <h4 class="mb-0"> Usuarios Registrados (<?php echo count($usuarios); ?>)</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Edad</th>
                                        <th>Ciudad</th>
                                        <th>Tipo</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($usuarios) > 0): ?>
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
                                                <a href="?editar=<?php echo $usuario['id']; ?>" 
                                                   class="btn btn-sm btn-warning">
                                                     Editar
                                                </a>
                                                <a href="?eliminar=<?php echo $usuario['id']; ?>" 
                                                   class="btn btn-sm btn-danger"
                                                   onclick="return confirm('¿Está seguro de eliminar este usuario?')">
                                                     Eliminar
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="text-muted">
                                                    No hay usuarios registrados. ¡Agrega el primero!
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
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