

<?php
session_start();
include '../conexion.php';
$con = $conectando->conexion(); 
global $con;

$folio = "RAY" . $_GET["id"];
$idVenta = $_GET["id"];
global $folio;

$ID = $con->prepare("SELECT ventas.Fecha, ventas.id_Sucursal, clientes.Nombre_Cliente, ventas.Total FROM ventas INNER JOIN clientes ON ventas.id_Cliente = clientes.id WHERE ventas.id = ?");
$ID->bind_param('i', $idVenta);
$ID->execute();
$ID->bind_result($fecha, $sucursal, $cliente, $total);
$ID->fetch();
$ID->close();

global $fecha;
global $sucursal;
global $cliente;
global $total;


$formatterES = new NumberFormatter("es-ES", NumberFormatter::SPELLOUT);;
$izquierda = intval(floor($total));
$derecha = intval(($total - floor($total)) * 100);
$formatTotal = $formatterES->format($izquierda) . " con " . $formatterES->format($derecha) . " centavos";
// ciento veintitrés coma cuarenta y cinco

global $formatTotal;
/*
$detalle = $con->prepare("SELECT detalle_venta.Cantidad,llantas.Descripcion, llantas.Marca, detalle_venta.precio_Unitario, detalle_venta.Importe FROM detalle_venta INNER JOIN llantas ON detalle_venta.id_llanta = llantas.id WHERE id_Venta = ?");
$detalle->bind_param('i', $id_venta);
$detalle->execute();
$resultado = $detalle->get_result(); 
global $resultado;*/

require('../../src/vendor/fpdf/fpdf.php');



if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}



class PDF extends FPDF
{

    
// Cabecera de página
function Header()



{
    if($_SESSION['sucursal'] == "Pedro"){
        $direccion = "Avenida Pedro Cardenas KM5 No.207";
        $colonia = "Col. Francisco Castellanos";
        
   }else if($_SESSION['sucursal'] == "Sendero"){
    $direccion = "Av. Sendero Nacional";
    $colonia = "Kilometro 50";
   }

   

  

    // Logo
    $this->Image('../../src/img/logo.jpg',20,10,25);
    // Arial bold 15
   
    
    $this->SetDrawColor(135, 134, 134);
    $this->SetTextColor(36, 35, 28);
    
    
    // Movernos a la derecha
    
    // Título

    $this->SetFont('Arial','B',12);
    $this->Cell(30,10,"'",0,0, 'C');
    $this->Cell(100,10,"Llantas y Servicios 'EL Rayo'",0,0, 'C');
    $this->SetFont('Arial','B',20);
    $this->Cell(60,10,'Reporte de Venta',0,0,'C');
    $this->Ln(5);
    $this->SetFont('Arial','',9);
    $this->Cell(160,10,utf8_decode($direccion),0,0,'C', false);
    $this->Ln(4);
    $this->Cell(160,10,utf8_decode($colonia),0,0,'C', false);
    $this->Ln(4);
    $this->Cell(160,10,utf8_decode("H. Matamoros Tam"),0,0,'C', false);
    $this->Ln(4);
    $this->Cell(160,10,utf8_decode("RFC:SARK9104063L6"),0,0,'C', false);
    $this->Ln(4);
    $this->Cell(160,10,utf8_decode("Telefono: 8688244404"),0,0,'C', false);
    $this->Ln(17);

    //$this->Rect(133, 58, 20, 7, 'F');
    //$this->Rect(133, 65, 20, 7, 'F');

    $this->SetFillColor(253, 229, 2);
    $this->SetFont('Times','B',12);
    $this->Cell(24,10,utf8_decode("Cliente:"),0,0,'L', 1);
    $this->SetFont('Times','',12);
    $this->SetFillColor(236, 236, 236);
    $this->Cell(70,10,utf8_decode($GLOBALS["cliente"]),0,0, 'L',1);
    $this->Cell(30,7,'',0,0,'', false);
    $this->SetFont('Arial','B',12);
    $this->SetTextColor(194, 34, 16);
    $this->Cell(20,7,utf8_decode("Folio:"),0,0, false);
    $this->SetFont('Times','',12);
    $this->SetTextColor(36, 35, 28);
    $this->Cell(50,7,utf8_decode($GLOBALS["folio"]),0,0,'', false);

    $this->Ln(7);

    
    $this->SetFont('Times','B',12);
    $this->SetFillColor(253, 229, 2);
    $this->Cell(24,7,utf8_decode("Vendedor:"),0,0,'L', 1);
    $this->SetFont('Times','',12);
    $this->SetFillColor(236, 236, 236);
    $vendedor = $_SESSION['nombre'] . " " . $_SESSION['apellidos'];
    $this->Cell(70,7,utf8_decode($vendedor),0,0, 'L',1);
    $this->Cell(30,7,'',0,0,'', false);

    $this->SetFont('Arial','B',12);
    $this->SetTextColor(194, 34, 16);
    $this->Cell(20,7,utf8_decode("Fecha:"),0,0, false);
    $this->SetFont('Times','',12);
    $this->SetTextColor(36, 35, 28);
    $this->Cell(50,7,utf8_decode($GLOBALS["fecha"]),0,0,'', false);

    // Salto de línea
    $this->Ln(18);
}

// Pie de página
function Footer()
{
    
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    //$this->Image('../src/img/logo-reporte.png',60,142,80);
    $this->Ln(3);
    // Arial italic 8
    $this->SetFont('Arial','',8);
    $this->SetTextColor(1, 1, 1);
    // Número de página
   
    $this->Cell(0,10,'El Rayo Service Manager 2021',0,0,'C');
}


