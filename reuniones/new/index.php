<!-------------------- Variables, require e include -------------------->
<?php
$title = "Nueva Reunión";
$page = "reu";

require "../../.res/funct/funct.php";
include "../../.res/templates/header.php";
?>
<!-------------------- Consultas generales -------------------->

<?php
/* Consulta todas las actividades categoria, materiales y objetivos de las actividades */
$allActSQL = "
SELECT
    act.act_id,
    act.act_name,
    act.act_desc,
    act.act_durat,
    GROUP_CONCAT(DISTINCT obj.obj_name SEPARATOR '<br>') AS act_objs,
    GROUP_CONCAT(DISTINCT cat.cat_name SEPARATOR '<br>') AS act_cats,
    GROUP_CONCAT(DISTINCT mat.mat_name SEPARATOR '<br>') AS act_mats
FROM act
LEFT JOIN act_obj ON act.act_id = act_obj.act_id
LEFT JOIN obj ON act_obj.obj_id = obj.obj_id

LEFT JOIN act_cat ON act.act_id = act_cat.act_id
LEFT JOIN cat ON act_cat.cat_id = cat.cat_id

LEFT JOIN act_mat ON act.act_id = act_mat.act_id
LEFT JOIN mat ON act_mat.mat_id = mat.mat_id

GROUP BY act.act_id, act.act_name, act.act_desc, act.act_durat;
";
$allActQuery = linkDB()->query($allActSQL);
$allActs = $allActQuery->fetch_all(MYSQLI_ASSOC);
?>

<?php
/* Consulta todas las ramas */
$allRamSQL = "SELECT rama_id, rama_name FROM rama";
$allRamQuery = linkDB()->query($allRamSQL);
$allRams = $allRamQuery->fetch_all(MYSQLI_ASSOC);
?>

<?php
/* Consulta todos los grupos */
$allGrpSQL = "SELECT grp_id, grp_name FROM grps";
$allGrpQuery = linkDB()->query($allGrpSQL);
$allGrps = $allGrpQuery->fetch_all(MYSQLI_ASSOC);
?>

