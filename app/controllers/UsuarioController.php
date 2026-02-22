<?php
require_once __DIR__ . '/../models/Usuario.php';

class UsuarioController {
    private $db;
    private $usuario;
    
    public function __construct($db) {
        $this->db = $db;
        $this->usuario = new Usuario($db);
    }
    
    /**
     * Procesar guardar (crear o actualizar)
     */
    public function guardar($datos) {
        // Variables para resultados
        $resultado = [
            'success' => false,
            'mensaje' => '',
            'tipo' => 'danger',
            'redirect' => null
        ];
        
        // Sanitizar y formatear datos
        $nombre = trim($datos['nombre'] ?? '');
        $apellido = trim($datos['apellido'] ?? '');
        $edad = trim($datos['edad'] ?? '');
        $email = trim($datos['email'] ?? '');
        $ciudad = $datos['ciudad'] ?? '';
        $tipo_usuario = $datos['tipo_usuario'] ?? '';
        $id = $datos['id'] ?? '';
        
        // Formatear a mayúsculas iniciales
        $nombre = ucfirst(strtolower($nombre));
        $apellido = ucwords(strtolower($apellido));
        
        // ===== VALIDACIONES =====
        // 1. Campos obligatorios
        if (empty($nombre) || empty($apellido) || empty($email) || empty($edad) || empty($ciudad) || empty($tipo_usuario)) {
            $resultado['mensaje'] = "Todos los campos son obligatorios";
            return $resultado;
        }
        
        // 2. Validar nombre (solo letras, sin espacios)
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ]+$/', $nombre)) {
            $resultado['mensaje'] = "El nombre debe ser un solo nombre (sin espacios) y solo puede contener letras";
            return $resultado;
        }
        
        // 3. Validar apellido (letras con espacios permitidos)
        if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $apellido)) {
            $resultado['mensaje'] = "El apellido solo puede contener letras y espacios";
            return $resultado;
        }
        
        // 4. Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $resultado['mensaje'] = "El email no es válido";
            return $resultado;
        }
        
        // 5. Validar dominio de email
        if (!preg_match('/@(gmail\.com|hotmail\.com|outlook\.com|yahoo\.es|yahoo\.com|icloud\.com|live\.com)$/i', $email)) {
            $resultado['mensaje'] = "Dominio no permitido. Use: Gmail, Hotmail, Outlook, Yahoo o iCloud";
            return $resultado;
        }
        
        // 6. Validar edad
        if (!is_numeric($edad) || $edad < 1 || $edad > 120) {
            $resultado['mensaje'] = "La edad debe ser entre 1 y 120 años";
            return $resultado;
        }
        
        // 7. Validación específica por tipo de usuario
        if ($tipo_usuario == 'Estudiante' && $edad < 5) {
            $resultado['mensaje'] = "Un estudiante debe tener al menos 5 años";
            return $resultado;
        }
        if ($tipo_usuario == 'Profesor' && $edad < 18) {
            $resultado['mensaje'] = "Un profesor debe tener al menos 18 años";
            return $resultado;
        }
        
        // ===== OPERACIONES CON BD =====
        try {
            // Verificar email duplicado (excepto si es el mismo usuario en edición)
            if ($this->usuario->emailExiste($email, $id)) {
                $resultado['mensaje'] = "El email ya está registrado";
                return $resultado;
            }
            
            $datosProcesados = [
                'nombre' => $nombre,
                'apellido' => $apellido,
                'edad' => $edad,
                'email' => $email,
                'ciudad' => $ciudad,
                'tipo_usuario' => $tipo_usuario
            ];
            
            if (!empty($id)) {
                // ACTUALIZAR
                $datosProcesados['id'] = $id;
                $this->usuario->actualizar($datosProcesados);
                
                $resultado['success'] = true;
                $resultado['mensaje'] = "Usuario actualizado correctamente";
                $resultado['tipo'] = "success";
                $resultado['redirect'] = "index.php?editar=" . $id;
                
            } else {
                // CREAR
                $this->usuario->crear($datosProcesados);
                
                $resultado['success'] = true;
                $resultado['mensaje'] = "Usuario creado correctamente";
                $resultado['tipo'] = "success";
                $resultado['redirect'] = "index.php";
            }
            
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) { // Error de duplicado
                $resultado['mensaje'] = "El email ya está registrado";
            } else {
                $resultado['mensaje'] = "Error en la base de datos: " . $e->getMessage();
            }
        }
        
        return $resultado;
    }
    
    /**
     * Eliminar un usuario
     */
    public function eliminar($id) {
        $resultado = [
            'success' => false,
            'mensaje' => '',
            'tipo' => 'danger'
        ];
        
        try {
            $this->usuario->eliminar($id);
            $resultado['success'] = true;
            $resultado['mensaje'] = "Usuario eliminado correctamente";
            $resultado['tipo'] = "warning";
            
        } catch(PDOException $e) {
            $resultado['mensaje'] = "Error al eliminar: " . $e->getMessage();
        }
        
        return $resultado;
    }
    
    /**
     * Obtener todos los usuarios
     */
    public function obtenerTodos() {
        return $this->usuario->obtenerTodos();
    }
    
    /**
     * Obtener un usuario por ID
     */
    public function obtenerPorId($id) {
        return $this->usuario->obtenerPorId($id);
    }
}
?>