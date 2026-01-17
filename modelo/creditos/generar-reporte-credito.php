<?php
session_start();
include '../conexion.php';
$con = $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
    exit;
}

require('../../src/vendor/fpdf/fpdf.php');
require('../helpers/utf8_decode.php');

// --- 1. OBTENCIÓN DE DATOS ---
$idVenta = $_GET['id'];
$folio = "RAY" . $idVenta;

// Venta y Cliente
$queryVenta = "SELECT v.*, c.Nombre_Cliente FROM ventas v INNER JOIN clientes c ON v.id_Cliente = c.id WHERE v.id = ?";
$stmt = $con->prepare($queryVenta);
$stmt->bind_param('i', $idVenta);
$stmt->execute();
$resVenta = $stmt->get_result()->fetch_assoc();

// Vendedor y Asesor
$stmt = $con->prepare("SELECT nombre, apellidos FROM usuarios WHERE id = ?");
$stmt->bind_param('i', $resVenta['id_Usuarios']);
$stmt->execute();
$vendedor = $stmt->get_result()->fetch_assoc();
$vendedor_nombre = $vendedor['nombre'] . " " . $vendedor['apellidos'];

$stmt = $con->prepare("SELECT u.nombre, u.apellidos FROM clientes c INNER JOIN usuarios u ON c.id_asesor = u.id WHERE c.id = ?");
$stmt->bind_param('i', $resVenta['id_Cliente']);
$stmt->execute();
$asesor = $stmt->get_result()->fetch_assoc();
$asesor_nombre = $asesor ? $asesor['nombre'] . " " . $asesor['apellidos'] : 'Sin asesor';

// Sucursal
$stmt = $con->prepare("SELECT * FROM sucursal WHERE id = ?");
$stmt->bind_param('i', $resVenta['id_sucursal']);
$stmt->execute();
$sucursal = $stmt->get_result()->fetch_assoc();

// Crédito
$stmt = $con->prepare("SELECT * FROM creditos WHERE id_venta = ?");
$stmt->bind_param('i', $idVenta);
$stmt->execute();
$credito = $stmt->get_result()->fetch_assoc();

// Formatear Total
$formatterES = new NumberFormatter("es-ES", NumberFormatter::SPELLOUT);
$izquierda = intval(floor($resVenta['Total']));
$derecha = intval(($resVenta['Total'] - $izquierda) * 100);
$totalLetra = strtoupper($formatterES->format($izquierda) . " PESOS " . $derecha . "/100 M.N.");

// --- 2. CLASE PDF CON FUNCIONES ESTÉTICAS ---
class PDF_Fancy extends FPDF {
    public $datos;

