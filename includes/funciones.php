<?php
// Funciones auxiliares del proyecto

/**
 * Escapa una cadena para evitar inyección SQL
 * @param string $string Cadena a escapar
 * @param object $conexion Conexión a la base de datos
 * @return string Cadena escapada
 */
function escapar($string, $conexion) {
    return $conexion->real_escape_string(strip_tags($string));
}

/**
 * Redirige a una URL
 * @param string $url URL a la que redirigir
 * @return void
 */
function redirigir($url) {
    header("Location: $url");
    exit();
}

/**
 * Muestra un mensaje de alerta
 * @param string $mensaje Mensaje a mostrar
 * @param string $tipo Tipo de alerta (success, danger, info)
 * @return void
 */
function mostrar_alerta($mensaje, $tipo = 'info') {
    echo '<div class="alert alert-' . $tipo . '">' . $mensaje . '</div>';
}

/**
 * Valida si un campo está vacío
 * @param string $campo Contenido del campo
 * @return bool True si está vacío, false si tiene contenido
 */
function campo_vacio($campo) {
    return empty(trim($campo));
}

/**
 * Valida un email
 * @param string $email Email a validar
 * @return bool True si es válido, false si no lo es
 */
function validar_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? true : false;
}
?>
