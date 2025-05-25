<!-------------------- Variables, require e include -------------------->
<?php
$title = "Nueva Reunión";
$page = "reu";

require "../../.res/funct/funct.php";
include "../../.res/templates/header.php";
?>
<!-- Consultas -->

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
    $conn = linkDB();

    // Sanitiza y recoge los datos principales
    $prog_date    = $_POST['prog_date'] ?? null;
    $prog_time    = $_POST['prog_time'] ?? null;
    $prog_coord   = $_POST['prog_coord'] ?? null;
    $prog_place   = $_POST['prog_place'] ?? null;
    $prog_child_N = $_POST['prog_child_N'] ?? null;
    $grp_id       = isset($_POST['grps'][0]) ? $_POST['grps'][0] : null;
    $rama_id      = isset($_POST['rama'][0]) ? $_POST['rama'][0] : null;

    // Validación básica
    if ($prog_date && $prog_time && $prog_coord && $prog_place && $prog_child_N && $grp_id && $rama_id) {
        // Inserta en prog
        $stmt = $conn->prepare("INSERT INTO prog (prog_date, prog_time, prog_coord, prog_place, prog_child_N, grp_id, rama_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssiii", $prog_date, $prog_time, $prog_coord, $prog_place, $prog_child_N, $grp_id, $rama_id);
        if ($stmt->execute()) {
            $prog_id = $conn->insert_id;

            // Inserta actividades asociadas
            if (isset($_POST['actividades']) && is_array($_POST['actividades'])) {
                foreach ($_POST['actividades'] as $order => $act) {
                    $act_id     = $act['act_id'] ?? null;
                    $encargado  = $act['encargado'] ?? '';
                    $comentario = $act['comentarios'] ?? '';
                    if ($act_id) {
                        $stmt2 = $conn->prepare("INSERT INTO prog_act (prog_id, act_id, act_order, act_respon, act_comment) VALUES (?, ?, ?, ?, ?)");
                        $stmt2->bind_param("iiiss", $prog_id, $act_id, $order, $encargado, $comentario);
                        $stmt2->execute();
                        $stmt2->close();
                    }
                }
            }
            echo "<div class='alert alert-success'>¡Reunión guardada correctamente!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error al guardar la reunión: " . $conn->error . "</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='alert alert-warning'>Por favor, completa todos los campos obligatorios.</div>";
    }
}
?>
<!---------------------------------------- CSS ---------------------------------------->
<style>
    :root {
        --colorRama : #efefef;
    }
    .branchCell {
        background-color: var(--colorRama);
    }
    table {
        font-family: "Calibri", sans-serif;
        overflow: hidden;
        width: 100%;
        font-size: 20px;
        line-height: 1.5;
        text-align: center;
        vertical-align: middle;  
        border: 2px solid var(--color004);
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 10px;
    }
    td {
        background-color: white;
        text-align: center;
        height: 30px;
        border: 0.5px solid #bebebe;
        padding: 10px;
    }
    .select2-container {
        width: 100% !important;
        font-family: "Calibri", sans-serif;
        text-align: center;
    }
    input {
        width: 100%;
        height: 100%;
        padding: 0px;
        border: 0.5px solid var(--color004);
        border-radius: 0px ;
        background-color: #ffffffcc;
        color: var(--color002);
        font-size: 18px;
        text-align: center;
    }
    .logoCell {
        width: 150px;
        height: 150px;
        padding: 0;
        text-align: center;
    }
    .logoCell img {
        width: 100%;
        height: 100%;
        object-fit: contain; /* Esto evitará que la imagen se deforme */
    }

