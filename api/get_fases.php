<?php
// Habilitamos la visualización de errores para depuración
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Incluimos la conexión a la base de datos
require_once '../config/db.php';

// Preparamos la respuesta que enviaremos
$response = [];

// Consultamos todas las fases maestras, ordenadas por el campo 'orden'
$sql = "SELECT id_fase, nombre_fase FROM fases_maestras ORDER BY orden ASC";
$result = $conexion->query($sql);

if ($result) {
    // Guardamos todas las filas encontradas en el array de respuesta
    $response = $result->fetch_all(MYSQLI_ASSOC);
} else {
    // Si hay un error, lo notificamos
    // http_response_code(500); // Envía un código de error de servidor
    $response = ['error' => 'Error al consultar las fases: ' . $conexion->error];
}

$conexion->close();

// Enviamos la respuesta final como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>