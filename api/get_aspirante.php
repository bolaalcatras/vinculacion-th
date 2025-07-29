<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once '../config/db.php';

if (!isset($_GET['cedula']) || empty($_GET['cedula'])) {
    echo json_encode(['success' => false, 'message' => 'No se proporcionó una cédula.']);
    exit;
}

$cedula = $_GET['cedula'];
$response = ['success' => false, 'aspirante' => null];

// Consulta para los datos principales del aspirante
$sql_aspirante = "SELECT * FROM aspirantes WHERE cedula = ? LIMIT 1";
$stmt_aspirante = $conexion->prepare($sql_aspirante);
$stmt_aspirante->bind_param("s", $cedula);
$stmt_aspirante->execute();
$result_aspirante = $stmt_aspirante->get_result();

if ($result_aspirante->num_rows > 0) {
    $aspirante_data = $result_aspirante->fetch_assoc();
    $id_aspirante = $aspirante_data['id_aspirante'];

    // Consulta para obtener el estado de TODAS sus fases
    $sql_fases = "SELECT * FROM seguimiento_fases WHERE id_aspirante = ?";
    $stmt_fases = $conexion->prepare($sql_fases);
    $stmt_fases->bind_param("i", $id_aspirante);
    $stmt_fases->execute();
    $result_fases = $stmt_fases->get_result();

    $fases_data = $result_fases->fetch_all(MYSQLI_ASSOC);

    // Unimos los datos del aspirante y sus fases en una sola respuesta
    $response['success'] = true;
    $response['aspirante'] = $aspirante_data;
    $response['fases'] = $fases_data;

    $stmt_fases->close();
} else {
    $response['message'] = 'No se encontró ningún aspirante con esa cédula.';
}

$stmt_aspirante->close();
$conexion->close();

echo json_encode($response);
?>