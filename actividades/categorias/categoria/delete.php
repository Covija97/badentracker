<?php
require "../../../.res/funct/funct.php";

$conn = linkDB();

if (isset($_GET["id"]) && is_numeric($_GET["id"])) {
    $id = intval($_GET["id"]);
    $stmt = $conn->prepare("DELETE FROM cat WHERE cat_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
}
header("Location: ../");
exit();
?>