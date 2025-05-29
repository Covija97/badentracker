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
$allActs = $allActQuery->fetch_all(mode: MYSQLI_ASSOC);
?>


<?php
/* Consulta los datos de la programación y las actividades asociadas */
$editMode = false;
$progData = [];
$progActs = [];
if (isset($_GET['id']) && is_numeric(value: $_GET['id'])) {
    $editMode = true;
    $edit_id = intval(value: $_GET['id']);
    $db = linkDB();
    // Obtener datos de la programación
    $stmt = $db->prepare(query: "SELECT * FROM prog WHERE prog_id = ?");
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
$ramaData = [];
if (isset($progData['rama_id']) && is_numeric($progData['rama_id'])) {
    $rama_id = intval(value: $progData['rama_id']);
    $db = linkDB();
    // Obtener datos de la rama
    $stmt = $db->prepare(query: "SELECT * FROM rama WHERE rama_id = ?");
    $stmt->bind_param('i', $rama_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ramaData = $result->fetch_assoc();
    $stmt->close();
}
?>

<?php
$grpData = [];
if (isset($progData['grp_id']) && is_numeric($progData['grp_id'])) {
    $grp_id = intval(value: $progData['grp_id']);
    $db = linkDB();
    // Obtener datos de la rama
    $stmt = $db->prepare(query: "SELECT * FROM grps WHERE grp_id = ?");
    $stmt->bind_param('i', $grp_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $grpData = $result->fetch_assoc();
    $stmt->close();
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

/* Calcular ronda solar */
function getRondaSolar($progDate): string
{
    $date = new DateTime($progDate);
    $mes = (int) $date->format('n'); // Número del mes (1–12)
    $anio = (int) $date->format('Y'); // Año con 4 dígitos

    if ($mes >= 9) {
        // De septiembre a diciembre
        $ronda = $anio . '/' . sprintf('%02d', ($anio + 1) % 100);
    } else {
        // De enero a agosto
        $ronda = ($anio - 1) . '/' . sprintf('%02d', $anio % 100);
    }
    return $ronda;
}
?>

<?php
/* -- PDF --------------------------------------------------------------- */

class PDF extends FPDF
{

    // Cabecera de página
    function Header(): void
    {
        // Logo
        $this->Image(file: '../../.res/fpdf/img/header.png', w: 170);
        // Arial bold 15
        $this->SetFont(family: 'Arial', style: 'B', size: 15);
        $this->Ln(h: 5);
    }


    // Pie de página
    function Footer(): void
    {
        // Posición: a 1,5 cm del final
        $this->SetY(y: -15);
        // Arial italic 8
        $this->SetFont(family: 'Arial', style: '', size: 10);
        // Número de página
        $this->Cell(w: 0, h: 10, txt: utf8_decode(string: 'Página ' . $this->PageNo() . '/{nb}'), border: 0, ln: 0, align: 'R');
    }

    // Tabla de contenido pedagógico
    function TablePedag($progData): void
    {
        // Selecionamos el color de la rama
        $colorRama = getRamaColor($progData['rama_id']);
        $this->SetFillColor(r: $colorRama[0], g: $colorRama[1], b: $colorRama[2]);

        // Selecionamos la fuente
        $this->SetFont(family: 'Arial', style: '', size: 10);

        // Formato del borde de la tabla
        $this->SetDrawColor(r: 190, g: 190, b: 190);
        $this->SetLineWidth(width: .01);

        // Tamaño celdas
        $cell_h = 7;
        $cell_w = [40, 40, 40, 50]; // 170

        // Encabezados
        $this->Cell(w: $cell_w[0], h: $cell_h, txt: utf8_decode(string: 'Ámbito'), border: 1, ln: 0, align: 'C', fill: false);
        $this->Cell(w: $cell_w[1], h: $cell_h, txt: utf8_decode(string: 'Área'), border: 1, ln: 0, align: 'C', fill: false);
        $this->Cell(w: $cell_w[2], h: $cell_h, txt: utf8_decode(string: 'Línea pedagógica'), border: 1, ln: 0, align: 'C', fill: false);
        $this->Cell(w: $cell_w[3], h: $cell_h, txt: utf8_decode(string: 'Contenidos'), border: 1, ln: 1, align: 'C', fill: false);

        // RESPONSABILIDAD
        $this->Cell(w: $cell_w[0], h: $cell_h * 2, txt: utf8_decode(string: 'Responsabilidad'), border: 1, ln: 0, align: 'L', fill: true);
        $this->Cell(w: $cell_w[1], h: $cell_h, txt: utf8_decode(string: 'Personalidad'), border: 1, fill: true);
        $this->Cell(w: $cell_w[2], h: $cell_h, txt: utf8_decode(string: 'Autonomía'), border: 1, fill: true);
        $this->Cell(w: $cell_w[3], h: $cell_h, txt: utf8_decode(string: 'Progreso personal'), border: 1, ln: 1, fill: true);

        $this->Cell(w: $cell_w[0], h: $cell_h, txt: '', border: 0, ln: 0); // celda vacía
        $this->Cell(w: $cell_w[1], h: $cell_h, txt: utf8_decode(string: 'Social'), border: 1);
        $this->Cell(w: $cell_w[2], h: $cell_h, txt: utf8_decode(string: 'Habilidades sociales'), border: 1);
        $this->Cell(w: $cell_w[3], h: $cell_h, txt: utf8_decode(string: 'Confianza'), border: 1, ln: 1);

        // PAÍS
        $this->Cell(w: $cell_w[0], h: $cell_h * 3, txt: utf8_decode(string: 'País'), border: 1, ln: 0, align: 'L', fill: true);
        $this->Cell(w: $cell_w[1], h: $cell_h, txt: utf8_decode(string: 'Personalidad'), border: 1, fill: true);
        $this->Cell(w: $cell_w[2], h: $cell_h, txt: utf8_decode(string: 'Compromiso'), border: 1, fill: true);
        $this->Cell(w: $cell_w[3], h: $cell_h, txt: utf8_decode(string: 'Participación social'), border: 1, ln: 1, fill: true);

        $this->Cell(w: $cell_w[0], h: $cell_h * 2, txt: '', border: 0, ln: 0);
        $this->Cell(w: $cell_w[1], h: $cell_h * 2, txt: utf8_decode(string: 'Emocional'), border: 1);
        $this->Cell(w: $cell_w[2], h: $cell_h * 2, txt: utf8_decode(string: 'Comunidad'), border: 1);
        $this->MultiCell(w: $cell_w[3], h: $cell_h, txt: utf8_decode(string: 'Relación con la rama y el grupo scouts'), border: 1, align: 1);

        // FE
        $this->Cell(w: $cell_w[0], h: $cell_h * 2, txt: utf8_decode(string: 'Fe'), border: 1, ln: 0, align: 'L', fill: true);
        $this->Cell(w: $cell_w[1], h: $cell_h, txt: utf8_decode(string: 'Espiritual'), border: 1, fill: true);
        $this->Cell(w: $cell_w[2], h: $cell_h, txt: utf8_decode(string: 'Oración'), border: 1, fill: true);
        $this->Cell(w: $cell_w[3], h: $cell_h, txt: utf8_decode(string: 'Oración y reflexión'), border: 1, ln: 1, fill: true);

        $this->Cell(w: $cell_w[0], h: $cell_h, txt: '', border: 0, ln: 0);
        $this->Cell(w: $cell_w[1], h: $cell_h, txt: utf8_decode(string: 'Físico'), border: 1);
        $this->Cell(w: $cell_w[2], h: $cell_h, txt: utf8_decode(string: 'Corporeidad'), border: 1);
        $this->Cell(w: $cell_w[3], h: $cell_h, txt: utf8_decode(string: 'Protección de la naturaleza'), border: 1, ln: 1);

        // Espaciado
        $this->Ln(h: 10);
    }

    // Tabla información del grupo

    function TableGroup($progData, $grpData, $ramaData): void
    {
        // Selecionamos el color de la rama
        $colorRama = getRamaColor(ramaId: $progData['rama_id']);
        $this->SetFillColor(r: $colorRama[0], g: $colorRama[1], b: $colorRama[2]);

        // Selecionamos la fuente
        $this->SetFont(family: 'Arial', style: '', size: 10);

        // Formato del borde de la tabla
        $this->SetDrawColor(r: 190, g: 190, b: 190);
        $this->SetLineWidth(width: .01);

        // Tamaño celdas
        $cell_h = 7;
        $cell_w = [40, 60, 35, 35]; // 170

        // Formato de fecha d-m-y
        $dateFormat = date(format: 'd-m-Y', timestamp: strtotime(datetime: $progData['prog_date']));

        // Contar numero de responsables
        $nRespRaw = count(value: array_filter(array: explode(separator: "\n", string: str_replace(search: ["\r\n", "\r"], replace: "\n", subject: $progData['responsibles'])), callback: fn($l): bool => trim(string: $l) !== ''));
        $nResp = max(5, $nRespRaw);


        //
        $this->cell(w: $cell_w[0], h: $cell_h, txt: utf8_decode(string: 'Grupo Scout'), border: 1, ln: 0, align: 'C', fill: true);
        $this->cell(w: $cell_w[1], h: $cell_h, txt: $grpData['grp_name'], border: 1, ln: 0, align: 'C', fill: false);
        $this->cell(w: $cell_w[2], h: $cell_h, txt: utf8_decode(string: 'Ronda Solar'), border: 1, ln: 0, align: 'C', fill: true);
        $this->cell(w: $cell_w[3], h: $cell_h, txt: getRondaSolar(progDate: $progData['prog_date']), border: 1, ln: 1, align: 'C', fill: false);

        $this->cell(w: $cell_w[0], h: $cell_h, txt: utf8_decode(string: 'Lugar'), border: 1, ln: 0, align: 'C', fill: true);
        $this->cell(w: $cell_w[1], h: $cell_h, txt: $progData['prog_place'], border: 1, ln: 0, align: 'C', fill: false);
        $this->cell(w: $cell_w[2], h: $cell_h, txt: utf8_decode(string: 'Fecha'), border: 1, ln: 0, align: 'C', fill: true);
        $this->cell(w: $cell_w[3], h: $cell_h, txt: $dateFormat, border: 1, ln: 1, align: 'C', fill: false);

        $this->cell(w: $cell_w[0], h: $cell_h, txt: utf8_decode(string: 'Coordinador'), border: 1, ln: 0, align: 'C', fill: true);
        $this->cell(w: $cell_w[1], h: $cell_h, txt: $progData['prog_coord'], border: 1, ln: 0, align: 'C', fill: false);
        $this->cell(w: $cell_w[2], h: $cell_h, txt: utf8_decode(string: 'Nº educandos'), border: 1, ln: 0, align: 'C', fill: true);
        $this->cell(w: $cell_w[3], h: $cell_h, txt: $progData['prog_child_N'], border: 1, ln: 1, align: 'C', fill: false);

        $this->cell(w: $cell_w[0] + $cell_w[1] + $cell_w[2], h: $cell_h, txt: utf8_decode(string: 'Responsables asistentes'), border: 1, ln: 0, align: 'C', fill: true);
        $this->cell(w: $cell_w[3], h: $cell_h, txt: utf8_decode(string: 'Logo del grupo'), border: 1, ln: 1, align: 'C', fill: true);

        $x = $this->GetX();
        $y = $this->GetY();

        $this->cell(w: $cell_w[0], h: $cell_h * $nResp, txt: $ramaData['rama_name'], border: 1, ln: 0, align: 'C', fill: true);
        $this->MultiCell(w: $cell_w[1] + $cell_w[2], h: $cell_h, txt: $progData['responsibles'], border: 1, align: 'L', fill: false);

        // Ponemos la imagen en la última celda
        $this->SetXY($x + $cell_w[0] + $cell_w[1] + $cell_w[2], $y);
        $this->cell(w: $cell_w[3], h: $cell_h * $nResp, txt: '', border: 1, ln: 0, align: 'C', fill: false);

        // Ajusta posición y tamaño de la imagen dentro de la celda
        $imageX = $x + $cell_w[0] + $cell_w[1] + $cell_w[2] + 2; // 2 mm de margen desde el borde izquierdo de la celda
        $imageY = $y + 2; // 2 mm desde arriba
        $imageW = $cell_w[3] - 4; // ancho menos margen
        $imageH = ($cell_h * $nResp) - 4; // alto menos margen

        // Inserta la imagen (ajusta la ruta a la correcta)
        $this->Image(file: '../../.res/img/logos-grupos/' . $progData['grp_id'] . '.png', x: $imageX, y: $imageY, w: 0, h: $imageH);

        $this->Ln(); // Salto de línea para continuar después
    }

}

// Creación del objeto de la clase heredada
$pdf = new PDF(orientation: 'P', unit: 'mm', size: 'A4');
$pdf->SetMargins(left: 20, top: 15, right: 20);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont(family: 'Times', style: '', size: 12);
// Llamar a la función para agregar la tabla al PDF
$pdf->TablePedag(progData: $progData);
$pdf->TableGroup(progData: $progData, grpData: $grpData, ramaData: $ramaData);
$pdf->Output();

?>