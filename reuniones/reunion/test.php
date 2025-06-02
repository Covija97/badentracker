<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/* -- Requires ---------------------------------------------------------- */
require('../../.res/fpdf/fpdf.php');
require "../../.res/funct/funct.php";
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
        $this->Cell(0, 10, 'Página ' . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }
}
?>

<?php
$pdf = new PDF('P', 'mm', 'A4');
$pdf->SetMargins(20, 15, 20);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times', '', 12);

$pdfName = $progData['prog_date'] . '-' . $progData['rama_name'] . '-' . $progData['grp_name'] . '.pdf';
$pdf->Output('I', $pdfName);

?>