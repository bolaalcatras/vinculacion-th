<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
require_once '../config/db.php';

$response = ['success' => false, 'message' => 'Ocurrió un error inesperado.'];
$id_aspirante = $_POST['id_aspirante'] ?? null; // Usamos el ID si existe

$conexion->begin_transaction();

try {
    if (empty($id_aspirante)) {
        // --- LÓGICA DE INSERTAR (CREAR NUEVO) ---
        $sql_aspirante = "INSERT INTO aspirantes (cedula, nombre_completo, id_empresa, id_regional) VALUES (?, ?, ?, ?)";
        $stmt_aspirante = $conexion->prepare($sql_aspirante);
        $stmt_aspirante->bind_param("ssii", $_POST['cedula'], $_POST['nombre'], $_POST['empresa'], $_POST['regional']);
        $stmt_aspirante->execute();
        $id_aspirante = $conexion->insert_id; // Obtenemos el nuevo ID
        $stmt_aspirante->close();

        // Inserta todas las fases por primera vez
        $sql_fases_maestras = "SELECT id_fase FROM fases_maestras";
        $resultado_fases_maestras = $conexion->query($sql_fases_maestras);
        $fases_maestras = $resultado_fases_maestras->fetch_all(MYSQLI_ASSOC);
        
        $sql_fase = "INSERT INTO seguimiento_fases (id_aspirante, id_fase, cumplio, fecha_cumplimiento, descripcion) VALUES (?, ?, ?, ?, ?)";
        $stmt_fase_insert = $conexion->prepare($sql_fase);

        foreach ($fases_maestras as $fase_maestra) {
            $id_fase_actual = $fase_maestra['id_fase'];
            $datos_fase_enviada = $_POST['fases'][$id_fase_actual] ?? null;
            $cumplio = isset($datos_fase_enviada['cumplio']) ? 1 : 0;
            $fecha = !empty($datos_fase_enviada['fecha']) ? $datos_fase_enviada['fecha'] : NULL;
            $descripcion = !empty($datos_fase_enviada['descripcion']) ? $datos_fase_enviada['descripcion'] : NULL;
            $stmt_fase_insert->bind_param("iisss", $id_aspirante, $id_fase_actual, $cumplio, $fecha, $descripcion);
            $stmt_fase_insert->execute();
        }
        $stmt_fase_insert->close();
        $response['message'] = 'Aspirante creado con éxito.';

    } else {
        // --- LÓGICA DE ACTUALIZAR (EDITAR EXISTENTE) ---
        $sql_aspirante = "UPDATE aspirantes SET cedula=?, nombre_completo=?, id_empresa=?, id_regional=? WHERE id_aspirante=?";
        $stmt_aspirante = $conexion->prepare($sql_aspirante);
        $stmt_aspirante->bind_param("ssiii", $_POST['cedula'], $_POST['nombre'], $_POST['empresa'], $_POST['regional'], $id_aspirante);
        $stmt_aspirante->execute();
        $stmt_aspirante->close();
        
        // Actualiza cada una de las fases
        $sql_fase_update = "UPDATE seguimiento_fases SET cumplio=?, fecha_cumplimiento=?, descripcion=? WHERE id_aspirante=? AND id_fase=?";
        $stmt_fase_update = $conexion->prepare($sql_fase_update);
        
        if (isset($_POST['fases']) && is_array($_POST['fases'])) {
            foreach ($_POST['fases'] as $id_fase => $datos_fase) {
                $cumplio = isset($datos_fase['cumplio']) ? 1 : 0;
                $fecha = !empty($datos_fase['fecha']) ? $datos_fase['fecha'] : NULL;
                $descripcion = !empty($datos_fase['descripcion']) ? $datos_fase['descripcion'] : NULL;
                $stmt_fase_update->bind_param("sssii", $cumplio, $fecha, $descripcion, $id_aspirante, $id_fase);
                $stmt_fase_update->execute();
            }
        }
        $stmt_fase_update->close();
        $response['message'] = 'Aspirante actualizado con éxito.';
    }

    $conexion->commit();
    $response['success'] = true;

} catch (Exception $e) {
    $conexion->rollback();
    $response['message'] = $e->getMessage();
}

$conexion->close();
echo json_encode($response);
?>