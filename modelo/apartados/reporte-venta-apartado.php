
<?php
session_start();
include '../conexion.php';
$con = $conectando->conexion(); 
global $con;

$folio = "RAY" . $_GET["id"];
$idVenta = $_GET["id"];
global $folio;

$ID = $con->prepare("SELECT a.id, a.id_sucursal, a.id_usuario, c.id, c.Nombre_Cliente, c.Telefono, a.primer_abono, a.restante, a.pago_efectivo, a.pago_tarjeta, a.pago_transferencia, a.pago_cheque, a.pago_sin_definir, a.total,  a.tipo, a.estatus, a.metodo_pago, a.hora, a.comentario, a.plazo, a.fecha_inicial, a.fecha_final FROM apartados a INNER JOIN clientes c ON a.id_cliente = c.id WHERE a.id_venta = ?");
$ID->bind_param('i', $idVenta);
$ID->execute();
$ID->bind_result($id_apartados, $sucursal, $vendedor_id, $id_cliente, $cliente, $telefono_cliente, $primer_abono, $restante, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_sin_definir, $total, $tipo, $estatus, $metodo_pago, $hora, $comentario, $plazo, $fecha_inicio, $fecha_final);
$ID->fetch();
$ID->close();

$ID = $con->prepare("SELECT pago_efectivo, pago_tarjeta, pago_transferencia, pago_cheque, pago_sin_definir FROM abonos_apartados WHERE id_apartado = ? AND estado = 0"); //El 0 indica el estado de apono de liquidacion
$ID->bind_param('i', $id_apartados);
$ID->execute();
$ID->bind_result($pago_efectivo_abono, $pago_tarjeta_abono, $pago_transferencia_abono, $pago_cheque_abono, $pago_sin_definir_abono);
$ID->fetch();
$ID->close();

//Revisar si no hay errores en los montos
$select = "SELECT SUM(abono) FROM abonos_apartados WHERE id_apartado = ?";
$re = $con->prepare($select);
$re->bind_param('i', $id_apartados);
$re->execute();
$re->bind_result($suma_abonos);
$re->fetch();
$re->close();


//Obtener el ultimo abono del apartadi
$select = "SELECT MAX(id) FROM abonos_apartados WHERE id_apartado = ?";
$re = $con->prepare($select);
$re->bind_param('i', $id_apartados);
$re->execute();
$re->bind_result($folio_ultimo_abono);
$re->fetch();
$re->close();

//Obtener el ultimo abono del apartadi
$select = "SELECT abono FROM abonos_apartados WHERE id = ?";
$re = $con->prepare($select);
$re->bind_param('i', $folio_ultimo_abono);
$re->execute();
$re->bind_result($ultimo_abono);
$re->fetch();
$re->close();


//Consultando formas de pago
$ID = $con->prepare("SELECT nombre, apellidos FROM usuarios WHERE id = ?");
$ID->bind_param('i', $vendedor_id);
$ID->execute();
$ID->bind_result($vendedor_name, $vendedor_apellido);
$ID->fetch();
$ID->close();

$ID = $con->prepare("SELECT id_asesor FROM clientes WHERE id = ?");
$ID->bind_param('i', $id_cliente);
$ID->execute();
$ID->bind_result($id_asesor);
$ID->fetch();
$ID->close();

$ID = $con->prepare("SELECT nombre, apellidos FROM usuarios WHERE id = ?");
$ID->bind_param('i', $id_asesor);
$ID->execute();
$ID->bind_result($asesor_name, $asesor_apellido);
$ID->fetch();
$ID->close();

$vendedor_usuario = $vendedor_name . ' ' . $vendedor_apellido;

//Trayendo datos de la sucursal
$ID = $con->prepare("SELECT code, nombre, calle, numero, colonia, ciudad, estado, pais, telefono, RFC, CP, telefono_2  FROM sucursal WHERE id = ?");
$ID->bind_param('i', $sucursal);
$ID->execute();
$ID->bind_result($codigo_sucursal, $sucursal, $calle_suc, $numero_suc, $colonia_suc, $ciudad_suc, $estado_suc, $pais_suc, $telefono_suc, $rfc_suc, $cp_suc, $telefono_suc_2);
$ID->fetch();
$ID->close();