<!---------------------------------------- POST ---------------------------------------->
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grps = isset($_POST['grps']) ? $_POST['grps'] : [];
    $rama = isset($_POST['rama']) ? $_POST['rama'] : [];
    $prog_place = trim($_POST['prog_place'] ?? '');
    $prog_date = trim($_POST['prog_date'] ?? '');
    $prog_coord = trim($_POST['prog_coord'] ?? '');
    $prog_child_N = intval($_POST['prog_child_N'] ?? 0);
    $responsables = trim($_POST['responsables'] ?? '');
    $prog_time = trim($_POST['prog_time'] ?? '');
    $actividades = $_POST['actividades'] ?? [];

    $db = linkDB();
    // Insertar reunión principal
    $stmt = $db->prepare("INSERT INTO prog (grp_id, rama_id, prog_place, prog_date, prog_coord, prog_child_N, responsibles, prog_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $grp_id = $grps[0] ?? null;
    $rama_id = $rama[0] ?? null;
    $stmt->bind_param('iisssiss', $grp_id, $rama_id, $prog_place, $prog_date, $prog_coord, $prog_child_N, $responsables, $prog_time);
    $stmt->execute();
    $reunion_id = $db->insert_id;
    $stmt->close();

    // Insertar actividades asociadas en prog_act
    if ($reunion_id && !empty($actividades)) {
        $order = 1;
        foreach ($actividades as $act) {
            $act_id = $act['act_id'] ?? null;
            $encargado = $act['encargado'] ?? '';
            $comentarios = $act['comentarios'] ?? '';
            $custom_name = $act['custom_name'] ?? '';
            if ($act_id) {
                if ($act_id === 'custom') {
                    // Guardar actividad personalizada (puedes usar act_respon para el nombre)
                    $stmt2 = $db->prepare("INSERT INTO prog_act (prog_id, act_id, act_order, act_respon, act_comment) VALUES (?, NULL, ?, ?, ?)");
                    $stmt2->bind_param('iiss', $reunion_id, $order, $custom_name, $comentarios);
                } else {
                    $stmt2 = $db->prepare("INSERT INTO prog_act (prog_id, act_id, act_order, act_respon, act_comment) VALUES (?, ?, ?, ?, ?)");
                    $stmt2->bind_param('iiiss', $reunion_id, $act_id, $order, $encargado, $comentarios);
                }
                $stmt2->execute();
                $stmt2->close();
                $order++;
            }
        }
    }
    header("Location: ../index.php");
    exit;
}
?>
<!---------------------------------------- CSS ---------------------------------------->
<link rel="stylesheet" href="../../.res/css/reunion.css">
<!---------------------------------------- main ---------------------------------------->
<main>
    <a class="but align-left" href="../index.php" title="Volver a Reuniones">
        <svg width="400" height="400" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="m 3.0000046,9.88004 h 4.91998 c 1.7,0 3.0800004,-1.38 3.0800004,-3.08 0,-1.7 -1.3800004,-3.07999 -3.0800004,-3.07999 h -6.76998"
                stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" id="path2" />
            <path d="m 2.5699846,5.26994 -1.57,-1.57998 1.57,-1.57" stroke-width="1.5" stroke-linecap="round"
                stroke-linejoin="round" id="path3" />
        </svg>
    </a>
    <br>

    <form method="POST" class="form-grid">
        <button type="submit" class="btn-submit" style="width: 25%;">
            Guardar reunión
        </button>
        <input type="hidden" name="act_id">
        <!-- Tabla de información general -->
        <table>
            <tr>
                <td class="branchCell" width="25%">
                    <label for="grop_id">
                        <b>Grupo scout</b>
                    </label>
                </td>
                <td width="35%">
                    <select name="grps[]" id="grps" class="select2" onchange="changeLogo('grps', 'logoGrupo')" required>
                        <option value="" selected disabled>Selecciona el grupo</option>
                        <?php
                        foreach ($allGrps as $grp) {
                            echo "<option value='" . $grp["grp_id"] . "'>" . $grp["grp_name"] . "</option>";
                        }
                        ?>
                    </select>
                </td>
                <td class="branchCell" width="20%">
                    Ronda Solar
                </td>
                <td width="20%">
                    <span id="rondaSolar" style="color:#888;">
                        Calculo automático...
                    </span>
                </td>
            </tr>
            <tr>
                <td class="branchCell">
                    <label for="prog_place">Lugar</label>
                </td>
                <td>
                    <input type="text" name="prog_place" id="prog_place">
                </td>
                <td class="branchCell">
                    <label for="prog_date">Fecha</label>
                </td>
                <td>
                    <input type="date" name="prog_date" id="prog_date"
                        onchange="calculateSolarRound('prog_date', 'rondaSolar')" required>
                </td>
            </tr>
            <tr>
                <td class="branchCell">
                    <label for="prog_coord">Coordinador</label>
                </td>
                <td>
                    <input type="text" name="prog_coord" id="prog_coord">
                </td>
                <td class="branchCell">
                    <label for="prog_child_N">Nº educandos</label>
                </td>
                <td>
                    <input type="number" name="prog_child_N" id="prog_child_N">
                </td>
            </tr>
            <tr>
                <td class="branchCell">
                    <label for="rama">
                        Rama
                    </label>
                </td>
                <td class="branchCell" colspan="2">
                    <label for="responsables">
                        Responsables asistentes
                    </label>
                </td>
                <td class="branchCell">
                    Logo del grupo
                </td>
            </tr>
            <tr>
                <td class="branchCell">
                    <select name="rama[]" id="rama" class="select2" onchange="colorCells('rama')" required>
                        <option value="" selected disabled></option>
                        <?php
                        foreach ($allRams as $rama) {
                            echo "<option value='" . $rama["rama_id"] . "'>" . $rama["rama_name"] . "</option>";
                        }
                        ?>
                    </select>
                </td>
                <td colspan="2">
                    <textarea id="responsables" name="responsables" rows="2" placeholder="Añada el nombre de un responsable por cada línea..." style="text-align: left;padding: 10px;" required></textarea>
                </td>
                <td class="logoCell" style="color:#888;">
                    <img id="logoGrupo" src="" alt="Seleccione un grupo">
                </td>
            </tr>
        </table>
        <!-- Tabla de actividades -->
        <table id="actTable">
            <tr class='branchCell'>
                <th width='5%'>
                    Nº
                </th>
                <th width='10%'>
                    Hora
                </th>
                <th width='50%'>
                    Actividad
                    <input hidden type="number" min="1" value="1" id="numAct" style="width: 50px; margin-left: 10px;" onchange="addAct(this.value)" />
                </th>
                <th width='10%'>
                    <a class="but align-left" onclick="actNumber('numAct', -1)" title="Añadir una actividad" style="width: 10px;">
                        <svg width="400" height="400" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                            <path d="m 2,6 h 8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" id="path1" />
                        </svg>
                    </a>
                    <a class="but align-right" onclick="actNumber('numAct',1)" title="Eliminar una actividad" style="width: 10px;">
                        <svg width="400" height="400" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                            <path d="m 2,6 h 8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" id="path1" />
                            <path d="M 6,10 V 2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" id="path2" />
                        </svg>
                    </a>
                </th>
                <th width='25%'>
                    Encargado
                </th>
            </tr>
        </table>
    </form>
</main>

<!---------------------------------------- Scripts de select2 ---------------------------------------->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2();
        addAct(1);
    });
