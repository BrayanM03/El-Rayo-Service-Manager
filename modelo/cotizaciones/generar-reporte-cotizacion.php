

<?php
session_start();
include '../conexion.php';
$con = $conectando->conexion(); 
$sucursal_sesion = $_SESSION['id_sucursal'];
global $con;
global $sucursal_sesion;
$folio = "RAY" . $_GET["id"]; 
$idCotiza = $_GET["id"];
global $folio;

$ID = $con->prepare("SELECT cotizaciones.id, cotizaciones.Fecha, cotizaciones.id_Sucursal, cotizaciones.sucursal, cotizaciones.id_Usuarios, clientes.Nombre_Cliente, cotizaciones.Total, cotizaciones.estatus, cotizaciones.hora, cotizaciones.comentario FROM cotizaciones INNER JOIN clientes ON cotizaciones.id_Cliente = clientes.id WHERE cotizaciones.id = ?");
$ID->bind_param('i', $idCotiza);
$ID->execute();
$ID->bind_result($id_cotizacion, $fecha, $id_sucursal, $sucursal, $vendedor_id, $cliente, $total, $estatus, $hora, $comentario );
$ID->fetch();
$ID->close();

if($comentario == "" || $comentario == null){
    $comentario = "";
}
$sucursal = $sucursal == null ? $id_sucursal : $sucursal;

$ID = $con->prepare("SELECT nombre, apellidos FROM usuarios WHERE id = ?");
$ID->bind_param('i', $vendedor_id);
$ID->execute();
$ID->bind_result($vendedor_name, $vendedor_apellido);
$ID->fetch();
$ID->close();

$vendedor_usuario = $vendedor_name . " " . $vendedor_apellido;

global $id_cotizacion;
global $fecha;
global $sucursal;
global $vendedor_usuario;
global $cliente;
global $total;
global $tipo;
global $estatus;
global $metodo_pago;
global $hora;
global $comentario;
global $id_sucursal;

$formatterES = new NumberFormatter("es-ES", NumberFormatter::SPELLOUT);
$izquierda = intval(floor($total));
$derecha = intval(($total - floor($total)) * 100);
$formatTotalminus = $formatterES->format($izquierda) . " y " . $derecha . "/100 m.n";
$formatTotal = strtoupper($formatTotalminus);
global $formatTotal;
/*
$detalle = $con->prepare("SELECT detalle_venta.Cantidad,llantas.Descripcion, llantas.Marca, detalle_venta.precio_Unitario, detalle_venta.Importe FROM detalle_venta INNER JOIN llantas ON detalle_venta.id_llanta = llantas.id WHERE id_Venta = ?");
$detalle->bind_param('i', $id_venta);
$detalle->execute();
$resultado = $detalle->get_result(); 
global $resultado;*/

require('../../src/vendor/fpdf/fpdf.php');
require('../helpers/utf8_decode.php');



if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}



