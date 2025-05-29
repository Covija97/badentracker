<?php
/* -- Requires ---------------------------------------------------------- */
require('../../.res/fpdf/fpdf.php');
require "../../.res/funct/funct.php";
?>
<?php
/* -- Consultas generales ----------------------------------------------- */
/* Consulta actividades, categoria, materiales y objetivos */
$allActSQL = "
SELECT
    act.act_id,
    act.act_name,
    act.act_desc,
    act.act_durat,
    GROUP_CONCAT(DISTINCT obj.obj_name SEPARATOR '<br>') AS act_objs,
    GROUP_CONCAT(DISTINCT cat.cat_name SEPARATOR '<br>') AS act_cats,
    GROUP_CONCAT(DISTINCT mat.mat_name SEPARATOR '<br>') AS act_mats
FROM act
LEFT JOIN act_obj ON act.act_id = act_obj.act_id
LEFT JOIN obj ON act_obj.obj_id = obj.obj_id

LEFT JOIN act_cat ON act.act_id = act_cat.act_id
LEFT JOIN cat ON act_cat.cat_id = cat.cat_id

LEFT JOIN act_mat ON act.act_id = act_mat.act_id
LEFT JOIN mat ON act_mat.mat_id = mat.mat_id

GROUP BY act.act_id, act.act_name, act.act_desc, act.act_durat;
";
$allActQuery = linkDB()->query(query: $allActSQL);
$allActs = $allActQuery->fetch_all(MYSQLI_ASSOC);
?>

<?php
/* Consulta todas las ramas */
$allRamSQL = "SELECT rama_id, rama_name FROM rama";
$allRamQuery = linkDB()->query(query: $allRamSQL);
$allRams = $allRamQuery->fetch_all(mode: MYSQLI_ASSOC);
?>

<?php
/* Consulta todos los grupos */
$allGrpSQL = "SELECT grp_id, grp_name FROM grps";
$allGrpQuery = linkDB()->query(query: $allGrpSQL);
$allGrps = $allGrpQuery->fetch_all(mode: MYSQLI_ASSOC);
?>

