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
/* -- PDF --------------------------------------------------------------- */

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
        $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    // Tabla de contenido pedagógico
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
        $this->Cell($cell_w[0], $cell_h, mb_convert_encoding('Ámbito', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', false);
        $this->Cell($cell_w[1], $cell_h, mb_convert_encoding('Área', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', false);
        $this->Cell($cell_w[2], $cell_h, mb_convert_encoding('Línea pedagógica', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', false);
        $this->Cell($cell_w[3], $cell_h, mb_convert_encoding('Contenidos', 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', false);

        // RESPONSABILIDAD
        $this->Cell($cell_w[0], $cell_h * 2, mb_convert_encoding('Responsabilidad', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
        $this->Cell($cell_w[1], $cell_h, mb_convert_encoding('Personalidad', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
        $this->Cell($cell_w[2], $cell_h, mb_convert_encoding('Autonomía', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
        $this->Cell($cell_w[3], $cell_h, mb_convert_encoding('Progreso personal', 'ISO-8859-1', 'UTF-8'), 1, 1, 'L', true);

        $this->Cell($cell_w[0], $cell_h, '', 0, 0);
        $this->Cell($cell_w[1], $cell_h, mb_convert_encoding('Social', 'ISO-8859-1', 'UTF-8'), 1);
        $this->Cell($cell_w[2], $cell_h, mb_convert_encoding('Habilidades sociales', 'ISO-8859-1', 'UTF-8'), 1);
        $this->Cell($cell_w[3], $cell_h, mb_convert_encoding('Confianza', 'ISO-8859-1', 'UTF-8'), 1, 1);

        // PAÍS
        $this->Cell($cell_w[0], $cell_h * 3, mb_convert_encoding('País', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
        $this->Cell($cell_w[1], $cell_h, mb_convert_encoding('Personalidad', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
        $this->Cell($cell_w[2], $cell_h, mb_convert_encoding('Compromiso', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
        $this->Cell($cell_w[3], $cell_h, mb_convert_encoding('Participación social', 'ISO-8859-1', 'UTF-8'), 1, 1, 'L', true);

        $this->Cell($cell_w[0], $cell_h * 2, '', 0, 0);
        $this->Cell($cell_w[1], $cell_h * 2, mb_convert_encoding('Emocional', 'ISO-8859-1', 'UTF-8'), 1);
        $this->Cell($cell_w[2], $cell_h * 2, mb_convert_encoding('Comunidad', 'ISO-8859-1', 'UTF-8'), 1);
        $this->MultiCell($cell_w[3], $cell_h, mb_convert_encoding('Relación con la rama y el grupo scouts', 'ISO-8859-1', 'UTF-8'), 1, 1);

        // FE
        $this->Cell($cell_w[0], $cell_h * 2, mb_convert_encoding('Fe', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
        $this->Cell($cell_w[1], $cell_h, mb_convert_encoding('Espiritual', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
        $this->Cell($cell_w[2], $cell_h, mb_convert_encoding('Oración', 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', true);
        $this->Cell($cell_w[3], $cell_h, mb_convert_encoding('Oración y reflexión', 'ISO-8859-1', 'UTF-8'), 1, 1, 'L', true);

        $this->Cell($cell_w[0], $cell_h, '', 0, 0);
        $this->Cell($cell_w[1], $cell_h, mb_convert_encoding('Físico', 'ISO-8859-1', 'UTF-8'), 1);
        $this->Cell($cell_w[2], $cell_h, mb_convert_encoding('Corporeidad', 'ISO-8859-1', 'UTF-8'), 1);
        $this->Cell($cell_w[3], $cell_h, mb_convert_encoding('Protección de la naturaleza', 'ISO-8859-1', 'UTF-8'), 1, 1);

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
        $this->cell($cell_w[0], $cell_h, mb_convert_encoding('Grupo Scout', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
        $this->cell($cell_w[1], $cell_h, mb_convert_encoding($progData['grp_name'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $this->cell($cell_w[2], $cell_h, mb_convert_encoding('Ronda Solar', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
        $this->cell($cell_w[3], $cell_h, mb_convert_encoding(getRondaSolar($progData['prog_date']), 'ISO-8859-1', 'UTF-8'), 1, 1, 'C');

        $this->cell($cell_w[0], $cell_h, 'Lugar', 1, 0, 'C', true);
        $this->cell($cell_w[1], $cell_h, mb_convert_encoding($progData['prog_place'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $this->cell($cell_w[2], $cell_h, 'Fecha', 1, 0, 'C', true);
        $this->cell($cell_w[3], $cell_h, $dateFormat, 1, 1, 'C');

        $this->cell($cell_w[0], $cell_h, 'Coordinador', 1, 0, 'C', true);
        $this->cell($cell_w[1], $cell_h, mb_convert_encoding($progData['prog_coord'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
        $this->cell($cell_w[2], $cell_h, mb_convert_encoding('Nº educandos', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
        $this->cell($cell_w[3], $cell_h, mb_convert_encoding($progData['prog_child_N'], 'ISO-8859-1', 'UTF-8'), 1, 1, 'C');

        $this->cell($cell_w[0] + $cell_w[1] + $cell_w[2], $cell_h, 'Responsables asistentes', 1, 0, 'C', true);
        $this->cell($cell_w[3], $cell_h, 'Logo del grupo', 1, 1, 'C', true);

        $x = $this->GetX();
        $y = $this->GetY();

        // Celdas responsables e imagen
        $this->cell($cell_w[0], $cell_h * $nResp, mb_convert_encoding($progData['rama_name'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
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
        $this->MultiCell($cell_w[1] + $cell_w[2], $cell_h, mb_convert_encoding($contenidoResponsables, 'ISO-8859-1', 'UTF-8'), 1, 'L', false);

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

    // Tabla de objetivos
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

        $this->cell($cell_w[0], $cell_h, mb_convert_encoding('LEMA', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
        $this->cell($cell_w[1] + $cell_w[2] + $cell_w[3], $cell_h, mb_convert_encoding(' En la manada, todos somos parte del mismo camino', 'ISO-8859-1', 'UTF-8'), 1, 1, 'L', false);

        $this->cell($cell_w[0] + $cell_w[1] + $cell_w[2] + $cell_w[3], $cell_h, mb_convert_encoding('OBJETIVOS GENERALES Y CONTENIDOS PEDAGÓGICOS', 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', true);

        $this->MultiCell(
            $cell_w[0] + $cell_w[1] + $cell_w[2] + $cell_w[3],
            $cell_h,
            mb_convert_encoding(' RESPONSABILIDAD:
- Fomentar la autonomía y el desarrollo personal a través de los territorios.
- Desarrollar la confianza de los nuevos educandos conforme al desarrollo positivo de sus habilidades sociales

PAÍS:
- Reforzar la participación social con el compromiso de la manada conforme al entorno que le rodea.
Trabajar en comunidad para así mejorar la cohesión y unión de la manada.

FE:
- Promover el aprendizaje de la oración del lobato y la reflexión sobre la misma.', 'ISO-8859-1', 'UTF-8'),
            1,
            'L',
            false
        );
    }

    // Tabla de materiales
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

        $this->cell($cell_w, $cell_h, mb_convert_encoding('Materiales para la realización de actividades', 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', true);

        // Listar los materiales
        $materiales = "";
        foreach ($matsData as $mat) {
            // Añadir cada material a la cadena
            $materiales .= mb_convert_encoding($mat['mat_name'], 'ISO-8859-1', 'UTF-8') . "\n";
        }
        $this->MultiCell($cell_w, $cell_h, $materiales, 1, 'L', false);

    }

    //Tabla de actividades general
    function TableActs($progData, $progactData)
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
        $cell_w = [20, 120, 30]; //170

        // Realizamos un salto de página
        $this->AddPage();

        // Encabezado
        $this->cell($cell_w[0], $cell_h, mb_convert_encoding('Hora', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
        $this->cell($cell_w[1], $cell_h, mb_convert_encoding('Actividad', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
        $this->cell($cell_w[2], $cell_h, mb_convert_encoding('Encargado', 'ISO-8859-1', 'UTF-8'), 1, 1, 'C', true);

        // Contenido
        $horaActual = DateTime::createFromFormat('H:i:s', $progData['prog_time']); // ej. '09:00:00'

        $fill = false; // 🔧 Alternancia de color

        foreach ($progactData as $act) {
            // Hora formateada
            $horaFormateada = $horaActual->format('H:i');
            $duraciónFormateada = (DateTime::createFromFormat('H:i:s', $act['act_durat']))->format('H:i');

            // Convertir duración hh:mm:ss a DateInterval y sumar
            $interval = new DateInterval(
                'PT' .
                intval(substr($act['act_durat'], 0, 2)) . 'H' .
                intval(substr($act['act_durat'], 3, 2)) . 'M' .
                intval(substr($act['act_durat'], 6, 2)) . 'S'
            );

            // Celdas con alternancia de fondo
            $this->cell($cell_w[0], $cell_h * 2, $horaFormateada, 1, 0, 'C', $fill);
            $this->cell($cell_w[1], $cell_h * 2, mb_convert_encoding($act['act_name'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', $fill);
            $this->cell($cell_w[2], $cell_h * 2, $duraciónFormateada, 1, 1, 'C', $fill);

            $horaActual->add($interval);

            $fill = !$fill;
        }

    }

    // Tabla de actividades específica (estandar)
    function tableActFormat0($progData, $progactData)
    {
        // Añadimos un salto de página y actividad por actividad 
        $this->AddPage();

        // Tamaño de las celdas
        $cell_h = 7;
        $cell_w = [30, 140]; //170

        foreach ($progactData as $act) {
            // Convertir duración hh:mm:ss a DateInterval y sumar
            $interval = new DateInterval(
                'PT' .
                intval(value: substr(string: $act['act_durat'], offset: 0, length: 2)) . 'H' .
                intval(value: substr(string: $act['act_durat'], offset: 3, length: 2)) . 'M' .
                intval(value: substr(string: $act['act_durat'], offset: 6, length: 2)) . 'S'
            );

            // Celdas con alternancia de fondo
            $this->cell($cell_w[0], $cell_h, mb_convert_encoding('Nombre', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
            $this->cell($cell_w[1], $cell_h, mb_convert_encoding($act['act_name'], 'ISO-8859-1', 'UTF-8'), 1, 1, 'L', false);

            $this->MultiCell($cell_w[0] + $cell_w[1], $cell_h, mb_convert_encoding('Desarrollo', 'ISO-8859-1', 'UTF-8'), 1, 'C', true);
            $this->MultiCell($cell_w[0] + $cell_w[1], $cell_h, mb_convert_encoding($act['act_desc'], 'ISO-8859-1', 'UTF-8'), 1, 'L', false);

            $this->Ln(5);

        }
    }

    // Tabla de actividades específica (formato badentracker)
    function tableActFormat1($progData, $progactData)
    {
        // Añadimos un salto de página y actividad por actividad 
        $this->AddPage();

        // Tamaño de las celdas
        $cell_h = 7;
        $cell_w = [30, 90, 30, 20]; //170

        $horaActual = DateTime::createFromFormat('H:i:s', $progData['prog_time']); // ej. '09:00:00'

        foreach ($progactData as $act) {
            // Hora formateada
            $horaFormateada = $horaActual->format('H:i');

            // Convertir duración hh:mm:ss a DateInterval y sumar
            $interval = new DateInterval(
                'PT' .
                intval(value: substr(string: $act['act_durat'], offset: 0, length: 2)) . 'H' .
                intval(value: substr(string: $act['act_durat'], offset: 3, length: 2)) . 'M' .
                intval(value: substr(string: $act['act_durat'], offset: 6, length: 2)) . 'S'
            );

            // Consulta actividades a la db
            $db = linkDB();

            $stmt = $db->prepare("
            SELECT GROUP_CONCAT(mat.mat_name SEPARATOR ', ') AS materiales 
            FROM act_mat
            JOIN mat ON act_mat.mat_id = mat.mat_id
            WHERE act_mat.act_id = ?");
            $stmt->bind_param('i', $act['act_id']);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $row = $resultado->fetch_assoc();
            $materiales = $row['materiales'] ?? 'Sin materiales';


            // Celdas con alternancia de fondo
            $this->cell($cell_w[0], $cell_h, mb_convert_encoding('Nombre', 'ISO-8859-1', 'UTF-8'), 1, 0, 'C', true);
            $this->cell($cell_w[1], $cell_h, mb_convert_encoding($act['act_name'], 'ISO-8859-1', 'UTF-8'), 1, 0, 'L', false);
            $this->cell($cell_w[2], $cell_h, $act['act_respon'], 1, 0, 'C', false);
            $this->cell($cell_w[3], $cell_h, $horaFormateada, 1, 1, 'C', false);

            $this->cell($cell_w[0] + $cell_w[1] + $cell_w[2] + $cell_w[3], $cell_h, 'Desarrollo', 1, 1, 'C', true);
            $this->MultiCell(
                $cell_w[0] + $cell_w[1] + $cell_w[2] + $cell_w[3],
                $cell_h,
                $act['act_desc'] . "\n" .
                $act['act_comment'] . "\n" .
                ' - Materiales' . "\n" .
                $materiales,
                1,
                'L',
                false
            );

            $this->Ln(5);

            $horaActual->add($interval);
        }
        $stmt->close();

    }



}
?>

<?php

// Creación del objeto de la clase heredada
$pdf = new PDF('P', 'mm', 'A4');
$pdf->SetMargins(20, 15, 20);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times', '', 12);
// Llamar a la función para agregar la tabla al PDF
$pdf->TablePedag($progData);
/* $pdf->TableGroup($progData);
$pdf->TableObjetives($progData);
$pdf->TableMats($progData, $matsData);
$pdf->TableActs($progData, $progactData);
 *//* 
if (isset($_GET['format']) && $_GET['format'] == 1) {
    $pdf->tableActFormat1($progData, $progactData);
} else {
    $pdf->tableActFormat0($progData, $progactData);
} */

$pdfName = mb_convert_encoding($progData['prog_date'] . '-' . $progData['rama_name'] . '-' . $progData['grp_name'] . '.pdf', 'ISO-8859-1', 'UTF-8');
$pdf->Output('I', $pdfName);

?>