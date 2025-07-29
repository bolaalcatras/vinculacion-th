<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once '../config/db.php';

// Leemos los datos que nos envía el JavaScript
$data = json_decode(file_get_contents('php://input'), true);

$id_aspirante = $data['id_aspirante'] ?? null;
$nuevo_estado = $data['estado'] ?? null;

if (empty($id_aspirante) || !in_array($nuevo_estado, ['Contratado', 'Rechazado'])) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
    exit;
}

$sql = "UPDATE aspirantes SET estado = ? WHERE id_aspirante = ?";
$stmt = $conexion->prepare($sql);

if ($stmt) {
    $stmt->bind_param("si", $nuevo_estado, $id_aspirante);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Estado del aspirante actualizado a ' . $nuevo_estado]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta.']);
}

$conexion->close();
?>