<?php
/* Consulta los datos de la programación y las actividades asociadas */
$editMode = false;
$progData = [];
$progActs = [];
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $editMode = true;
    $edit_id = intval(value: $_GET['id']);
    $db = linkDB();
    // Obtener datos de la programación
    $stmt = $db->prepare("SELECT * FROM prog WHERE prog_id = ?");
    $stmt->bind_param('i', $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $progData = $result->fetch_assoc();
    $stmt->close();
    // Obtener actividades asociadas
    $stmt2 = $db->prepare("SELECT * FROM prog_act WHERE prog_id = ? ORDER BY act_order ASC");
    $stmt2->bind_param('i', $edit_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    while ($row = $result2->fetch_assoc()) {
        $progActs[] = $row;
    }
    $stmt2->close();
}
?>

<?php
/* -- Funciones --------------------------------------------------------- */
/* Obtener color de la rama */
function getRamaColor($ramaId): array
{
    switch ($ramaId) {
        case '2':
            return [252, 229, 205]; // Castores
        case '3':
            return [255, 241, 204]; // Lobatos
        case '4':
            return [201, 218, 248]; // Rangers
        case '5':
            return [244, 204, 204]; // Pioneros
        case '6':
            return [217, 234, 211]; // Rutas
        default:
            return [255, 255, 255]; // Default color
    }
}
?>

<?php
/* -- Variables de entorno ---------------------------------------------- */
// Obtener el color de la rama
$colorRama = getRamaColor(ramaId: 2);

?>
<?php
/* -- PDF --------------------------------------------------------------- */

class PDF extends FPDF
{

    // Cabecera de página
    function Header()
    {
        // Logo
        $this->Image(file: '../../.res/fpdf/img/header.png', w: 170);
        // Arial bold 15
        $this->SetFont(family: 'Arial', style: 'B', size: 15);
        $this->Ln(h: 5);
    }


    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', '', 10);
        // Número de página
        $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), border: 0, ln: 0, align: 'R');
    }

    // Tabla de contenido pedagógico
    function TablePedag($colorRama): void
    {
        // Selecionamos el color de la rama
        $this->SetFillColor(r: $colorRama[0], g: $colorRama[1], b: $colorRama[2]);

        // Selecionamos la fuente
        $this->SetFont(family: 'Arial', style: 'B', size: 10);

        // 
        $this->SetDrawColor(r: 190, g: 190, b: 190);
        $this->SetLineWidth(width: .01);

        // Altura celdas
        $cell_h = 7;

        // Encabezados
        $this->Cell(w: 42, h: $cell_h, txt: utf8_decode(string: 'Ámbito'), border: 1, ln: 0, align: 'C', fill: false);
        $this->Cell(w: 42, h: $cell_h, txt: utf8_decode(string: 'Área'), border: 1, ln: 0, align: 'C', fill: false);
        $this->Cell(w: 42, h: $cell_h, txt: utf8_decode(string: 'Línea pedagógica'), border: 1, ln: 0, align: 'C', fill: false);
        $this->Cell(w: 44, h: $cell_h, txt: utf8_decode(string: 'Contenidos'), border: 1, ln: 1, align: 'C', fill: false);

        // Filas
        $this->SetFont(family: 'Arial', style: '', size: 10);

        // Grupo de datos por ámbito
        // RESPONSABILIDAD
        $this->Cell(w: 42, h: $cell_h * 2, txt: utf8_decode(string: 'Responsabilidad'), border: 1, ln: 0, align: 'L', fill: true);
        $this->Cell(w: 42, h: $cell_h, txt: utf8_decode(string: 'Personalidad'), border: 1);
        $this->Cell(w: 42, h: $cell_h, txt: utf8_decode(string: 'Autonomía'), border: 1);
        $this->Cell(w: 44, h: $cell_h, txt: utf8_decode(string: 'Progreso personal'), border: 1, ln: 1);

        $this->Cell(w: 42, h: $cell_h, txt: '', border: 0, ln: 0); // celda vacía para el ámbito
        $this->Cell(w: 42, h: $cell_h, txt: 'Social', border: 1);
        $this->Cell(42, $cell_h, utf8_decode('Habilidades sociales'), 1);
        $this->Cell(44, $cell_h, 'Confianza', 1, 1);

        // PAÍS
        $this->Cell(42, $cell_h * 3, utf8_decode('País'), 1, 0, 'L', true);
        $this->Cell(42, $cell_h, 'Personalidad', 1);
        $this->Cell(42, $cell_h, 'Compromiso', 1);
        $this->Cell(44, $cell_h, utf8_decode('Participación social'), 1, 1);

        $this->Cell(42, $cell_h * 2, '', 0, 0);
        $this->Cell(42, $cell_h * 2, 'Emocional', 1);
        $this->Cell(42, $cell_h * 2, 'Comunidad', 1);
        $this->MultiCell(44, $cell_h, utf8_decode('Relación con la rama y el grupo scouts'), 1, 1);

        // FE
        $this->Cell(42, $cell_h * 2, 'Fe', 1, 0, 'L', true);
        $this->Cell(42, $cell_h, 'Espiritual', 1);
        $this->Cell(42, $cell_h, utf8_decode('Oración'), 1);
        $this->Cell(44, $cell_h, utf8_decode('Oración y reflexión'), 1, 1);

        $this->Cell(42, $cell_h, '', 0, 0);
        $this->Cell(42, $cell_h, 'Físico', 1);
        $this->Cell(42, $cell_h, 'Corporeidad', 1);
        $this->Cell(44, $cell_h, utf8_decode('Protección de la naturaleza'), 1, 1);
    }

}

// Creación del objeto de la clase heredada
$pdf = new PDF(orientation: 'P', unit: 'mm', size: 'A4');
$pdf->SetMargins(left: 20, top: 15, right: 20);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont(family: 'Times', style: '', size: 12);
// Llamar a la función para agregar la tabla al PDF
$pdf->TablePedag(colorRama: getRamaColor(ramaId: $progData['rama_id']));
$pdf->Output();
?>