class PDF extends FPDF
{

    
// Cabecera de página
function Header()



{
    if($GLOBALS['id_sucursal'] == 1){
        $titulo_sucursal = "Llantas y Servicios 'EL Rayo'";
        $direccion = "Avenida Pedro Cardenas KM5 No.207";
        $colonia = "Col. Francisco Castellanos";
        $telefono = "8682348049";
        $rfc = "SARK9104063L6";
        $this->Image('../../src/img/logo.jpg',20,10,25);
        
   }else if($GLOBALS["sucursal"] == "Sendero"){
    $titulo_sucursal = "Llantas y Servicios 'EL Rayo'";
    $direccion = "Av. Sendero Nacional";
    $colonia = "Kilometro 50";
    $telefono = "868 127 5833";
    $rfc = "REFR971218619";
    $this->Image('../../src/img/logo.jpg',20,10,25);
   }else if($GLOBALS["sucursal"] == "Valle Hermoso"){
    $titulo_sucursal = "Llantas y Servicios 'EL Rayo'";
    $direccion = "Calle 120A Entre insurgentes y Eva Samano";
    $colonia = "";
    $telefono = "8948424459";
    $rfc = "SARK9104063L6";
    
    $this->Image('../../src/img/logo.jpg',20,10,25);
   }else if($GLOBALS["sucursal"] == "Rio Bravo"){
    $titulo_sucursal = 'Llantas economicas "Del Rio"';
    $direccion = "Av.Madero Entre Tamaulipas y Poniente 3";
    $colonia = "Centro";
    $telefono = "89-99-30-51-03";
    $rfc = "XAXX010101000";
    $this->Image('../../src/img/logo-del-rio.jpg',16,7,35);

   }else if($GLOBALS["sucursal"] == "Cavazos Lerma"){
    $titulo_sucursal = "Llantas y Servicios 'EL Rayo'";
    $direccion = "Av. Manuel Cavazos Lerma C. 18 Julio #2 Interior, 87389";
    $colonia = "Chapultepec";
    $telefono = "868 127 4584";
    $rfc = "SARK9104063L6";
    $this->Image('../../src/img/logo.jpg',20,10,25);
   }

    // Logo
    
    // Arial bold 15
   
    
    $this->SetDrawColor(135, 134, 134);
    $this->SetTextColor(36, 35, 28);
    
    
    // Movernos a la derecha
    
    // Título

    $this->SetFont('Arial','B',12);
    $this->Cell(30,10,"",0,0, 'C');
    $this->Cell(100,10,$titulo_sucursal,0,0, 'C');
    $this->SetFont('Arial','B',20);
    $this->Cell(60,10,utf8_decode_('Cotización'),0,0,'C');
    $this->Ln(5);
   
    $estatus = $GLOBALS["estatus"];
    $this->SetFont('Arial','',9);
    $this->Cell(25,15,'',0,0,'C');
    $this->Cell(115,10,utf8_decode_($direccion),0,0,'C', false);
    $this->SetFont('Arial','',12);
    $this->SetTextColor(194, 34, 16);
    $this->Cell(50,15,"Folio: " . $GLOBALS["id_cotizacion"],0,0,'C');
    $this->SetTextColor(36, 35, 28);
    $this->SetFont('Arial','',9);
    $this->Ln(4);
    $this->Cell(160,10,utf8_decode_($colonia),0,0,'C', false);
    $this->Ln(4);
    $this->Cell(160,10,utf8_decode_("H. Matamoros Tam"),0,0,'C', false);
    $this->Ln(4);
    $this->Cell(160,10,utf8_decode_("RFC: " .$rfc),0,0,'C', false);
    $this->Ln(4);
    $this->Cell(160,10,utf8_decode_("Telefono: " .$telefono),0,0,'C', false);
    $this->Ln(17);

    //$this->Rect(133, 58, 20, 7, 'F');
    //$this->Rect(133, 65, 20, 7, 'F');

    $this->SetFillColor(253, 229, 2);
    $this->SetFont('Times','B',12);
    $this->Cell(24,10,utf8_decode_("Cliente:"),0,0,'L', 1);
    $this->SetFont('Times','',12);
    $this->SetFillColor(236, 236, 236);
    $this->Cell(70,10,utf8_decode_($GLOBALS["cliente"]),0,0, 'L',1);

    $this->Ln(7);

    
    $this->SetFont('Times','B',12);
    $this->SetFillColor(253, 229, 2);
    $this->Cell(24,7,utf8_decode_("Vendedor:"),0,0,'L', 1);
    $this->SetFont('Times','',12);
    $this->SetFillColor(236, 236, 236);
    $this->Cell(70,7,utf8_decode_($GLOBALS["vendedor_usuario"]),0,0, 'L',1);
    $this->SetFont('Arial','B',12);
    $this->SetTextColor(194, 34, 16);
    $this->Cell(30,7,'Hora: ',0,0,'R', false);
    $this->SetFont('Times','',12);
    $this->SetTextColor(36, 35, 28);
    $this->Cell(20,7,utf8_decode_($GLOBALS["hora"]),0,0,'', false);

    $this->SetFont('Arial','B',12);
    $this->SetTextColor(194, 34, 16);
    $this->Cell(20,7,utf8_decode_("Fecha:"),0,0, false);
    $this->SetFont('Times','',12);
    $this->SetTextColor(36, 35, 28);
    $this->Cell(50,7,utf8_decode_($GLOBALS["fecha"]),0,0,'', false);

    // Salto de línea
    $this->Ln(18);
}

// Pie de página
function Footer()
{
    $year = date('Y'); 
    if($GLOBALS['sucursal_sesion'] == 6) {
        $footer_title = 'Del Rio Service Manager';
    }else{
        $footer_title = 'El Rayo Service Manager';
    }
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    //$this->Image('../src/img/logo-reporte.png',60,142,80);
    $this->Ln(3);
    // Arial italic 8
    $this->SetFont('Arial','',8);
    $this->SetTextColor(1, 1, 1);
    // Número de página
   
    $this->Cell(0,10,$footer_title . ' ' . $year,0,0,'C');
}


