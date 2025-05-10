
<!-- Variables, require e include -->
<?php
$title = "Categoria";
$page = "act";

require "../../../.res/funct/funct.php";
include "../../../.res/templates/header.php";
?>
<!-- Agregar en el <head> -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Consultas -->

<!-------------------- Consultas por id -------------------->
<?php
/* Consulta categoria segun el id */
$id = $_GET["id"];
$catSQL = "
SELECT
    cat_id,
    cat_name,
    cat_desc
FROM cat
WHERE cat_id = $id";
$catQuery = linkDB() -> query($catSQL);
$cat = $catQuery->fetch_assoc();
?>

<!-------------------- Consultas generales -------------------->

<!-------------------- POST -------------------->
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cat_id = $_POST["cat_id"];
    $cat_name = $_POST["cat_name"];
    $cat_desc = $_POST["cat_desc"];

    // Actualizar el categoria
    linkDB()->query("
        UPDATE cat
        SET cat_name = '$cat_name', cat_desc = '$cat_desc'
        WHERE cat_id = $cat_id;
    ");
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
    <a class="but align-left" href="delete.php?id=<?php echo $cat["cat_id"];?> " title="Borrar actividad" onclick="return confirm('¿Estás seguro de que deseas borrar esta categoria?');">
        <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M21 5.97998C17.67 5.64998 14.32 5.47998 10.98 5.47998C9 5.47998 7.02 5.57998 5.04 5.77998L3 5.97998"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M8.5 4.97L8.72 3.66C8.88 2.71 9 2 10.69 2H13.31C15 2 15.13 2.75 15.28 3.67L15.5 4.97"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M18.85 9.14001L18.2 19.21C18.09 20.78 18 22 15.21 22H8.79002C6.00002 22 5.91002 20.78 5.80002 19.21L5.15002 9.14001"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M10.33 16.5H13.66"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M9.5 12.5H14.5"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>

    <h1><?php echo $cat["cat_name"]; ?></h1>

    <form method="POST" class="form-grid">
        <input type="hidden" name="cat_id" value="<?php echo $cat["cat_id"]; ?>">

        <!-- Nombre del categoria -->
        <div class="form-row">
            <label for="cat_name">Nombre:</label>
            <input type="text" id="cat_name" name="cat_name" value="<?php echo $cat["cat_name"]; ?>" required>
        </div>

        <!-- Descripción de la actividad -->
        <div class="form-row textarea-row">
            <label for="cat_desc">Descripción:</label>
            <textarea id="cat_desc" name="cat_desc" rows="4"><?php echo $cat["cat_desc"]; ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Actualizar</button>
        </div>
    </form>
</main>

<!-- Footer -->
<?php
include "../../../.res/templates/footer.php";
?>

<!-------------------- Scripts de select2 -------------------->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>

