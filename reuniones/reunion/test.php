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
class PDF extends FPDF
{

    // Cabecera de página
    function Header(): void
    {
        // Logo
        $this->Image('../../.res/fpdf/img/header.png', null, null, 170);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        $this->Ln(7);
    }

    // Pie de página
    function Footer(): void
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', '', 10);
        // Número de página
        $this->Cell(0, 10, utf8_decode('Página ' . $this->PageNo() . '/{nb}'), 0, 0, 'R');
    }

    function TablePedag($progData): void
    {
        // Selecionamos el color de la rama
        $colorRama = getRamaColor($progData['rama_id']);
        $this->SetFillColor($colorRama[0], $colorRama[1], $colorRama[2]);

        // Selecionamos la fuente
        $this->SetFont('Arial', '', 10);

        // Formato del borde de la tabla
        $this->SetDrawColor(190, 190, 190);
        $this->SetLineWidth(.01);

        // Tamaño celdas
        $cell_h = 7;
        $cell_w = [40, 40, 40, 50]; // 170

        // Encabezados
        $this->Cell($cell_w[0], $cell_h, utf8_decode('Ámbito'), 1, 0, 'C', false);
        $this->Cell($cell_w[1], $cell_h, utf8_decode('Área'), 1, 0, 'C', false);
        $this->Cell($cell_w[2], $cell_h, utf8_decode('Línea pedagógica'), 1, 0, 'C', false);
        $this->Cell($cell_w[3], $cell_h, utf8_decode('Contenidos'), 1, 1, 'C', false);

        // RESPONSABILIDAD
        $this->Cell($cell_w[0], $cell_h * 2, utf8_decode('Responsabilidad'), 1, 0, 'L', true);
        $this->Cell($cell_w[1], $cell_h, utf8_decode('Personalidad'), 1, 0, 'L', true);
        $this->Cell($cell_w[2], $cell_h, utf8_decode('Autonomía'), 1, 0, 'L', true);
        $this->Cell($cell_w[3], $cell_h, utf8_decode('Progreso personal'), 1, 1, 'L', true);

        $this->Cell($cell_w[0], $cell_h, '', 0, 0);
        $this->Cell($cell_w[1], $cell_h, utf8_decode('Social'), 1);
        $this->Cell($cell_w[2], $cell_h, utf8_decode('Habilidades sociales'), 1);
        $this->Cell($cell_w[3], $cell_h, utf8_decode('Confianza'), 1, 1);

        // PAÍS
        $this->Cell($cell_w[0], $cell_h * 3, utf8_decode('País'), 1, 0, 'L', true);
        $this->Cell($cell_w[1], $cell_h, utf8_decode('Personalidad'), 1, 0, 'L', true);
        $this->Cell($cell_w[2], $cell_h, utf8_decode('Compromiso'), 1, 0, 'L', true);
        $this->Cell($cell_w[3], $cell_h, utf8_decode('Participación social'), 1, 1, 'L', true);

        $this->Cell($cell_w[0], $cell_h * 2, '', 0, 0);
        $this->Cell($cell_w[1], $cell_h * 2, utf8_decode('Emocional'), 1);
        $this->Cell($cell_w[2], $cell_h * 2, utf8_decode('Comunidad'), 1);
        $this->MultiCell($cell_w[3], $cell_h, utf8_decode('Relación con la rama y el grupo scouts'), 1, 1);

        // FE
        $this->Cell($cell_w[0], $cell_h * 2, utf8_decode('Fe'), 1, 0, 'L', true);
        $this->Cell($cell_w[1], $cell_h, utf8_decode('Espiritual'), 1, 0, 'L', true);
        $this->Cell($cell_w[2], $cell_h, utf8_decode('Oración'), 1, 0, 'L', true);
        $this->Cell($cell_w[3], $cell_h, utf8_decode('Oración y reflexión'), 1, 1, 'L', true);

        $this->Cell($cell_w[0], $cell_h, '', 0, 0);
        $this->Cell($cell_w[1], $cell_h, utf8_decode('Físico'), 1);
        $this->Cell($cell_w[2], $cell_h, utf8_decode('Corporeidad'), 1);
        $this->Cell($cell_w[3], $cell_h, utf8_decode('Protección de la naturaleza'), 1, 1);

        // Espaciado
        $this->Ln(5);
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

        // Tamaño de las celdas
        $cell_h = 7;
        $cell_w = [40, 60, 35, 35];

        $dateFormat = date('d-m-Y', strtotime($progData['prog_date']));

        // Contar número de responsables
        $rawText = is_resource($progData['responsibles']) ? stream_get_contents($progData['responsibles']) : $progData['responsibles'];
        $nombres = array_filter(explode("\n", str_replace(["\r\n", "\r"], "\n", $rawText)), fn($n): bool => trim($n) !== '');
        $count = count($nombres);
        $nResp = max(5, $count); // mínimo de 5 filas

        // Encabezado de tabla
        $this->cell($cell_w[0], $cell_h, utf8_decode('Grupo Scout'), 1, 0, 'C', true);
        $this->cell($cell_w[1], $cell_h, utf8_decode($progData['grp_name']), 1, 0, 'C');
        $this->cell($cell_w[2], $cell_h, utf8_decode('Ronda Solar'), 1, 0, 'C', true);
        $this->cell($cell_w[3], $cell_h, utf8_decode(getRondaSolar($progData['prog_date'])), 1, 1, 'C');

        $this->cell($cell_w[0], $cell_h, utf8_decode('Lugar'), 1, 0, 'C', true);
        $this->cell($cell_w[1], $cell_h, utf8_decode($progData['prog_place']), 1, 0, 'C');
        $this->cell($cell_w[2], $cell_h, utf8_decode('Fecha'), 1, 0, 'C', true);
        $this->cell($cell_w[3], $cell_h, $dateFormat, 1, 1, 'C');

        $this->cell($cell_w[0], $cell_h, utf8_decode('Coordinador'), 1, 0, 'C', true);
        $this->cell($cell_w[1], $cell_h, utf8_decode($progData['prog_coord']), 1, 0, 'C');
        $this->cell($cell_w[2], $cell_h, utf8_decode('Nº educandos'), 1, 0, 'C', true);
        $this->cell($cell_w[3], $cell_h, utf8_decode($progData['prog_child_N']), 1, 1, 'C');

        $this->cell($cell_w[0] + $cell_w[1] + $cell_w[2], $cell_h, utf8_decode('Responsables asistentes'), 1, 0, 'C', true);
        $this->cell($cell_w[3], $cell_h, utf8_decode('Logo del grupo'), 1, 1, 'C', true);

        $x = $this->GetX();
        $y = $this->GetY();

        // Celdas responsables e imagen
        $this->cell($cell_w[0], $cell_h * $nResp, utf8_decode($progData['rama_name']), 1, 0, 'C', true);
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
        $this->MultiCell($cell_w[1] + $cell_w[2], $cell_h, utf8_decode($contenidoResponsables), 1, 'L', false);

        // Volvemos a la posición para dibujar la celda de la imagen
        $this->SetXY($x + $cell_w[0] + $cell_w[1] + $cell_w[2], $y);
        $this->cell($cell_w[3], $cell_h * $nResp, '', 1, 1, 'C');

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
        // Espaciado
        $this->Ln(5);

    }

    function TableObjetives($progData)
    {
        // Selecionamos el color de la rama
        $colorRama = getRamaColor($progData['rama_id']);
        $this->SetFillColor($colorRama[0], $colorRama[1], $colorRama[2]);

        // Fuente y formato
        $this->SetFont('Arial', '', 10);
        $this->SetDrawColor(190, 190, 190);
        $this->SetLineWidth(.01);

        // Tamaño de las celdas
        $cell_h = 7;
        $cell_w = [40, 60, 35, 35];

        $this->cell($cell_w[0], $cell_h, utf8_decode('LEMA'), 1, 0, 'C', true);
        $this->cell($cell_w[1] + $cell_w[2] + $cell_w[3], $cell_h, utf8_decode(' En la manada, todos somos parte del mismo camino'), 1, 1, 'L', false);

        $this->cell($cell_w[0] + $cell_w[1] + $cell_w[2] + $cell_w[3], $cell_h, utf8_decode('OBJETIVOS GENERALES Y CONTENIDOS PEDAGÓGICOS'), 1, 1, 'C', true);

        $this->MultiCell(
            $cell_w[0] + $cell_w[1] + $cell_w[2] + $cell_w[3],
            $cell_h,
            utf8_decode(' RESPONSABILIDAD:
- Fomentar la autonomía y el desarrollo personal a través de los territorios.
- Desarrollar la confianza de los nuevos educandos conforme al desarrollo positivo de sus habilidades sociales

PAÍS:
- Reforzar la participación social con el compromiso de la manada conforme al entorno que le rodea.
Trabajar en comunidad para así mejorar la cohesión y unión de la manada.

FE:
- Promover el aprendizaje de la oración del lobato y la reflexión sobre la misma.'),
            1,
            'L',
            false
        );
    }

    function TableMats($progData, $matsData)
    {
        // Selecionamos el color de la rama
        $colorRama = getRamaColor($progData['rama_id']);
        $this->SetFillColor($colorRama[0], $colorRama[1], $colorRama[2]);

        // Fuente y formato
        $this->SetFont('Arial', '', 10);
        $this->SetDrawColor(190, 190, 190);
        $this->SetLineWidth(.01);

        // Tamaño de las celdas
        $cell_h = 7;
        $cell_w = 170; //170

        // Realizamos un salto de página
        $this->AddPage();

        $this->cell($cell_w, $cell_h, utf8_decode('Materiales para la realización de actividades'), 1, 1, 'C', true);

        // Listar los materiales
        $materiales = "";
        foreach ($matsData as $mat) {
            // Añadir cada material a la cadena
            $materiales .= utf8_decode($mat['mat_name']) . "\n";
        }
        $this->MultiCell($cell_w, $cell_h, $materiales, 1, 'L', false);

    }
}
?>

<?php
$pdf = new PDF('P', 'mm', 'A4');
$pdf->SetMargins(20, 15, 20);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times', '', 12);

// Llamar a la función para agregar la tabla al PDF
$pdf->TablePedag($progData);
$pdf->TableGroup($progData);
$pdf->TableObjetives($progData);
$pdf->TableMats($progData, $matsData);

$pdfName = $progData['prog_date'] . '-' . $progData['rama_name'] . '-' . $progData['grp_name'] . '.pdf';
$pdf->Output('I', $pdfName);

?>