 //Aqui justifico


 


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
    
    $pdf->Cell(19,8,utf8_decode_("Cantidad"),0,0);  
    $pdf->Cell(55,8,utf8_decode_("Concepto"),0,0, 'C');
    $pdf->Cell(30,8,utf8_decode_("Modelo"),0,0, 'C');
    $pdf->Cell(30,8,utf8_decode_("Marca"),0,0, 'C');
    $pdf->Cell(30,8,utf8_decode_("Precio Uni"),0,0, 'C');
    $pdf->Cell(30,8,utf8_decode_("Importe"),0,0, 'C');
    $pdf->Ln(0);
    $pdf->Line(11,81,196,81);

    $pdf->Ln(12);
    
    
    
    $pdf->SetDrawColor(1, 1, 1);
    $pdf->SetLineWidth(0);

    $pdf->SetFillColor(236, 236, 236);
    
    $pdf->SetFont('Times','',12);

    $conexion = $GLOBALS["con"];
    $id_venta = $GLOBALS["id_cotizacion"];


        $detalle = $conexion->prepare("SELECT 
        CASE
            WHEN dc.Unidad = 'servicio' THEN 'NA'
            ELSE l.Modelo
        END AS Modelo,
        dc.Cantidad, 
        CASE
            WHEN dc.Unidad = 'servicio' THEN s.Descripcion
            ELSE l.Descripcion
        END AS Descripcion,
        CASE
            WHEN dc.Unidad = 'servicio' THEN 'NA'
            ELSE l.Marca
        END AS Marca, 
        dc.Precio_Unitario, 
        dc.Importe 
    FROM 
        detalle_cotizacion dc
    LEFT JOIN 
        llantas l ON dc.id_Llanta = l.id 
    LEFT JOIN 
        servicios s ON dc.id_Llanta = s.id 
    WHERE 
        dc.id_Cotiza = ?");
        $detalle->bind_param('i', $id_venta);
        $detalle->execute();
        $resultado = $detalle->get_result(); 
        $detalle->free_result(); 
        //$cantRes = $detalle->nums_rows() 
        $detalle->close();
        $ejeY = 85;
        
       
        $k=1;
        while($fila = $resultado->fetch_assoc()) {
            $cantidad = $fila["Cantidad"];
            $modelo = $fila["Modelo"];
            $descripcion = $fila["Descripcion"];
            $marca = $fila["Marca"];
            $precio_unitario = $fila["Precio_Unitario"];
            $importe = $fila["Importe"];
            $caracteres = mb_strlen($descripcion);
            
            if ($caracteres < 25) {
                $pdf->Cell(10,10,$cantidad,0,0,'C',1);
                $pdf->MultiCell(58,5, utf8_decode_($descripcion),1,'L',1); //$descripcion
                $pdf->SetY($ejeY);
                $ejeY = $ejeY + 15;
                $pdf->SetX(82);
                $pdf->Cell(40,10, utf8_decode_($modelo),0,0,'C',1);
                $pdf->Cell(20,10, utf8_decode_($marca),0,0,'C',1);
                $pdf->Cell(30,10,utf8_decode_($precio_unitario),0,0, 'C',1);
                $pdf->Cell(30,10,utf8_decode_($importe),0,0, 'C',1);
                $pdf->Ln(15);
          
            }else if ($caracteres > 25 && $caracteres < 45) {
                $pdf->Cell(14,10,$cantidad,0,0,'C',1);
                $pdf->MultiCell(58,5, utf8_decode_($descripcion),0,'L',1);
                $pdf->SetY($ejeY);
                $ejeY = $ejeY + 15;
                $pdf->SetX(82);
                $pdf->Cell(40,10, utf8_decode_($modelo),0,0,'C',1);
                $pdf->Cell(20,10, utf8_decode_($marca),0,0,'C',1);
                $pdf->Cell(30,10,utf8_decode_($precio_unitario),0,0, 'C',1);
                $pdf->Cell(30,10,utf8_decode_($importe),0,0, 'C',1);
                $pdf->Ln(15);
          
            }else{
                $pdf->Cell(14,12,$cantidad,0,0,'C',1);
                $pdf->MultiCell(58,6, utf8_decode_($descripcion),0,'L',1);
                $pdf->SetY($ejeY);
                $ejeY = $ejeY + 15;
                $pdf->SetX(82);
                $pdf->Cell(40,12, utf8_decode_($marca),0,0,'C',1);
                $pdf->Cell(20,12, utf8_decode_($marca),0,0,'C',1);
                $pdf->Cell(30,12,utf8_decode_($precio_unitario),0,0, 'C',1);
                $pdf->Cell(30,12,utf8_decode_($importe),0,0, 'C',1);
                $pdf->Ln(15);
            }
    
           if($k==12){//Esto de abajo es para agregar nuevas paginas jejej
            $pdf->AddPage();
            $pdf->SetFont('Times','B',12);
            
            $pdf->SetDrawColor(135, 134, 134);
            $pdf->SetTextColor(36, 35, 28);
            
            
            
            //$pdf->Rect(10, 80, 189, 8, 'F');
            $pdf->SetDrawColor(194, 34, 16);
            $pdf->SetLineWidth(1);
            //$pdf->Line(11,95,192,95);
            
            $pdf->Cell(19,8,utf8_decode_("Cantidad"),0,0);  
            $pdf->Cell(55,8,utf8_decode_("Concepto"),0,0, 'C');
            $pdf->Cell(30,8,utf8_decode_("Modelo"),0,0, 'C');
            $pdf->Cell(30,8,utf8_decode_("Marca"),0,0, 'C');
            $pdf->Cell(30,8,utf8_decode_("Precio Uni"),0,0, 'C');
            $pdf->Cell(30,8,utf8_decode_("Importe"),0,0, 'C');
            $pdf->Ln(0);
            $pdf->Line(11,81,196,81);
        
            $pdf->Ln(12);
            
            
            
            $pdf->SetDrawColor(1, 1, 1);
            $pdf->SetLineWidth(0);
        
            $pdf->SetFillColor(236, 236, 236);
            
            $pdf->SetFont('Times','',12);
            $ejeY = 85;
           }
            
           $k=$k+1;
           
        }
    
        


   // $pdf->Cell(10,10,$total,0,0,'C',1);
    
   /*
    */

    
  
    
    
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
    $pdf->Cell(180,8,utf8_decode_($formatTotal),0,0,'L',1);
    $pdf->Ln(15);

    $pdf->SetFont('Times','B',12);
    $pdf->Cell(189,6,'Oservaciones: ',0,0);
    $observacion = $GLOBALS["comentario"];
    if($observacion == "" || $observacion == null || $observacion == " "){
        $pdf->Ln(8);
        $pdf->SetFont('Courier','',12);
        $pdf->Cell(180,20,$observacion,0,0,'L',1);
        $pdf->Ln(22);
    }else{
        
        $pdf->Ln(8);
        $pdf->SetFont('Courier','',12);
        $pdf->MultiCell(180,6,$observacion,0,'L',1);
        $pdf->Ln(22);
    };
    
    

    $pdf->SetTextColor(194, 34, 16);
    $pdf->SetFont('Arial','B',5);
    $text = 'COTIZACION CON PRECIO PARA ESTA FECHA EN CURSO';
    $text2 = 'PRECIOS PUEDEN CAMBIAR POR MOTIVOS EXTERNOS';
    $pdf->Cell(189,6,utf8_decode_($text),0,0,'L');
    $pdf->Ln(2);
    $pdf->Cell(189,6,utf8_decode_($text2),0,0,'L');
   
    
    $pdf->Ln(10);

    $pdf->SetTextColor(1, 1, 1);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(185,6,utf8_decode_("Gracias por su preferencia"),0,0,'C');
    $pdf->Ln(18);
    $pdf->Line(78,268,130,268);
    $pdf->SetTextColor(1, 1, 1);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(193,6,utf8_decode_("Recibido"),0,0,'C');

    $pdf->SetDrawColor(194, 34, 16);
    $pdf->SetLineWidth(1);
    $pdf->Line(10,285,200,285);

    $pdf->Output("Folio Cotizacion " . $_GET["id"] .".pdf", "I");
}

cuerpoTabla();




?>