 //Aqui justifico


 function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
{
    $k=$this->k;
    if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AcceptPageBreak())
    {
        $x=$this->x;
        $ws=$this->ws;
        if($ws>0)
        {
            $this->ws=0;
            $this->_out('0 Tw');
        }
        $this->AddPage($this->CurOrientation);
        $this->x=$x;
        if($ws>0)
        {
            $this->ws=$ws;
            $this->_out(sprintf('%.3F Tw',$ws*$k));
        }
    }
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $s='';
    if($fill || $border==1)
    {
        if($fill)
            $op=($border==1) ? 'B' : 'f';
        else
            $op='S';
        $s=sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
    }
    if(is_string($border))
    {
        $x=$this->x;
        $y=$this->y;
        if(is_int(strpos($border,'L')))
            $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
        if(is_int(strpos($border,'T')))
            $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
        if(is_int(strpos($border,'R')))
            $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
        if(is_int(strpos($border,'B')))
            $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
    }
    if($txt!='')
    {
        if($align=='R')
            $dx=$w-$this->cMargin-$this->GetStringWidth($txt);
        elseif($align=='C')
            $dx=($w-$this->GetStringWidth($txt))/2;
        elseif($align=='FJ')
        {
            //Set word spacing
            $wmax=($w-2*$this->cMargin);
            $this->ws=($wmax-$this->GetStringWidth($txt))/substr_count($txt,' ');
            $this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
            $dx=$this->cMargin;
        }
        else
            $dx=$this->cMargin;
        $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
        if($this->ColorFlag)
            $s.='q '.$this->TextColor.' ';
        $s.=sprintf('BT %.2F %.2F Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$txt);
        if($this->underline)
            $s.=' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
        if($this->ColorFlag)
            $s.=' Q';
        if($link)
        {
            if($align=='FJ')
                $wlink=$wmax;
            else
                $wlink=$this->GetStringWidth($txt);
            $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$wlink,$this->FontSize,$link);
        }
    }
    if($s)
        $this->_out($s);
    if($align=='FJ')
    {
        //Remove word spacing
        $this->_out('0 Tw');
        $this->ws=0;
    }
    $this->lasth=$h;
    if($ln>0)
    {
        $this->y+=$h;
        if($ln==1)
            $this->x=$this->lMargin;
    }
    else
        $this->x+=$w;
}



// *-----------*//


}

// Creación del objeto de la clase heredada

