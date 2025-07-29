document.addEventListener('DOMContentLoaded', function() {
    
    // --- Referencias a los elementos del DOM ---
    const formulario = document.getElementById('formulario-vinculacion');
    const formBusqueda = document.getElementById('form-busqueda');
    const inputBusquedaCedula = document.getElementById('busqueda-cedula');
    const inputIdAspirante = document.getElementById('id_aspirante');
    const inputCedula = document.getElementById('cedula');
    const inputNombre = document.getElementById('nombre');
    const inputCargo = document.getElementById('cargo');
    const selectEmpresa = document.getElementById('empresa');
    const selectZona = document.getElementById('zona');
    const selectRegional = document.getElementById('regional');
    const fasesContainer = document.getElementById('fases-container');
    const estadoBadge = document.getElementById('estado-aspirante-badge');
    const btnContratado = document.getElementById('btn-contratado');
    const btnRechazado = document.getElementById('btn-rechazado');
    const btnLimpiar = document.getElementById('btn-limpiar');

    let datosCompletos = { organizacion: null, fases: null };

    // --- Lógica de Carga Inicial ---
    async function cargarDatosIniciales() {
        try {
            const [respuestaOrg, respuestaFases] = await Promise.all([
                fetch(`api/get_organizacion.php?t=${new Date().getTime()}`),
                fetch(`api/get_fases.php?t=${new Date().getTime()}`)
            ]);
            if (!respuestaOrg.ok || !respuestaFases.ok) throw new Error('No se pudieron cargar todos los datos iniciales.');
            
            datosCompletos.organizacion = await respuestaOrg.json();
            datosCompletos.fases = await respuestaFases.json();
            
            rellenarSelectsIniciales();
            rellenarFases();
            inicializarLogicaDeFases();
        } catch (error) { console.error('Error al cargar datos:', error); }
    }

    // --- Lógica de Búsqueda y Carga de Aspirante ---
    formBusqueda.addEventListener('submit', async function(event) {
        event.preventDefault();
        const cedula = inputBusquedaCedula.value.trim();
        if (!cedula) return;

        try {
            const respuesta = await fetch(`api/get_aspirante.php?cedula=${cedula}`);
            const data = await respuesta.json();
            if (data.success) {
                cargarDatosDeAspirante(data);
            } else {
                alert('Error: ' + data.message);
                limpiarFormularioCompleto();
            }
        } catch (error) { alert('Ocurrió un error de conexión al buscar.'); }
    });

    function cargarDatosDeAspirante(data) {
        const { aspirante, fases } = data;
        
        inputIdAspirante.value = aspirante.id_aspirante;
        inputCedula.value = aspirante.cedula;
        inputNombre.value = aspirante.nombre_completo;
        inputCargo.value = aspirante.cargo || '';
        selectEmpresa.value = aspirante.id_empresa;
        
        const regionalDelAspirante = datosCompletos.organizacion.regionales.find(r => r.id_regional == aspirante.id_regional);
        if (regionalDelAspirante) selectZona.value = regionalDelAspirante.id_zona;
        selectZona.dispatchEvent(new Event('change'));
        setTimeout(() => { selectRegional.value = aspirante.id_regional; }, 0);
    
        fases.forEach(faseSeguimiento => {
            const checkbox = document.getElementById(`fase-check-${faseSeguimiento.id_fase}`);
            if (checkbox) {
                const faseItem = checkbox.closest('.fase-item');
                checkbox.checked = (faseSeguimiento.cumplio == 1);
                faseItem.querySelector('.input-fecha').value = faseSeguimiento.fecha_cumplimiento || '';
                faseItem.querySelector('.input-descripcion').value = faseSeguimiento.descripcion || '';
            }
        });
        
        actualizarEstadoFases();
        actualizarBadgeYBotones(aspirante.estado);
    }
    
    // --- Lógica de Guardado ---
    formulario.addEventListener('submit', async function(event) {
        event.preventDefault();
        const formData = new FormData(formulario);
        const botonGuardar = event.submitter;
        botonGuardar.disabled = true;
        botonGuardar.textContent = 'Guardando...';

        try {
            const respuesta = await fetch('api/guardar_aspirante.php', { method: 'POST', body: formData });
            const resultado = await respuesta.json();
            if (resultado.success) {
                alert(resultado.message);
                if (!inputIdAspirante.value) limpiarFormularioCompleto();
            } else {
                alert('Error: ' + resultado.message);
            }
        } catch (error) {
            alert('Ocurrió un error de conexión al guardar.');
        } finally {
            botonGuardar.disabled = false;
            botonGuardar.textContent = 'Guardar Cambios';
        }
    });

    // --- Lógica de Botones de Acción ---
    async function cambiarEstadoAspirante(estado) {
        const id = inputIdAspirante.value;
        if (!id) return;
        if (!confirm(`¿Está seguro que desea marcar este aspirante como "${estado}"?`)) return;
        try {
            const respuesta = await fetch('api/actualizar_estado.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_aspirante: id, estado: estado })
            });
            const resultado = await respuesta.json();
            if (resultado.success) {
                alert(resultado.message);
                actualizarBadgeYBotones(estado);
            } else {
                alert('Error: ' + resultado.message);
            }
        } catch (error) { alert('Error de conexión al cambiar el estado.'); }
    }

    function actualizarBadgeYBotones(estado) {
        estadoBadge.textContent = estado;
        estadoBadge.className = 'badge fs-6';
        if (estado === 'Contratado') estadoBadge.classList.add('bg-success');
        else if (estado === 'Rechazado') estadoBadge.classList.add('bg-danger');
        else estadoBadge.classList.add('bg-secondary');
        
        estadoBadge.style.display = 'inline-block';
        btnContratado.disabled = (estado !== 'Activo');
        btnRechazado.disabled = (estado !== 'Activo');
    }

    function limpiarFormularioCompleto() {
        formulario.reset();
        inputIdAspirante.value = '';
        estadoBadge.style.display = 'none';
        btnContratado.disabled = true;
        btnRechazado.disabled = true;
        actualizarEstadoFases();
    }

    btnContratado.addEventListener('click', () => cambiarEstadoAspirante('Contratado'));
    btnRechazado.addEventListener('click', () => cambiarEstadoAspirante('Rechazado'));
    btnLimpiar.addEventListener('click', limpiarFormularioCompleto);

    // --- Funciones Auxiliares ---
    function rellenarSelectsIniciales() {
        selectEmpresa.innerHTML = '<option value="">Seleccione...</option>';
        datosCompletos.organizacion.empresas.forEach(e => { selectEmpresa.innerHTML += `<option value="${e.id_empresa}">${e.nombre_empresa}</option>`; });
        selectZona.innerHTML = '<option value="">Seleccione...</option>';
        datosCompletos.organizacion.zonas.forEach(z => { selectZona.innerHTML += `<option value="${z.id_zona}">${z.nombre_zona}</option>`; });
        selectRegional.innerHTML = '<option value="">Seleccione...</option>';
        selectRegional.disabled = true;
    }
    function rellenarFases() {
        fasesContainer.innerHTML = '';
        datosCompletos.fases.forEach(fase => {
            fasesContainer.innerHTML += `<div class="fase-item"><label class="form-label fw-bold">${fase.nombre_fase}</label><div class="row"><div class="col-md-3"><div class="form-check"><input class="form-check-input checkbox-fase" type="checkbox" id="fase-check-${fase.id_fase}" name="fases[${fase.id_fase}][cumplio]"><label class="form-check-label" for="fase-check-${fase.id_fase}">Cumplió</label></div></div><div class="col-md-9"><input type="date" class="form-control form-control-sm input-fecha" name="fases[${fase.id_fase}][fecha]"></div></div><textarea class="form-control form-control-sm mt-2 input-descripcion" name="fases[${fase.id_fase}][descripcion]" placeholder="Descripción..."></textarea></div>`;
        });
    }
    function actualizarRegionales() {
        const idZonaSeleccionada = selectZona.value;
        selectRegional.innerHTML = '<option value="">Seleccione...</option>'; 
        if (!idZonaSeleccionada) { selectRegional.disabled = true; return; }
        selectRegional.disabled = false;
        const regionalesFiltradas = datosCompletos.organizacion.regionales.filter(r => r.id_zona == idZonaSeleccionada);
        regionalesFiltradas.forEach(r => { selectRegional.innerHTML += `<option value="${r.id_regional}">${r.nombre_regional}</option>`; });
    }
    function inicializarLogicaDeFases() {
        fasesContainer.addEventListener('change', e => {
            if (e.target.classList.contains('checkbox-fase')) {
                gestionarFechaAutomatica(e.target);
                actualizarEstadoFases();
            }
        });
        actualizarEstadoFases();
    }
    
    // ==========================================================
    // ===               AQUÍ ESTÁ EL CAMBIO                  ===
    // ==========================================================
    function gestionarFechaAutomatica(checkbox) {
        const faseItem = checkbox.closest('.fase-item');
        const campoFecha = faseItem.querySelector('.input-fecha');

        if (checkbox.checked) {
            // Ya no asignamos la fecha automáticamente.
            // campoFecha.value = new Date().toISOString().slice(0, 10); 
        } else {
            // Si se desmarca, sí borramos la fecha.
            campoFecha.value = '';
        }
    }
    // ==========================================================

    function actualizarEstadoFases() {
        const todasLasFases = document.querySelectorAll('.fase-item');
        todasLasFases.forEach((fase, index) => {
            const checkbox = fase.querySelector('.checkbox-fase');
            const habilitar = (index === 0) || todasLasFases[index - 1].querySelector('.checkbox-fase').checked;
            
            // Habilitamos o deshabilitamos todos los inputs dentro de la fase, excepto el checkbox principal
            // para permitir desmarcarlo y revertir el proceso.
            const inputsParaGestionar = fase.querySelectorAll('input[type="date"], textarea');
            inputsParaGestionar.forEach(el => el.disabled = !habilitar);
            
            // El checkbox de la siguiente fase
            if (index > 0) {
                checkbox.disabled = !todasLasFases[index - 1].querySelector('.checkbox-fase').checked;
            }

            if (!habilitar) {
                checkbox.checked = false;
                fase.querySelector('.input-fecha').value = '';
                fase.querySelector('.input-descripcion').value = '';
            }
        });
    }

    selectZona.addEventListener('change', actualizarRegionales);
    cargarDatosIniciales();
});