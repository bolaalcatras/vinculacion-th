<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Vinculación</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'menu.php'; ?>

    <main class="main-container">
        <div class="form-container" style="margin-bottom: 2rem; background-color: #e9ecef;">
            <h2 class="h5">Buscar Aspirante para Editar</h2>
            <form id="form-busqueda" class="row g-3 align-items-end">
                <div class="col">
                    <label for="busqueda-cedula" class="form-label">Cédula del Aspirante</label>
                    <input type="text" class="form-control" id="busqueda-cedula" placeholder="Ingrese cédula...">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-secondary">Buscar</button>
                </div>
            </form>
        </div>
        
        <form id="formulario-vinculacion" class="form-container">
            <input type="hidden" id="id_aspirante" name="id_aspirante" value="">
            
            <div class="d-flex justify-content-between align-items-center">
                <h1>Formulario de Vinculación</h1>
                <span id="estado-aspirante-badge" class="badge fs-6" style="display: none;"></span>
            </div>

            <section>
                <h2>1. Datos del Aspirante</h2>
                <div class="row g-3">
                    <div class="col-md-6"><label for="cedula" class="form-label">Cédula</label><input type="text" id="cedula" name="cedula" class="form-control" required></div>
                    <div class="col-md-6"><label for="nombre" class="form-label">Nombre Completo</label><input type="text" id="nombre" name="nombre" class="form-control" required></div>
                </div>
                <div class="col-md-4">
                        <label for="cargo" class="form-label">Cargo al que Aspira</label>
                        <input type="text" id="cargo" name="cargo" class="form-control">
                </div>
            </section>

            <section>
                <h2>2. Asignación Organizacional</h2>
                <div class="row g-3">
                    <div class="col-md-4"><label for="empresa" class="form-label">Empresa</label><select id="empresa" name="empresa" class="form-select" required></select></div>
                    <div class="col-md-4"><label for="zona" class="form-label">Zona</label><select id="zona" name="zona" class="form-select" required></select></div>
                    <div class="col-md-4"><label for="regional" class="form-label">Regional</label><select id="regional" name="regional" class="form-select" required></select></div>
                </div>
            </section>

            <section>
                <h2>3. Fases del Proceso</h2>
                <div id="fases-container"></div>
            </section>
            
            <hr class="my-4">
            
            <div class="d-flex justify-content-between flex-wrap gap-2">
                <div>
                    <button type="button" id="btn-contratado" class="btn btn-success" disabled>Marcar como Contratado</button>
                    <button type="button" id="btn-rechazado" class="btn btn-danger" disabled>Marcar como Rechazado</button>
                </div>
                <div>
                    <button type="button" id="btn-limpiar" class="btn btn-outline-secondary">Limpiar Formulario</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </div>
        </form>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="main.js"></script>
</body>
</html>