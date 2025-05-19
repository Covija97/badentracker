<!-- Variables, require e include -->
<?php
$title = "Nueva Actividad";
$page = "act";

require "../../.res/funct/funct.php";
include "../../.res/templates/header.php";
?>
<!-- Agregar en el <head> -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Consultas -->

<!-------------------- Consultas generales -------------------->

<?php
/* Consulta todas las actividades */
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
$allRam = $allRamQuery->fetch_all(MYSQLI_ASSOC);
?>

<!-------------------- POST -------------------->
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

}
?>

<!-------------------- main -------------------->

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

    <form method="POST" class="form-grid">
        <input type="hidden" name="act_id">

        <!-- Nombre de la actividad -->
        <div class="form-row">
            <label for="act_name">Nombre:</label>
            <input type="text" id="act_name" name="act_name" required>
        </div>

        <!-- Duración de la actividad -->
        <div class="form-row">
            <label for="act_durat">Duración:</label>
            <input type="time" id="act_durat" name="act_durat">
        </div>

        <!-- Descripción de la actividad -->
        <div class="form-row textarea-row">
            <label for="act_desc">Descripción:</label>
            <textarea id="act_desc" name="act_desc" rows="4"></textarea>
        </div>

        <!-- Objetivos de la actividad -->
        <div class="form-row">
            <label>
                <a class="but" href="../objetivos" title="Ver Objetivos">
                    Objetivos
                </a>
            </label>
            <select name="objs[]" id="objs" class="select2" multiple>
                <?php
                foreach ($allObjs as $obj) {
                    echo "<option value='" . $obj["obj_id"] . "' $selected>" . $obj["obj_name"] . "</option>";
                }
                ?>
            </select>
        </div>

        <!-- Categorias de la actividad -->
        <div class="form-row">
            <label>
                <a class="but" href="../categorias" title="Ver Categorias">
                    Categorias
                </a>
            </label>
            <select name="cat[]" id="cat" class="select2" multiple>
                <?php
                foreach ($allCats as $cat) {
                    $selected = "";
                    foreach ($catQuery as $selectedCat) {
                        if ($selectedCat["cat_name"] === $cat["cat_name"]) {
                            $selected = "selected";
                            break;
                        }
                    }
                    echo "<option value='" . $cat["cat_id"] . "' $selected>" . $cat["cat_name"] . "</option>";
                }
                ?>
            </select>
        </div>

        <!-- Materiales de la actividad -->
        <div class="form-row">
            <label>
                <a class="but" href="../materiales" title="Ver Materiales">
                    Materiales
                </a>
            </label>
            <select name="mat[]" id="mat" class="select2" multiple>
                <?php
                foreach ($allMats as $mat) {
                    $selected = "";
                    foreach ($matQuery as $selectedMat) {
                        if ($selectedMat["mat_name"] === $mat["mat_name"]) {
                            $selected = "selected";
                            break;
                        }
                    }
                    echo "<option value='" . $mat["mat_id"] . "' $selected>" . $mat["mat_name"] . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Añadir actividad</button>
        </div>
    </form>
</main>