
<?php
session_start();
setlocale(LC_MONETARY, 'es_MX');
setlocale(LC_TIME, "es_ES.UTF-8");
include '../conexion.php';
$con = $conectando->conexion(); 
global $con;

$id_cliente = $_GET['id_cliente'];
$id_sucursal = $_SESSION['id_sucursal'];
$fecha = date('Y-m-d');
$hora = date('h:s a');
$detalle = $con->prepare("SELECT COUNT(*) FROM creditos cr INNER JOIN clientes c 
ON c.id = cr.id_cliente WHERE cr.id_cliente = ? AND cr.estatus != 5 AND cr.estatus != 3");
$detalle->bind_param('i', $id_cliente);
$detalle->execute();
$detalle->bind_result($no_registros);
$detalle->fetch();
$detalle->close();

$detalle = $con->prepare("SELECT COUNT(a.id) FROM creditos cr INNER JOIN clientes c 
ON c.id = cr.id_cliente INNER JOIN abonos a ON a.id_credito = cr.id WHERE cr.id_cliente = ? AND cr.estatus != 5 AND cr.estatus != 3");
$detalle->bind_param('i', $id_cliente);
$detalle->execute();
$detalle->bind_result($no_abonos_realizados);
$detalle->fetch();
$detalle->close();


$stmt = $con->prepare("SELECT cr.id, c.Nombre_Cliente as cliente, c.Correo, c.Direccion, cr.pagado, cr.restante, cr.total FROM creditos cr INNER JOIN clientes c 
ON c.id = cr.id_cliente WHERE cr.id_cliente = ? AND cr.estatus != 5 AND cr.estatus != 3");
$stmt->bind_param('i', $id_cliente);
$stmt->execute();
$resultado = $stmt->get_result();
$data = array(); 
$pagado=0;
$restante=0;
$total_deuda=0;       
while ($fila = $resultado->fetch_assoc()) {

    $pagado += floatval($fila['pagado']);
    $restante += floatval($fila['restante']);
    $total_deuda += floatval($fila['total']);
    $cliente = $fila['cliente'];
    $correo_cliente = $fila['Correo'];
    $direccion_cliente = $fila['Direccion'];
    $data[] = [
        'id' => $fila['id'], // Ahora 'id' estará disponible
        'nombre_cliente' => $fila['cliente'],
    ];
}
$stmt->close();


//Trayendo datos de la sucursal
$ID = $con->prepare("SELECT code, nombre, calle, numero, colonia, ciudad, estado, pais, telefono, RFC, CP, correo, telefono_2  FROM sucursal WHERE id = ?");
$ID->bind_param('i', $id_sucursal);
$ID->execute();
$ID->bind_result($codigo_sucursal, $sucursal, $calle_suc, $numero_suc, $colonia_suc, $ciudad_suc, $estado_suc, $pais_suc, $telefono_suc, $rfc_suc, $cp_suc, $correo_suc, $telefono_suc_2);
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
global $rfc_suc;
global $cp_suc;
global $cliente;
global $fecha;
global $correo_cliente;
global $pagado;
global $restante;
global $total_deuda;
global $direccion_cliente;
global $hora;
global $no_abonos_realizados;
global $resultado;
/* $formatterES = new NumberFormatter("es-ES", NumberFormatter::SPELLOUT);
$izquierda = intval(floor($total));
$derecha = intval(($total - floor($total)) * 100);
$formatTotalminus = $formatterES->format($izquierda) . " y " . $derecha . "/100 m.n";
$formatTotal = strtoupper($formatTotalminus); */
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

function asDollars($value)     
{
    return '$' . number_format($value, 2);
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
    $telefono_2 = $GLOBALS["telefono_suc_2"];
    $rfc = $GLOBALS["rfc_suc"];
    $cp = $GLOBALS["cp_suc"];
    $correo_cliente = $GLOBALS['correo_cliente'];
    $direccion_cliente = $GLOBALS['direccion_cliente'];
  
    if($numero == 0 || $numero == null){
        $numero = '';
    }

    $top_direction = $calle . " " . $numero . " " ;
    $middle_direction = $colonia;
    $middle_direction_2 = $ciudad . " " . $estado . " " . $pais;

    if($GLOBALS['codigo_sucursal'] == 'RIOB') {
        $this->Image('../../src/img/logo-del-rio.jpg',10,4,43);
        $titulo_sucursal = 'Llantera economica "Del Rio"';
    }else{
        $titulo_sucursal = 'Llantera y Servicios "El Rayo"';
        $this->Image('../../src/img/logo.jpg',20,5,25);
    }

    $this->Ln(5);
   

    // Logo
    $this->AddFont('Exo2-Bold','B','Exo2-Bold.php');
    // Arial bold 15
    $this->SetFont('Arial','B',11);
    $this->Cell(173,4,utf8_decode_('Folio cliente'),0,0,'R');
    $this->Ln(4);
    $this->SetDrawColor(253,144,138);
    $this->SetLineWidth(0.5);
    $this->SetTextColor(203, 58, 27);
    $this->Cell(270,6,'Activo',0,0,'C');
    $this->SetTextColor(36, 35, 28);

    /* $this->Line(166,15,183,15); */
    $this->Ln(1.7);
    $this->Cell(155.5,10,utf8_decode_(''),0,0,'L', false);
    $this->SetFillColor(197, 203, 212);
    
    $this->RoundedRect(166, 20, 17, 7, 2, '1234', 'DF');
    $this->Cell(18,6,utf8_decode_($_GET['id_cliente']),0,0,'C');
    
    $this->SetDrawColor(135, 134, 134);
    $this->SetTextColor(36, 35, 28);
    
    
    // Movernos a la derecha
    
    // Título
    $this->Ln(11);
    $this->SetFont('Arial','B',10);
    //$this->Cell(30,10,"'",0,0, 'C');
    $this->Cell(108.3,10, $titulo_sucursal,0,0, 'L');
    $this->SetFont('Exo2-Bold','B',12);
    $this->Cell(60,10,utf8_decode_('Estado de cuenta'),0,0,'C');
    $this->Ln(5);
   
    $estatus = "Reporte";
    $this->SetFont('Arial','',9);
    $this->Cell(120,10,utf8_decode_($top_direction . " ".$colonia . ", " . $cp),0,0,'L', false);
    //$this->Cell(88,10,utf8_decode_($colonia . ", " . $cp),0,0,'L', false);
    $this->SetFont('Arial','B',9);
    $this->Cell(25,10,utf8_decode_("Fecha: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(50,10,utf8_decode_($GLOBALS['fecha']),0,0,'L', false);
    $this->Ln(4);

   
    $this->Cell(21,10,utf8_decode_($ciudad.","),0,0,'L', false);
    $this->Cell(18,10,utf8_decode_($estado.","),0,0,'L', false);
    $this->Cell(81,10,utf8_decode_($pais),0,0,'L', false);
    $this->SetFont('Arial','B',9);
    $this->Cell(25,10,utf8_decode_("Hora: "),0,0,'L', false);
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
    $this->Cell(50,10,utf8_decode_($GLOBALS['correo_suc']),0,0,'L', false);
    $this->SetFont('Arial','B',9);
    $this->Ln(14);
    
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

    $this->SetDrawColor(253,144,138);
    $this->SetFillColor(235, 238, 242);
    //RoundedRect($left, $top, $width, $height, $radius, $corners = '1234', $style = '')
    $this->RoundedRect(10, 105, 40, 20, 2, '34', '');
    $this->SetFont('Arial','B',10);
    $this->Cell(40,8,utf8_decode_('Saldo pendiente'),1,0,'C', false);
    $this->Cell(10,8,utf8_decode_(''),0,0,'C', false);
    $this->Cell(40,8,utf8_decode_('Total abonado'),1,0,'C', false);
    $this->Cell(10,8,utf8_decode_(''),0,0,'C', false);
    $this->Cell(40,8,utf8_decode_('Total credito'),1,0,'C', false);
    $this->Cell(10,8,utf8_decode_(''),0,0,'C', false);
    $this->Cell(40,8,utf8_decode_('Abonos realizados'),1,0,'C', false);
    $this->Ln(10);
    $this->RoundedRect(60, 105, 40, 20, 2, '34', '');
    $this->RoundedRect(110, 105, 40, 20, 2, '34', '');
    $this->RoundedRect(160, 105, 40, 20, 2, '34', '');
    $this->SetFont('Arial','',10);
    $restante = $this->asDollars($GLOBALS['restante']);
    $pagado = $this->asDollars($GLOBALS['pagado']);
    $total_deuda = $this->asDollars($GLOBALS['total_deuda']);
    $total_abonos_realizados = $GLOBALS['no_abonos_realizados']==0?'0':$GLOBALS['no_abonos_realizados'];
    $this->Cell(40,8,utf8_decode_($restante),0,0,'C', false);
    $this->Cell(10,8,utf8_decode_(''),0,0,'C', false);
    $this->Cell(40,8,utf8_decode_($pagado),0,0,'C', false);
    $this->Cell(10,8,utf8_decode_(''),0,0,'C', false);
    $this->Cell(40,8,utf8_decode_($total_deuda),0,0,'C', false);
    $this->Cell(10,8,utf8_decode_(''),0,0,'C', false);
    $this->Cell(40,8,utf8_decode_($total_abonos_realizados),0,0,'C', false);

    $this->Ln(18);

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
   $año = date("Y");
   $this->Cell(0,10,$footer_title . ' ' . $year,0,0,'C');
}
}

// Creación del objeto de la clase heredada

function cuerpoTabla(){
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Exo2-Bold','B',10);
    
    $pdf->SetDrawColor(135, 134, 134);
    $pdf->SetTextColor(36, 35, 28);

    $pdf->Cell(19,8,utf8_decode_("RAY"),0,0,'C');  
    $pdf->Cell(20,8,utf8_decode_("Inicio"),0,0, 'L');
    $pdf->Cell(20,8,utf8_decode_("Vence"),0,0, 'L');
    $pdf->Cell(20,8,utf8_decode_("Total"),0,0, 'L');
    $pdf->Cell(20,8,utf8_decode_("Pagado"),0,0, 'L');
    $pdf->Cell(20,8,utf8_decode_("Restante"),0,0, 'L');
    $pdf->Cell(22,8,utf8_decode_("Estatus"),0,0, 'L');
    $pdf->Cell(20,8,utf8_decode_("Plazo"),0,0, 'L');
    $pdf->Cell(20,8,utf8_decode_("Dias vencidos"),0,0, 'L');
    $pdf->Ln(10);

    $pdf->SetFont('Arial','',9);
    $conexion = $GLOBALS["con"];
    $stmt = $conexion->prepare("SELECT cr.* FROM creditos cr INNER JOIN clientes c 
    ON c.id = cr.id_cliente WHERE cr.id_cliente = ? AND cr.estatus != 5 AND cr.estatus != 3
    ORDER BY cr.estatus, cr.fecha_inicio");
    $stmt->bind_param('i', $_GET['id_cliente']);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $data = array();     

    $meses = [
        'Jan' => 'Ene',
        'Feb' => 'Feb',
        'Mar' => 'Mar',
        'Apr' => 'Abr',
        'May' => 'May',
        'Jun' => 'Jun',
        'Jul' => 'Jul',
        'Aug' => 'Ago',
        'Sep' => 'Sep',
        'Oct' => 'Oct',
        'Nov' => 'Nov',
        'Dec' => 'Dic'
    ];

    $plazos = [
        1=> '1 semana',
        2=> '15 dias',
        3=> '1 mes',
        4=> '1 año',
        5=> '1 semana',
        6=> '1 día'
    ];

    $estatus = [
        '0'=> 'Sin abono',
        '1'=> 'Prime abono',
        '2'=> 'Pagando',
        '3'=> 'Finalizado',
        '4'=> 'Vencido',
        '5'=> 'Cancelada',
    ];

    while ($fila = $resultado->fetch_assoc()) {
        $data[] =$fila;
        $fecha_inicio = new DateTime($fila['fecha_inicio']);
        $formato_fecha_inicio = $fecha_inicio->format('d-M-y');
        $fecha_final = new DateTime($fila['fecha_final']);
        $formato_fecha_final = $fecha_final->format('d-M-y');

        $mes_abreviado_fecha_inicio = $meses[$fecha_inicio->format('M')];
        $mes_abreviado_fecha_final = $meses[$fecha_final->format('M')];
        $fecha_inicio_formateada = str_replace($fecha_inicio->format('M'), $mes_abreviado_fecha_inicio, $formato_fecha_inicio);
        $fecha_final_formateada = str_replace($fecha_final->format('M'), $mes_abreviado_fecha_final, $formato_fecha_final);

        $pdf->Cell(19,8,$fila['id_venta'],0,0,'C');  
        $pdf->Cell(20,8,utf8_decode_($fecha_inicio_formateada),0,0, 'L');
        $pdf->Cell(20,8,utf8_decode_($fecha_final_formateada),0,0, 'L');

        $total_credito= $pdf->asDollars($fila['total']);
        $pagado = $pdf->asDollars($fila['pagado']);
        $restante= $pdf->asDollars($fila['restante']);

        $pdf->Cell(20,8,utf8_decode_($total_credito),0,0, 'L');
        $pdf->Cell(20,8,utf8_decode_($pagado),0,0, 'L');
        $pdf->Cell(20,8,utf8_decode_($restante),0,0, 'L');
        $pdf->Cell(22,8,utf8_decode_($estatus[$fila['estatus']]),0,0, 'L');
        $pdf->Cell(20,8,utf8_decode_($plazos[$fila['plazo']]),0,0, 'L');

      
        $fecha_actual = new DateTime();
        $diferencia = $fecha_inicio->diff($fecha_actual);
       /*  print_r($diferencia);
        die(); */
        $pdf->Cell(20,8,$diferencia->days,0,0, 'L');
        $pdf->Ln(10);
    }
    $stmt->close();
/* 
    $pdf->SetFont('Exo2-Bold','B',10);
    $pdf->Cell(19,8,utf8_decode_("Cantidad"),0,0,'C');  
    $pdf->Cell(45,8,utf8_decode_("Descripción"),0,0, 'L');
    $pdf->Cell(35,8,utf8_decode_("Modelo"),0,0, 'L');
    $pdf->Cell(30,8,utf8_decode_("Marca"),0,0, 'C');
    $pdf->Cell(30,8,utf8_decode_("Prec. Unit"),0,0, 'C');
    $pdf->Cell(30,8,utf8_decode_("Importe"),0,0, 'L');
    $pdf->Ln(0); */
    //$pdf->Line(11,81,196,81);
    
    $pdf->Ln(10);
    
    
    
    $pdf->SetDrawColor(1, 1, 1);
    $pdf->SetLineWidth(0);

    
    $pdf->SetFont('Arial','',10);



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

    $pdf->Output("Estado de cuenta cliente " . $GLOBALS['cliente'] .".pdf", "I");
}

cuerpoTabla();









?>