    // Función para dibujar rectángulos redondeados
    function RoundedRect($x, $y, $w, $h, $r, $style = '', $angle = '1234') {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F') $op='f';
        elseif($style=='FD' || $style=='DF') $op='B';
        else $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k));

        $xc = $x+$w-$r; $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-$y)*$k));
        if (strpos($angle, '2')===false) $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$y)*$k));
        else $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);

        $xc = $x+$w-$r; $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        if (strpos($angle, '3')===false) $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-($y+$h))*$k));
        else $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);

        $xc = $x+$r; $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        if (strpos($angle, '4')===false) $this->_out(sprintf('%.2F %.2F l',$x*$k,($hp-($y+$h))*$k));
        else $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);

        $xc = $x+$r ; $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',$x*$k,($hp-$yc)*$k));
        if (strpos($angle, '1')===false) {
            $this->_out(sprintf('%.2F %.2F l',$x*$k,($hp-$y)*$k));
            $this->_out(sprintf('%.2F %.2F l',($x+$r)*$k,($hp-$y)*$k));
        } else $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3) {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k, $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }

    function Header() {
        $d = $this->datos;
        // Logo
        $logo = ($d['sucursal']['code'] == 'RIOB') ? '../../src/img/logo-del-rio.jpg' : '../../src/img/logo.jpg';
        $logo_alt_y = ($d['sucursal']['code'] == 'RIOB') ? 0 : 6;
        $nombre_empresa = ($d['sucursal']['code'] == 'RIOB') ? 'Llantera economica Del Rio' : 'Llantera y Servicios "El Rayo"';
        $this->Image($logo, 12, $logo_alt_y, ($d['sucursal']['code'] == 'RIOB' ? 35 : 22));

        // Título y Sucursal
        $this->SetXY(45, 10);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(100, 7, utf8_decode_($nombre_empresa), 0, 1, 'C');
        $this->Ln(-1);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(170, 7, utf8_decode_($d['sucursal']['nombre']), 0, 1, 'C');
        $this->SetX(45);
        $this->SetFont('Arial', '', 8);
        $this->Cell(100, 4, utf8_decode_($d['sucursal']['calle']." ".$d['sucursal']['numero'].", ".$d['sucursal']['colonia'] .$d['sucursal']['CP'] . ', ' .$d['sucursal']['ciudad'] .$d['sucursal']['estado']), 0, 1, 'C');

        // Estatus y Reporte
        $this->SetXY(150, 10);
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(50);
        $this->Cell(50, 7, 'REPORTE DE CREDITO', 0, 1, 'R');
        
        // Estatus dinámico
        $estatus = strtoupper($d['venta']['estatus']);
        $estatus_credito = $d['credito']['estatus'];
       
       // Definimos variables por defecto (por si no entra en ningún case)
        $estatus_label = "Desconocido";
        $r = 0; $g = 0; $b = 0; 

        switch ($estatus_credito) {
            case 0:
                $estatus_label = "Sin abono";
                $r = 0; $g = 123; $b = 255; // Primary (Azul)
                break;
            case 1:
                $estatus_label = "Primer abono";
                $r = 23; $g = 162; $b = 184; // Info (Cian)
                break;
            case 2:
                $estatus_label = "Pagando";
                $r = 255; $g = 193; $b = 7; // Warning (Amarillo/Naranja)
                break;
            case 3:
                $estatus_label = "Finalizado";
                $r = 40; $g = 167; $b = 69; // Success (Verde)
                break;
            case 4:
                $estatus_label = "VENCIDO";
                $r = 220; $g = 53; $b = 69; // Danger (Rojo)
                break;
            case 5:
                $estatus_label = "Cancelada";
                $r = 52; $g = 58; $b = 64; // Dark (Gris oscuro)
                break;
            default:
                $estatus_label = "N/A";
                $r = 100; $g = 100; $b = 100;
                break;
        }

        // Aplicamos el color al PDF y ya tienes disponible la variable $estatus_label
        $this->SetTextColor($r, $g, $b);
        
        $this->SetX(150);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(50, 5, $estatus_label, 0, 1, 'R');
        $this->SetTextColor(0);

        $this->Ln(10);
        
        // Tabla de Info Cliente (Diseño Compacto)
        $this->SetFillColor(253, 229, 2);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(18, 6, ' CLIENTE:', 0, 0, 'L', true);
        $this->SetFillColor(245);
        $this->SetFont('Arial', '', 8);
        $this->Cell(85, 6, ' '.utf8_decode_($d['venta']['Nombre_Cliente']), 0, 0, 'L', true);
        
        $this->SetX(115);
        $this->SetFillColor(253, 229, 2);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(18, 6, ' FOLIO:', 0, 0, 'L', true);
        $this->SetFillColor(245);
        $this->SetFont('Arial', '', 8);
        $this->Cell(35, 6, ' '.$d['folio'], 0, 0, 'L', true);
        $this->Cell(32, 6, ' FECHA: '.$d['venta']['Fecha'], 0, 1, 'R');

     

        $this->SetFillColor(253, 229, 2);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(18, 6, ' ASESOR:', 0, 0, 'L', true);
        $this->SetFillColor(245);
        $this->SetFont('Arial', '', 8);
        $this->Cell(85, 6, ' '.utf8_decode_($d['asesor_nombre']), 0, 0, 'L', true);
        
        $this->SetX(115);
        $this->SetFillColor(253, 229, 2);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(18, 6, ' VENDEDOR:', 0, 0, 'L', true);
        $this->SetFillColor(245);
        $this->SetFont('Arial', '', 8);
        $this->Cell(35, 6, ' '.utf8_decode_($d['vendedor_nombre']), 0, 0, 'L', true);
        $this->Cell(32, 6, 'HORA: '.$d['venta']['hora'], 0, 1, 'R');

        $this->Ln(4);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $anio = date('Y');
        $this->Cell(0, 10, utf8_decode_('El Rayo Service Manager '.$anio .' - Página ').$this->PageNo().'/{nb}', 0, 0, 'C');
    }
}

