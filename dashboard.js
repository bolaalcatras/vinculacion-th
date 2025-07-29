document.addEventListener('DOMContentLoaded', function() {
    
    // --- Referencias a los elementos del DOM ---
    const filtroForm = document.getElementById('filtro-form');
    const filtroFechaInicio = document.getElementById('filtro-fecha-inicio');
    const filtroFechaFin = document.getElementById('filtro-fecha-fin');
    const filtroCedula = document.getElementById('filtro-cedula');
    const filtroEmpresa = document.getElementById('filtro-empresa');
    const filtroRegional = document.getElementById('filtro-regional');
    const filtroZona = document.getElementById('filtro-zona');
    
    const kpiContratados = document.getElementById('kpi-contratados');
    const kpiTiempoPromedio = document.getElementById('kpi-tiempo-promedio');
    const kpiProcesosIniciados = document.getElementById('kpi-procesos-iniciados');
    const kpiConversion = document.getElementById('kpi-conversion');
    const tablaFasesBody = document.getElementById('tabla-fases-body');
    
    let graficoRegionalChart, graficoComparativoChart, graficoPastelChart;

    // --- Inicialización de Gráficos ---
    function inicializarGraficos() {
        // Gráfico de Barras (Regional)
        const ctxRegional = document.getElementById('graficoRegional').getContext('2d');
        graficoRegionalChart = new Chart(ctxRegional, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Días Promedio',
                    data: [],
                    backgroundColor: 'rgba(13, 110, 253, 0.5)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                scales: { x: { beginAtZero: true } },
                plugins: { legend: { display: false } }
            }
        });

        // Gráfico de Pastel (Empresa)
        const ctxPastel = document.getElementById('grafico-pastel-empresa').getContext('2d');
        graficoPastelChart = new Chart(ctxPastel, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: ['rgba(13, 110, 253, 0.7)', 'rgba(25, 135, 84, 0.7)', 'rgba(255, 193, 7, 0.7)']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true
            }
        });

        // Gráfico de Barras Comparativo (Iniciados vs Contratados vs Rechazados)
        const ctxComparativo = document.getElementById('grafico-comparativo-mes').getContext('2d');
        graficoComparativoChart = new Chart(ctxComparativo, {
            type: 'bar',
            data: {
                labels: [], // Meses
                datasets: [
                    {
                        label: 'Procesos Iniciados',
                        data: [],
                        backgroundColor: 'rgba(108, 117, 125, 0.5)', // Gris
                    },
                    {
                        label: 'Contrataciones',
                        data: [],
                        backgroundColor: 'rgba(25, 135, 84, 0.7)', // Verde
                    },
                    {
                        label: 'Rechazados',
                        data: [],
                        backgroundColor: 'rgba(135, 0, 0, 0.9)', // Rojo
                    }
                ]
            },
            options: {
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // --- Función para poblar los filtros ---
    async function poblarFiltros() {
        try {
            const respuesta = await fetch('api/get_organizacion.php');
            const datos = await respuesta.json();
            const rellenarSelect = (el, data, val, txt, def) => {
                el.innerHTML = `<option value="">${def}</option>`;
                data.forEach(i => { el.innerHTML += `<option value="${i[val]}">${i[txt]}</option>`; });
            };
            rellenarSelect(filtroEmpresa, datos.empresas, 'id_empresa', 'nombre_empresa', 'Toda Empresa');
            rellenarSelect(filtroZona, datos.zonas, 'id_zona', 'nombre_zona', 'Toda Zona');
            rellenarSelect(filtroRegional, datos.regionales, 'id_regional', 'nombre_regional', 'Toda Regional');
        } catch (error) { console.error("No se pudieron cargar los filtros", error); }
    }

    // --- Función principal para actualizar todo el dashboard ---
    async function actualizarDashboard() {
        const params = new URLSearchParams({
            fecha_inicio: filtroFechaInicio.value,
            fecha_fin: filtroFechaFin.value,
            cedula: filtroCedula.value,
            regional: filtroRegional.value,
            zona: filtroZona.value,
            empresa: filtroEmpresa.value
        });

        try {
            const respuesta = await fetch(`api/get_indicadores.php?${params.toString()}`);
            const data = await respuesta.json();
            if (!data.success) throw new Error(data.message || 'Error en la API');
            
            kpiContratados.textContent = data.kpis.contrataciones_efectivas;
            kpiTiempoPromedio.textContent = data.kpis.tiempo_promedio_contratacion;
            kpiProcesosIniciados.textContent = data.kpis.procesos_iniciados;
            kpiConversion.textContent = data.kpis.tasa_conversion;

            graficoRegionalChart.data.labels = data.grafico_tiempo_regional.map(item => item.nombre_regional);
            graficoRegionalChart.data.datasets[0].data = data.grafico_tiempo_regional.map(item => item.promedio_dias);
            graficoRegionalChart.update();

            graficoComparativoChart.data.labels = data.grafico_comparativo_mes.map(item => item.mes);
            graficoComparativoChart.data.datasets[0].data = data.grafico_comparativo_mes.map(item => item.iniciados);
            graficoComparativoChart.data.datasets[1].data = data.grafico_comparativo_mes.map(item => item.contratados);
            graficoComparativoChart.data.datasets[2].data = data.grafico_comparativo_mes.map(item => item.rechazados);
            graficoComparativoChart.update();

            graficoPastelChart.data.labels = data.grafico_distribucion_empresa.map(item => item.nombre_empresa);
            graficoPastelChart.data.datasets[0].data = data.grafico_distribucion_empresa.map(item => item.total);
            graficoPastelChart.update();
            
            tablaFasesBody.innerHTML = '';
            if (data.tabla_duracion_fases && data.tabla_duracion_fases.length > 0) {
                data.tabla_duracion_fases.forEach(fase => {
                    tablaFasesBody.innerHTML += `<tr><td>${fase.nombre_fase}</td><td class="text-end"><strong>${parseFloat(fase.promedio_dias).toFixed(1)} días</strong></td></tr>`;
                });
            } else {
                tablaFasesBody.innerHTML = '<tr><td colspan="2">No hay datos para esta vista.</td></tr>';
            }
        } catch (error) {
            console.error("Error al actualizar dashboard:", error);
            alert("No se pudo cargar la información del dashboard.");
        }
    }

    // --- Event Listener para el formulario de filtros ---
    filtroForm.addEventListener('submit', function(event) {
        event.preventDefault();
        actualizarDashboard();
    });

    // --- Carga inicial de la página ---
    inicializarGraficos();
    poblarFiltros();
    actualizarDashboard();
});