</script>
<!---------------------------------------- Scripts de funciones ---------------------------------------->
<script>

    function addAct(num) {

        num = parseInt(num);
        const tabla = document.getElementById('actTable');

        // 1. Guardar los valores actuales de los inputs
        const datosPrevios = {};
        // Guardar la hora de inicio de la programación
        const progTimePrevio = document.getElementById('prog_time')?.value || '';
        // Guardar los encargados seleccionados
        const encargadosPrevios = [];
        for (let i = 0; i < tabla.rows.length - 1; i++) {
            const idx = i;
            datosPrevios[idx] = {
                hora: document.querySelector(`[name='actividades[${idx}][hora]']`)?.value || '',
                act_id: document.querySelector(`[name='actividades[${idx}][act_id]']`)?.value || '',
                duracion: document.querySelector(`[name='actividades[${idx}][duracion]']`)?.value || '',
                encargado: document.querySelector(`[name='actividades[${idx}][encargado]']`)?.value || '',
                comentarios: document.querySelector(`[name='actividades[${idx}][comentarios]']`)?.value || ''
            };
            // Guardar encargado seleccionado
            encargadosPrevios[idx] = document.querySelector(`[name='actividades[${idx}][encargado]']`)?.value || '';
        }

        // Elimina las filas de actividades existentes, excepto la primera
        while (tabla.rows.length > 1) {
            tabla.deleteRow(1);
        }

        for (let i = 0; i < num; i++) {
            // Celda 0: Número de actividad
            const fila = tabla.insertRow();
            const celdaNumero = fila.insertCell();
            celdaNumero.rowSpan = 2;
            celdaNumero.className = 'branchCell';
            celdaNumero.innerHTML = `<span>${i + 1}</span>`;

            // Celda 1: Hora
            const celdaHora = fila.insertCell();
            if (i === 0) {
                // Si es la primera fila, introduce la la hora de inicio de la programación
                celdaHora.innerHTML += `<input type="time" name="prog_time" id="prog_time" style="width: 100%;" onchange="copyHour(this.value)" required>`;
            } else {
                // Si no es la primera fila, introduce un input de hora vacío
                celdaHora.innerHTML += `<input type="time" name="actividades[${i}][hora]" id="horaActividad${i}" readonly>`;
            }

            // Celda 2: Actividad (select)
            const celdaActividad = fila.insertCell();
            celdaActividad.width = '50%';
            let selectHTML = `<select name="actividades[${i}][act_id]" class="select2" onchange="toggleCustomAct(${i}, this)">`;
            selectHTML += `<option value="" disabled${!(datosPrevios[i] && datosPrevios[i].act_id) ? ' selected' : ''}>Selecciona una actividad</option>`;
            <?php foreach ($allActs as $act): ?>
                selectHTML += `<option value="<?= $act['act_id'] ?>" data-duracion="<?= $act['act_durat'] ?>"><?= $act['act_name'] ?></option>`;
            <?php endforeach; ?>
            selectHTML += `<option value="custom">Personalizada...</option>`;
            selectHTML += `</select>`;
            celdaActividad.innerHTML = selectHTML;
            celdaActividad.innerHTML += `<input type="text" name="actividades[${i}][custom_name]" id="customAct_${i}" placeholder="Nombre de la actividad" style="display:none; width:90%;">`;

            // Celda 3: Duración (solo hora)
            const celdaDuracion = fila.insertCell();
            celdaDuracion.innerHTML = `<input type="text" style="color:#888;" name="actividades[${i}][duracion]" id="duracion_${i}" readonly>`;

            // Celda 4: Encargado
            const celdaEncargado = fila.insertCell();
            celdaEncargado.innerHTML = `<select name="actividades[${i}][encargado]" id="encargado_${i}" class="select2 select-encargado"><option value="" disabled>Selecciona responsable</option></select>`;

            // Fila adicional para comentarios
            const filaComentario = tabla.insertRow();
            const celdaComentario = filaComentario.insertCell();
            celdaComentario.colSpan = 4;
            celdaComentario.innerHTML = `<textarea name="actividades[${i}][comentarios]" id="comentarios_${i}" placeholder="Comentarios sobre la actividad..."></textarea>`;
        }
        $('.select2').select2();

        // 2. Restaurar los valores guardados
        for (let i = 0; i < num; i++) {
            if (datosPrevios[i]) {
                if (i === 0) {
                    document.getElementById('prog_time').value = progTimePrevio;
                } else {
                    document.getElementById(`horaActividad${i}`).value = datosPrevios[i].hora;
                }
                const select = document.querySelector(`[name='actividades[${i}][act_id]']`);
                if (select) {
                    if (datosPrevios[i].act_id) {
                        select.value = datosPrevios[i].act_id;
                    } else {
                        select.value = "";
                    }
                    $(select).trigger('change');
                }
                const duracion = document.querySelector(`[name='actividades[${i}][duracion]']`);
                if (duracion) duracion.value = datosPrevios[i].duracion;
                // Restaurar encargado después de actualizar opciones
                const encargadoSelect = document.querySelector(`[name='actividades[${i}][encargado]']`);
                if (encargadoSelect) {
                    setTimeout(() => {
                        encargadoSelect.value = encargadosPrevios[i] || '';
                        $(encargadoSelect).trigger('change');
                    }, 0);
                }
                const comentarios = document.querySelector(`[name='actividades[${i}][comentarios]']`);
                if (comentarios) comentarios.value = datosPrevios[i].comentarios;
            }
        }

        // 3. Añadir listeners para actualizar duración al seleccionar actividad (compatible con Select2)
        for (let i = 0; i < num; i++) {
            const select = document.querySelector(`[name='actividades[${i}][act_id]']`);
            const duracionInput = document.querySelector(`[name='actividades[${i}][duracion]']`);
            if (select && duracionInput) {
                $(select).on('change', function () {
                    const selectedOption = this.options[this.selectedIndex];
                    let duracion = selectedOption ? selectedOption.getAttribute('data-duracion') : '';
                    if (duracion && duracion.length >= 5) {
                        duracion = duracion.substring(0, 5); // Solo hh:mm
                    }
                    duracionInput.value = duracion;
                    calcularHorasActividades();
                });
                // Si ya hay una opción seleccionada, mostrar la duración al cargar
                const selectedOption = select.options[select.selectedIndex];
                if (selectedOption && selectedOption.getAttribute('data-duracion')) {
                    let duracion = selectedOption.getAttribute('data-duracion');
                    if (duracion && duracion.length >= 5) {
                        duracion = duracion.substring(0, 5); // Solo hh:mm
                    }
                    duracionInput.value = duracion;
                }
                // Listener para cambios manuales en duración (por si acaso)
                duracionInput.addEventListener('input', calcularHorasActividades);
            }
        }
        // Listener para cambios en la hora de inicio de la programación
        const progTime = document.getElementById('prog_time');
        if (progTime) {
            progTime.addEventListener('input', calcularHorasActividades);
        }
        calcularHorasActividades();
        actualizarEncargados();
    }

    function calcularHorasActividades() {
        // Obtener la hora de inicio de la programación
        const horaInicio = document.getElementById('prog_time')?.value;
        if (!horaInicio) return;
        let [h, m] = horaInicio.split(":").map(Number);
        let minutosAcumulados = h * 60 + m;
        const num = document.querySelectorAll("[name^='actividades']").length / 5; // 5 campos por actividad
        for (let i = 0; i < num; i++) {
            let horaInput = document.getElementById(i === 0 ? 'prog_time' : `horaActividad${i}`);
            if (i !== 0 && horaInput) {
                // Calcular hora para esta actividad
                let horas = Math.floor(minutosAcumulados / 60).toString().padStart(2, '0');
                let minutos = (minutosAcumulados % 60).toString().padStart(2, '0');
                horaInput.value = `${horas}:${minutos}`;
            }
            // Sumar duración de esta actividad
            const duracionInput = document.querySelector(`[name='actividades[${i}][duracion]']`);
            let duracion = duracionInput?.value || '';
            if (duracion && duracion.length >= 5) {
                let [dh, dm] = duracion.split(":").map(Number);
                minutosAcumulados += (dh * 60 + dm);
            }
        }
    }

    // En addAct, reemplaza el input de encargado por un select que se llenará dinámicamente
    function actualizarEncargados() {
        // Obtener responsables del textarea (uno por línea, sin vacíos)
        const responsables = (document.getElementById('responsables')?.value || '').split('\n').map(r => r.trim()).filter(r => r);
        // Actualizar cada select de encargado
        document.querySelectorAll('.select-encargado').forEach(select => {
            const valorPrevio = select.value;
            select.innerHTML = '<option value="">Selecciona responsable</option>' + responsables.map(r => `<option value="${r}">${r}</option>`).join('');
            // Restaurar valor si sigue siendo válido
            if (responsables.includes(valorPrevio)) {
                select.value = valorPrevio;
            } else {
                select.value = '';
            }
        });
    }
    // Listener para cambios en textarea de responsables
    const responsablesTextarea = document.getElementById('responsables');
    if (responsablesTextarea) {
        responsablesTextarea.addEventListener('input', actualizarEncargados);
    }
    actualizarEncargados();

    function toggleCustomAct(idx, select) {
        const customInput = document.getElementById(`customAct_${idx}`);
        if (select.value === "custom") {
            customInput.style.display = "inline-block";
            customInput.required = true;
        } else {
            customInput.style.display = "none";
            customInput.required = false;
        }
    }
</script>
<!-------------------- Errores a solucionar -------------------->
<?php include "../../.res/templates/footer.php"; ?>