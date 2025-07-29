<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once '../config/db.php';

// Función auxiliar para ejecutar consultas
function ejecutarConsulta($conexion, $sql, $params = [], $types = "") {
    $stmt = $conexion->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error al preparar la consulta: " . $conexion->error);
    }
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $resultado = $stmt->get_result();
    $stmt->close();
    return $resultado;
}

$response = ['success' => false, 'message' => 'Error inicializando la respuesta.'];

try {
    // 1. PREPARACIÓN DE FILTROS
    $params = [];
    $types = "";
    $where_clauses = ["1 = 1"];
    if (!empty($_GET['fecha_inicio'])) { $where_clauses[] = "a.fecha_inicio_proceso >= ?"; $params[] = $_GET['fecha_inicio']; $types .= "s"; }
    if (!empty($_GET['fecha_fin'])) { $where_clauses[] = "a.fecha_inicio_proceso <= ?"; $params[] = $_GET['fecha_fin']; $types .= "s"; }
    if (!empty($_GET['empresa'])) { $where_clauses[] = "a.id_empresa = ?"; $params[] = $_GET['empresa']; $types .= "i"; }
    if (!empty($_GET['zona'])) { $where_clauses[] = "r.id_zona = ?"; $params[] = $_GET['zona']; $types .= "i"; }
    if (!empty($_GET['regional'])) { $where_clauses[] = "a.id_regional = ?"; $params[] = $_GET['regional']; $types .= "i"; }
    if (!empty($_GET['cedula'])) { $where_clauses[] = "a.cedula = ?"; $params[] = $_GET['cedula']; $types .= "s"; }
    $where_sql = implode(" AND ", $where_clauses);

    // 2. CÁLCULO DE KPIs
    $kpis = [];
    $sql_iniciados = "SELECT COUNT(a.id_aspirante) as total FROM aspirantes a LEFT JOIN regionales r ON a.id_regional = r.id_regional WHERE $where_sql";
    $kpis['procesos_iniciados'] = ejecutarConsulta($conexion, $sql_iniciados, $params, $types)->fetch_assoc()['total'] ?? 0;
    $sql_contratados = "SELECT COUNT(DISTINCT a.id_aspirante) as total FROM aspirantes a LEFT JOIN regionales r ON a.id_regional = r.id_regional WHERE $where_sql AND a.estado = 'Contratado'";
    $kpis['contrataciones_efectivas'] = ejecutarConsulta($conexion, $sql_contratados, $params, $types)->fetch_assoc()['total'] ?? 0;
    $sql_tiempo_promedio = "SELECT AVG(DATEDIFF(fecha_fin, fecha_inicio)) as promedio_dias FROM (SELECT a.id_aspirante, MIN(sf.fecha_cumplimiento) as fecha_inicio, MAX(sf.fecha_cumplimiento) as fecha_fin FROM aspirantes a JOIN seguimiento_fases sf ON a.id_aspirante = sf.id_aspirante LEFT JOIN regionales r ON a.id_regional = r.id_regional WHERE $where_sql AND sf.cumplio = 1 AND sf.fecha_cumplimiento IS NOT NULL GROUP BY a.id_aspirante HAVING COUNT(sf.id_fase) > 1 ) as duraciones";
    $kpis['tiempo_promedio_contratacion'] = round(ejecutarConsulta($conexion, $sql_tiempo_promedio, $params, $types)->fetch_assoc()['promedio_dias'] ?? 0, 1);
    $kpis['tasa_conversion'] = ($kpis['procesos_iniciados'] > 0) ? round(($kpis['contrataciones_efectivas'] / $kpis['procesos_iniciados']) * 100, 1) : 0;

    // 3. DATOS PARA GRÁFICOS Y TABLAS
    $sql_grafico_regional = "SELECT r.nombre_regional, AVG(DATEDIFF(t.fecha_fin, t.fecha_inicio)) as promedio_dias FROM (SELECT id_aspirante, MIN(fecha_cumplimiento) as fecha_inicio, MAX(fecha_cumplimiento) as fecha_fin FROM seguimiento_fases WHERE cumplio = 1 AND fecha_cumplimiento IS NOT NULL GROUP BY id_aspirante HAVING COUNT(id_fase) > 1) as t JOIN aspirantes a ON t.id_aspirante = a.id_aspirante JOIN regionales r ON a.id_regional = r.id_regional JOIN zonas z ON r.id_zona = z.id_zona WHERE $where_sql GROUP BY r.nombre_regional ORDER BY promedio_dias DESC";
    $grafico_tiempo_regional = ejecutarConsulta($conexion, $sql_grafico_regional, $params, $types)->fetch_all(MYSQLI_ASSOC);
    
    $sql_pastel_empresa = "SELECT e.nombre_empresa, COUNT(a.id_aspirante) as total FROM aspirantes a JOIN empresas e ON a.id_empresa = e.id_empresa LEFT JOIN regionales r ON a.id_regional = r.id_regional WHERE $where_sql GROUP BY e.nombre_empresa";
    $grafico_distribucion_empresa = ejecutarConsulta($conexion, $sql_pastel_empresa, $params, $types)->fetch_all(MYSQLI_ASSOC);

    // === GRÁFICO COMPARATIVO ACTUALIZADO: INICIADOS, CONTRATADOS Y RECHAZADOS POR MES ===
    $sql_comparativo_mes = "
        SELECT 
            DATE_FORMAT(a.fecha_inicio_proceso, '%Y-%m') AS mes,
            COUNT(DISTINCT a.id_aspirante) AS iniciados,
            COUNT(DISTINCT CASE WHEN a.estado = 'Contratado' THEN a.id_aspirante END) AS contratados,
            COUNT(DISTINCT CASE WHEN a.estado = 'Rechazado' THEN a.id_aspirante END) AS rechazados
        FROM aspirantes a
        LEFT JOIN regionales r ON a.id_regional = r.id_regional
        WHERE $where_sql
        GROUP BY mes
        ORDER BY mes ASC
    ";
    $grafico_comparativo_mes = ejecutarConsulta($conexion, $sql_comparativo_mes, $params, $types)->fetch_all(MYSQLI_ASSOC);

    // Tabla: Duración Promedio entre Fases
    $sql_eventos_fases = "SELECT a.id_aspirante, fm.nombre_fase, sf.fecha_cumplimiento, fm.orden FROM seguimiento_fases sf JOIN fases_maestras fm ON sf.id_fase = fm.id_fase JOIN aspirantes a ON sf.id_aspirante = a.id_aspirante LEFT JOIN regionales r ON a.id_regional = r.id_regional WHERE $where_sql AND sf.cumplio = 1 AND sf.fecha_cumplimiento IS NOT NULL ORDER BY a.id_aspirante, fm.orden ASC";
    $todos_los_eventos = ejecutarConsulta($conexion, $sql_eventos_fases, $params, $types)->fetch_all(MYSQLI_ASSOC);
    $duraciones_por_transicion = [];
    $estado_anterior = [];
    foreach ($todos_los_eventos as $evento) {
        $id_aspirante = $evento['id_aspirante'];
        if (isset($estado_anterior[$id_aspirante])) {
            $fecha1 = date_create($estado_anterior[$id_aspirante]['fecha']);
            $fecha2 = date_create($evento['fecha_cumplimiento']);
            $diferencia = date_diff($fecha1, $fecha2);
            $dias = $diferencia->days;
            $nombre_transicion = $estado_anterior[$id_aspirante]['nombre_fase'] . ' → ' . $evento['nombre_fase'];
            if (!isset($duraciones_por_transicion[$nombre_transicion])) { $duraciones_por_transicion[$nombre_transicion] = []; }
            $duraciones_por_transicion[$nombre_transicion][] = $dias;
        }
        $estado_anterior[$id_aspirante] = ['fecha' => $evento['fecha_cumplimiento'], 'nombre_fase' => $evento['nombre_fase']];
    }
    $tabla_duracion_fases = [];
    foreach ($duraciones_por_transicion as $nombre_transicion => $duraciones) {
        if (count($duraciones) > 0) {
            $promedio = array_sum($duraciones) / count($duraciones);
            $tabla_duracion_fases[] = ['nombre_fase' => $nombre_transicion, 'promedio_dias' => $promedio];
        }
    }

    // 4. ENSAMBLAR Y ENVIAR RESPUESTA FINAL
    $response = [
        'success' => true,
        'kpis' => $kpis,
        'grafico_tiempo_regional' => $grafico_tiempo_regional,
        'tabla_duracion_fases' => $tabla_duracion_fases,
        'grafico_comparativo_mes' => $grafico_comparativo_mes,
        'grafico_distribucion_empresa' => $grafico_distribucion_empresa
    ];

} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
    http_response_code(500);
}

$conexion->close();
echo json_encode($response);
?>