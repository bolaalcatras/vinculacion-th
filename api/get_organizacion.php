<?php
// =================================================================
// PARTE 1: CÓDIGO PARA MOSTRAR ERRORES (SIEMPRE AL INICIO)
// =================================================================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// =================================================================
// PARTE 2: INCLUIR LA CONEXIÓN (AQUÍ ESTÁ EL REQUIRE_ONCE)
// =================================================================
// Usamos '../' para subir de la carpeta 'api' a la raíz 'vinculacion' y luego entrar a 'config'
require_once '../config/db.php';


// =================================================================
// PARTE 3: PREPARAR Y EJECUTAR LAS CONSULTAS
// =================================================================
$response = [
    'empresas' => [],
    'zonas' => [],
    'regionales' => []
];

// Obtener Empresas
$sql_empresas = "SELECT id_empresa, nombre_empresa FROM empresas ORDER BY nombre_empresa";
$result_empresas = $conexion->query($sql_empresas);
if ($result_empresas) {
    $response['empresas'] = $result_empresas->fetch_all(MYSQLI_ASSOC);
} else {
    die(json_encode(['error' => 'Error al consultar empresas: ' . $conexion->error]));
}

// Obtener Zonas
$sql_zonas = "SELECT id_zona, nombre_zona FROM zonas ORDER BY nombre_zona";
$result_zonas = $conexion->query($sql_zonas);
if ($result_zonas) {
    $response['zonas'] = $result_zonas->fetch_all(MYSQLI_ASSOC);
} else {
    die(json_encode(['error' => 'Error al consultar zonas: ' . $conexion->error]));
}

// Obtener Regionales
$sql_regionales = "SELECT id_regional, nombre_regional, id_zona FROM regionales ORDER BY nombre_regional";
$result_regionales = $conexion->query($sql_regionales);
if ($result_regionales) {
    $response['regionales'] = $result_regionales->fetch_all(MYSQLI_ASSOC);
} else {
    die(json_encode(['error' => 'Error al consultar regionales: ' . $conexion->error]));
}

$conexion->close();


// =================================================================
// PARTE 4: ENVIAR LA RESPUESTA FINAL COMO JSON
// =================================================================
header('Content-Type: application/json');
echo json_encode($response);

?>