global $codigo_sucursal;
global $sucursal;
global $calle_suc;
global $numero_suc;
global $colonia_suc;
global $ciudad_suc;
global $estado_suc;
global $pais_suc;
global $telefono_cliente;
global $telefono_suc;
global $telefono_suc_2;
global $rfc_suc;
global $cp_suc;
global $vendedor_usuario;
global $cliente;
global $total;
global $tipo;
global $estatus;
global $metodo_pago;
global $hora;
global $comentario;
global $plazo;
global $fecha_inicio;
global $fecha_final;
global $restante;
global $primer_abono;
global $direccion_cliente;
global $correo_cliente;
global $id_apartados;
global $pago_efectivo_abono;
global $pago_tarjeta_abono;
global $pago_transferencia_abono;
global $pago_cheque_abono;
global $pago_sin_definir_abono;
global $suma_abonos;
global $ultimo_abono;
global $asesor_name;
global $asesor_apellido;

$formatterES = new NumberFormatter("es-ES", NumberFormatter::SPELLOUT);
$izquierda = intval(floor($ultimo_abono));
$derecha = intval(($ultimo_abono - floor($ultimo_abono)) * 100);
$formatTotalminus = $formatterES->format($izquierda) . " y " . $derecha . "/100 m.n";
$formatTotal = strtoupper($formatTotalminus);
// ciento veintitrés coma cuarenta y cinco

