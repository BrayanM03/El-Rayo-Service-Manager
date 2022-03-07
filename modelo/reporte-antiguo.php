<?php
require('../src/vendor/fpdf/fpdf.php');

class PDF extends FPDF
{
// Cabecera de página
function Header()
{
    // Logo
    $this->Image('../src/img/logo.jpg',20,10,25);
    // Arial bold 15
    
    $this->SetFont('Arial','B',20);
    $this->SetDrawColor(135, 134, 134);
    $this->SetTextColor(36, 35, 28);
    $this->SetFillColor(111, 110, 103);
    
    // Movernos a la derecha
    $this->Cell(80);
    // Título
    
    $this->Cell(50,10,'Reporte de Venta',0,0,'C');
    $this->Ln(10);

    $this->SetFont('Arial','B',12);
    $this->Cell(210,10,utf8_decode("Llantas y Servicios 'EL Rayo'"),0,0,'C', false);
    $this->Ln(6);

    $this->SetFont('Arial','',9);
    $location = "Avenida Pedro Cardenas KM5 No.207";
    $this->Cell(210,10,utf8_decode($location),0,0,'C', false);
    $this->Ln(4);


    $this->Cell(210,10,utf8_decode("Col. Francisco Castellanos"),0,0,'C', false);
    $this->Ln(4);
    $this->Cell(210,10,utf8_decode("RFC:SARK9104063L6"),0,0,'C', false);
    $this->Ln(4);
    $this->Cell(210,10,utf8_decode("Telefono: 8688244404"),0,0,'C', false);
    $this->Ln(20);

    $this->Rect(133, 58, 20, 7, 'F');
    $this->Rect(133, 65, 20, 7, 'F');

    
    $this->SetFont('Times','B',12);
    $this->Cell(24,10,utf8_decode("Cliente:"),0,0, false);
    $this->SetFont('Times','',12);
    $this->Cell(99,10,utf8_decode("Publico en General"),0,0, false);

    $this->SetTextColor(255, 255, 255);
    $this->Cell(20,7,utf8_decode("Folio:"),1,0, false);
    $this->SetFont('Times','',12);
    $this->SetTextColor(36, 35, 28);
    $this->Cell(50,7,utf8_decode("A1"),1,0,'C', false);

    $this->Ln(7);

    $this->SetFont('Times','B',12);
    $this->Cell(24,7,utf8_decode("Vendedor:"),0,0, false);
    $this->SetFont('Times','',12);
    $this->Cell(99,7,utf8_decode("Candelaria Diaz"),0,0, false);

    $this->SetTextColor(255, 255, 255);
    $this->Cell(20,7,utf8_decode("Fecha:"),1,0, false);
    $this->SetFont('Times','',12);
    $this->SetTextColor(36, 35, 28);
    $this->Cell(50,7,utf8_decode("23-Marzo-2021"),1,0,'C', false);

    // Salto de línea
    $this->Ln(15);
}

// Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','',8);
    // Número de página
    $this->Image('../src/img/logo-reporte.png',88,272,30);
    $this->Ln(3);
    $this->Cell(0,10,'El Rayo Service Manager 2021',0,0,'C');
}
}

// Creación del objeto de la clase heredada

function cuerpoTabla(){
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Times','B',12);
    
    $pdf->SetDrawColor(135, 134, 134);
    $pdf->SetTextColor(36, 35, 28);
    $pdf->SetFillColor(241, 210, 34);
    
    
    $pdf->Rect(10, 80, 189, 8, 'F');
    $pdf->Cell(19,8,utf8_decode("Cantidad"),1,0);  
    $pdf->Cell(80,8,utf8_decode("Concepto"),1,0, 'C');
    $pdf->Cell(30,8,utf8_decode("Marca"),1,0, 'C');
    $pdf->Cell(30,8,utf8_decode("Precio Uni"),1,0, 'C');
    $pdf->Cell(30,8,utf8_decode("Importe"),1,0, 'C');
    $pdf->Ln(8);
    
    
    
    
    
    $pdf->SetFont('Times','',12);
    $pdf->Cell(19,10,utf8_decode("1"),1,0, false);
    $pdf->Cell(80,10,utf8_decode("Llanta 175/65R16 T8 Wintrun"),1,0, 'C', false);
    $pdf->Cell(30,10,utf8_decode("Goodyear"),1,0, 'C', false);
    $pdf->Cell(30,10,utf8_decode("$758.36"),1,0, 'C', false);
    $pdf->Cell(30,10,utf8_decode("$758.36"),1,0, 'C', false);
    $pdf->Ln(10);
    
    $pdf->Cell(19,10,utf8_decode("1"),1,0, false);
    $pdf->Cell(80,10,utf8_decode("Llanta 175/65R16 T8 Wintrun"),1,0, 'C', false);
    $pdf->Cell(30,10,utf8_decode("Goodyear"),1,0, 'C', false);
    $pdf->Cell(30,10,utf8_decode("$758.36"),1,0, 'C', false);
    $pdf->Cell(30,10,utf8_decode("$758.36"),1,0, 'C', false);
    $pdf->Ln(10);
    
    $pdf->Cell(19,10,utf8_decode("1"),1,0, false);
    $pdf->Cell(80,10,utf8_decode("Llanta 175/65R16 T8 Wintrun"),1,0, 'C', false);
    $pdf->Cell(30,10,utf8_decode("Goodyear"),1,0, 'C', false);
    $pdf->Cell(30,10,utf8_decode("$758.36"),1,0, 'C', false);
    $pdf->Cell(30,10,utf8_decode("$758.36"),1,0, 'C', false);
    $pdf->Ln(10);

    //Subtotal
    $pdf->Cell(129,6,'',0,0);
    $pdf->Cell(30,6,'Subtotal',1,0, 'C');
    $pdf->Cell(30,6,'$15102,00',1,0, 'C');
    $pdf->Ln(6);

    $pdf->Cell(129,6,'',0,0);
    $pdf->Cell(30,6,'IVA',1,0, 'C');
    $pdf->Cell(30,6,'$1510,00',1,0, 'C');
    $pdf->Ln(6);

    $pdf->Cell(129,6,'',0,0);
    $pdf->Cell(30,8,'Total',1,0, 'C');
    $pdf->Cell(30,8,'$15102,00',1,0, 'C');
    $pdf->Ln(25);

    //Importe y observaciones
    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(189,6,'Importe total con letra: ',0,0);
    $pdf->Ln(8);
    $pdf->SetFont('Courier','',12);
    $pdf->Cell(140,8,'Setesientos pesos',1,0);
    $pdf->Ln(15);

    $pdf->SetFont('Arial','B',12);
    $pdf->Cell(189,6,'Oservaciones: ',0,0);
    $pdf->Ln(8);
    $pdf->SetFont('Courier','',12);
    $pdf->Cell(140,30,'Que onda soy Brayan',1,0);
    $pdf->Ln(37);

    
    $pdf->SetFont('Arial','',10);
    $text = 'GARANTÍA DE UN AÑO CONTRA DEFECTO DE FABRICA A PARTIR DE ESTA FECHA';
    $text2 = ' FAVOR DE PRESENTAR ESTE COMPROBANTE DE VENTA PARA HACER VALIDO LA GARANTÍA';
    $pdf->Cell(189,6,utf8_decode($text),0,0,'C');
    $pdf->Ln(6);
    $pdf->Cell(189,6,utf8_decode($text2),0,0,'C');
    


    $pdf->Output();
}

cuerpoTabla();




?>