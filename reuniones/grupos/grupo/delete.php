<?php
// Borrar grupo
require_once "../../../.res/funct/funct.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$id = intval($_GET['id']);
$db = linkDB();

// Eliminar grupo de la base de datos
$stmt = $db->prepare("DELETE FROM grps WHERE grp_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();

// Eliminar logo si existe
$logoPath = __DIR__ . "/../../../.res/img/logos-grupos/{$id}.png";
if (file_exists($logoPath)) {
    unlink($logoPath);
}

header("Location: ../index.php");
exit;
?>
