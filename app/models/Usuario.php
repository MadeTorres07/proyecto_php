<?php
class Usuario {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Crear un nuevo usuario
     */
    public function crear($datos) {
        $sql = "INSERT INTO usuarios (nombre, apellido, edad, email, ciudad, tipo_usuario) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['nombre'], 
            $datos['apellido'], 
            $datos['edad'],
            $datos['email'], 
            $datos['ciudad'], 
            $datos['tipo_usuario']
        ]);
    }
    
    /**
     * Actualizar un usuario existente
     */
    public function actualizar($datos) {
        $sql = "UPDATE usuarios SET nombre = ?, apellido = ?, edad = ?, 
                email = ?, ciudad = ?, tipo_usuario = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $datos['nombre'],
            $datos['apellido'],
            $datos['edad'],
            $datos['email'],
            $datos['ciudad'],
            $datos['tipo_usuario'],
            $datos['id']
        ]);
    }
    
    /**
     * Eliminar un usuario por ID
     */
    public function eliminar($id) {
        $sql = "DELETE FROM usuarios WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Obtener todos los usuarios
     */
    public function obtenerTodos() {
        $sql = "SELECT * FROM usuarios ORDER BY creado_en DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtener un usuario por ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Verificar si un email ya existe (excluyendo un ID específico para edición)
     */
    public function emailExiste($email, $excluirId = null) {
        if ($excluirId) {
            $sql = "SELECT COUNT(*) FROM usuarios WHERE email = ? AND id != ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email, $excluirId]);
        } else {
            $sql = "SELECT COUNT(*) FROM usuarios WHERE email = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);
        }
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Contar total de usuarios
     */
    public function contarTodos() {
        $sql = "SELECT COUNT(*) FROM usuarios";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
?>