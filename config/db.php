<?php
// Configuración de la conexión a la base de datos

// 1. Define tus credenciales
$servidor = "vinculacionth.electrocreditosdelcauca.com";
$usuario = "electroc_webmast";
$password = "4Rxf1vNLYW_w";
$base_de_datos = "electroc_vinculacionth";

// 2. Crear la conexión
$conexion = new mysqli($servidor, $usuario, $password, $base_de_datos);

// 3. Establecer el juego de caracteres a UTF-8
$conexion->set_charset("utf8mb4");

// 4. Verificar si la conexión falló
if ($conexion->connect_error) {
    // Detener la ejecución y mostrar el error (esto solo se ejecutará si hay un problema)
    die("Error de conexión: " . $conexion->connect_error);
}

// IMPORTANTE: No debe haber ninguna línea 'echo' o 'print' aquí.
// El archivo debe terminar sin imprimir nada si la conexión es exitosa.
?>