// --- 3. GENERACIÓN ---
$pdf = new PDF_Fancy();
$pdf->datos = [
    'venta' => $resVenta,
    'credito' => $credito,
    'vendedor_nombre' => $vendedor_nombre,
    'asesor_nombre' => $asesor_nombre,
    'sucursal' => $sucursal,
    'folio' => $folio
];
$pdf->AliasNbPages();
$pdf->AddPage();

// Tabla de Productos
$pdf->SetFillColor(194, 34, 16);
$pdf->SetTextColor(255);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(15, 7, 'CANT', 0, 0, 'C', true);
$pdf->Cell(95, 7, 'CONCEPTO / DESCRIPCION', 0, 0, 'L', true);
$pdf->Cell(25, 7, 'MARCA', 0, 0, 'C', true);
$pdf->Cell(25, 7, 'PRECIO', 0, 0, 'C', true);
$pdf->Cell(30, 7, 'IMPORTE', 0, 1, 'C', true);

$pdf->SetTextColor(0);
$pdf->SetFont('Arial', '', 8);

$queryDetalle = "SELECT dv.*, IFNULL(l.Descripcion, s.descripcion) as descrip, l.Marca 
                 FROM detalle_venta dv 
                 LEFT JOIN llantas l ON dv.id_llanta = l.id AND dv.Unidad = 'pieza'
                 LEFT JOIN servicios s ON dv.id_llanta = s.id AND dv.Unidad = 'servicio'
                 WHERE dv.id_Venta = ?";
$stmt = $con->prepare($queryDetalle);
$stmt->bind_param('i', $idVenta);
$stmt->execute();
$resDetalle = $stmt->get_result();

$fill = false;
while($row = $resDetalle->fetch_assoc()) {
    $pdf->SetFillColor(248);
    $pdf->Cell(15, 6, $row['Cantidad'], 0, 0, 'C', $fill);
    $x = $pdf->GetX();
    $y = $pdf->GetY();
    $pdf->MultiCell(95, 6, utf8_decode_($row['descrip']), 0, 'L', $fill);
    $pdf->SetXY($x+95, $y);
    $pdf->Cell(25, 6, utf8_decode_($row['Marca'] ?? 'N/A'), 0, 0, 'C', $fill);
    $pdf->Cell(25, 6, '$'.number_format($row['precio_Unitario'], 2), 0, 0, 'C', $fill);
    $pdf->Cell(30, 6, '$'.number_format($row['Importe'], 2), 0, 1, 'C', $fill);
    $fill = !$fill;
}

// Totales
$pdf->Ln(2);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(135); $pdf->Cell(25, 6, 'TOTAL:', 0, 0, 'R'); 
$pdf->SetFillColor(230);
$pdf->Cell(30, 6, '$'.number_format($resVenta['Total'], 2), 0, 1, 'C', true);

$pdf->Cell(135); $pdf->Cell(25, 6, 'PAGADO:', 0, 0, 'R'); 
$pdf->Cell(30, 6, '$'.number_format($credito['pagado'], 2), 0, 1, 'C', true);

$pdf->Cell(135); $pdf->SetTextColor(194, 34, 16); $pdf->Cell(25, 6, 'RESTANTE:', 0, 0, 'R'); 
$pdf->Cell(30, 6, '$'.number_format($credito['restante'], 2), 0, 1, 'C', true);
$pdf->SetTextColor(0);

// Plazos
switch ($credito['plazo']) {
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
    # code...
    break;
}

