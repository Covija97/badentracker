<?php
/* -- Requires ---------------------------------------------------------- */
require('../../.res/fpdf/fpdf.php');
require "../../.res/funct/funct.php";
?>

<?php
/* Consulta los datos de la programación y las actividades asociadas */
$editMode = false;
$progData = [];
$progactData = [];
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $editMode = true;
    $edit_id = intval($_GET['id']);
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
        $this->Ln(h: 7);
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
        $this->Ln(h: 5);
    }

    // Tabla información del grupo
    function TableGroup($progData): void
    {
        // Selecionamos el color de la rama
        $colorRama = getRamaColor($progData['rama_id']);
        $this->SetFillColor($colorRama[0], $colorRama[1], $colorRama[2]);

        // Fuente y formato
        $this->SetFont(family: 'Arial', style: '', size: 10);
        $this->SetDrawColor(r: 190, g: 190, b: 190);
        $this->SetLineWidth(width: .01);

        // Tamaño de las celdas
        $cell_h = 7;
        $cell_w = [40, 60, 35, 35];

        $dateFormat = date(format: 'd-m-Y', timestamp: strtotime(datetime: $progData['prog_date']));

        // Contar número de responsables
        $rawText = is_resource(value: $progData['responsibles']) ? stream_get_contents(stream: $progData['responsibles']) : $progData['responsibles'];
        $nombres = array_filter(array: explode(separator: "\n", string: str_replace(search: ["\r\n", "\r"], replace: "\n", subject: $rawText)), callback: fn($n): bool => trim(string: $n) !== '');
        $count = count(value: $nombres);
        $nResp = max(5, $count); // mínimo de 5 filas

        // Encabezado de tabla
        $this->cell(w: $cell_w[0], h: $cell_h, txt: utf8_decode(string: 'Grupo Scout'), border: 1, ln: 0, align: 'C', fill: true);
        $this->cell(w: $cell_w[1], h: $cell_h, txt: $progData['grp_name'], border: 1, ln: 0, align: 'C');
        $this->cell(w: $cell_w[2], h: $cell_h, txt: utf8_decode(string: 'Ronda Solar'), border: 1, ln: 0, align: 'C', fill: true);
        $this->cell(w: $cell_w[3], h: $cell_h, txt: getRondaSolar(progDate: $progData['prog_date']), border: 1, ln: 1, align: 'C');

        $this->cell(w: $cell_w[0], h: $cell_h, txt: utf8_decode(string: 'Lugar'), border: 1, ln: 0, align: 'C', fill: true);
        $this->cell(w: $cell_w[1], h: $cell_h, txt: $progData['prog_place'], border: 1, ln: 0, align: 'C');
        $this->cell(w: $cell_w[2], h: $cell_h, txt: utf8_decode(string: 'Fecha'), border: 1, ln: 0, align: 'C', fill: true);
        $this->cell(w: $cell_w[3], h: $cell_h, txt: $dateFormat, border: 1, ln: 1, align: 'C');

        $this->cell(w: $cell_w[0], h: $cell_h, txt: utf8_decode(string: 'Coordinador'), border: 1, ln: 0, align: 'C', fill: true);
        $this->cell(w: $cell_w[1], h: $cell_h, txt: $progData['prog_coord'], border: 1, ln: 0, align: 'C');
        $this->cell(w: $cell_w[2], h: $cell_h, txt: utf8_decode(string: 'Nº educandos'), border: 1, ln: 0, align: 'C', fill: true);
        $this->cell(w: $cell_w[3], h: $cell_h, txt: $progData['prog_child_N'], border: 1, ln: 1, align: 'C');

        $this->cell(w: $cell_w[0] + $cell_w[1] + $cell_w[2], h: $cell_h, txt: utf8_decode(string: 'Responsables asistentes'), border: 1, ln: 0, align: 'C', fill: true);
        $this->cell(w: $cell_w[3], h: $cell_h, txt: utf8_decode(string: 'Logo del grupo'), border: 1, ln: 1, align: 'C', fill: true);

        $x = $this->GetX();
        $y = $this->GetY();

        // Celdas responsables e imagen
        $this->cell($cell_w[0], $cell_h * $nResp, $progData['rama_name'], 1, 0, 'C', true);
        // Rellenar hasta mínimo 5 líneas
        $responsablesArray = explode("\n", str_replace(["\r\n", "\r"], "\n", $rawText));
        $responsablesLimpios = array_filter(array_map('trim', $responsablesArray), fn($r) => $r !== '');
        $lineas = $responsablesLimpios;

        // Añadir líneas vacías si hay menos de 5
        while (count(value: $lineas) < 5) {
            $lineas[] = ' ';
        }

        // Recombinar para MultiCell
        $contenidoResponsables = implode("\n", $lineas);

        $this->MultiCell($cell_w[1] + $cell_w[2], $cell_h, $contenidoResponsables, 1, 'L', false);

        // Volvemos a la posición para dibujar la celda de la imagen
        $this->SetXY($x + $cell_w[0] + $cell_w[1] + $cell_w[2], $y);
        $this->cell(w: $cell_w[3], h: $cell_h * $nResp, txt: '', border: 1, ln: 1, align: 'C');

        // Insertar imagen centrada
        $imagePath = '../../.res/img/logos-grupos/' . $progData['grp_id'] . '.png';

        if (file_exists(filename: $imagePath)) {
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
            $this->Image(file: $imagePath, x: $imageX, y: $imageY, w: $finalW, h: $finalH);
        }
        // Espaciado
        $this->Ln(h: 5);

    }

    // Tabla de objetivos
    function TableObjetives($progData)
    {
        // Selecionamos el color de la rama
        $colorRama = getRamaColor(ramaId: $progData['rama_id']);
        $this->SetFillColor($colorRama[0], $colorRama[1], $colorRama[2]);

        // Fuente y formato
        $this->SetFont(family: 'Arial', style: '', size: 10);
        $this->SetDrawColor(r: 190, g: 190, b: 190);
        $this->SetLineWidth(width: .01);

        // Tamaño de las celdas
        $cell_h = 7;
        $cell_w = [40, 60, 35, 35];

        $this->cell(w: $cell_w[0], h: $cell_h, txt: utf8_decode(string: 'LEMA'), border: 1, ln: 0, align: 'C', fill: true);
        $this->cell(w: $cell_w[1] + $cell_w[2] + $cell_w[3], h: $cell_h, txt: utf8_decode(string: ' En la manada, todos somos parte del mismo camino'), border: 1, ln: 1, align: 'L', fill: false);

        $this->cell(w: $cell_w[0] + $cell_w[1] + $cell_w[2] + $cell_w[3], h: $cell_h, txt: utf8_decode(string: 'OBJETIVOS GENERALES Y CONTENIDOS PEDAGÓGICOS'), border: 1, ln: 1, align: 'C', fill: true);

        $this->MultiCell(
            w: $cell_w[0] + $cell_w[1] + $cell_w[2] + $cell_w[3],
            h: $cell_h,
            txt: utf8_decode(string:
                ' RESPONSABILIDAD:
    - Fomentar la autonomía y el desarrollo personal a través de los territorios.
    - Desarrollar la confianza de los nuevos educandos conforme al desarrollo positivo de sus habilidades sociales

 PAÍS:
    - Reforzar la participación social con el compromiso de la manada conforme al entorno que le rodea.
    Trabajar en comunidad para así mejorar la cohesión y unión de la manada.

 FE:
    - Promover el aprendizaje de la oración del lobato y la reflexión sobre la misma.'),
            border: 1,
            align: 'L',
            fill: false
        );
    }

    // Tabla de materiales
    function TableMats($progData, $matsData)
    {
        // Selecionamos el color de la rama
        $colorRama = getRamaColor(ramaId: $progData['rama_id']);
        $this->SetFillColor($colorRama[0], $colorRama[1], $colorRama[2]);

        // Fuente y formato
        $this->SetFont(family: 'Arial', style: '', size: 10);
        $this->SetDrawColor(r: 190, g: 190, b: 190);
        $this->SetLineWidth(width: .01);

        // Tamaño de las celdas
        $cell_h = 7;
        $cell_w = 170; //170

        // Realizamos un salto de página
        $this->AddPage();

        $this->cell(w: $cell_w, h: $cell_h, txt: utf8_decode(string: 'Materiales para la realización de actividades'), border: 1, ln: 1, align: 'C', fill: true);
        
        // Listar los materiales
        $materiales = "";
        foreach ($matsData as $mat) {
            // Añadir cada material a la cadena
            $materiales .= utf8_decode(string: $mat['mat_name']) . "\n";
        }
        $this->MultiCell(w: $cell_w, h: $cell_h, txt: $materiales, border: 1, align: 'L', fill: false);

    }

    //Tabla de actividades general
    function TableActs($progData, $progactData)
    {
        // Selecionamos el color de la rama
        $colorRama = getRamaColor(ramaId: $progData['rama_id']);
        $this->SetFillColor($colorRama[0], $colorRama[1], $colorRama[2]);

        // Fuente y formato
        $this->SetFont(family: 'Arial', style: '', size: 10);
        $this->SetDrawColor(r: 190, g: 190, b: 190);
        $this->SetLineWidth(width: .01);

        // Tamaño de las celdas
        $cell_h = 7;
        $cell_w = [20, 120, 30]; //170

        // Realizamos un salto de página
        $this->AddPage();

        // Encabezado
        $this->cell(w: $cell_w[0], h: $cell_h, txt: utf8_decode(string: 'Hora'), border: 1, ln: 0, align: 'C', fill: true);
        $this->cell(w: $cell_w[1], h: $cell_h, txt: utf8_decode(string: 'Actividad'), border: 1, ln: 0, align: 'C', fill: true);
        $this->cell(w: $cell_w[2], h: $cell_h, txt: utf8_decode(string: 'Encargado'), border: 1, ln: 1, align: 'C', fill: true);

        // Contenido
        $horaActual = DateTime::createFromFormat(format: 'H:i:s', datetime: $progData['prog_time']); // ej. '09:00:00'

        $fill = false; // 🔧 Alternancia de color

        foreach ($progactData as $act) {
            // Hora formateada
            $horaFormateada = $horaActual->format(format: 'H:i');
            $duraciónFormateada = (DateTime::createFromFormat(format: 'H:i:s', datetime: $act['act_durat']))->format(format: 'H:i');

            // Convertir duración hh:mm:ss a DateInterval y sumar
            $interval = new DateInterval(
                'PT' .
                intval(substr($act['act_durat'], 0, 2)) . 'H' .
                intval(substr($act['act_durat'], 3, 2)) . 'M' .
                intval(substr($act['act_durat'], 6, 2)) . 'S'
            );

            // Celdas con alternancia de fondo
            $this->cell(w: $cell_w[0], h: $cell_h * 2, txt: $horaFormateada, border: 1, ln: 0, align: 'C', fill: $fill);
            $this->cell(w: $cell_w[1], h: $cell_h * 2, txt: utf8_decode($act['act_name']), border: 1, ln: 0, align: 'L', fill: $fill);
            $this->cell(w: $cell_w[2], h: $cell_h * 2, txt: $duraciónFormateada, border: 1, ln: 1, align: 'C', fill: $fill);

            $horaActual->add(interval: $interval);

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
            $this->cell(w: $cell_w[0], h: $cell_h, txt: utf8_decode(string: 'Nombre'), border: 1, ln: 0, align: 'C', fill: true);
            $this->cell(w: $cell_w[1], h: $cell_h, txt: utf8_decode(string: $act['act_name']), border: 1, ln: 1, align: 'L', fill: false);

            $this->MultiCell(w: $cell_w[0] + $cell_w[1], h: $cell_h, txt: utf8_decode(string: 'Desarrollo'), border: 1, align: 'C', fill: true);
            $this->MultiCell(w: $cell_w[0] + $cell_w[1], h: $cell_h, txt: utf8_decode(string: $act['act_desc']), border: 1, align: 'L', fill: false);

            $this->Ln(h: 5);

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

        $horaActual = DateTime::createFromFormat(format: 'H:i:s', datetime: $progData['prog_time']); // ej. '09:00:00'

        foreach ($progactData as $act) {
            // Hora formateada
            $horaFormateada = $horaActual->format(format: 'H:i');

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
            $this->cell(w: $cell_w[0], h: $cell_h, txt: utf8_decode(string: 'Nombre'), border: 1, ln: 0, align: 'C', fill: true);
            $this->cell(w: $cell_w[1], h: $cell_h, txt: utf8_decode(string: $act['act_name']), border: 1, ln: 0, align: 'L', fill: false);
            $this->cell(w: $cell_w[2], h: $cell_h, txt: utf8_decode(string: $act['act_respon']), border: 1, ln: 0, align: 'C', fill: false);
            $this->cell(w: $cell_w[3], h: $cell_h, txt: $horaFormateada, border: 1, ln: 1, align: 'C', fill: false);

            $this->cell(w: $cell_w[0] + $cell_w[1] + $cell_w[2] + $cell_w[3], h: $cell_h, txt: utf8_decode(string: 'Desarrollo'), border: 1, ln: 1, align: 'C', fill: true);
            $this->MultiCell(
                w: $cell_w[0] + $cell_w[1] + $cell_w[2] + $cell_w[3],
                h: $cell_h,
                txt: utf8_decode(string:
                    $act['act_desc'] . "\n" .
                    $act['act_comment'] . "\n" .
                    ' - Materiales' . "\n" .
                    $materiales),
                border: 1,
                align: 'L',
                fill: false
            );

            $this->Ln(h: 5);

            $horaActual->add(interval: $interval);
        }
        $stmt->close();

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
$pdf->TableObjetives(progData: $progData);

$pdf->TableMats(progData: $progData, matsData: $matsData);

$pdf->TableActs(progData: $progData, progactData: $progactData);

if (isset($_GET['format']) && $_GET['format'] == 1) {
    $pdf->tableActFormat1(progData: $progData, progactData: $progactData);
} else {
    $pdf->tableActFormat0(progData: $progData, progactData: $progactData);
}

// Formación del nombre del pdf
$pdfName = $progData['prog_date'] . '-' . $progData['rama_name'] . '-' . $progData['grp_name'] . '.pdf';
$pdf->Output(name: $pdfName);

?>