</style>
<!---------------------------------------- main ---------------------------------------->
<main>
    <a class="but align-left" href="../index.php" title="Volver a Reuniones">
        <svg width="400" height="400" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
            d="m 3.0000046,9.88004 h 4.91998 c 1.7,0 3.0800004,-1.38 3.0800004,-3.08 0,-1.7 -1.3800004,-3.07999 -3.0800004,-3.07999 h -6.76998"
            stroke-width="1.5"
            stroke-miterlimit="10"
            stroke-linecap="round"
            stroke-linejoin="round"
            id="path2" />
        <path
            d="m 2.5699846,5.26994 -1.57,-1.57998 1.57,-1.57"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round"
            id="path3" />
        </svg>
    </a>
    <br>

    <form method="POST" class="form-grid">
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
                    <select name="grps[]" id="grps" class="select2" onchange="cambioLogo('grps', 'logoGrupo')">
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
                    <span id="rondaSolar">
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
                    <input type="date" name="prog_date" id="prog_date" onchange="calculaRondaSolar('prog_date', 'rondaSolar')">
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
                    <label for="prog_child_N">
                        Responsables asistentes
                    </label>
                </td>
                <td class="branchCell">
                    Logo del grupo
                </td>
            </tr>
            <tr>
                <td class="branchCell">
                    <select name="rama[]" id="rama" class="select2" onchange="colorearCeldas()">
                        <option value="" selected disabled>Selecciona la rama</option>
                        <?php
                        foreach ($allRams as $rama) {
                            echo "<option value='" . $rama["rama_id"] . "'>" . $rama["rama_name"] . "</option>";
                        }
                        ?>
                    </select>
                </td>
                <td colspan="2">

                </td>
                <td class="logoCell">
                    <img id="logoGrupo" src="" alt="Seleccione un grupo">
                </td>
            </tr>
        </table>

        <table id="tablaActividades">
            <tr class='branchCell'>
                <th width='25%' colspan="2">
                    <label for="prog_time">Hora de inicio</label>
                    <br>
                    <input type="time" name="prog_time" id="prog_time" class="but" style="width: 50%" onchange="copyHour(this.value)">
                    
                </th>
                <th  width='55%' colspan="2">
                    <label for="">Número de actividades</label>
                    <br>
                    <input type="number" name="num_activities" id="num_activities" class="but" onchange="addActivity(this.value)" value="1" min="1" max="10" style="width: 50%">
                </th>
                <th  width='20%'>

                </th>
            </tr>


        <!-- Tabla de actividades -->

            <tr>
                <td class='branchCell'>
                    Nº
                </td>
                <td class='branchCell'>
                    Hora
                </td>
                <td colspan="2" class='branchCell'>
                    Actividad
                </td>
                <td class='branchCell'>
                    Encargado
                </td>
            </tr>
        </table>
        <button type="submit" class="but align-left" style="width: 25%;">
            Guardar reunión
        </button>
    </form>
    
</main>



<!---------------------------------------- Scripts de select2 ---------------------------------------->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2();
        addActivity(1); // <-- Añade esta línea para tener una fila por defecto
    });