global $formatTotal;

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
    $calle = $GLOBALS["calle_suc"];
    $numero = $GLOBALS["numero_suc"];
    $colonia = $GLOBALS["colonia_suc"];
    $ciudad = $GLOBALS["ciudad_suc"];
    $estado = $GLOBALS["estado_suc"];
    $pais = $GLOBALS["pais_suc"];
    $telefono = $GLOBALS["telefono_suc"];
    $rfc = $GLOBALS["rfc_suc"];
    $cp = $GLOBALS["cp_suc"];
    $correo_cliente = $GLOBALS['correo_cliente'];
    $direccion_cliente = $GLOBALS['direccion_cliente'];
    if($GLOBALS['asesor_name']!=''){
        $nombre_asesor = $GLOBALS['asesor_name'] . ' '. $GLOBALS['asesor_apellido'];
    }else{
        $nombre_asesor = 'Sin asesor';
    }

    if($numero == 0 || $numero == null){
        $numero = '';
    }
    if($numero == 0 || $numero == null){
        $numero = "";
    }

    $top_direction = $calle . " " . $numero . " " ;
    $middle_direction = $colonia;
    $middle_direction_2 = $ciudad . " " . $estado . " " . $pais;

    if($GLOBALS['codigo_sucursal'] == 'RIOB') {
        $this->Image('../../src/img/logo-del-rio.jpg',10,4,43);
        $titulo_sucursal = 'Llantera economica "Del Rio"';
    }else{
        $titulo_sucursal = 'Llantera y Servicios "El Rayo"';
        $this->Image('../../src/img/logo.jpg',20,7,25);
    }

    $this->Ln(5);
   

    // Logo
    $this->AddFont('Exo2-Bold','B','Exo2-Bold.php');
    // Arial bold 15
    $this->SetFont('Arial','B',11);
    $this->Cell(170,4,utf8_decode_('Folio'),0,0,'R');
    $this->Ln(4);
    $this->SetDrawColor(253,144,138);
    $this->SetLineWidth(0.5);
    $this->SetTextColor(203, 58, 27);
    $this->Cell(270,6,$GLOBALS['estatus'],0,0,'C');
    /* $this->Line(166,15,183,15); */
    $this->Ln(1.7);
    $this->Cell(155.5,10,utf8_decode_(''),0,0,'L', false);
    $this->SetFillColor(197, 203, 212);
    
    $this->RoundedRect(166, 20, 17, 7, 2, '1234', 'DF');
    $this->Cell(18,6,utf8_decode_($_GET["id"]),0,0,'C');

   

    
    $this->SetDrawColor(135, 134, 134);
    $this->SetTextColor(36, 35, 28);
    
    
    // Movernos a la derecha
    
    // Título
    $this->Ln(11);
    $this->SetFont('Arial','B',10);
    //$this->Cell(30,10,"'",0,0, 'C');
    $this->Cell(108.3,10, $titulo_sucursal,0,0, 'L');
    $this->SetFont('Exo2-Bold','B',12);
    $this->Cell(60,10,utf8_decode_('Reporte de venta'),0,0,'C');
    $this->Ln(5);
   
    $estatus = "Reporte";
    $this->SetFont('Arial','',9);
    $this->Cell(120,10,utf8_decode_($top_direction . " ".$colonia . ", " . $cp),0,0,'L', false);
    $this->SetFont('Arial','B',9);
    $this->Cell(25,10,utf8_decode_("Fecha adelanto: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(50,10,utf8_decode_($GLOBALS['fecha_inicio']),0,0,'L', false);
    $this->Ln(4);

   
    $this->Cell(21,10,utf8_decode_($ciudad.","),0,0,'L', false);
    $this->Cell(18,10,utf8_decode_($estado.","),0,0,'L', false);
    $this->Cell(81,10,utf8_decode_($pais),0,0,'L', false);
    $this->SetFont('Arial','B',9);
    $this->Cell(25,10,utf8_decode_("Hora abono: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(50,10,utf8_decode_($GLOBALS['hora']),0,0,'L', false);
    $this->Ln(4);
    
    $this->SetFont('Arial','B',9);
    $this->Cell(26,10,utf8_decode_("Telefono ventas: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(25,10,utf8_decode_($GLOBALS['telefono_suc']),0,0,'L', false);
    $this->SetFont('Arial','B',9);
    $this->Cell(34,10,utf8_decode_("Telefono facturación: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(35,10,utf8_decode_($GLOBALS['telefono_suc_2']),0,0,'L', false);
    $this->SetFont('Arial','B',9);
    $this->Cell(25,10,utf8_decode_("Sucursal: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(50,10,utf8_decode_($GLOBALS['sucursal']),0,0,'L', false);
    $this->Ln(4);

    $this->SetFont('Arial','B',9);
    $this->Cell(15,10,utf8_decode_("RFC: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(105,10,utf8_decode_($rfc),0,0,'L', false);  
    $this->SetFont('Arial','B',9);
    $this->Cell(25,10,utf8_decode_("Correo: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(50,10,utf8_decode_("karlasanchezr@gmail.com"),0,0,'L', false);
    $this->Ln(4);
    $this->Cell(120,10,'',0,0,'L', false);
    $this->SetFont('Arial','B',9);
    $this->Cell(25,10,utf8_decode_('Vendedor: '),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(10,10,utf8_decode_($GLOBALS['vendedor_usuario']),0,0,'L', false);

    $this->Ln(4);
    $this->Cell(120,10,'',0,0,'L', false);
    $this->SetFont('Arial','B',9);
    $this->Cell(25,10,utf8_decode_("Asesor: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
   
    $this->Cell(10,10,utf8_decode_($nombre_asesor),0,0,'L', false);

    $this->Ln(6);
    
    $this->SetFont('Exo2-Bold','B',12);
    $this->Cell(50,10,utf8_decode_("Receptor"),0,0,'L', false);
    $this->Ln(12);
    $this->SetDrawColor(253,144,138);
    $this->SetFillColor(235, 238, 242);
    //RoundedRect($left, $top, $width, $height, $radius, $corners = '1234', $style = '')
    $this->RoundedRect(10, 71, 186, 26, 2, '34', 'DF');
   
    $this->SetFont('Arial','B',10);
    $this->Cell(3,3,'',0,0,'L', false);
    $this->Cell(48,3,utf8_decode_("Nombre o Razón Social: "),0,0,'L', false);
    $this->SetFont('Arial','',10);
    $this->Cell(30,3,utf8_decode_($GLOBALS['cliente']),0,0,'L', false);
    $this->Ln(5);
    $this->SetFont('Arial','B',10);
    $this->Cell(3,3,'',0,0,'L', false);
    $this->Cell(47.5,3,utf8_decode_("Teléfono: "),0,0,'L', false);
    $this->SetFont('Arial','',10);
    $this->Cell(30,3,utf8_decode_($GLOBALS['telefono_cliente']),0,0,'L', false);
    $this->Ln(5);
    $this->SetFont('Arial','B',10);
    $this->Cell(3,3,'',0,0,'L', false);
    $this->Cell(47.5,3,utf8_decode_("Correo: "),0,0,'L', false);
    $this->SetFont('Arial','',10);
    $this->Cell(30,3,utf8_decode_($correo_cliente),0,0,'L', false);
    $this->Ln(5);
    $this->SetFont('Arial','B',10);
    $this->Cell(3,3,'',0,0,'L', false);
    $this->Cell(47.5,3,utf8_decode_("Direccion: "),0,0,'L', false);
    $this->SetFont('Arial','',10);
    $this->Cell(30,3,utf8_decode_($direccion_cliente),0,0,'L', false);
    $this->Ln(15);




}

// Pie de página
function Footer()
{
    $year = date('Y'); 
    if($GLOBALS['codigo_sucursal'] == 'RIOB') {
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
    $this->SetTextColor(92, 89, 89);
    // Número de página
   
   $this->Cell(0,10,$footer_title . ' ' . $year,0,0,'C');
}


 //Aqui justifico


 


}

// Creación del objeto de la clase heredada

function cuerpoTabla(){
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Exo2-Bold','B',10);
    
    $pdf->SetDrawColor(135, 134, 134);
    $pdf->SetTextColor(36, 35, 28);
    
    $pdf->Cell(19,8,utf8_decode_("Cantidad"),0,0,'C');  
    $pdf->Cell(45,8,utf8_decode_("Descripción"),0,0, 'L');
    $pdf->Cell(35,8,utf8_decode_("Modelo"),0,0, 'L');
    $pdf->Cell(30,8,utf8_decode_("Marca"),0,0, 'C');
    $pdf->Cell(30,8,utf8_decode_("Prec. Unit"),0,0, 'C');
    $pdf->Cell(30,8,utf8_decode_("Importe"),0,0, 'L');
    $pdf->Ln(0);
    //$pdf->Line(11,81,196,81);
    
    $pdf->Ln(10);
    
    
    
    $pdf->SetDrawColor(1, 1, 1);
    $pdf->SetLineWidth(0);

    
    $pdf->SetFont('Arial','',10);

    $conexion = $GLOBALS["con"];
    $id_apartados = $GLOBALS["id_apartados"];
    $total = 0;
    $detalles = $conexion->prepare("SELECT da.modelo, da.cantidad,servicios.descripcion, da.precio_unitario, da.importe FROM detalle_apartado da INNER JOIN servicios ON da.id_llanta = servicios.id WHERE da.id_apartado = ?");
        $detalles->bind_param('i', $id_apartados);
        $detalles->execute();
        $resultadoServ = $detalles->get_result();
        $detalles->close(); 

    if($total == 0){
    
        $detalle = $conexion->prepare("SELECT da.modelo, da.cantidad,llantas.Descripcion, llantas.Marca, da.precio_unitario, da.importe FROM detalle_apartado da INNER JOIN llantas ON da.id_llanta = llantas.id WHERE da.id_apartado = ?  AND da.modelo != 'no aplica'");
        $detalle->bind_param('i', $id_apartados);
        $detalle->execute();
        $resultado = $detalle->get_result(); 
        $detalle->close();  
        
        $pdf->SetFillColor(255,255,255);
        $ejeY = 115.6;
       
        $contador=0;
      
         while($fila = $resultado->fetch_assoc()) {
            $cantidad = $fila["cantidad"];
            $modelo = $fila["modelo"];
            $descripcion = $fila["Descripcion"];
            $marca = $fila["Marca"];
            $precio_unitario = $fila["precio_unitario"];
            $importe = $fila["importe"];
            $caracteres = mb_strlen($descripcion);
           

            if($caracteres >=75){
                $pdf->Cell(12,6,$cantidad,0,0,'C',0);
                $pdf->SetFont('Arial','',10);
                $pdf->MultiCell(45,4.2, utf8_decode_($descripcion),0,0,'L',0);
                $pdf->SetFont('Arial','',10);
                $pdf->SetY($ejeY);
                $ejeY = $ejeY + 18;
                $pdf->SetX(69);
                $pdf->Cell(40,6, utf8_decode_($modelo),0,0,'L',0);
                $pdf->Cell(20,6, utf8_decode_($marca),0,0,'L',1);
                $pdf->Cell(20,6, utf8_decode_($precio_unitario),0,0,'L',0);
                $pdf->Cell(30,6, utf8_decode_($importe),0,0,'L',0);
                $pdf->Ln(19);
            }else if($caracteres > 34 && $caracteres < 75){
                $pdf->Cell(18,6,$cantidad,0,0,'C',1);
                $pdf->MultiCell(50,3.5, utf8_decode_($descripcion),0,0,'C',1);
                $pdf->SetY($ejeY);
                $ejeY = $ejeY + 8;
                $pdf->SetX(75);
                $pdf->Cell(40,6, utf8_decode_($modelo),0,0,'L',1); 
                $pdf->Cell(30,6, utf8_decode_($marca),0,0,'L',1);   
                $pdf->Cell(25,6, utf8_decode_($precio_unitario),0,0,'L',1);
                $pdf->Cell(30,6, utf8_decode_($importe),0,0,'L',1);
                $pdf->Ln(10);
            }else if($caracteres <= 34){
                $pdf->Cell(18,6,$cantidad,0,0,'C',0);
                $caractere = strlen($descripcion);
                $pdf->MultiCell(55,6, utf8_decode_($descripcion),0,0,'L',0);
                $pdf->SetY($ejeY);
                $ejeY = $ejeY + 11.1;
                $pdf->SetX(76);
                $pdf->Cell(40,6, utf8_decode_($modelo),0,0,'L',1);
                $pdf->Cell(30,6, utf8_decode_($marca),0,0,'L',1);
                $pdf->Cell(30,6, utf8_decode_($precio_unitario),0,0,'L',1);
                $pdf->Cell(30,6, utf8_decode_($importe),0,0,'L',1);
                $pdf->Ln(9);

            }

           
 
        } 
        //print_r($ejeY);
        
        $pdf->SetDrawColor(172, 172, 172);
        $nueva_altura = $ejeY - 100.6;
        //$pdf->SetAlpha(0);
        $pdf->SetFillColor(255,255,255);
       // $pdf->SetAlpha(0.2);
        //Aqui debera ser variable la altura del recuadro
        $pdf->RoundedRect(9.9, 113, 188, $nueva_altura, 2, '34', '');
        $pdf->SetDrawColor(253, 144, 138);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(10,113,198,113);


    }else if($total > 0){ 

    
        $detalles = $conexion->prepare("SELECT da.modelo, da.cantidad,servicios.descripcion, da.precio_unitario, da.importe FROM detalle_apartado da INNER JOIN servicios ON da.id_llanta = servicios.id WHERE da.id_apartado = ?");
        $detalles->bind_param('i', $id_apartados);
        $detalles->execute();
        $resultadoServ = $detalles->get_result();
        $detalles->close(); 

        $detalle = $conexion->prepare("SELECT da.modelo, da.cantidad,llantas.Descripcion, llantas.Marca, da.precio_unitario, da.importe FROM detalle_apartado da INNER JOIN llantas ON da.id_llanta = llantas.id WHERE da.id_apartado = ?  AND da.modelo != 'no aplica'");
        $detalle->bind_param('i', $id_apartados);
        $detalle->execute();
        $resultado = $detalle->get_result(); 
        $detalle->close(); 

        $pdf->SetFillColor(255,255,255);
        $ejeY = 114.7;
        $k=1;

        while($fila = $resultadoServ->fetch_assoc()) {
    
            $cantidad = $fila["Cantidad"];
            $modelo = "N/A";
            $descripcion = $fila["descripcion"];
            $marca = "N/A";
            $precio_unitario = $fila["precio_Unitario"];
            $importe = $fila["Importe"];
            $caracteres = mb_strlen($descripcion);
            
            if ($caracteres < 25) {
                $pdf->Cell(19,10,$cantidad,0,0,'C',1);
                $pdf->MultiCell(48,10, utf8_decode_($descripcion),0,0,'L',1); //$descripcion
                $pdf->SetY($ejeY);
                $ejeY = $ejeY + 13;
                $pdf->SetX(77);
                $pdf->Cell(41,10, utf8_decode_($modelo),0,0,'L',1);
                $pdf->Cell(28,10, utf8_decode_($marca),0,0,'L',1);
                $pdf->Cell(23,10,utf8_decode_($precio_unitario),0,0, 'L',1);
                $pdf->Cell(30,10,utf8_decode_($importe),0,0, 'L',1);
                $pdf->Ln(15);
          
            }else if ($caracteres > 25 && $caracteres < 45) {
                $pdf->Cell(19,10,$cantidad,0,0,'C',1);
                $pdf->MultiCell(58,5, utf8_decode_($descripcion),0,'L',1);
                $pdf->SetY($ejeY);
                $ejeY = $ejeY + 15;
                $pdf->SetX(77);
                $pdf->Cell(41,10, utf8_decode_($modelo),0,0,'L',1);
                $pdf->Cell(28,10, utf8_decode_($marca),0,0,'L',1);
                $pdf->Cell(23,10,utf8_decode_($precio_unitario),0,0, 'L',1);
                $pdf->Cell(30,10,utf8_decode_($importe),0,0, 'L',1);
                $pdf->Ln(15);
          
            }else{
                $pdf->Cell(19,12,$cantidad,0,0,'C',1);
                $pdf->MultiCell(58,6, utf8_decode_($descripcion),0,'L',1);
                $pdf->SetY($ejeY);
                $ejeY = $ejeY + 15;
                $pdf->SetX(77);
                $pdf->Cell(41,12, utf8_decode_($modelo),0,0,'C',1);
                $pdf->Cell(28,12, utf8_decode_($marca),0,0,'C',1);
                $pdf->Cell(23,12,utf8_decode_($precio_unitario),0,0, 'C',1);
                $pdf->Cell(30,12,utf8_decode_($importe),0,0, 'L',1);
                $pdf->Ln(15);
            }
    
           if($k==12){
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


        while($fila = $resultado->fetch_assoc()) {
    
            $cantidad = $fila["Cantidad"];
            $modelo = $fila["Modelo"];
            $descripcion = $fila["Descripcion"];
            $marca = $fila["Marca"];
            $precio_unitario = $fila["precio_Unitario"];
            $importe = $fila["Importe"];
            $caracteres = mb_strlen($descripcion);
            
            if ($caracteres < 25) {
              
                $pdf->Cell(19,10,$cantidad,0,0,'C',1);
                $pdf->MultiCell(48,5, utf8_decode_($descripcion),0,1,'L',1); //$descripcion
                $pdf->SetY($ejeY);
                $ejeY = $ejeY + 15;
                $pdf->SetX(77);
                $pdf->Cell(41,10, utf8_decode_($modelo),0,0,'C',1);
                $pdf->Cell(28,10, utf8_decode_($marca),0,0,'C',1);
                $pdf->Cell(23,10,utf8_decode_($precio_unitario),0,0, 'C',1);
                $pdf->Cell(30,10,utf8_decode_($importe),0,0, 'C',1);
                $pdf->Ln(15);
          
            }else if ($caracteres > 25 && $caracteres < 45) {
                
                $pdf->Cell(19,10,$cantidad,0,0,'C',1);
                $pdf->MultiCell(48,5, utf8_decode_($descripcion),0,0,'L',1);
                $pdf->SetY($ejeY);
                $ejeY = $ejeY + 15;
                $pdf->SetX(77);
                $pdf->Cell(41,10, utf8_decode_($modelo),0,0,'L',1);
                $pdf->Cell(28,10, utf8_decode_($marca),0,0,'L',1);
                $pdf->Cell(23,10,utf8_decode_($precio_unitario),0,0, 'L',1);
                $pdf->Cell(30,10,utf8_decode_($importe),0,0, 'L',1);
                $pdf->Ln(15);
          
            }else{
                $pdf->Cell(19,12,$cantidad,0,0,'C',1);
                $pdf->MultiCell(58,6, utf8_decode_($descripcion),0,'L',1);
                $pdf->SetY($ejeY);
                $ejeY = $ejeY + 15;
                $pdf->SetX(77);
                $pdf->Cell(41,12, utf8_decode_($marca),0,0,'C',1);
                $pdf->Cell(28,12, utf8_decode_($marca),0,0,'C',1);
                $pdf->Cell(23,12,utf8_decode_($precio_unitario),0,0, 'C',1);
                $pdf->Cell(30,12,utf8_decode_($importe),0,0, 'C',1);
                $pdf->Ln(15);
            }
    
           if($k==12){
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
        
            //$pdf->SetFillColor(236, 236, 236);
            
           // $pdf->SetFont('Times','',12);
            $ejeY = 85;
           }
            
           $k=$k+1;
           
        }


/*CAMBIOSSSSSSSSSSSSSSSSS */
    $pdf->SetDrawColor(218, 223, 230);
    $nueva_altura = $ejeY - 100.6;
    $pdf->RoundedRect(9.9, 113, 188, $nueva_altura, 2, '34', '');
    $pdf->SetDrawColor(253, 144, 138);
    $pdf->SetLineWidth(0.5);
    $pdf->Line(10,113,198,113);
        
    }


    
    $pdf->Ln(20);
    switch ($GLOBALS['plazo']) {
            case '1':
            $plazos = '1 semana';
            break;
            
            case '2':
            $plazos = '15 dias';
            break;
            
            case '3':
            $plazos = '1 mes';
            break;

            case '4':
            $plazos = '1 año';
            break;

            case '5':
            $plazos = 'Sin definir';
            break;

            case '6':
                $plazos = '1 día';
            break;
        
        default:
        $plazos = 'Sin definir';
            break;
    }

    $pdf->SetFont('Arial','',11);
    $pdf->Cell(29,6,"Plazo",0,0,'C',1);
    $pdf->Cell(49,6,"Fecha inicio" ,0,0,'C',1);
    $pdf->Cell(50,6,"Fecha vencimiento" ,0,0,'C',1);
    $pdf->Ln(6);
    $pdf->SetFont('Courier','',12);
    $pdf->Cell(29,6,$plazos,0,0,'C',1);
    $pdf->Cell(49,6,$GLOBALS["fecha_inicio"] ,0,0,'C',1);
    $pdf->Cell(50,6,$GLOBALS["fecha_final"] ,0,0,'C',1);
   
  
    $pdf->Ln(15);
    $pdf->SetFont('Exo2-Bold','B',11);
    $pdf->Cell(50,6,"Abono con letra:",0,0,'L',1);
    $pdf->Ln(7);
    $pdf->SetFont('Courier','B',11);
    $pdf->Cell(150,6,$GLOBALS["formatTotal"],0,0,'L',1);
    $pdf->Ln(15);

    $pdf->Ln(4);
    $pdf->SetFont('Exo2-Bold','B',11);
    $pdf->Cell(30,6,'Formas de pago:',0,0,'L',1);
    $pdf->Ln(7);
    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(30,5,'Efectivo:',0,0,'L',1);
    $pdf->Cell(30,5,'Tarjeta:',0,0,'L',1);
    $pdf->Cell(35,5,'Transferencia:',0,0,'L',1);
    $pdf->Cell(30,5,'Cheque:',0,0,'L',1);
    $pdf->Cell(38,5,'Por definir:',0,0,'L',1);
    $pdf->Ln(7);
    $pdf->SetFont('Arial','',9);
    $pdf->Cell(30,5, $GLOBALS['pago_efectivo_abono'],0,0,'L',1);
    $pdf->Cell(30,5,$GLOBALS['pago_tarjeta_abono'],0,0,'L',1);
    $pdf->Cell(35,5,$GLOBALS['pago_transferencia_abono'],0,0,'L',1);
    $pdf->Cell(30,5,$GLOBALS['pago_cheque_abono'],0,0,'L',1);
    $pdf->Cell(38,5,$GLOBALS['pago_sin_definir_abono'],0,0,'L',1);

    $pdf->Ln(4);
    $pdf->SetFont('Exo2-Bold','B',12);
    //Subtotal
    $pdf->Cell(132,6,'Condiciones y comentarios',0,0);  
    $pdf->Ln(6.5);
    $pdf->SetFont('Arial','',6);
    $pdf->SetFontSize(8);
    //Subtotal
    /* $pdf->Cell(132,6, utf8_decode_($GLOBALS["comentario"]),0,0); */
    $pdf->Ln(4.5);
    $pdf->MultiCell(170,6, $GLOBALS["comentario"],0,0,'L',0);
    $pdf->Ln(4.5);
    $pdf->SetFont('Arial','',5);
    $pdf->MultiCell(180,4, utf8_decode_("GARANTÍA DE UN AÑO CONTRA DEFECTO DE FABRICACION; NO GOLPES, NO CORTES PROVOCADOS POR MAL MANEJO, PRESION DE AIRE INADECUADA,EXCESO DE PESO, ETC. A PARTIR DE ESTA FECHA
FAVOR DE PRESENTAR ESTA NOTA PARA EMPEZAR EL PROCEDIMIENTO ADECUADO PARA GARANTIA. SI NO SE PRESENTA LA NOTA NO SE PODRA SEGUIR EL PROCESO; EN MALA INSTALACION
SOLAMENTE SERA VALIDA LA GARANTIA DENTRO DEL PRIMER MES DESPUES DE LA COMPRA, SI TIENE PARCHE AUTOMATICAMENTE PIERDE LA GARANTIA; EN CASO DE PROCEDER GARANTIA SE COBRARÁEL DESGASTE SI ES EL CASO; TIEMPO ESTIMADO DE RESPUESTA DE 1-2 SEMANAS. APLICA RESTRICCIONES. VENTAS DE APARTADO: EL PLAZO PARA PAGAR ES DE 1 MES, EN CASO DE NO CUMPLIR EL PAGO A TIEMPO EL MONTO DEL ADELANTO NO SERÁ REEMBOLSABLE"),0,0,'C',0);
    
    $ejeY = $ejeY +17;
    $pdf->SetY($ejeY);
    $pdf->SetX(142);
    
    $pdf->SetFont('Exo2-Bold','B',10);
    $pdf->Cell(30,6,"Ultimo abono:",0,0, 'R');
    $pdf->SetTextColor(1, 1, 1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(10,6,"$",0,0, 'L',1);
    $abono = number_format($GLOBALS["ultimo_abono"],2);
    $pdf->Cell(15,6,$abono,0,0, 'R',1);
    $pdf->Ln(8.5);
    
    $pdf->Cell(132,6,'',0,0);
    $pdf->SetFont('Exo2-Bold','B',10);
    $pdf->Cell(30,6,"Total abonado:",0,0, 'R');
    $pdf->SetTextColor(1, 1, 1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(10,6,"$",0,0, 'L',1);
    $restante = number_format($GLOBALS["suma_abonos"],2);
    $pdf->Cell(15,6,$restante,0,0, 'R',1);
    $pdf->Ln(6.5);

    $pdf->SetFont('Exo2-Bold','B',10);
   
    //Subtotal
    $pdf->Cell(132,6,'',0,0);
    $pdf->Cell(30,6,'Total',0,0, 'R');
    $pdf->SetTextColor(1, 1, 1);
    $pdf->SetFont('Arial','',10);
    $total = number_format($GLOBALS["total"],2);
    $pdf->Cell(10,6,"$",0,0, 'L',1);
    $pdf->Cell(15,6,$total,0,0, 'R',1);
    $pdf->Ln(98);
    $pdf->SetTextColor(1, 1, 1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(93,6,utf8_decode_("Recibido cliente"),0,0,'C'); 
    $pdf->Cell(93,6,utf8_decode_("Entregado por"),0,0,'C'); 
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

    $pdf->Output("Reporte de venta apartado RAY" . $_GET["id"] .".pdf", "I");
}

cuerpoTabla();









?>


