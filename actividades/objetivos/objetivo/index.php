
<!-- Variables, require e include -->
<?php
$title = "Objetivo";
$page = "act";

require "../../../.res/funct/funct.php";
include "../../../.res/templates/header.php";
?>
<!-- Agregar en el <head> -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Consultas -->

<!-------------------- Consultas por id -------------------->
<?php
/* Consulta objetivo segun el id */
$id = $_GET["id"];
$objSQL = "
SELECT
    obj_id,
    obj_name,
    obj_desc
FROM obj
WHERE obj_id = $id";
$objQuery = linkDB() -> query($objSQL);
$obj = $objQuery->fetch_assoc();
?>

<!-------------------- Consultas generales -------------------->


<!-------------------- POST -------------------->
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $obj_id = $_POST["obj_id"];
    $obj_name = $_POST["obj_name"];
    $obj_desc = $_POST["obj_desc"];

    // Actualizar el objetivo
    linkDB()->query("
        UPDATE obj
        SET obj_name = '$obj_name', obj_desc = '$obj_desc'
        WHERE obj_id = $obj_id;
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
    
    <a class="but align-left" href="delete.php?id=<?php echo $obj["obj_id"];?> " title="Borrar objetivo" onclick="return confirm('¿Estás seguro de que deseas borrar este objetivo?');">
        <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M21 5.97998C17.67 5.64998 14.32 5.47998 10.98 5.47998C9 5.47998 7.02 5.57998 5.04 5.77998L3 5.97998"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M8.5 4.97L8.72 3.66C8.88 2.71 9 2 10.69 2H13.31C15 2 15.13 2.75 15.28 3.67L15.5 4.97"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M18.85 9.14001L18.2 19.21C18.09 20.78 18 22 15.21 22H8.79002C6.00002 22 5.91002 20.78 5.80002 19.21L5.15002 9.14001"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M10.33 16.5H13.66"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        <path d="M9.5 12.5H14.5"  stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </a>

    <h1><?php echo $obj["obj_name"]; ?></h1>

    <form method="POST" class="form-grid">
        <input type="hidden" name="obj_id" value="<?php echo $obj["obj_id"]; ?>">

        <!-- Nombre del objetivo -->
        <div class="form-row">
            <label for="obj_name">Nombre:</label>
            <input type="text" id="obj_name" name="obj_name" value="<?php echo $obj["obj_name"]; ?>" required>
        </div>

        <!-- Descripción de la actividad -->
        <div class="form-row textarea-row">
            <label for="obj_desc">Descripción:</label>
            <textarea id="obj_desc" name="obj_desc" rows="4"><?php echo $obj["obj_desc"]; ?></textarea>
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

