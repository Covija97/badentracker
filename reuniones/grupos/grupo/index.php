<!-- Consulta de grupo -->
<?php
// Consulta de grupo
$page = "reu";
$title = "Grupo Scout";

require_once "../../../.res/funct/funct.php";
include_once "../../../.res/templates/header.php";

// Usar una sola conexión para todo el script
$db = linkDB();

$id = intval($_GET['id']);
$sql = "SELECT grp_id, grp_name, grp_address, grp_info FROM grps WHERE grp_id = $id LIMIT 1";
$query = $db->query($sql);
$grp = $query->fetch_assoc();
?>

<!-- POST -->
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grp_id'])) {
    $grp_id = intval($_POST['grp_id']);
    $grp_name = trim($_POST['grp_name']);
    $grp_address = trim($_POST['grp_address']);
    $grp_info = trim($_POST['grp_info']);
    $stmt = $db->prepare("UPDATE grps SET grp_name=?, grp_address=?, grp_info=? WHERE grp_id=?");
    $stmt->bind_param('sssi', $grp_name, $grp_address, $grp_info, $grp_id);
    $stmt->execute();
    $stmt->close();
    // Guardar logo si se subió
    if (isset($_FILES['grp_logo']) && $_FILES['grp_logo']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['grp_logo']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['grp_logo']['name'], PATHINFO_EXTENSION));
        if ($ext === 'png') {
            $dest = __DIR__ . "/../../../.res/img/logos-grupos/{$grp_id}.png";
            move_uploaded_file($tmp_name, $dest);
        }
        // Si no es PNG, simplemente no se guarda
    }
    // Recargar datos actualizados
    $sql = "SELECT grp_id, grp_name, grp_address, grp_info FROM grps WHERE grp_id = $id LIMIT 1";
    $query = $db->query($sql);
    $grp = $query->fetch_assoc();
    header("Location: ../index.php");
    exit;
}
?>
<!-- MAIN -->
<main>
    <a class="but align-left" href="../" title="Volver a Grupos">
        <!-- SVG de volver -->
        <svg width="400" height="400" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="m 3.0000046,9.88004 h 4.91998 c 1.7,0 3.0800004,-1.38 3.0800004,-3.08 0,-1.7 -1.3800004,-3.07999 -3.0800004,-3.07999 h -6.76998"
                stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
            <path d="m 2.5699846,5.26994 -1.57,-1.57998 1.57,-1.57" stroke-width="1.5" stroke-linecap="round"
                stroke-linejoin="round" />
        </svg>
    </a>
    <a class="but align-left" href="delete.php?id=<?php echo $grp["grp_id"]; ?> " title="Borrar grupo"
        onclick="return confirm('¿Estás seguro de que deseas borrar este grupo?');">
        <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M21 5.97998C17.67 5.64998 14.32 5.47998 10.98 5.47998C9 5.47998 7.02 5.57998 5.04 5.77998L3 5.97998"
                stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M8.5 4.97L8.72 3.66C8.88 2.71 9 2 10.69 2H13.31C15 2 15.13 2.75 15.28 3.67L15.5 4.97"
                stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
            <path
                d="M18.85 9.14001L18.2 19.21C18.09 20.78 18 22 15.21 22H8.79002C6.00002 22 5.91002 20.78 5.80002 19.21L5.15002 9.14001"
                stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M10.33 16.5H13.66" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M9.5 12.5H14.5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </a>

    <table style="text-align: center; padding: 0 10% 0 10%;">
        <tr>
            <th width="30%">
                <img src="<?php echo "../../../.res/img/logos-grupos/" . $grp["grp_id"] . ".png?v=" . time(); ?>"
                    style="height: 200px; width: 200px" alt="Logo del grupo">
            </th>
            <th>
                <h1 style="margin: 0; font-size: 2rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    <?php echo "Grupo Scout " . htmlspecialchars($grp["grp_name"] ?? ""); ?>
                </h1>
            </th>
        </tr>

    </table>

    <form method="POST" class="form-grid" enctype="multipart/form-data">
        <div class="form-row">
            <label for="grp_name">Nombre del Grupo:</label>
            <input type="text" id="grp_name" name="grp_name"
                value="<?php echo htmlspecialchars($grp["grp_name"] ?? ""); ?>" required>
        </div>
        <div class="form-row">
            <label for="grp_logo">Logo:</label>
            <input type="file" id="grp_logo" name="grp_logo" accept="image/png" style="width:66%;">
        </div>
        <div class="form-row">
            <label for="grp_address">Dirección:</label>
            <input type="text" id="grp_address" name="grp_address"
                value="<?php echo htmlspecialchars($grp["grp_address"] ?? ''); ?>"
                style="width:100%;margin-bottom:0.5rem;">
        </div>
        <div class="form-row textarea-row">
            <label for="grp_info">Información adicional:</label>
            <textarea class="but2" id="grp_info" name="grp_info"
                rows="4"><?php echo htmlspecialchars($grp["grp_info"] ?? ""); ?></textarea>
        </div>
        <div class="form-row">
            <input type="hidden" name="grp_id" value="<?php echo $grp["grp_id"] ?? ''; ?>">
            <button type="submit" class="btn-submit">Guardar Cambios</button>
        </div>
    </form>
</main>
<?php
include_once "../../../.res/templates/footer.php";
?>