

<?php
session_start();
include '../conexion.php';
$con = $conectando->conexion(); 
global $con;

$folio =  $_GET["id"];
$idMov = $_GET["id"]; 
global $folio;

$ID = $con->prepare("SELECT id, cantidad, dot, descripcion, marca, comentario_inicial, analisis, dictamen, lugar_expedicion, fecha_expedicion, factura, id_sucursal, cliente, sucursal, id_venta FROM vista_garantias WHERE id = ?");
$ID->bind_param('i', $idMov);
$ID->execute();
$ID->bind_result($id_garantia, $cantidad, $dot, $descripcion, $marca, $comentario_inicial, $analisis, $dictamen, $lugar_expedicion, $fecha_expedicion, $factura, $id_sucursal, $cliente, $sucursal, $id_venta);
$ID->fetch();
$ID->close();
 

global $id_garantia;
global $id_venta;
global $cantidad;
global $dot;
global $descripcion;
global $marca;
global $comentario_inicial;
global $analisis;
global $dictamen;
global $lugar_expedicion;
global $fecha_expedicion;
global $factura;
global $id_sucursal;
global $cliente;
global $sucursal;

$ID = $con->prepare("SELECT code, nombre, calle, numero, colonia, ciudad, estado, pais, Telefono, RFC, CP FROM sucursal WHERE id = ?");
$ID->bind_param('i', $id_sucursal);
$ID->execute();
$ID->bind_result($code, $sucursal, $calle_suc, $numero_suc, $colonia_suc, $ciudad_suc, $estado_suc, $pais_suc, $telefono_suc, $rfc_suc, $cp_suc);
$ID->fetch();
$ID->close();

global $code;
global $sucursal;
global $calle_suc;
global $numero_suc;
global $colonia_suc;
global $ciudad_suc;
global $estado_suc;
global $pais_suc;
global $telefono_suc;
global $rfc_suc;
global $cp_suc;

$sel = "SELECT * FROM garantias_imagenes WHERE id_garantia = ?";
$res = $con->prepare($sel);
$res->bind_param('s', $id_garantia);
$res->execute();
$resultado_imagenes = $res->get_result();  
$res->free_result();
$res->close();
while($fila_ = $resultado_imagenes->fetch_assoc()){
    $data_img[] = $fila_;
}

global $data_img;

require('../../src/vendor/fpdf/fpdf.php');
//require('../../../vistas/plugins/fpdf/rounded_rect2.php');



if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

require('../helpers/utf8_decode.php');

class PDF extends FPDF
{

    function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '')
{
    $k = $this->k;
    $hp = $this->h;
    if($style=='F')
        $op='f';
    elseif($style=='FD' || $style=='DF')
        $op='B';
    else
        $op='S';
    $MyArc = 4/3 * (sqrt(2) - 1);
    $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));

    $xc = $x+$w-$r;
    $yc = $y+$r;
    $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
    if (strpos($corners, '2')===false)
        $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k,($hp-$y)*$k ));
    else
        $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);

    $xc = $x+$w-$r;
    $yc = $y+$h-$r;
    $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
    if (strpos($corners, '3')===false)
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-($y+$h))*$k));
    else
        $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);

    $xc = $x+$r;
    $yc = $y+$h-$r;
    $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
    if (strpos($corners, '4')===false)
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-($y+$h))*$k));
    else
        $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);

    $xc = $x+$r ;
    $yc = $y+$r;
    $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
    if (strpos($corners, '1')===false)
    {
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$y)*$k ));
        $this->_out(sprintf('%.2F %.2F l',($x+$r)*$k,($hp-$y)*$k ));
    }
    else
        $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
    $this->_out($op);
}

function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
{
    $h = $this->h;
    $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
        $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
}


protected $extgstates = array();

// alpha: real value from 0 (transparent) to 1 (opaque)
// bm:    blend mode, one of the following:
//          Normal, Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn,
//          HardLight, SoftLight, Difference, Exclusion, Hue, Saturation, Color, Luminosity
function SetAlpha($alpha, $bm='Normal')
{
    // set alpha for stroking (CA) and non-stroking (ca) operations
    $gs = $this->AddExtGState(array('ca'=>$alpha, 'CA'=>$alpha, 'BM'=>'/'.$bm));
    $this->SetExtGState($gs);
}

function AddExtGState($parms)
{
    $n = count($this->extgstates)+1;
    $this->extgstates[$n]['parms'] = $parms;
    return $n;
}

