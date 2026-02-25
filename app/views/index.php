<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://bootswatch.com/5/slate/bootstrap.min.css">
    <title>Agregar/Editar Usuario - CRUD PHP</title>
</head>
<body>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <h1 class="text-center mb-4"> Registro de Usuarios</h1>
                
                <!-- Mensajes de alerta desde sesión -->
                <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?> alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['mensaje']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php 
                    // Limpiar mensajes después de mostrarlos
                    unset($_SESSION['mensaje']);
                    unset($_SESSION['tipo_mensaje']);
                endif; 
                ?>
                
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <?php echo isset($_GET['editar']) ? '✏️ Editar Usuario' : ' Nuevo Usuario'; ?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/Proyecto_php/public/procesar_guardar.php">
                            <input type="hidden" name="id" value="<?php echo $_GET['editar'] ?? ''; ?>">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nombre" class="form-label">Nombre *</label>
                                    <input type="text" name="nombre" class="form-control" 
                                           value="<?php echo htmlspecialchars($_SESSION['datos_form']['nombre'] ?? $_GET['nombre'] ?? ''); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="apellido" class="form-label">Apellido *</label>
                                    <input type="text" name="apellido" class="form-control" 
                                           value="<?php echo htmlspecialchars($_SESSION['datos_form']['apellido'] ?? $_GET['apellido'] ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="edad" class="form-label">Edad *</label>
                                    <input type="number" name="edad" class="form-control" 
                                           value="<?php echo $_SESSION['datos_form']['edad'] ?? $_GET['edad'] ?? ''; ?>" min="1" max="100" required>
                                    <div class="form-text" id="edad-feedback"></div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" name="email" class="form-control" 
                                           value="<?php echo htmlspecialchars($_SESSION['datos_form']['email'] ?? $_GET['email'] ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="ciudad" class="form-label">Ciudad *</label>
                                <select class="form-select" name="ciudad" required>
                                    <option value="">Seleccione...</option>
                                    <?php 
                                    $ciudades = ['Bogotá', 'Medellín', 'Cali', 'Barranquilla', 'Cartagena'];
                                    $ciudad_seleccionada = $_SESSION['datos_form']['ciudad'] ?? $_GET['ciudad'] ?? '';
                                    foreach ($ciudades as $ciudad): 
                                    ?>
                                        <option value="<?php echo $ciudad; ?>" <?php echo ($ciudad_seleccionada == $ciudad) ? 'selected' : ''; ?>>
                                            <?php echo $ciudad; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Tipo de Usuario *</label>
                                <?php 
                                $tipo_seleccionado = $_SESSION['datos_form']['tipo_usuario'] ?? $_GET['tipo_usuario'] ?? 'Estudiante';
                                ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipo_usuario" 
                                           id="estudiante" value="Estudiante" 
                                           <?php echo ($tipo_seleccionado == 'Estudiante') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="estudiante">
                                        Estudiante (mínimo 5 años)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipo_usuario" 
                                           id="profesor" value="Profesor" 
                                           <?php echo ($tipo_seleccionado == 'Profesor') ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="profesor">
                                        Profesor (mínimo 18 años)
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <?php if (isset($_GET['editar'])): ?>
                                    <a href="/Proyecto_php/public/index.php?vista=index" class="btn btn-secondary me-md-2">Cancelar</a>
                                <?php endif; ?>
                                <button type="submit" name="guardar" class="btn btn-primary">
                                    <?php echo isset($_GET['editar']) ? 'Actualizar' : 'Guardar'; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Enlace para ver lista de usuarios -->
                <div class="text-center mt-4">
                    <a href="/Proyecto_php/public/index.php?vista=listar">
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
                edadInput.addEventListener('input', validarEdad);
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
<?php 
// Limpiar datos del formulario después de mostrarlos
unset($_SESSION['datos_form']);
?>