</script>
<!---------------------------------------- Scripts de funciones ---------------------------------------->
<script>   
    function colorearCeldas() {
        const rama = document.getElementById('rama').value;
        console.log("Valor de rama:", rama);
        let color = '';
        switch (rama) {
            case '2':
                color = '#fce5cd';
                break;
            case '3':
                color = '#fff1cc';
                break;
            case '4':
                color = '#c9daf8';
                break;
            case '5':
                color = '#f4cccc';
                break;
            case '6':
                color = '#d9ead3';
                break;
            default:
                color = '#efefef';
        }
        // Actualizar la variable CSS
        document.documentElement.style.setProperty('--colorRama', color);
    }

    function addActivity(num) {
        // 1. Guarda los valores seleccionados y encargados actuales
        const prevValues = [];
        for (let i = 0; i < 20; i++) { // 20 es un máximo arbitrario, puedes ajustarlo
            const sel = document.querySelector(`[name="actividades[${i}][act_id]"]`);
            const enc = document.querySelector(`[name="actividades[${i}][encargado]"]`);
            prevValues.push({
                act_id: sel ? sel.value : "",
                encargado: enc ? enc.value : ""
            });
        }

        const tabla = document.getElementById('tablaActividades');
        while (tabla.rows.length > 2) {
            tabla.deleteRow(2);
        }

        for (let i = 0; i < num; i++) {
            const fila = tabla.insertRow();
            const celdaNumero = fila.insertCell();
            celdaNumero.innerHTML = `<span>${i + 1}</span>`;

            // Celda 1: Hora
            const celdaHora = fila.insertCell();
            celdaHora.innerHTML = `<input type="time" name="actividades[${i}][hora]" id="horaActividad${i}" readonly>`;

            // Celda 2: Actividad (select)
            const celdaActividad = fila.insertCell();
            let selectHTML = `<select name="actividades[${i}][act_id]" class="select2" onchange="mostrarDuracion(this, 'duracion_${i}'); recalcularHoras();">`;
            selectHTML += `<option disabled selected>Selecciona una actividad</option>`;
            <?php foreach ($allActs as $act): ?>
                selectHTML += `<option value="<?= $act['act_id'] ?>" data-duracion="<?= $act['act_durat'] ?>"><?= $act['act_name'] ?></option>`;
            <?php endforeach; ?>
            selectHTML += `</select>`;
            celdaActividad.innerHTML = selectHTML;

            // Celda 3: Duración (solo hora)
            const celdaDuracion = fila.insertCell();
            celdaDuracion.style.width = '10%';
            celdaDuracion.innerHTML = `<span id="duracion_${i}" style="color:#888;"></span>`;

            // Celda 4: Encargado
            const celdaEncargado = fila.insertCell();
            celdaEncargado.innerHTML = `<input type="text" name="actividades[${i}][encargado]">`;

            // --- Nueva fila para comentarios ---
            const filaComentarios = tabla.insertRow();
            const celdaComentarios = filaComentarios.insertCell();
            celdaComentarios.colSpan = 5; // Ajusta según el número de columnas de tu tabla
            celdaComentarios.innerHTML = `<textarea class="but" name="actividades[${i}][comentarios]" rows="2" style="width:98%;" placeholder="Comentarios para la actividad ${i + 1}"></textarea>`;
        }

        $('.select2').select2();

        // 2. Restaura los valores seleccionados y encargados
        for (let i = 0; i < num; i++) {
            const sel = document.querySelector(`[name="actividades[${i}][act_id]"]`);
            const enc = document.querySelector(`[name="actividades[${i}][encargado]"]`);
            if (sel && prevValues[i] && prevValues[i].act_id) {
                sel.value = prevValues[i].act_id;
                // Forzar el evento change para actualizar duración y horas
                sel.dispatchEvent(new Event('change'));
            }
            if (enc && prevValues[i]) {
                enc.value = prevValues[i].encargado;
            }
        }

        // Inicializa la hora de la primera actividad
        const progTime = document.getElementById('prog_time');
        const horaActividad0 = document.getElementById('horaActividad0');
        if (progTime && horaActividad0) {
            horaActividad0.value = progTime.value;
            progTime.addEventListener('input', function () {
                horaActividad0.value = this.value;
                recalcularHoras();
            });
        }
    }

    // Suma minutos a una hora en formato "HH:MM"
    function sumarMinutos(hora, minutos) {
        if (!hora) return '';
        const [h, m] = hora.split(':').map(Number);
        const date = new Date(0, 0, 0, h, m + minutos, 0, 0);
        const hh = String(date.getHours()).padStart(2, '0');
        const mm = String(date.getMinutes()).padStart(2, '0');
        return `${hh}:${mm}`;
    }

    // Recalcula las horas de todas las actividades
    function recalcularHoras() {
        const num = document.getElementById('num_activities').value;
        let horaAnterior = document.getElementById('prog_time').value;

        for (let i = 0; i < num; i++) {
            const select = document.querySelector(`[name="actividades[${i}][act_id]"]`);
            const inputHora = document.getElementById(`horaActividad${i}`);
            if (i === 0) {
                inputHora.value = horaAnterior;
            } else {
                // Obtiene la duración de la actividad anterior
                const selectPrev = document.querySelector(`[name="actividades[${i-1}][act_id]"]`);
                let duracion = 0;
                if (selectPrev && selectPrev.value) {
                    const option = selectPrev.options[selectPrev.selectedIndex];
                    const duracionStr = option.getAttribute('data-duracion') || '00:00:00';
                    const [hh, mm] = duracionStr.split(':');
                    duracion = parseInt(hh) * 60 + parseInt(mm);
                }
                horaAnterior = sumarMinutos(horaAnterior, duracion);
                inputHora.value = horaAnterior;
            }
        }
    }

    // Nueva función para mostrar la duración
    function mostrarDuracion(select, spanId) {
        const selected = select.options[select.selectedIndex];
        let duracion = selected.getAttribute('data-duracion') || '';
        if (duracion) {
            // Si el formato es hh:mm:ss, solo mostramos hh:mm
            duracion = duracion.substring(0, 5);
            document.getElementById(spanId).textContent = duracion;
        } else {
            document.getElementById(spanId).textContent = '';
        }
    }
</script>
<!-------------------- Errores a solucionar -------------------->
<?php include "../../.res/templates/footer.php"; ?>