function SetExtGState($gs)
{
    $this->_out(sprintf('/GS%d gs', $gs));
}

function _enddoc()
{
    if(!empty($this->extgstates) && $this->PDFVersion<'1.4')
        $this->PDFVersion='1.4';
    parent::_enddoc();
}

function _putextgstates()
{
    for ($i = 1; $i <= count($this->extgstates); $i++)
    {
        $this->_newobj();
        $this->extgstates[$i]['n'] = $this->n;
        $this->_put('<</Type /ExtGState');
        $parms = $this->extgstates[$i]['parms'];
        $this->_put(sprintf('/ca %.3F', $parms['ca']));
        $this->_put(sprintf('/CA %.3F', $parms['CA']));
        $this->_put('/BM '.$parms['BM']);
        $this->_put('>>');
        $this->_put('endobj');
    }
}

function _putresourcedict()
{
    parent::_putresourcedict();
    $this->_put('/ExtGState <<');
    foreach($this->extgstates as $k=>$extgstate)
        $this->_put('/GS'.$k.' '.$extgstate['n'].' 0 R');
    $this->_put('>>');
}

function _putresources()
{
    $this->_putextgstates();
    parent::_putresources();
}

    
// Cabecera de página
function Header()

{
  
      date_default_timezone_set("America/Matamoros");
      $sucursal_nombre = $GLOBALS["sucursal"];
      $calle = $GLOBALS["calle_suc"];
      $numero = $GLOBALS["numero_suc"];
      $colonia = $GLOBALS["colonia_suc"];
      $ciudad = $GLOBALS["ciudad_suc"];
      $estado = $GLOBALS["estado_suc"];
      $pais = $GLOBALS["pais_suc"];
      $telefono = $GLOBALS["telefono_suc"];
      $rfc = $GLOBALS["rfc_suc"];
      $cp = $GLOBALS["cp_suc"];


      $id_garantia = $GLOBALS["id_garantia"];
      $cantidad = $GLOBALS['cantidad'];
      $dot = $GLOBALS['dot'];
      $descripcion = $GLOBALS['descripcion'];
      $marca = $GLOBALS['marca'];
      $comentario_inicial = $GLOBALS['comentario_inicial'];
      $analisis = $GLOBALS['analisis'];
      $dictamen = $GLOBALS['dictamen'];
      $lugar_expedicion = $GLOBALS['lugar_expedicion'];
      $fecha_expedicion = $GLOBALS['fecha_expedicion'];
      $factura = $GLOBALS['factura'];
      $id_sucursal = $GLOBALS['id_sucursal'];
      $cliente = $GLOBALS['cliente'];
      $sucursal = $GLOBALS['sucursal'];
      

      if($numero == 0 || $numero == null){
        $numero = "";
        }

        $direccion = $calle . " " . $numero . " " ;
 

        if($GLOBALS['code'] == 'RIOB') {
            $this->Image('../../src/img/logo-del-rio.jpg',10,4,43);
            $titulo_sucursal = 'Llantera economica "Del Rio"';
        }else{
            $titulo_sucursal = 'Llantera y Servicios "El Rayo"';
            $this->Image('../../src/img/logo.jpg',20,10,25);
        }
    $this->Ln(5);
   

    // Logo
    $this->AddFont('Exo2-Bold','B','Exo2-Bold.php');
    // Arial bold 15
    $this->SetFont('Arial','B',11);
    $this->Cell(170,4,utf8_decode_('Folio'),0,0,'R');
    /* $this->Line(166,15,183,15); */
    $this->SetDrawColor(253,144,138);
    $this->Ln(5);
    $this->Cell(155.5,10,utf8_decode_(''),0,0,'L', false);
    $this->SetFillColor(197, 203, 212);
    $this->RoundedRect(166, 20, 17, 7, 2, '1234', 'DF');
    $this->Cell(18,6,utf8_decode_($_GET["id"]),0,0,'C');
    $this->Ln(8);
    $this->Cell(173,4,utf8_decode_('Garantia'),0,0,'R');
    $this->SetLineWidth(0.5);

   

    
    $this->SetDrawColor(135, 134, 134);
    $this->SetTextColor(36, 35, 28);
    
    
    // Movernos a la derecha
    $this->Ln(15);
    $this->SetFont('Arial','B',10);
    $this->Cell(108.3,10,$titulo_sucursal,0,0, 'L');
    // Título
    /* $this->Ln(15);
    $this->SetFont('Arial','B',10);
    //$this->Cell(30,10,"'",0,0, 'C');
    $this->Cell(108.3,10,$titulo_sucursal,0,0, 'L');
    $this->SetFont('Exo2-Bold','B',12);
    
    $this->Cell(60,10,utf8_decode_('Dictamen: ' .$dictamen),0,0,'C');
    $this->Ln(5);

    $this->SetFont('Arial','',9);
    $this->Cell(32,10,utf8_decode_($direccion . " "),0,0,'L', false);
    $this->Cell(88,10,utf8_decode_($colonia . ", " . $cp),0,0,'L', false);
    $this->SetFont('Arial','B',9);
    $this->Cell(25,10,utf8_decode_("Fecha expedición: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(50,10,utf8_decode_($fecha_expedicion),0,0,'L', false);
    $this->Ln(4);
   
    $this->Cell(21,10,utf8_decode_($ciudad.","),0,0,'L', false);
    $this->Cell(18,10,utf8_decode_($estado.","),0,0,'L', false);
    $this->Cell(81,10,utf8_decode_($pais),0,0,'L', false);
    $this->SetFont('Arial','B',9); */
    /* $this->Cell(25,10,utf8_decode_("Hora emisión: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(50,10,utf8_decode_($hora_mov),0,0,'L', false);
    $this->Ln(4); */

   /*  $this->SetFont('Arial','B',9);
    $this->Cell(15,10,utf8_decode_("Telefono: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(105,10,utf8_decode_($telefono),0,0,'L', false);
    $this->SetFont('Arial','B',9);
    $this->Cell(25,10,utf8_decode_("Sucursal: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(50,10,utf8_decode_($sucursal_nombre),0,0,'L', false);
    $this->Ln(4);

    $this->SetFont('Arial','B',9);
    $this->Cell(15,10,utf8_decode_("RFC: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(105,10,utf8_decode_($rfc),0,0,'L', false);  
    $this->SetFont('Arial','B',9);
    $this->Cell(25,10,utf8_decode_("Correo: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(50,10,utf8_decode_("karlasanchezr@gmail.com"),0,0,'L', false);
 */
    $this->Ln(1);
    
    $this->SetFont('Exo2-Bold','B',12);
    /* $this->Cell(50,10,utf8_decode_("Movimiento:"),0,0,'L', false); */
    $this->Ln(12);
    $this->SetDrawColor(253,144,138);
    $this->SetFillColor(255,255,255);//(235, 238, 242);

    $H1 = $this->GetY();
    $altura = $this->GetY();
    $nH = $altura - $H1;
   //RoundedRect($left, $top, $width, $height, $radius, $corners = '1234', $style = '')
   /* $this->RoundedRect(10, 71, 186, $nH, 2, '34');
   $this->Ln(6); */

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
    $this->SetTextColor(92, 89, 89);
    // Número de página
   $año = date("Y");
   $title_footer = "LLANTAS Y SERVICIOS 'EL RAYO' " . $año;
    $this->Cell(0,10, $title_footer, 0,0,'C');
    $this->Cell(0,10," El rayo",0,0,'R');
}

}

