<?php
// Nuevo Grupo
$page = "reu";
$title = "Nuevo Grupo";

require_once "../../../.res/funct/funct.php";
include_once "../../../.res/templates/header.php";

$db = linkDB();

// Procesar formulario de nuevo grupo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $grp_name = trim($_POST['grp_name'] ?? '');
    $grp_address = trim($_POST['grp_address'] ?? '');
    $grp_info = trim($_POST['grp_info'] ?? '');
    if ($grp_name !== '') {
        $stmt = $db->prepare("INSERT INTO grps (grp_name, grp_address, grp_info) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $grp_name, $grp_address, $grp_info);
        $stmt->execute();
        $new_id = $db->insert_id;
        $stmt->close();
        // Guardar logo si se subió
        if (isset($_FILES['grp_logo']) && $_FILES['grp_logo']['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['grp_logo']['tmp_name'];
            $ext = strtolower(pathinfo($_FILES['grp_logo']['name'], PATHINFO_EXTENSION));
            if ($ext === 'png') {
                $dest = __DIR__ . "/../../../.res/img/logos-grupos/{$new_id}.png";
                move_uploaded_file($tmp_name, $dest);
            }
        }
        header("Location: ../index.php");
        exit;
    }
}
?>
<main>
    <a class="but align-left" href="../" title="Volver a Grupos">
        <!-- SVG de volver -->
        <svg width="400" height="400" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="m 3.0000046,9.88004 h 4.91998 c 1.7,0 3.0800004,-1.38 3.0800004,-3.08 0,-1.7 -1.3800004,-3.07999 -3.0800004,-3.07999 h -6.76998" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
            <path d="m 2.5699846,5.26994 -1.57,-1.57998 1.57,-1.57" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </a>
    <h1>Nuevo Grupo Scout</h1>
    <form method="POST" class="form-grid" enctype="multipart/form-data">
        <div class="form-row">
            <label for="grp_name">Nombre del Grupo:</label>
            <input type="text" id="grp_name" name="grp_name" required>
        </div>
        <div class="form-row">
            <label for="grp_logo">Logo:</label>
            <input type="file" id="grp_logo" name="grp_logo" accept="image/png" style="width:66%;">
        </div>
        <div class="form-row">
            <label for="grp_address">Dirección:</label>
            <input type="text" id="grp_address" name="grp_address" style="width:100%;margin-bottom:0.5rem;">
        </div>
        <div class="form-row textarea-row">
            <label for="grp_info">Información adicional:</label>
            <textarea class="but" id="grp_info" name="grp_info" rows="4"></textarea>
        </div>
        <div class="form-row">
            <button type="submit" class="btn-submit">Crear Grupo</button>
        </div>
    </form>
</main>
<?php
include_once "../../../.res/templates/footer.php";
?>
