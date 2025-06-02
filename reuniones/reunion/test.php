<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
/* -- Requires ---------------------------------------------------------- */
require('../../.res/fpdf/fpdf.php');
require "../../.res/funct/funct.php";
?>

<?php
$pdf = new PDF('P', 'mm', 'A4');
$pdf->SetMargins(20, 15, 20);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont( 'Times', '', 12);

?>