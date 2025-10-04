<?php

include '../conexion.php';
include '../proveedores/Proveedor.php';
$con = $conectando->conexion();
$proveedor_resp= new Proveedor($con);
$proveedor = $proveedor_resp->obtenerEstadoCuenta($_GET['id_proveedor']);
setlocale(LC_MONETARY, 'es_MX');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$data = $proveedor['data'];
global $data;
if (!$con) {
    echo "Problemas con la conexion";
}
session_start();
require('../../src/vendor/fpdf/fpdf.php');
//require('../../../vistas/plugins/fpdf/rounded_rect2.php');


if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}
$session_sucursal = $_SESSION['id_sucursal'];
global $session_sucursal;

require('../helpers/utf8_decode.php');

class PDF extends FPDF
{
    public function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if($style == 'F') {
            $op = 'f';
        } elseif($style == 'FD' || $style == 'DF') {
            $op = 'B';
        } else {
            $op = 'S';
        }
        $MyArc = 4 / 3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));

        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));
        if (strpos($corners, '2') === false) {
            $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $y) * $k));
        } else {
            $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);
        }

        $xc = $x + $w - $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
        if (strpos($corners, '3') === false) {
            $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - ($y + $h)) * $k));
        } else {
            $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);
        }

        $xc = $x + $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
        if (strpos($corners, '4') === false) {
            $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - ($y + $h)) * $k));
        } else {
            $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);
        }

        $xc = $x + $r ;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $yc) * $k));
        if (strpos($corners, '1') === false) {
            $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $y) * $k));
            $this->_out(sprintf('%.2F %.2F l', ($x + $r) * $k, ($hp - $y) * $k));
        } else {
            $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
        }
        $this->_out($op);
    }

    public function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c ',
            $x1 * $this->k,
            ($h - $y1) * $this->k,
            $x2 * $this->k,
            ($h - $y2) * $this->k,
            $x3 * $this->k,
            ($h - $y3) * $this->k
        ));
    }


    protected $extgstates = array();

    // alpha: real value from 0 (transparent) to 1 (opaque)
    // bm:    blend mode, one of the following:
    //          Normal, Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn,
    //          HardLight, SoftLight, Difference, Exclusion, Hue, Saturation, Color, Luminosity
    public function SetAlpha($alpha, $bm = 'Normal')
    {
        // set alpha for stroking (CA) and non-stroking (ca) operations
        $gs = $this->AddExtGState(array('ca' => $alpha, 'CA' => $alpha, 'BM' => '/' . $bm));
        $this->SetExtGState($gs);
    }

    public function AddExtGState($parms)
    {
        $n = count($this->extgstates) + 1;
        $this->extgstates[$n]['parms'] = $parms;
        return $n;
    }

    public function SetExtGState($gs)
    {
        $this->_out(sprintf('/GS%d gs', $gs));
    }

    public function _enddoc()
    { 
        if(!empty($this->extgstates) && $this->PDFVersion < '1.4') {
            $this->PDFVersion = '1.4';
        }
        parent::_enddoc();
    }

    public function _putextgstates()
    {
        for ($i = 1; $i <= count($this->extgstates); $i++) {
            $this->_newobj();
            $this->extgstates[$i]['n'] = $this->n;
            $this->_put('<</Type /ExtGState');
            $parms = $this->extgstates[$i]['parms'];
            $this->_put(sprintf('/ca %.3F', $parms['ca']));
            $this->_put(sprintf('/CA %.3F', $parms['CA']));
            $this->_put('/BM ' . $parms['BM']);
            $this->_put('>>');
            $this->_put('endobj');
        }
    }

    public function _putresourcedict()
    {
        parent::_putresourcedict();
        $this->_put('/ExtGState <<');
        foreach($this->extgstates as $k => $extgstate) {
            $this->_put('/GS' . $k . ' ' . $extgstate['n'] . ' 0 R');
        }
        $this->_put('>>');
    }

    public function _putresources()
    {
        $this->_putextgstates();
        parent::_putresources();
    }


    // Cabecera de página
    public function Header()
    {

        if($GLOBALS['session_sucursal'] == 6){
            $this->Image('../../src/img/logo-del-rio.jpg',10,4,43);
            $titulo_sucursal = 'Llantera economica "Del Rio"';
        }else{
            $titulo_sucursal = 'Llantera y Servicios "El Rayo"';
            $this->Image('../../src/img/logo.jpg',20,6,25);
        }

        $this->Ln(5);


        // Logo
        $this->AddFont('Exo2-Bold', 'B', 'Exo2-Bold.php');
        // Arial bold 15
        $this->SetFont('Exo2-Bold', 'B', 12);
        $this->Cell(170, 4, utf8_decode_('Estado de cuenta'), 0, 0, 'R');
        $this->SetFont('Exo2-Bold', 'B', 14);
        $this->Ln(7);
        $this->Cell(190, 4, utf8_decode_($GLOBALS['data']['proveedor']), 0, 0, 'R');
        $this->Ln(4);
  

      //  $this->RoundedRect(166, 20, 17, 7, 2, '1234', 'DF');
        //$this->Cell(18, 6, utf8_decode_($_GET["id"]), 0, 0, 'C');

        $this->SetDrawColor(135, 134, 134);
        $this->SetTextColor(36, 35, 28);


        // Movernos a la derecha

        // Título
        $this->Ln(11);
        $this->SetFont('Arial', 'B', 10);
        //$this->Cell(30,10,"'",0,0, 'C');
        $this->Cell(108.3, 10, $titulo_sucursal, 0, 0, 'L');
        $this->Ln(5);

    }

    // Pie de página
    public function Footer()
    {

        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        //$this->Image('../src/img/logo-reporte.png',60,142,80);
        $this->Ln(3);
        // Arial italic 8
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(92, 89, 89);
        // Número de página
        $año = date("Y");
        $title_footer = "Service manager " . $año;
        $this->Cell(0, 10, $title_footer, 0, 0, 'C');
        $this->Cell(0, 10, " El rayo", 0, 0, 'R');
    }

}

