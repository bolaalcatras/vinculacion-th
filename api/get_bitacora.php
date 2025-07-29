<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config/db.php';

// La consulta base ahora ordena por cédula y luego por fecha para agrupar
$sql = "
    SELECT 
        a.id_aspirante, a.cedula, a.nombre_completo,
        sf.fecha_cumplimiento, sf.descripcion,
        fm.nombre_fase, r.nombre_regional
    FROM 
        seguimiento_fases AS sf
    JOIN aspirantes AS a ON sf.id_aspirante = a.id_aspirante
    JOIN fases_maestras AS fm ON sf.id_fase = fm.id_fase
    JOIN regionales AS r ON a.id_regional = r.id_regional
    LEFT JOIN zonas AS z ON r.id_zona = z.id_zona
";

// La lógica de filtros sigue funcionando igual
$where_clauses = ["sf.cumplio = TRUE", "sf.fecha_cumplimiento IS NOT NULL"];
$params = [];
$types = "";

if (!empty($_GET['cedula'])) {
    $where_clauses[] = "a.cedula LIKE ?";
    $params[] = "%" . $_GET['cedula'] . "%"; $types .= "s";
}
if (!empty($_GET['nombre'])) {
    $where_clauses[] = "a.nombre_completo LIKE ?";
    $params[] = "%" . $_GET['nombre'] . "%"; $types .= "s";
}
if (!empty($_GET['empresa'])) {
    $where_clauses[] = "a.id_empresa = ?";
    $params[] = $_GET['empresa']; $types .= "i";
}
if (!empty($_GET['zona'])) {
    $where_clauses[] = "r.id_zona = ?";
    $params[] = $_GET['zona']; $types .= "i";
}
if (!empty($_GET['regional'])) {
    $where_clauses[] = "a.id_regional = ?";
    $params[] = $_GET['regional']; $types .= "i";
}

$sql .= " WHERE " . implode(" AND ", $where_clauses);
$sql .= " ORDER BY a.cedula, sf.fecha_cumplimiento ASC"; // Ordenamos para agrupar

$stmt = $conexion->prepare($sql);

$response = ['success' => false, 'aspirantes' => []];

if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    // --- NUEVA LÓGICA DE AGRUPACIÓN ---
    $aspirantes_agrupados = [];
    while ($row = $result->fetch_assoc()) {
        $cedula = $row['cedula'];
        // Si es la primera vez que vemos esta cédula, creamos su entrada
        if (!isset($aspirantes_agrupados[$cedula])) {
            $aspirantes_agrupados[$cedula] = [
                'nombre_completo' => $row['nombre_completo'],
                'regional' => $row['nombre_regional'],
                'historial' => []
            ];
        }
        // Añadimos el evento de la fase a su historial
        $aspirantes_agrupados[$cedula]['historial'][] = [
            'fecha' => $row['fecha_cumplimiento'],
            'fase' => $row['nombre_fase'],
            'descripcion' => $row['descripcion']
        ];
    }
    
    $response['success'] = true;
    $response['aspirantes'] = $aspirantes_agrupados;
    $stmt->close();
} else {
    $response['message'] = 'Error al preparar la consulta: ' . $conexion->error;
}

$conexion->close();

header('Content-Type: application/json');
echo json_encode($response);
?>