// Creación del objeto de la clase heredada

function cuerpoTabla(){
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',8);
    $id_garantia = $GLOBALS["id_garantia"];
      $cantidad = $GLOBALS['cantidad'];
      $dot = $GLOBALS['dot'];
      $descripcion = $GLOBALS['descripcion'];
      $marca = $GLOBALS['marca'];
      $comentario_inicial = $GLOBALS['comentario_inicial'];
      $id_venta = $GLOBALS['id_venta'];
      $analisis = $GLOBALS['analisis'];
      $dictamen = $GLOBALS['dictamen'];
      $lugar_expedicion = $GLOBALS['lugar_expedicion'];
      $fecha_expedicion = $GLOBALS['fecha_expedicion'];
      $factura = $GLOBALS['factura'];
      $id_sucursal = $GLOBALS['id_sucursal'];
      $cliente = $GLOBALS['cliente'];
      $sucursal = $GLOBALS['sucursal'];
    
      $line_height =$pdf->GetY();
      $line_height = $line_height;
      $pdf->SetFillColor(255,255,255);
      $ejeY = $line_height;
      $k=1;
    $articulos = "   CON REFERENCIA A:
     I.- ARTICULOS; 32,33 Y 34 DE LA LEY DE PROTECCIÓN AL CONSUMIDOR
     II.- ARTICULO; IV DEL ACUERDO SOBRE BASE MINIMAS DE POLIZAS DE GARANTIA
                            (DIARIO OFICIAL DE MAYO 4 DE 1976)
     III.- GARANTIA DEL FABRICANTE";
     $pdf->MultiCell(130,3, utf8_decode_($articulos),0,1,'C',0);
     $pdf->Ln(1);
     $pdf->SetFont('Exo2-Bold','B',10);
     $pdf->Cell(17,19,utf8_decode_("Cliente: "),0,0,'C');
     $pdf->SetFont('Arial','',10);
     $pdf->Cell(40,19,utf8_decode_($cliente),0,0,'C');  
     $pdf->SetFont('Exo2-Bold','B',10);

     $pdf->Ln(15);
    $pdf->SetDrawColor(135, 134, 134);
    $pdf->SetTextColor(36, 35, 28);
    $pdf->SetFontSize(10);
    $pdf->Cell(20,9,utf8_decode_("Cantidad"),0,0,'C');  
    $pdf->Cell(90,9,utf8_decode_("Descripción"),0,0, 'L');
    $pdf->Cell(23,9,utf8_decode_("Marca"),0,0, 'L');
    $pdf->Cell(20,9,utf8_decode_("No Serie / DOT"),0,0, 'C');
    $pdf->Ln(0);
    //$pdf->Line(11,81,196,81);
   
    $pdf->Ln(11);
    
    $pdf->SetDrawColor(1, 1, 1);
    $pdf->SetLineWidth(0);

    
    $pdf->SetFont('Arial','',10);
    $conexion = $GLOBALS["con"];

    $pdf->Cell(20,9,$cantidad,0,0,'C',1);
    $pdf->Cell(90,9, utf8_decode_($descripcion),0,0,'L',1); //$descripcion
    $pdf->Cell(23,9, utf8_decode_($marca),0,0,'L',1);
    $pdf->Cell(20,9, utf8_decode_($dot),0,0,'C',1);
    $pdf->Ln(15);

    /*CAMBIOSSSSSSSSSSSSSSSSS */
    $pdf->SetDrawColor(218, 223, 230);
    //Dibujando cuadro de detalle gris y linea narjando divisora
    $pdf->RoundedRect(9.9, 95, 188, 40, 2, '34', '');
    $pdf->Line(10,109,198,109);
    $pdf->SetDrawColor(253, 144, 138);
    $pdf->SetLineWidth(0.5);
    $pdf->Line(10,95,198,95);
    $pdf->Ln(3);
    $pdf->Cell(46,9, utf8_decode_('Del Analisis: '),0,0,'C',0); 
    $pdf->multiCell(100,3.5, utf8_decode_($analisis),0,1,'L',0); 
    $pdf->Ln(20);
  
    $pdf->SetFont('Exo2-Bold','B',10);
    //Subtotal
    $pdf->Cell(22,6,'Dictamen',0,0);
    $pdf->SetFont('Arial','',10);

    $pdf->Cell(47,6, utf8_decode_('La presente reclamación es: '), 0,0);
    $pdf->SetFont('Arial','B',10);

    if($dictamen == 'pendiente'){
        $pdf->SetTextColor(137, 137, 137);
    }else if($dictamen == 'procedente'){
        $pdf->SetTextColor(113, 173, 113);

    }else if($dictamen == 'improcedente'){
        $pdf->SetTextColor(249, 117, 24);

    }
    $pdf->Cell(60,6, strtoupper($dictamen),0,1,'L',0);

    $pdf->SetFont('Exo2-Bold','B',10);
    $pdf->SetTextColor(1, 1, 1);
    $pdf->Ln(5);
    $pdf->Cell(22,6,'Importante',0,0);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,6, utf8_decode_('La garantia cubre unicamente defectos de fabricacíon y/o mano de obra'), 0,0);
    $pdf->SetFont('Exo2-Bold','B',10);

    $pdf->Ln(5);
    $pdf->Cell(22,6,'Comentario',0,0);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,6, utf8_decode_('Folio venta RAY' . $id_venta), 0,0);
    $pdf->SetFont('Exo2-Bold','B',10);
  
    $pdf->Ln(10);
    $pdf->Cell(50,6,utf8_decode_('Lugar y fecha de expedición: '),0,0);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,6, utf8_decode_($lugar_expedicion . ' ' . $fecha_expedicion), 0,0);
    $pdf->SetFont('Exo2-Bold','B',10);
    $pdf->Ln(6);
    $pdf->Cell(50,6,utf8_decode_('Nombre y firma del ajustador: '),0,0);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(100,6, utf8_decode_(''), 0,0);
    $pdf->SetFont('Exo2-Bold','B',10);
    
    $ejeY = $ejeY +17;
    $pdf->SetY($ejeY);
    $pdf->SetX(142);
    
    //Subtotal

    $pdf->SetTextColor(1, 1, 1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(193,6,utf8_decode_("Recibido"),0,0,'C'); 
    $pdf->Ln(7);
    $pdf->SetFont('Arial','B',11);

    /*

    
    $pdf->Cell(129,6,'',0,0);
    $pdf->SetFont('Arial','B',10);
    $pdf->SetTextColor(0, 32, 77);
    $pdf->Cell(30,6,'IVA',0,0, 'R');
    $pdf->SetTextColor(1, 1, 1);
    $pdf->SetFont('Courier','',10);
    $pdf->Cell(30,6,'$1510,00',0,0, 'C',1);
    $pdf->Ln(7);*/

    /* $pdf->Cell(129,6,'',0,0);
    $pdf->SetFont('Arial','B',12);
    $pdf->SetTextColor(0, 32, 77);
    $pdf->Cell(30,8,'Total',0,0, 'R');
    $pdf->SetTextColor(1, 1, 1);
    $pdf->SetFont('Courier','',12);
    $pdf->Cell(30,8,"0",0,0, 'C',1);
    $pdf->Ln(20); */


    //Importe y observaciones
    /* $pdf->SetFont('Times','B',12);
    $pdf->Cell(189,6,'Importe total con letra: ',0,0);
    $pdf->Ln(8);
    $pdf->SetFont('Courier','',12);
    $formatTotal = $GLOBALS["formatTotal"];
    $pdf->Cell(180,8,utf8_decode_($formatTotal),0,0,'L',1);
    $pdf->Ln(15); */

   /*  $pdf->SetFont('Times','B',12);
    $pdf->Cell(189,6,'Oservaciones: ',0,0);
    if(isset($GLOBALS["comentario"])){
        $observacion = $GLOBALS["comentario"];
    }else{
        $observacion ="";
    }
    $pdf->Ln(8);
    $pdf->SetFont('Courier','',12);
    $pdf->MultiCell(140,6,utf8_decode_($observacion),0,'L',1);
    $pdf->Ln(52); */

  /*   $pdf->SetTextColor(0, 32, 77);
    $pdf->SetFont('Arial','B',5);
    $text = 'GARANTÍA DE UN AÑO CONTRA DEFECTO DE FABRICA A PARTIR DE ESTA FECHA';
    $text2 = 'FAVOR DE PRESENTAR ESTE COMPROBANTE DE VENTA PARA HACER VALIDO LA GARANTÍA';
    $pdf->Cell(189,6,utf8_decode_($text),0,0,'L');
    $pdf->Ln(2);
    $pdf->Cell(189,6,utf8_decode_($text2),0,0,'L'); */
   
    
/*     $pdf->Ln(10);

    $pdf->SetTextColor(1, 1, 1);
    $pdf->SetFont('Arial','B',10);
   // $pdf->Cell(185,6,utf8_decode_("Gracias por su compra"),0,0,'C');
    $pdf->Ln(18);
    $pdf->Line(78,268,130,268);
    $pdf->SetTextColor(1, 1, 1);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(193,6,utf8_decode_("Recibido"),0,0,'C'); */
    
   /*  $pdf->Image('../../../vistas/dist/img/QR_code.png',20,238,35); */
    $pdf->SetDrawColor(253, 144, 138);
    $pdf->SetLineWidth(1);
    $pdf->Line(10,285,200,285);

       //Imagenes
    $pdf->Ln(110);
    $pdf->Cell(40,10,'Evidencia fotografica',0,0, 'L');

    $img_array = $GLOBALS["data_img"];
    $m_left = 10;
    $altura_y = 200;
    foreach ($img_array as $key => $value) {
        $pdf->Image('../../src/docs/garantias/'.$GLOBALS['id_garantia'].'/'.$value['ruta'],$m_left,$altura_y,43);
        $m_left+=50;
    }
    
    $pdf->Output("Remision " . $_GET["id"] .".pdf", "I");
    
}

cuerpoTabla();


?>