// Creación del objeto de la clase heredada

function cuerpoTabla()
{
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Exo2-Bold', 'B', 7);

    $pdf->SetDrawColor(135, 134, 134);
    $pdf->SetTextColor(36, 35, 28);
    $pdf->Ln(5);
    $pdf->Cell(15, 8, utf8_decode_('Folio'), 0, 0, 'L');
    $pdf->Cell(25, 8, utf8_decode_('Fecha emisión'), 0, 0, 'L');
    $pdf->Cell(25, 8, utf8_decode_('Fecha vencido'), 0, 0, 'L');
    $pdf->Cell(14, 8, utf8_decode_('Total'), 0, 0, 'L');
    $pdf->Cell(14, 8, utf8_decode_('Pagado'), 0, 0, 'L');
    $pdf->Cell(14, 8, utf8_decode_('Restante'), 0, 0, 'L');
    $pdf->Ln(0);
    //$pdf->Line(11,81,196,81);

    $pdf->Ln(10);
    $pdf->SetDrawColor(1, 1, 1);
    $pdf->SetLineWidth(0);

    $pdf->SetFont('Arial', '', 7);

    $conexion = $GLOBALS['con'];
    $datos_filtrados = informacionFiltros($conexion);
   /*
    @var object $datos_filtrados;
    */
    $numero_datos_filtrados = count($datos_filtrados['data']);
    $elementos_x_pagina = 54;
    $max_elementos_ultima_pag = 42;
    $total_paginas = ceil($numero_datos_filtrados/$elementos_x_pagina);
    $elementos_ultima_pagina = $numero_datos_filtrados % $elementos_x_pagina;
    
    if($numero_datos_filtrados > 0) {
   
        $indexY = 0;
        $k=1;
        $add_page= false;
        $pagina = 1;
        if($total_paginas > 1){
            $pdf->SetDrawColor(218, 223, 230);
            $pdf->RoundedRect(9.9, 50, 188.4, 225, 2, '34', '');
            $pdf->SetDrawColor(253, 144, 138);
            $pdf->SetLineWidth(0.5);
            $pdf->Line(10, 50, 198, 50);
        }
        $sumatoria_total =0;
        $sumatoria_importe_sin_serv = 0;
         foreach ($datos_filtrados['data'] as $key => $fila)  {
            
            $folio = $fila['id'];
            $fecha_emision = $fila['fecha_emision'];
            $fecha_vencido = $fila['fecha_vencido'];
            $total = $fila['total'];
            $pagado  = $fila['pagado'];
            $restante = $fila['restante'];

            $pdf->Cell(15, 6, utf8_decode_($folio), 0, 0, 'L');
            $pdf->Cell(25, 6, utf8_decode_($fecha_emision), 0, 0, 'L');
            $pdf->Cell(25, 6, utf8_decode_($fecha_vencido), 0, 0, 'L');
            $pdf->Cell(14, 6, utf8_decode_($total), 0, 0, 'L');
            $pdf->Cell(14, 6, utf8_decode_($pagado), 0, 0, 'L');
            $pdf->Cell(14, 6, utf8_decode_($restante), 0, 0, 'L');
            $pdf->Ln(4);
           
            $indexY +=4.1;
             if($k == 55) {
                
                $pagina++;
                $add_page=true;
                $pdf->AddPage();
                $pdf->SetFont('Exo2-Bold', 'B', 7);
                $pdf->SetDrawColor(135, 134, 134);
                $pdf->SetTextColor(36, 35, 28);
                //$indexY = 3;
                $k=1;
                $pdf->Ln(5);
                $pdf->Cell(15, 8, utf8_decode_('Folio'), 0, 0, 'L');
                $pdf->Cell(25, 8, utf8_decode_('Fecha emisión'), 0, 0, 'L');
                $pdf->Cell(25, 8, utf8_decode_('Fecha vencido'), 0, 0, 'L');
                $pdf->Cell(14, 8, utf8_decode_('Total'), 0, 0, 'L');
                $pdf->Cell(14, 8, utf8_decode_('Pagado'), 0, 0, 'L');
                $pdf->Cell(14, 8, utf8_decode_('Restante'), 0, 0, 'L');
                
                $pdf->Ln(12);
                $pdf->SetDrawColor(1, 1, 1);
                $pdf->SetLineWidth(0);
                $pdf->SetFillColor(236, 236, 236);
                $pdf->SetFont('Arial', '', 7);
                if($pagina == $total_paginas){
                    $altura_el_ultima_pagina =(($elementos_ultima_pagina) * 4.1);
                    $pdf->SetDrawColor(218, 223, 230);
                    $pdf->RoundedRect(9.9, 50, 188.4, $altura_el_ultima_pagina +10, 2, '34', '');
                    $pdf->SetDrawColor(253, 144, 138);
                    $pdf->SetLineWidth(0.5);
                    $pdf->Line(10, 50, 198, 50);
                   
                   
                }else{
                    $pdf->SetDrawColor(218, 223, 230);
                    $pdf->RoundedRect(9.9, 50, 188.4, 225, 2, '34', '');
                    $pdf->SetDrawColor(253, 144, 138);
                    $pdf->SetLineWidth(0.5);
                    $pdf->Line(10, 50, 198, 50);
                }
                 
            }
            if($key == ($numero_datos_filtrados -1) && isset($altura_el_ultima_pagina)){
            
               /*  $pdf->RoundedRect(143, $altura_el_ultima_pagina +70, 55, 20, 2, '34', '');
                $pdf->SetFillColor(236, 236, 236);
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->Ln(24);
                $pdf->Cell(135, 8, '', 0, 0, 'L');
                $pdf->Cell(30, 8, utf8_decode_('Suma:'), 0, 0, 'L');
                $pdf->SetFont('Arial', '', 7);
                $pdf->Cell(10, 8, utf8_decode_('$'.$sumatoria_total_ft), 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->Cell(135, 8, '', 0, 0, 'L');
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->Cell(30, 8, utf8_decode_('Suma sin servicios:'), 0, 0, 'L');
                $pdf->SetFont('Arial', '', 7);
                $pdf->Cell(10, 8, utf8_decode_('$'.$sumatoria_importe_sin_serv_ft), 0, 0, 'L');
                $pdf->Ln(5);
                $pdf->Cell(135, 8, '', 0, 0, 'L');
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->Cell(30, 8, utf8_decode_('Total de ventas:'), 0, 0, 'L');
                $pdf->SetFont('Arial', '', 7);
                $pdf->Cell(10, 8, utf8_decode_($numero_datos_filtrados), 0, 0, 'L'); */
            }
            $k ++;
        }  

      
        if(!$add_page){
         /*    $pdf->SetDrawColor(218, 223, 230);
            $pdf->RoundedRect(9.9, 50, 188.4, $indexY +10, 2, '34', '');
            $pdf->SetDrawColor(253, 144, 138);
            $pdf->SetLineWidth(0.5);
            $pdf->Line(10, 50, 198, 50);

            $pdf->RoundedRect(143, $indexY +65, 55, 20, 2, '34', '');
            $pdf->SetFillColor(236, 236, 236);
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Ln(15);
            $pdf->Cell(135, 8, '', 0, 0, 'L');
            $pdf->Cell(30, 8, utf8_decode_('Suma:'), 0, 0, 'L');
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(10, 8, utf8_decode_('$'.$sumatoria_total_ft), 0, 0, 'L');
            $pdf->Ln(5);
            $pdf->Cell(135, 8, '', 0, 0, 'L');
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Cell(30, 8, utf8_decode_('Suma sin servicios:'), 0, 0, 'L');
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(10, 8, utf8_decode_('$'.$sumatoria_importe_sin_serv_ft), 0, 0, 'L');
            $pdf->Ln(5);
            $pdf->Cell(135, 8, '', 0, 0, 'L');
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->Cell(30, 8, utf8_decode_('Total de ventas:'), 0, 0, 'L');
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(10, 8, utf8_decode_($numero_datos_filtrados), 0, 0, 'L'); */
        }
    }

    $pdf->Output("Reporte de filtro.pdf", "I");
}



function informacionFiltros($con)
{

    //Funcion que sanitiza entradas
    $sanitizacion = function ($valor, $con) {
        $valor_ = $con->real_escape_string($valor);
        $valor_str = "'" . $valor_ . "'";
        return $valor_str; 
    };
    
    $tipo = 'Normal,Apartado,Pedido';
    $estatus = 'Pagado';
    $id_proveedor = $_GET['id_proveedor'];
    // Define la consulta SQL base
    $sql = "SELECT * FROM vista_movimientos WHERE tipo = 2 AND restante > 0 AND proveedor_id = " . $id_proveedor . " AND (estado_factura != 4 AND estado_factura != 1 AND estado_factura != '')";


    $stmt = $con->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    /* print_r($sql);
    die(); */
    $total_resultados = count($data);
    if($total_resultados > 0) {
        $estatus = true;
        $mensaje = 'Se encontrarón resultados';
    } else {
        $data = [];
        $estatus = true;
        $mensaje = 'No se encontrarón resultados';
    }

    $res = array('query' => $sql, 'post' => $_POST, 'data' => $data, 'estatus' => $estatus, 'mensaje' => $mensaje, 'numero_resultados' => $total_resultados);
    return $res;

}


cuerpoTabla();
