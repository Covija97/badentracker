<?php
// Borrar reunión
require_once "../../.res/funct/funct.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../index.php");
    exit;
}

$id = intval($_GET['id']);
$db = linkDB();

// Eliminar actividades asociadas a la reunión
$stmt = $db->prepare("DELETE FROM prog_act WHERE prog_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();

// Eliminar la reunión principal
$stmt = $db->prepare("DELETE FROM prog WHERE prog_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();

header("Location: ../index.php");
exit;
?>
