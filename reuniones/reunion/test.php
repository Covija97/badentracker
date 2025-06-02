<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/* -- Requires ---------------------------------------------------------- */
require('../../.res/fpdf/fpdf.php');
require "../../.res/funct/funct.php";
?>

<?php
/* Consulta los datos de la programación y las actividades asociadas */
$editMode = false;
$progData = [];
$progactData = [];
$matsData = []; // <-- Añade esta línea
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $editMode = true;
    $edit_id = intval($_GET['id']);
    $db = linkDB();
    // Obtener datos de la programación, grupos y ramas
    $stmt = $db->prepare("
    SELECT *
    FROM prog, rama, grps
    WHERE 
        prog.rama_id = rama.rama_id AND
        prog.grp_id = grps.grp_id AND
        prog_id = ?");
    $stmt->bind_param('i', $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $progData = $result->fetch_assoc();
    $stmt->close();
    // Obtener actividades asociadas
    $stmt2 = $db->prepare("
    SELECT *
    FROM prog_act
    JOIN act ON prog_act.act_id = act.act_id
    JOIN act_cat ON act.act_id = act_cat.act_id
    JOIN cat ON act_cat.cat_id = cat.cat_id
    WHERE prog_act.prog_id = ?
    ORDER BY prog_act.act_order ASC;");
    $stmt2->bind_param('i', $edit_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    while ($row = $result2->fetch_assoc()) {
        $progactData[] = $row;
    }
    $stmt2->close();
    // Obtenemos todos los materiales de todas las actividades agrupados
    $stmt3 = $db->prepare("
    SELECT DISTINCT m.mat_name
    FROM prog_act pa
    JOIN act_mat am ON pa.act_id = am.act_id
    JOIN mat m ON am.mat_id = m.mat_id
    WHERE pa.prog_id = ?;");
    $stmt3->bind_param('i', $edit_id);
    $stmt3->execute();
    $result3 = $stmt3->get_result();
    while ($row = $result3->fetch_assoc()) {
        $matsData[] = $row;
    }
    $stmt3->close();
}
?>