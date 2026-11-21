<?php
/**
 * app/views/novedades/Formulario.php
 * Formulario compartido por crear.php y editar.php.
 * Variables: $datos, $errores, $secciones, $responsables, $urlAccion, $textoBoton, $id (solo en edición)
 */
$datos = $datos ?? [];
$errores = $errores ?? [];
$secciones = $secciones ?? [];
$responsables = $responsables ?? [];
$usarHoraSistema = $usarHoraSistema ?? false;
$textoBoton = $textoBoton ?? ($esEdicion ?? false ? 'Guardar cambios' : 'Crear novedad');
$claseCampo = fn(string $campo): string => isset($errores[$campo]) ? ' is-invalid' : '';
$esEdicion = isset($id);
?>
<form id="formulario-novedad" method="post" action="<?= htmlspecialchars((string) ($urlAccion ?? '')) ?>" novalidate>

    <?php if ($esEdicion): ?>
        <input type="hidden" name="id" value="<?= (int) ($id ?? 0) ?>">
    <?php endif; ?>

    <?php if (!empty($errores)): ?>
        <div class="alert alert-danger" role="alert">
            <?= isset($errores['general'])
                ? htmlspecialchars($errores['general'])
                : 'Revisá los campos marcados en rojo antes de continuar.' ?>
        </div>
    <?php endif; ?>

    <div class="row g-3">
        <div class="col-12">
            <label for="descripcion" class="form-label fw-medium">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="4" maxlength="1000" required
                      class="form-control<?= $claseCampo('descripcion') ?>"><?= htmlspecialchars((string) ($datos['descripcion'] ?? '')) ?></textarea>
            <?php if (isset($errores['descripcion'])): ?>
                <div class="invalid-feedback"><?= htmlspecialchars($errores['descripcion']) ?></div>
            <?php endif; ?>
        </div>

        
        <div class="col-12 col-md-4">
            <label for="seccion_id" class="form-label fw-medium">Sección</label>
            <select id="seccion_id" name="seccion_id" required class="form-select<?= $claseCampo('seccion_id') ?>">
                <option value="">Elegí una sección…</option>
                <?php foreach ($secciones as $s): ?>
                    <option value="<?= (int) $s['id'] ?>" <?= (string) ($datos['seccion_id'] ?? '') === (string) $s['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errores['seccion_id'])): ?>
                <div class="invalid-feedback"><?= htmlspecialchars($errores['seccion_id']) ?></div>
            <?php endif; ?>
            <?php if (($_SESSION['usuario_actual']['rol'] ?? '') === 'Administrador'): ?>
            <div class="mt-3">
                <label for="nueva_seccion" class="form-label fw-medium">Agregar sección</label>
                <div class="input-group">
                    <input type="text" id="nueva_seccion" name="nueva_seccion" maxlength="100"
                           class="form-control" autocomplete="off" placeholder="Nombre de la nueva sección">
                    <button type="button" id="agregar-seccion" class="btn btn-outline-secondary">Agregar</button>
                </div>
                <div class="form-text">El nombre no puede coincidir con una sección existente.</div>
                <div id="mensaje-seccion" class="small mt-2" role="status" aria-live="polite"></div>
                <div id="seccion-duplicada" class="alert alert-warning mt-2 py-2 d-none" role="alert">
                    <p id="pregunta-renombrar-seccion" class="mb-2"></p>
                    <div id="opciones-renombrar-seccion" class="d-flex gap-2">
                        <button type="button" id="confirmar-renombrar-seccion" class="btn btn-sm btn-warning">
                            Sí, modificar nombre
                        </button>
                        <button type="button" id="cancelar-renombrar-seccion" class="btn btn-sm btn-outline-secondary">
                            No
                        </button>
                    </div>
                    <div id="panel-renombrar-seccion" class="d-none">
                        <label for="nuevo_nombre_seccion" class="form-label">Nuevo nombre de la sección</label>
                        <div class="input-group">
                            <input type="text" id="nuevo_nombre_seccion" maxlength="100"
                                   class="form-control" autocomplete="off">
                            <button type="button" id="guardar-renombre-seccion" class="btn btn-warning">
                                Guardar nombre
                            </button>
                        </div>
                        <button type="button" id="volver-renombrar-seccion"
                                class="btn btn-link btn-sm px-0">Cancelar</button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-12 col-md-4">
            <label for="responsable_id" class="form-label fw-medium">Usuario responsable</label>
            <select id="responsable_id" name="responsable_id" required
                    class="form-select<?= $claseCampo('responsable_id') ?>">
                <option value="">Seleccione usuario responsable</option>
                <?php foreach ($responsables as $responsable): ?>
                    <option value="<?= (int) $responsable['id'] ?>"
                        <?= (string) ($datos['responsable_id'] ?? '') === (string) $responsable['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($responsable['apellido'] . ', ' . $responsable['nombre'] . ' (' . $responsable['email'] . ')') ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errores['responsable_id'])): ?>
                <div class="invalid-feedback"><?= htmlspecialchars($errores['responsable_id']) ?></div>
            <?php endif; ?>
        </div>
                
        <div class="col-12 col-md-4">
            <label for="fecha_hora" class="form-label fw-medium">
                <?= $esEdicion ? 'Fecha y hora del alta' : 'Fecha y hora' ?>
            </label>
            <input type="datetime-local" id="fecha_hora" name="fecha_hora" required
                   class="form-control<?= $claseCampo('fecha_hora') ?>"
                   <?= $esEdicion ? 'readonly' : '' ?>
                   value="<?= htmlspecialchars((string) ($datos['fecha_hora'] ?? '')) ?>">
            <?php if (isset($errores['fecha_hora'])): ?>
                <div class="invalid-feedback"><?= htmlspecialchars($errores['fecha_hora']) ?></div>
            <?php endif; ?>
            <?php if ($esEdicion): ?>
                <div class="form-text">Se conserva la fecha y hora registradas al dar de alta la novedad.</div>
            <?php endif; ?>
        </div>

        <div class="col-12 col-md-4">
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4 pt-3">
        <a href="<?= BASE_URL ?>/index.php?ctrl=novedades&accion=index" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-accent">
            <i class="bi bi-check-lg me-1"></i> <?= htmlspecialchars($textoBoton) ?>
        </button>
    </div>
</form>

<?php if ($usarHoraSistema): ?>
    <script>
        (() => {
            const campoFechaHora = document.getElementById('fecha_hora');
            if (!campoFechaHora) return;

            const ahora = new Date();
            const dosDigitos = valor => String(valor).padStart(2, '0');
            campoFechaHora.value = `${ahora.getFullYear()}-${dosDigitos(ahora.getMonth() + 1)}-${dosDigitos(ahora.getDate())}T${dosDigitos(ahora.getHours())}:${dosDigitos(ahora.getMinutes())}`;
        })();
    </script>
<?php endif; ?>

<?php if (($_SESSION['usuario_actual']['rol'] ?? '') === 'Administrador'): ?>
<script>
    (() => {
        const formulario = document.getElementById('formulario-novedad');
        const selectorSeccion = document.getElementById('seccion_id');
        const campoNuevaSeccion = document.getElementById('nueva_seccion');
        const campoNuevoNombre = document.getElementById('nuevo_nombre_seccion');
        const mensaje = document.getElementById('mensaje-seccion');
        const alertaDuplicada = document.getElementById('seccion-duplicada');
        const preguntaRenombrar = document.getElementById('pregunta-renombrar-seccion');
        const opcionesRenombrar = document.getElementById('opciones-renombrar-seccion');
        const panelRenombrar = document.getElementById('panel-renombrar-seccion');
        const botonAgregar = document.getElementById('agregar-seccion');
        const botonConfirmarRenombre = document.getElementById('confirmar-renombrar-seccion');
        const botonCancelarRenombre = document.getElementById('cancelar-renombrar-seccion');
        const botonGuardarRenombre = document.getElementById('guardar-renombre-seccion');
        const botonVolverRenombre = document.getElementById('volver-renombrar-seccion');

        if (!formulario || !selectorSeccion || !campoNuevaSeccion) return;

        const urlCrear = <?= json_encode(BASE_URL . '/index.php?ctrl=novedades&accion=crearSeccion') ?>;
        const urlRenombrar = <?= json_encode(BASE_URL . '/index.php?ctrl=novedades&accion=renombrarSeccion') ?>;
        let seccionDuplicadaId = null;

        const mostrarMensaje = (texto, esError = false) => {
            mensaje.textContent = texto;
            mensaje.className = `small mt-2 ${esError ? 'text-danger' : 'text-success'}`;
        };

        const enviarSolicitud = async (url, campos) => {
            const datos = new FormData(formulario);
            Object.entries(campos).forEach(([nombre, valor]) => datos.set(nombre, valor));
            const respuesta = await fetch(url, {
                method: 'POST',
                headers: { Accept: 'application/json' },
                body: datos
            });
            const resultado = await respuesta.json();
            return { respuesta, resultado };
        };

        const seleccionarSeccion = (id, nombre) => {
            let opcion = Array.from(selectorSeccion.options).find(item => item.value === String(id));
            if (!opcion) {
                opcion = new Option(nombre, String(id));
                selectorSeccion.add(opcion);
            }
            opcion.textContent = nombre;
            selectorSeccion.value = String(id);
        };

        const mostrarDuplicada = (resultado) => {
            seccionDuplicadaId = resultado.id;
            preguntaRenombrar.textContent =
                `La sección "${resultado.nombre}" ya existe en el sistema. ¿Deseás modificar su nombre?`;
            opcionesRenombrar.classList.remove('d-none');
            panelRenombrar.classList.add('d-none');
            campoNuevoNombre.value = '';
            alertaDuplicada.classList.remove('d-none');
            mostrarMensaje(resultado.mensaje, true);
        };

        botonAgregar.addEventListener('click', async () => {
            const nombre = campoNuevaSeccion.value.trim();
            if (nombre === '' || nombre.length > 100) {
                mostrarMensaje('Ingresá un nombre de hasta 100 caracteres.', true);
                campoNuevaSeccion.focus();
                return;
            }
            botonAgregar.disabled = true;
            try {
                const { respuesta, resultado } = await enviarSolicitud(urlCrear, { nueva_seccion: nombre });
                if (resultado.estado === 'duplicada') {
                    mostrarDuplicada(resultado);
                    return;
                }
                if (!respuesta.ok) throw new Error(resultado.mensaje || 'No se pudo agregar la sección.');

                seleccionarSeccion(resultado.id, resultado.nombre);
                campoNuevaSeccion.value = '';
                alertaDuplicada.classList.add('d-none');
                mostrarMensaje('Sección agregada y seleccionada para esta novedad.');
            } catch (error) {
                mostrarMensaje(error instanceof Error ? error.message : 'No se pudo agregar la sección.', true);
            } finally {
                botonAgregar.disabled = false;
            }
        });

        botonConfirmarRenombre.addEventListener('click', () => {
            opcionesRenombrar.classList.add('d-none');
            panelRenombrar.classList.remove('d-none');
            campoNuevoNombre.focus();
        });

        botonCancelarRenombre.addEventListener('click', () => {
            alertaDuplicada.classList.add('d-none');
            seccionDuplicadaId = null;
            mostrarMensaje('');
        });

        botonVolverRenombre.addEventListener('click', () => {
            panelRenombrar.classList.add('d-none');
            opcionesRenombrar.classList.remove('d-none');
            campoNuevoNombre.value = '';
        });

        botonGuardarRenombre.addEventListener('click', async () => {
            const nombre = campoNuevoNombre.value.trim();
            if (seccionDuplicadaId === null) return;
            if (nombre === '' || nombre.length > 100) {
                mostrarMensaje('Ingresá un nombre nuevo de hasta 100 caracteres.', true);
                campoNuevoNombre.focus();
                return;
            }
            botonGuardarRenombre.disabled = true;
            try {
                const { respuesta, resultado } = await enviarSolicitud(urlRenombrar, {
                    seccion_existente_id: String(seccionDuplicadaId),
                    nuevo_nombre_seccion: nombre
                });
                if (resultado.estado === 'duplicada') {
                    mostrarMensaje(resultado.mensaje, true);
                    return;
                }
                if (!respuesta.ok) throw new Error(resultado.mensaje || 'No se pudo modificar la sección.');

                if (resultado.estado === 'renombrada') {
                    seleccionarSeccion(resultado.id, resultado.nombre);
                    mostrarMensaje('Nombre de sección modificado y seleccionado.');
                } else {
                    mostrarMensaje(resultado.mensaje || 'No hubo cambios en el nombre.');
                }
                alertaDuplicada.classList.add('d-none');
                seccionDuplicadaId = null;
            } catch (error) {
                mostrarMensaje(error instanceof Error ? error.message : 'No se pudo modificar la sección.', true);
            } finally {
                botonGuardarRenombre.disabled = false;
            }
        });
    })();
</script>
<?php endif; ?>