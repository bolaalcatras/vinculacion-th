document.addEventListener('DOMContentLoaded', function() {
    
    // --- Referencias a los elementos ---
    const filtroForm = document.getElementById('filtro-form');
    const historialContainer = document.getElementById('historial-container');
    const paginacionNav = document.getElementById('paginacion-nav');
    const btnAnterior = document.getElementById('btn-anterior');
    const btnSiguiente = document.getElementById('btn-siguiente');
    const paginaInfo = document.getElementById('pagina-info');
    
    // Referencias a los campos del filtro
    const filtroCedula = document.getElementById('filtro-cedula');
    const filtroNombre = document.getElementById('filtro-nombre');
    const filtroEmpresa = document.getElementById('filtro-empresa');
    const filtroZona = document.getElementById('filtro-zona');
    const filtroRegional = document.getElementById('filtro-regional');

    // --- Estado de la paginación ---
    let datosAgrupados = {};
    let cedulasPaginadas = [];
    let indiceActual = 0;

    // --- Función para renderizar la página actual ---
    function renderizarPagina() {
        if (cedulasPaginadas.length === 0) {
            historialContainer.innerHTML = '<div class="form-container text-center"><p>No se encontraron aspirantes con los filtros aplicados.</p></div>';
            paginacionNav.style.display = 'none';
            return;
        }

        paginacionNav.style.display = 'flex';
        
        const cedulaActual = cedulasPaginadas[indiceActual];
        const dataAspirante = datosAgrupados[cedulaActual];
        
        let historialHTML = dataAspirante.historial.map(evento => `
            <tr>
                <td>${evento.fecha}</td>
                <td>${evento.fase}</td>
                <td>${evento.descripcion || ''}</td>
            </tr>
        `).join('');

        historialContainer.innerHTML = `
            <div class="form-container">
                <h3>${dataAspirante.nombre_completo}</h3>
                <p class="text-muted">Cédula: ${cedulaActual} | Regional: ${dataAspirante.regional}</p>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Fase Cumplida</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>${historialHTML}</tbody>
                    </table>
                </div>
            </div>
        `;
        
        paginaInfo.textContent = `Aspirante ${indiceActual + 1} de ${cedulasPaginadas.length}`;
        btnAnterior.disabled = (indiceActual === 0);
        btnSiguiente.disabled = (indiceActual === cedulasPaginadas.length - 1);
    }

    // --- Función principal para cargar y procesar los datos ---
    async function cargarBitacora(queryString = '') {
        historialContainer.innerHTML = '<div class="form-container text-center"><p>Cargando registros...</p></div>';
        paginacionNav.style.display = 'none';

        try {
            const respuesta = await fetch(`api/get_bitacora.php?${queryString}`);
            const data = await respuesta.json();

            if (!data.success) throw new Error(data.message || 'Error al cargar los datos.');
            
            datosAgrupados = data.aspirantes;
            cedulasPaginadas = Object.keys(datosAgrupados);
            indiceActual = 0;
            
            renderizarPagina();

        } catch (error) {
            console.error("Error:", error);
            historialContainer.innerHTML = `<div class="form-container text-center text-danger"><p>${error.message}</p></div>`;
        }
    }

    // --- **LA FUNCIÓN QUE FALTABA** ---
    // Función para poblar los menús desplegables de los filtros
    async function poblarFiltros() {
        try {
            const respuesta = await fetch('api/get_organizacion.php');
            const datos = await respuesta.json();

            // Limpiamos las opciones por defecto antes de añadir las nuevas
            filtroEmpresa.innerHTML = '<option value="">Toda Empresa</option>';
            datos.empresas.forEach(empresa => {
                filtroEmpresa.innerHTML += `<option value="${empresa.id_empresa}">${empresa.nombre_empresa}</option>`;
            });
            
            filtroZona.innerHTML = '<option value="">Toda Zona</option>';
            datos.zonas.forEach(zona => {
                filtroZona.innerHTML += `<option value="${zona.id_zona}">${zona.nombre_zona}</option>`;
            });

            filtroRegional.innerHTML = '<option value="">Toda Regional</option>';
            datos.regionales.forEach(regional => {
                filtroRegional.innerHTML += `<option value="${regional.id_regional}">${regional.nombre_regional}</option>`;
            });

        } catch (error) {
            console.error("No se pudieron cargar los filtros", error);
        }
    }

    // --- Event Listeners ---
    filtroForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const params = new URLSearchParams({
            cedula: filtroCedula.value,
            nombre: filtroNombre.value,
            empresa: filtroEmpresa.value,
            zona: filtroZona.value,
            regional: filtroRegional.value
        });
        cargarBitacora(params.toString());
    });
    
    btnSiguiente.addEventListener('click', () => {
        if (indiceActual < cedulasPaginadas.length - 1) {
            indiceActual++;
            renderizarPagina();
        }
    });

    btnAnterior.addEventListener('click', () => {
        if (indiceActual > 0) {
            indiceActual--;
            renderizarPagina();
        }
    });

    // --- Carga Inicial ---
    poblarFiltros(); // <-- AHORA SÍ SE LLAMA A LA FUNCIÓN CORRECTA
    cargarBitacora(); // Carga todos los registros la primera vez
});