<!-- Estilo común para los Select2 -->
<link rel="stylesheet" href="/.res/css/select2.css?v=2">
<!-- Variables, require e include -->
<?php
$title = "Actividad";
$page = "act";

require "../../.res/funct/funct.php";
include "../../.res/templates/header.php";
?>
<!-- Agregar en el <head> -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Consultas -->

<!-------------------- Consultas por id -------------------->
<?php
/* Consulta actividad segun el id */
$id = $_GET["id"];
$actSQL = "
SELECT
    act_id,
    act_name,
    act_desc,
    act_durat
FROM act
WHERE act_id = $id";
$actQuery = linkDB() -> query($actSQL);
$act = $actQuery->fetch_assoc();

/* Consulta objetivos segun el id */
$objSQL = "
SELECT obj.obj_name, obj.obj_desc
FROM obj
INNER JOIN act_obj ON obj.obj_id = act_obj.obj_id
WHERE act_obj.act_id = $id
";
$objQuery = linkDB() -> query($objSQL);
$obj = $objQuery->fetch_all(MYSQLI_ASSOC);

/* Consulta categorias segun el id */
$catSQL = "
SELECT cat.cat_name, cat.cat_desc
FROM cat
INNER JOIN act_cat ON cat.cat_id = act_cat.cat_id
WHERE act_cat.act_id = $id
";
$catQuery = linkDB() -> query($catSQL);
$cat = $catQuery->fetch_all(MYSQLI_ASSOC);

/* Consulta materiales segun el id */
$matSQL = "
SELECT mat.mat_name, mat.mat_desc
FROM mat
INNER JOIN act_mat ON mat.mat_id = act_mat.mat_id
WHERE act_mat.act_id = $id
";
$matQuery = linkDB() -> query($matSQL);
$mat = $matQuery->fetch_all(MYSQLI_ASSOC);
?>

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
    $act_id = $_POST["act_id"];
    $act_name = $_POST["act_name"];
    $act_desc = $_POST["act_desc"];
    $act_durat = $_POST["act_durat"];
    $selectedObjs = $_POST["objs"]; // Array con los IDs de los objetivos seleccionados
    $selectedCats = $_POST["cat"]; // Array con los IDs de las categorias seleccionadas
    $selectedMats = $_POST["mat"]; // Array con los IDs de los materiales seleccionados

    // Actualizar la actividad
    linkDB()->query("
        UPDATE act
        SET act_name = '$act_name',
            act_desc = '$act_desc',
            act_durat = '$act_durat'
        WHERE act_id = $act_id;
    ");

    // Actualizar los objetivos asociados
    linkDB()->query("DELETE FROM act_obj WHERE act_id = $act_id;");
    foreach ($selectedObjs as $obj_id) {
        linkDB()->query("INSERT INTO act_obj (act_id, obj_id) VALUES ($act_id, $obj_id);");
    }

    // Actualizar las categorias asociadas
    linkDB()->query("DELETE FROM act_cat WHERE act_id = $act_id;");
    foreach ($selectedCats as $cat_id) {
        linkDB()->query("INSERT INTO act_cat (act_id, cat_id) VALUES ($act_id, $cat_id);");
    }

    // Actualizar los materiales asociados
    linkDB()->query("DELETE FROM act_mat WHERE act_id = $act_id;");
    foreach ($selectedMats as $mat_id) {
        linkDB()->query("INSERT INTO act_mat (act_id, mat_id) VALUES ($act_id, $mat_id);");
    }

    // Redirigir a la página de actividades
    header("Location: ../index.php");
}
?>

<!-------------------- main -------------------->

<main>
    <a class="but align-left" href="../" title="Volver a Actividades">
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
    <a class="but align-left" href="delete.php?id=<?php echo $act["act_id"];?> " title="Borrar actividad" onclick="return confirm('¿Estás seguro de que deseas borrar esta actividad?');">
        <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M21 5.97998C17.67 5.64998 14.32 5.47998 10.98 5.47998C9 5.47998 7.02 5.57998 5.04 5.77998L3 5.97998"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M8.5 4.97L8.72 3.66C8.88 2.71 9 2 10.69 2H13.31C15 2 15.13 2.75 15.28 3.67L15.5 4.97"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M18.85 9.14001L18.2 19.21C18.09 20.78 18 22 15.21 22H8.79002C6.00002 22 5.91002 20.78 5.80002 19.21L5.15002 9.14001"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M10.33 16.5H13.66"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M9.5 12.5H14.5"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>

    <h1><?php echo $act["act_name"]; ?></h1>

    <form method="POST" class="form-grid">
        <input type="hidden" name="act_id" value="<?php echo $act["act_id"]; ?>">

        <!-- Nombre de la actividad -->
        <div class="form-row">
            <label for="act_name">Nombre:</label>
            <input type="text" id="act_name" name="act_name" value="<?php echo $act["act_name"]; ?>" required>
        </div>

        <!-- Duración de la actividad -->
        <div class="form-row">
            <label for="act_durat">Duración:</label>
            <input type="time" id="act_durat" name="act_durat" value="<?php echo $act["act_durat"]; ?>" required>
        </div>

        <!-- Descripción de la actividad -->
        <div class="form-row textarea-row">
            <label for="act_desc">Descripción:</label>
            <textarea id="act_desc" name="act_desc" rows="4"><?php echo $act["act_desc"]; ?></textarea>
        </div>

        <!-- Objetivos de la actividad -->
        <div class="form-row">
            <label>
                <a class="but2" href="../objetivos" title="Ir a objetivos">
                    Objetivos
                </a>
            </label>
            <select name="objs[]" id="objs" class="select2" multiple>
                <?php
                foreach ($allObjs as $obj) {
                    $selected = "";
                    foreach ($objQuery as $selectedObj) {
                        if ($selectedObj["obj_name"] === $obj["obj_name"]) {
                            $selected = "selected";
                            break;
                        }
                    }
                    echo "<option value='" . $obj["obj_id"] . "' $selected>" . $obj["obj_name"] . "</option>";
                }
                ?>
            </select>
        </div>

        <!-- Categorias de la actividad -->
        <div class="form-row">
            <label>
                <a class="but2" href="../categorias" title="Ir a categorías">
                    Categorias
                </a>
            </label>
            <select name="cat[]" id="cat" class="select2" multiple required>
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
                <a class="but2" href="../materiales" title="Ir a materiales">
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
            <button type="submit" class="btn-submit">Actualizar</button>
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

