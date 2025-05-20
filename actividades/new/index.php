<!-- Estilo común para los Select2 -->
<link rel="stylesheet" href="/badentracker/.res/css/select2.css?v=2">
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
/* Consulta todos los objetivos */
$allObjSQL = "SELECT obj_id, obj_name FROM obj";
$allObjQuery = linkDB()->query($allObjSQL);
$allObjs = $allObjQuery->fetch_all(MYSQLI_ASSOC);
?>

<?php
/* Consulta todas las categorias */
$allCatSQL = "SELECT cat_id, cat_name FROM cat";
$allCatQuery = linkDB()->query($allCatSQL);
$allCats = $allCatQuery->fetch_all(MYSQLI_ASSOC);
?>

<?php
/* Consulta todos los materiales */
$allMatSQL = "SELECT mat_id, mat_name FROM mat";
$allMatQuery = linkDB()->query($allMatSQL);
$allMats = $allMatQuery->fetch_all(MYSQLI_ASSOC);
?>

<!-------------------- POST -------------------->
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $act_name = $_POST["act_name"];
    $act_desc = $_POST["act_desc"];
    $act_durat = $_POST["act_durat"];
    $selectedObjs = $_POST["objs"]; // Array con los IDs de los objetivos seleccionados
    $selectedCats = $_POST["cat"]; // Array con los IDs de las categorias seleccionadas
    $selectedMats = $_POST["mat"]; // Array con los IDs de los materiales seleccionados

    // Conexión a la base de datos
    $db = linkDB();

    // Añadir la actividad
    $insertActQuery = "
        INSERT INTO act (act_name, act_desc, act_durat)
        VALUES ('$act_name', '$act_desc', '$act_durat');
    ";
    if (!$db->query($insertActQuery)) {
        die("Error al insertar actividad: " . $db->error);
    }

    // Obtener el ID de la actividad recién insertada
    $act_id = $db->insert_id;
    if (!$act_id) {
        die("Error: No se pudo obtener el ID de la actividad recién insertada.");
    }

    // Añadir los objetivos asociados
    foreach ($selectedObjs as $obj_id) {
        $insertObjQuery = "
            INSERT INTO act_obj (act_id, obj_id)
            VALUES ('$act_id', '$obj_id');
        ";
        if (!$db->query($insertObjQuery)) {
            die("Error al insertar objetivo: " . $db->error);
        }
    }

    // Añadir las categorias asociadas
    foreach ($selectedCats as $cat_id) {
        $insertCatQuery = "
            INSERT INTO act_cat (act_id, cat_id)
            VALUES ('$act_id', '$cat_id');
        ";
        if (!$db->query($insertCatQuery)) {
            die("Error al insertar categoría: " . $db->error);
        }
    }

    // Añadir los materiales asociados
    foreach ($selectedMats as $mat_id) {
        $insertMatQuery = "
            INSERT INTO act_mat (act_id, mat_id)
            VALUES ('$act_id', '$mat_id');
        ";
        if (!$db->query($insertMatQuery)) {
            die("Error al insertar material: " . $db->error);
        }
    }

    // Redirigir a la página de actividades
    header("Location: ../index.php");
}
?>

<!-------------------- main -------------------->

<main>
    <a class="but align-left" href="../index.php" title="Volver a Actividades">
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

<!-- Footer -->
<?php
include "../../.res/templates/footer.php";
?>

<!-------------------- Scripts de select2 -------------------->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>

