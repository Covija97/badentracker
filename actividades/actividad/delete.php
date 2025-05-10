<?php
require "../../.res/funct/funct.php";

$conn = linkDB();

if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $conn->query("DELETE FROM act WHERE act_id = $id;");
}
header("Location: ../");
exit();
?>