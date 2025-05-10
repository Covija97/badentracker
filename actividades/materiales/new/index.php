<!-- Variables, require e include -->
<?php
$title = "Nuevo Material";
$page = "act";

require "../../../.res/funct/funct.php";
include "../../../.res/templates/header.php";
?>
<!-- Agregar en el <head> -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Consultas -->

<!-------------------- Consultas generales -------------------->

<?php
/* Consulta todos los materiales */
$allMatSQL = "SELECT mat_id, mat_name FROM mat";
$allMatQuery = linkDB()->query($allMatSQL);
$allMats = $allMatQuery->fetch_all(MYSQLI_ASSOC);
?>

<!-------------------- POST -------------------->
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mat_name = $_POST["mat_name"];
    $mat_desc = $_POST["mat_desc"];

    // Conexión a la base de datos
    $db = linkDB();

    // Añadir el material
    $insertMatQuery = "
        INSERT INTO mat (mat_name, mat_desc)
        VALUES ('$mat_name', '$mat_desc');
    ";
    if (!$db->query($insertMatQuery)) {
        die("Error al insertar material: " . $db->error);
    }

    // Redirigir a la página de materiales
    header("Location: ../");
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
        <input type="hidden" name="mat_id">

        <!-- Nombre del material -->
        <div class="form-row">
            <label for="mat_name">Nombre:</label>
            <input type="text" id="mat_name" name="mat_name" required>
        </div>

        <!-- Descripción del material -->
        <div class="form-row textarea-row">
            <label for="mat_desc">Descripción:</label>
            <textarea id="mat_desc" name="mat_desc" rows="4"></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">Añadir material</button>
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

