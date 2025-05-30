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
    // Obtener datos de la programación, grupos y ramas
    $stmt = $db->prepare(query: "
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

/* Calcular ronda solar */
function getRondaSolar($progDate): string
{
    $date = new DateTime(datetime: $progDate);
    $mes = (int) $date->format(format: 'n'); // Número del mes (1–12)
    $anio = (int) $date->format(format: 'Y'); // Año con 4 dígitos

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

    function TableGroup($progData): void
    {
        // Selecionamos el color de la rama
        $colorRama = getRamaColor($progData['rama_id']);
        $this->SetFillColor($colorRama[0], $colorRama[1], $colorRama[2]);

        // Fuente y formato
        $this->SetFont('Arial', '', 10);
        $this->SetDrawColor(190, 190, 190);
        $this->SetLineWidth(.01);

        $cell_h = 7;
        $cell_w = [40, 60, 35, 35];

        $dateFormat = date('d-m-Y', strtotime($progData['prog_date']));

        // Contar número de responsables
        $rawText = is_resource($progData['responsibles']) ? stream_get_contents($progData['responsibles']) : $progData['responsibles'];
        $nombres = array_filter(explode("\n", str_replace(["\r\n", "\r"], "\n", $rawText)), fn($n) => trim($n) !== '');
        $count = count($nombres);
        $nResp = max(5, $count); // mínimo de 5 filas

        // Encabezado de tabla
        $this->cell($cell_w[0], $cell_h, utf8_decode('Grupo Scout'), 1, 0, 'C', true);
        $this->cell($cell_w[1], $cell_h, $progData['grp_name'], 1, 0, 'C');
        $this->cell($cell_w[2], $cell_h, utf8_decode('Ronda Solar'), 1, 0, 'C', true);
        $this->cell($cell_w[3], $cell_h, getRondaSolar($progData['prog_date']), 1, 1, 'C');

        $this->cell($cell_w[0], $cell_h, utf8_decode('Lugar'), 1, 0, 'C', true);
        $this->cell($cell_w[1], $cell_h, $progData['prog_place'], 1, 0, 'C');
        $this->cell($cell_w[2], $cell_h, utf8_decode('Fecha'), 1, 0, 'C', true);
        $this->cell($cell_w[3], $cell_h, $dateFormat, 1, 1, 'C');

        $this->cell($cell_w[0], $cell_h, utf8_decode('Coordinador'), 1, 0, 'C', true);
        $this->cell($cell_w[1], $cell_h, $progData['prog_coord'], 1, 0, 'C');
        $this->cell($cell_w[2], $cell_h, utf8_decode('Nº educandos'), 1, 0, 'C', true);
        $this->cell($cell_w[3], $cell_h, $progData['prog_child_N'], 1, 1, 'C');

        $this->cell($cell_w[0] + $cell_w[1] + $cell_w[2], $cell_h, utf8_decode('Responsables asistentes'), 1, 0, 'C', true);
        $this->cell($cell_w[3], $cell_h, utf8_decode('Logo del grupo'), 1, 1, 'C', true);

        $x = $this->GetX();
        $y = $this->GetY();

        // Celdas responsables e imagen
        $this->cell($cell_w[0], $cell_h * $nResp, $progData['rama_name'], 1, 0, 'C', true);
        // Rellenar hasta mínimo 5 líneas
        $responsablesArray = explode("\n", str_replace(["\r\n", "\r"], "\n", $rawText));
        $responsablesLimpios = array_filter(array_map('trim', $responsablesArray), fn($r) => $r !== '');
        $lineas = $responsablesLimpios;

        // Añadir líneas vacías si hay menos de 5
        while (count($lineas) < 5) {
            $lineas[] = ' ';
        }

        // Recombinar para MultiCell
        $contenidoResponsables = implode("\n", $lineas);

        $this->MultiCell($cell_w[1] + $cell_w[2], $cell_h, $contenidoResponsables, 1, 'L', false);

        // Volvemos a la posición para dibujar la celda de la imagen
        $this->SetXY($x + $cell_w[0] + $cell_w[1] + $cell_w[2], $y);
        $this->cell($cell_w[3], $cell_h * $nResp, '', 1, 0, 'C');

        // Insertar imagen centrada
        $imagePath = '../../.res/img/logos-grupos/' . $progData['grp_id'] . '.png';

        if (file_exists($imagePath)) {
            // Obtener dimensiones reales de la imagen
            [$imgWidth, $imgHeight] = getimagesize($imagePath);

            // Altura y ancho disponibles en la celda
            $maxW = $cell_w[3] - 4; // dejando 2 mm de margen a cada lado
            $maxH = ($cell_h * $nResp) - 4;

            // Calcular escala para mantener proporción
            $scale = min($maxW / $imgWidth, $maxH / $imgHeight);

            // Tamaño final escalado
            $finalW = $imgWidth * $scale;
            $finalH = $imgHeight * $scale;

            // Centrar dentro de la celda
            $imageX = $x + $cell_w[0] + $cell_w[1] + $cell_w[2] + ($cell_w[3] - $finalW) / 2;
            $imageY = $y + (($cell_h * $nResp) - $finalH) / 2;

            // Insertar imagen
            $this->Image($imagePath, $imageX, $imageY, $finalW, $finalH);
        }


        $this->Ln(); // Salto de línea
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
$pdf->TableGroup(progData: $progData);
$pdf->Output();

?>