function cuerpoTabla(){
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Times','B',12);
    
    $pdf->SetDrawColor(135, 134, 134);
    $pdf->SetTextColor(36, 35, 28);
    
    
    
    //$pdf->Rect(10, 80, 189, 8, 'F');
    $pdf->SetDrawColor(194, 34, 16);
    $pdf->SetLineWidth(1);
    //$pdf->Line(11,95,192,95);
    
    $pdf->Cell(19,8,utf8_decode("Cantidad"),0,0);  
    $pdf->Cell(80,8,utf8_decode("Concepto"),0,0, 'C');
    $pdf->Cell(30,8,utf8_decode("Marca"),0,0, 'C');
    $pdf->Cell(30,8,utf8_decode("Precio Uni"),0,0, 'C');
    $pdf->Cell(30,8,utf8_decode("Importe"),0,0, 'C');
    $pdf->Ln(0);
    $pdf->Line(11,81,196,81);

    $pdf->Ln(12);
    
    
    
    $pdf->SetDrawColor(1, 1, 1);
    $pdf->SetLineWidth(0);

    $pdf->SetFillColor(236, 236, 236);
    
    $pdf->SetFont('Times','',12);

    $conexion = $GLOBALS["con"];
    $id_venta = $GLOBALS["idVenta"];
    
    
    $detalle = $conexion->prepare("SELECT detalle_venta.Cantidad,llantas.Descripcion, llantas.Marca, detalle_venta.precio_Unitario, detalle_venta.Importe FROM detalle_venta INNER JOIN llantas ON detalle_venta.id_llanta = llantas.id WHERE id_Venta = ?");
    $detalle->bind_param('i', $id_venta);
    $detalle->execute();
    $resultado = $detalle->get_result();  

   
    /* obtener los valores */
    while($fila = $resultado->fetch_assoc()) {

        $cantidad = $fila["Cantidad"];
        $descripcion = $fila["Descripcion"];
        $marca = $fila["Marca"];
        $precio_unitario = $fila["precio_Unitario"];
        $importe = $fila["Importe"];
       
        
        $pdf->Cell(19,10,$cantidad,0,0,'C',1);
        $pdf-> Cell(30,10, $descripcion,0,0,'C',1);
        $pdf->Cell(30,10,utf8_decode($marca),0,0, 'C',1);
        $pdf->Cell(30,10,utf8_decode($precio_unitario),0,0, 'C',1);
        $pdf->Cell(30,10,utf8_decode($importe),0,0, 'C',1);
        $pdf->Ln(11);
    }

    

    
  
    
    
    $pdf->Ln(11);
    /*$pdf->SetFont('Arial','B',10);
    $pdf->SetTextColor(194, 34, 16);
    //Subtotal
    $pdf->Cell(129,6,'',0,0);
    $pdf->Cell(30,6,'Subtotal',0,0, 'R');
    $pdf->SetTextColor(1, 1, 1);
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(30,6,'$15102,00',0,0, 'C',1);
    $pdf->Ln(7);

    
    $pdf->Cell(129,6,'',0,0);
    $pdf->SetFont('Arial','B',10);
    $pdf->SetTextColor(194, 34, 16);
    $pdf->Cell(30,6,'IVA',0,0, 'R');
    $pdf->SetTextColor(1, 1, 1);
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(30,6,'$1510,00',0,0, 'C',1);
    $pdf->Ln(7);*/

    $pdf->Cell(129,6,'',0,0);
    $pdf->SetFont('Arial','B',12);
    $pdf->SetTextColor(194, 34, 16);
    $pdf->Cell(30,8,'Total',0,0, 'R');
    $pdf->SetTextColor(1, 1, 1);
    $pdf->SetFont('Courier','',12);
    $pdf->Cell(30,8,$GLOBALS["total"],0,0, 'C',1);
    $pdf->Ln(20);


    //Importe y observaciones
    $pdf->SetFont('Times','B',12);
    $pdf->Cell(189,6,'Importe total con letra: ',0,0);
    $pdf->Ln(8);
    $pdf->SetFont('Courier','',12);
    $formatTotal = $GLOBALS["formatTotal"];
    $pdf->Cell(140,8,$formatTotal,0,0,'L',1);
    $pdf->Ln(15);

    $pdf->SetFont('Times','B',12);
    $pdf->Cell(189,6,'Oservaciones: ',0,0);
    $pdf->Ln(8);
    $pdf->SetFont('Courier','',12);
    $pdf->Cell(140,20,'',0,0,'L',1);
    $pdf->Ln(22);

    $pdf->SetTextColor(194, 34, 16);
    $pdf->SetFont('Arial','B',5);
    $text = 'GARANTÍA DE UN AÑO CONTRA DEFECTO DE FABRICA A PARTIR DE ESTA FECHA';
    $text2 = ' FAVOR DE PRESENTAR ESTE COMPROBANTE DE VENTA PARA HACER VALIDO LA GARANTÍA';
    $pdf->Cell(189,6,utf8_decode($text),0,0,'L');
    $pdf->Ln(2);
    $pdf->Cell(189,6,utf8_decode($text2),0,0,'L');
   
    
    $pdf->Ln(10);

    $pdf->SetTextColor(1, 1, 1);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(185,6,utf8_decode("Gracias por su compra"),0,0,'C');
    $pdf->Ln(18);
    $pdf->Line(78,268,130,268);
    $pdf->SetTextColor(1, 1, 1);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(193,6,utf8_decode("Recibido"),0,0,'C');

    $pdf->SetDrawColor(194, 34, 16);
    $pdf->SetLineWidth(1);
    $pdf->Line(10,285,200,285);

    $pdf->Output("Folio RAY" . $_GET["id"] .".pdf", "I");
}

cuerpoTabla();




?>