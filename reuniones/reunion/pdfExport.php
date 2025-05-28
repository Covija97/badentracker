<?php

require ('../../.res/fpdf/fpdf.php');

class PDF extends FPDF
{
// Cabecera de página
function Header()
{
    // Logo
    $this->Image(file: '../../.res/fpdf/img/header.png',w: 170);
    // Arial bold 15
    $this->SetFont(family: 'Arial',style: 'B',size: 15);
    $this->Ln(h: 5);
}

// Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','',10);
    // Número de página
    $this->Cell(0,10,utf8_decode('Página '.$this->PageNo().'/{nb}'),border: 0,ln: 0,align: 'R');
}

// Tabla de contenido pedagógico

}

// Creación del objeto de la clase heredada
$pdf = new PDF(orientation: 'P', unit: 'mm', size: 'A4');
$pdf->SetMargins(left: 20, top: 15, right: 20);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
for($i=1;$i<=40;$i++)
    $pdf->Cell(0,10,utf8_decode('Imprimiendo línea número '.$i),0,1);
$pdf->Output();
?>