<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitácora de Procesos</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'menu.php'; ?>

    <main class="container-fluid px-4">
        <h1>📖 Historial por Aspirante</h1>
        
        <form id="filtro-form" class="form-container" style="background-color: #e9ecef;">
            <h2 class="h5">Filtros de Búsqueda</h2>
            <div class="row g-2 align-items-center">
                <div class="col-auto"><input type="text" id="filtro-cedula" class="form-control" placeholder="Cédula..."></div>
                <div class="col-auto"><input type="text" id="filtro-nombre" class="form-control" placeholder="Nombre..."></div>
                <div class="col-auto"><select id="filtro-empresa" class="form-select"><option value="">Empresa</option></select></div>
                <div class="col-auto"><select id="filtro-zona" class="form-select"><option value="">Zona</option></select></div>
                <div class="col-auto"><select id="filtro-regional" class="form-select"><option value="">Regional</option></select></div>
                <div class="col-auto"><button type="submit" class="btn btn-primary">Buscar</button></div>
            </div>
        </form>

        <nav id="paginacion-nav" class="d-flex justify-content-between align-items-center my-4" style="display: none;">
            <button id="btn-anterior" class="btn btn-outline-secondary">Anterior</button>
            <span id="pagina-info" class="fw-bold"></span>
            <button id="btn-siguiente" class="btn btn-outline-secondary">Siguiente</button>
        </nav>

        <div id="historial-container">
            <p class="text-center">Cargando registros...</p>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="bitacora.js"></script>
</body>
</html>