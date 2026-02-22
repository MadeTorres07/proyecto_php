<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://bootswatch.com/5/sketchy/bootstrap.min.css">
    <title>Lista de Usuarios - CRUD PHP</title>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <h1 class="text-center mb-4"> Lista de Usuarios</h1>
                
                <!-- Mensajes de alerta desde sesión -->
                <?php if (isset($_SESSION['mensaje'])): ?>
                <div class="alert alert-<?php echo $_SESSION['tipo_mensaje']; ?> alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['mensaje']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php 
                    unset($_SESSION['mensaje']);
                    unset($_SESSION['tipo_mensaje']);
                endif; 
                ?>
                
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            Usuarios Registrados (<?php echo count($usuarios ?? []); ?>)
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($usuarios)): ?>
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
                                                    <a href="/Proyecto_php/public/index.php?vista=index&editar=<?php echo $usuario['id']; ?>&nombre=<?php echo urlencode($usuario['nombre']); ?>&apellido=<?php echo urlencode($usuario['apellido']); ?>&edad=<?php echo $usuario['edad']; ?>&email=<?php echo urlencode($usuario['email']); ?>&ciudad=<?php echo urlencode($usuario['ciudad']); ?>&tipo_usuario=<?php echo $usuario['tipo_usuario']; ?>" 
                                                       class="btn btn-warning btn-sm px-3">
                                                        Editar
                                                    </a>
                                                    <a href="/Proyecto_php/public/procesar_eliminar.php?id=<?php echo $usuario['id']; ?>"
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
                    <a href="/Proyecto_php/public/index.php?vista=index">
                         Agregar Nuevo Usuario
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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