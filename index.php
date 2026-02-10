<?php
session_start();
require_once 'config/database.php';

// Instancia de la base de datos
$database = new Database();
$db = $database->getConnection();

// Variables para mensajes
$mensaje = "";
$tipo_mensaje = "";

// Variables del formulario
$id = $nombre = $apellido = $edad = $email = $ciudad = $tipo_usuario = "";
$modo_edicion = false;

// ========== OPERACIONES CRUD ==========

// CREAR o ACTUALIZAR usuario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['guardar'])) {
        $nombre = trim($_POST['nombre']);
        $apellido = trim($_POST['apellido']);
        // CONVERTIR a formato correcto
        $nombre = ucfirst(strtolower($nombre));
        $apellido = ucwords(strtolower($apellido));
        $edad = trim($_POST['edad']);
        $email = trim($_POST['email']);
        $ciudad = trim($_POST['ciudad']);
        $tipo_usuario = $_POST['tipo_usuario'];
        
        // Validaciones básicas
        if (empty($nombre) || empty($apellido) || empty($email) || empty($edad) || empty($ciudad) || empty($tipo_usuario)) {
            $mensaje = "Todos los campos son obligatorios";
            $tipo_mensaje = "danger";
        } 
        elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$/', $nombre)) {
            $mensaje = "El nombre debe ser un solo nombre (sin espacios)";
            $tipo_mensaje = "danger";
            
        // 3. Validar apellido (CON espacios permitidos)
        }
        elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $apellido)) {
            $mensaje = "El apellido solo puede contener letras y espacios";
            $tipo_mensaje = "danger";
        }
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensaje = "El email no es válido";
            $tipo_mensaje = "danger";
        } 
        elseif (!preg_match('/@(gmail\.com|hotmail\.com|outlook\.com|yahoo\.es|yahoo\.com|icloud\.com|live\.com)$/i', $email)) {
            $mensaje = "Dominio no permitido. Use: Gmail, Hotmail, Outlook, Yahoo o iCloud";
            $tipo_mensaje = "danger";
        }
        elseif (!is_numeric($edad) || $edad < 1 || $edad > 120) {
            $mensaje = "La edad debe ser entre 1 y 120 años";
            $tipo_mensaje = "danger";
        } else {
            // Validación específica por tipo de usuario
            $error_validacion = "";
            if ($tipo_usuario == 'Estudiante' && $edad < 5) {
                $error_validacion = "Un estudiante debe tener al menos 5 años";
            } elseif ($tipo_usuario == 'Profesor' && $edad < 18) {
                $error_validacion = "Un profesor debe tener al menos 18 años";
            }
            
            if (!empty($error_validacion)) {
                $mensaje = $error_validacion;
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

                    // Redirigir después de actualizar
                    echo '<script>window.location.href = "index.php?editar=' . $id . '";</script>';
                    exit();

                } else {
                    // Es creación
                    $sql = "INSERT INTO usuarios (nombre, apellido, edad, email, ciudad, tipo_usuario) 
                            VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $db->prepare($sql);
                    $stmt->execute([$nombre, $apellido, $edad, $email, $ciudad, $tipo_usuario]);

                    $mensaje = "Usuario creado correctamente";
                    $tipo_mensaje = "success";

                    // ⭐⭐ REDIRIGIR para evitar reenvío al refrescar ⭐⭐
                    echo '<script>window.location.href = "index.php";</script>';
                    exit();
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://bootswatch.com/5/sketchy/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Agregar/Editar Usuario - CRUD PHP</title>
</head>
<body>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <h1 class="text-center mb-4"> Registro de Usuarios</h1>
                
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
                            <?php echo $modo_edicion ? '✏️ Editar Usuario' : ' Nuevo Usuario'; ?>
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
                                           value="<?php echo $edad; ?>" min="1" max="100" required>
                                    <div class="form-text" id="edad-feedback"></div>
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
                                        Estudiante (mínimo 5 años)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipo_usuario" 
                                           id="profesor" value="Profesor" 
                                           <?php echo ($tipo_usuario == 'Profesor') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="profesor">
                                        Profesor (mínimo 18 años)
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
                
                <!-- Enlace para ver lista de usuarios -->
                <div class="text-center mt-4">
                    <a href="listar.php" class="btn btn-outline-primary">
                         Ver Lista de Usuarios
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validación en tiempo real de edad según tipo de usuario
        document.addEventListener('DOMContentLoaded', function() {
            const edadInput = document.querySelector('input[name="edad"]');
            const tipoRadios = document.querySelectorAll('input[name="tipo_usuario"]');
            const feedbackDiv = document.getElementById('edad-feedback');
            
            function validarEdad() {
                const edad = parseInt(edadInput.value);
                const tipo = document.querySelector('input[name="tipo_usuario"]:checked')?.value;
                
                if (!edad || !tipo) return;
                
                let mensaje = '';
                let esValido = true;
                
                if (tipo === 'Estudiante' && edad < 5) {
                    mensaje = '⚠️ Un estudiante debe tener al menos 5 años';
                    esValido = false;
                } else if (tipo === 'Profesor' && edad < 18) {
                    mensaje = '⚠️ Un profesor debe tener al menos 18 años';
                    esValido = false;
                } else if (edad < 1 || edad > 100) {
                    mensaje = '⚠️ La edad debe estar entre 1 y 100 años';
                    esValido = false;
                } else {
                    mensaje = '✅ Edad válida para ' + tipo.toLowerCase();
                }
                
                feedbackDiv.innerHTML = mensaje;
                feedbackDiv.className = esValido ? 'form-text text-success' : 'form-text text-danger';
            }
            
            if (edadInput) {
                edadInput.addEventListener('input', validarEdid);
                edadInput.addEventListener('change', validarEdad);
            }
            
            if (tipoRadios.length > 0) {
                tipoRadios.forEach(radio => {
                    radio.addEventListener('change', validarEdad);
                });
            }
            
            // Ejecutar validación al cargar si hay valores
            if (edadInput.value || document.querySelector('input[name="tipo_usuario"]:checked')) {
                validarEdad();
            }
        });
        
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