$pdf->SetY($pdf->GetY()-18);
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(30, 5, 'PLAZO', 1, 0, 'C'); $pdf->Cell(30, 5, 'INICIO', 1, 0, 'C'); $pdf->Cell(30, 5, 'VENCIMIENTO', 1, 1, 'C');
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(30, 5, $plazos, 1, 0, 'C');
$pdf->Cell(30, 5, $credito['fecha_inicio'], 1, 0, 'C'); // Simplificado
$pdf->Cell(30, 5, $credito['fecha_final'], 1, 1, 'C');

// --- SECCIÓN GARANTÍA ---
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 7);
$pdf->SetTextColor(194, 34, 16);
$pdf->Cell(0, 4, utf8_decode_('TÉRMINOS DE GARANTÍA Y CONDICIONES:'), 0, 1);
$pdf->SetTextColor(80);
$pdf->SetFont('Arial', '', 7);
include '../helpers/condiciones_comentarios.php';

$garantiaText = "1. Toda llanta nueva cuenta con garantía contra defectos de fabricación. 2. No hay garantía en cortes, golpes o rodado con baja presión. 3. En servicios eléctricos y suspensiones la garantía es de 30 días. 4. Las piezas eléctricas no cuentan con garantía.";
$pdf->MultiCell(0, 3, utf8_decode_($text), 0, 'L');
$pdf->Cell(189,6,utf8_decode_($text2),0,0,'L');
    $pdf->Ln(2);
    $pdf->Cell(189,8,utf8_decode_($text3),0,0,'L');
    $pdf->Ln(4);
    $pdf->Cell(189,8,utf8_decode_($text4),0,0,'L');
    $pdf->Ln(4);
    $pdf->Cell(189,8,utf8_decode_($text6),0,0,'L');
    $pdf->Ln(6);
    $pdf->MultiCell(189,3.5,utf8_decode_($text7),0,'L');

// --- SECCIÓN PAGARÉ (CON DISEÑO BONITO) ---
$pdf->Ln(10);
$pdf->SetFillColor(252, 252, 252);
$pdf->SetDrawColor(200);
$yPagare = $pdf->GetY();
// Dibujar fondo redondeado
$pdf->RoundedRect(10, $yPagare, 190, 35, 3, 'DF');

$pdf->SetXY(15, $yPagare + 2);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetTextColor(0);
$pdf->Cell(0, 5, utf8_decode_('PAGARÉ INCONDICIONAL'), 0, 1, 'C');

$pdf->SetFont('Arial', '', 7.5);
$pdf->SetX(15);
$textoPagare = "DEBO Y PAGARÉ INCONDICIONALMENTE POR ESTE PAGARÉ A LA ORDEN DE KARLA KARINA SANCHEZ REYNA EN H. MATAMOROS TAMAULIPAS, LA CANTIDAD DE: " . $totalLetra . ". RECIBIDO A MI ENTERA SATISFACCIÓN, ESTE PAGARÉ CAUSARÁ INTERESES A RAZÓN DEL 3.25% MENSUAL DESDE LA FECHA DE VENCIMIENTO HASTA SU TOTAL LIQUIDACIÓN. PAGADERO CONJUNTAMENTE CON EL PRINCIPAL.";
$pdf->MultiCell(180, 4, utf8_decode_($textoPagare), 0, 'J');

// Firmas
$pdf->SetY($yPagare + 38);
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(95, 15, '', 0, 0); $pdf->Cell(95, 15, '', 0, 1); // Espacio para firma
$pdf->Line(30, $pdf->GetY(), 80, $pdf->GetY());
$pdf->Line(130, $pdf->GetY(), 180, $pdf->GetY());
$pdf->Cell(95, 5, 'ACEPTO (CLIENTE)', 0, 0, 'C');
$pdf->Cell(95, 5, 'ENTREGADO POR', 0, 1, 'C');
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(95, 4, utf8_decode_($resVenta['Nombre_Cliente']), 0, 0, 'C');
$pdf->Cell(95, 4, utf8_decode_($vendedor_nombre), 0, 1, 'C');

$pdf->Output("I", "Reporte_Credito_" . $folio . ".pdf");