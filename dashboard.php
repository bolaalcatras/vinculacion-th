<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indicadores y Dashboard - Gestor RH</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <?php include 'menu.php'; ?>

    <main class="container-fluid px-4 py-4">
        <h1>📊 Indicadores (Dashboard)</h1>
        
        <form id="filtro-form" class="form-container mb-4" style="background-color: #e9ecef;">
            <h2 class="h5">Filtros</h2>
            <div class="row g-3 align-items-end">
                <div class="col-lg col-md-6">
                    <label for="filtro-fecha-inicio" class="form-label">Fecha Inicio</label>
                    <input type="date" id="filtro-fecha-inicio" class="form-control">
                </div>
                <div class="col-lg col-md-6">
                    <label for="filtro-fecha-fin" class="form-label">Fecha Fin</label>
                    <input type="date" id="filtro-fecha-fin" class="form-control">
                </div>
                <div class="col-lg col-md-6">
                    <label for="filtro-cedula" class="form-label">Cédula</label>
                    <input type="text" id="filtro-cedula" class="form-control" placeholder="Cédula...">
                </div>
                <div class="col-lg col-md-6">
                    <label for="filtro-empresa" class="form-label">Empresa</label>
                    <select id="filtro-empresa" class="form-select"></select>
                </div>
                <div class="col-lg col-md-6">
                    <label for="filtro-regional" class="form-label">Regional</label>
                    <select id="filtro-regional" class="form-select"></select>
                </div>
                <div class="col-lg col-md-6">
                    <label for="filtro-zona" class="form-label">Zona</label>
                    <select id="filtro-zona" class="form-select"></select>
                </div>
                <div class="col-lg-12">
                    <button type="submit" class="btn btn-primary w-100 mt-3">Aplicar Filtros</button>
                </div>
            </div>
        </form>

        <div class="row g-4 mb-4" id="kpi-cards-container">
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100"><div class="card-body d-flex flex-column justify-content-center">
                    <h6 class="card-subtitle mb-2 text-muted">Contrataciones Efectivas</h6>
                    <p id="kpi-contratados" class="card-title h2">--</p>
                </div></div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100"><div class="card-body d-flex flex-column justify-content-center">
                    <h6 class="card-subtitle mb-2 text-muted">Tiempo Promedio Contratación</h6>
                    <p class="card-title h2"><span id="kpi-tiempo-promedio">--</span> Días</p>
                </div></div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100"><div class="card-body d-flex flex-column justify-content-center">
                    <h6 class="card-subtitle mb-2 text-muted">Procesos Iniciados</h6>
                    <p id="kpi-procesos-iniciados" class="card-title h2">--</p>
                </div></div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center h-100"><div class="card-body d-flex flex-column justify-content-center">
                    <h6 class="card-subtitle mb-2 text-muted">Tasa de Conversión</h6>
                    <p class="card-title h2"><span id="kpi-conversion">--</span> %</p>
                </div></div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card h-100">
                    <div class="card-header">Procesos Iniciados vs. Contrataciones por Mes</div>
                    <div class="card-body">
                        <canvas id="grafico-comparativo-mes"></canvas>
                    </div>
                </div>
            </div>
             <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header">Distribución por Empresa</div>
                    <div class="card-body d-flex align-items-center justify-content-center">
                        <canvas id="grafico-pastel-empresa" style="max-height: 350px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mt-4">
                <div class="card h-100">
                    <div class="card-header">Tiempo Promedio por Regional (Días)</div>
                    <div class="card-body"><canvas id="graficoRegional"></canvas></div>
                </div>
            </div>
            <div class="col-lg-6 mt-4">
                <div class="card h-100">
                    <div class="card-header">Cuello de Botella: Duración Promedio por Fase (Días)</div>
                    <div class="card-body table-responsive" style="max-height: 400px;">
                        <table class="table table-sm">
                            <tbody id="tabla-fases-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="dashboard.js"></script>
</body>
</html>