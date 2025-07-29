<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gestor RH</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'menu.php'; ?>

    <main class="container text-center py-5">
        <h1 class="display-5 fw-bold">Bienvenido al Gestor de Contrataciones</h1>
        <p class="lead text-muted mb-5">Seleccione una opción para comenzar.</p>

        <div class="row g-4 justify-content-center">
            
            <div class="col-md-6 col-lg-4">
                <a href="index.php" class="dashboard-card">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="bi bi-person-plus-fill card-icon"></i>
                            <h5 class="card-title mt-3">Ingresar / Editar Aspirante</h5>
                            <p class="card-text">Añadir aspirantes o editar su proceso de vinculación.</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4">
                <a href="bitacora.php" class="dashboard-card">
                    <div class="card h-100">
                        <div class="card-body">
                            <i class="bi bi-journal-text card-icon"></i>
                            <h5 class="card-title mt-3">Bitácora de Procesos</h5>
                            <p class="card-text">Consultar el historial de fases de todos los aspirantes.</p>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4">
                <a href="dashboard.php" class="dashboard-card"> <div class="card h-100">
                        <div class="card-body">
                            <i class="bi bi-bar-chart-line-fill card-icon"></i>
                            <h5 class="card-title mt-3">Indicadores (Dashboard)</h5>
                            <p class="card-text">Visualizar estadísticas, KPIs y tiempos del proceso.</p>
                        </div>
                    </div>
                </a>
            </div>

        </div> </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>