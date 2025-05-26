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

                </td>
                <td class="logoCell" style="color:#888;">
                    <img id="logoGrupo" src="" alt="Seleccione un grupo">
                </td>
            </tr>
        </table>
        <!-- Tabla de actividades -->
        <table id="actTable">
            <tr>
                <th class='branchCell' width='5%'>
                    Nº
                </th>
                <th class='branchCell' width='10%'>
                    Hora
                </th>
                <th class='branchCell' width='60%'>
                    Actividad
                </th>
                <th>
                    <a href="">
                        <svg width="400" height="400" viewBox="0 0 12 12" xmlns="http://www.w3.org/2000/svg">
                            <path d="m 2,6 h 8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                id="path1" />
                            <path d="M 6,10 V 2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                                id="path2" />
                        </svg>
                    </a>
                </th>
                <th class='branchCell' width='25%'>
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
        addAct(1); // <-- Añade esta línea para tener una fila por defecto
    });
</script>
<!---------------------------------------- Scripts de funciones ---------------------------------------->
<script>

    function addAct(num) {

        const tabla = document.getElementById('actTable');

        // Elimina las filas de actividades existentes, excepto la primera
        while (tabla.rows.length > 2) {
            tabla.deleteRow(2);
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
            let selectHTML = `<select name="actividades[${i}][act_id]" class="select2">`;
            selectHTML += `<option selected>Selecciona una actividad</option>`;
            <?php foreach ($allActs as $act): ?>
                selectHTML += `<option value="<?= $act['act_id'] ?>" data-duracion="<?= $act['act_durat'] ?>"><?= $act['act_name'] ?></option>`;
            <?php endforeach; ?>
            selectHTML += `</select>`;
            celdaActividad.innerHTML = selectHTML;

            // Celda 3: Duración (solo hora)
            const celdaDuracion = fila.insertCell();
            celdaDuracion.innerHTML = `<input type="text" name="actividades[${i}][duracion]" id="duracion_${i}" readonly>`;

            // Celda 4: Encargado
            const celdaEncargado = fila.insertCell();
            celdaEncargado.innerHTML = `<input type="text" name="actividades[${i}][encargado]" id="encargado_${i}">`;

            // Fila adicional para comentarios
            const filaComentario = tabla.insertRow();
            const celdaComentario = filaComentario.insertCell();
            celdaComentario.colSpan = 5;
            celdaComentario.innerHTML = `<textarea name="actividades[${i}][comentarios]" id="comentarios_${i}" placeholder="Comentarios sobre la actividad..."></textarea>`;
        }
        $('.select2').select2();

    }

</script>
<!-------------------- Errores a solucionar -------------------->
<?php include "../../.res/templates/footer.php"; ?>