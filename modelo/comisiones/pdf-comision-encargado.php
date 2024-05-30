<?php

include '../conexion.php';
$con = $conectando->conexion();
setlocale(LC_MONETARY, 'es_MX');


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
        $this->Cell(170, 4, utf8_decode_('Reporte de filtro'), 0, 0, 'R');
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
    $pdf->Cell(17, 8, utf8_decode_('Fecha'), 0, 0, 'L');
    $pdf->Cell(52, 8, utf8_decode_('Cliente'), 0, 0, 'L');
    $pdf->Cell(12, 8, utf8_decode_('Tipo'), 0, 0, 'L');
    $pdf->Cell(12, 8, utf8_decode_('Estatus'), 0, 0, 'L');
    $pdf->Cell(30, 8, utf8_decode_('Vendedor'), 0, 0, 'L');
    $pdf->Cell(14, 8, utf8_decode_('Total'), 0, 0, 'L');
    $pdf->Cell(16, 8, utf8_decode_('Forma pago'), 0, 0, 'L');
    $pdf->Cell(15, 8, utf8_decode_('Sucursal'), 0, 0, 'L');
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
            
            $folio = "RAY{$fila['id']}";
            $fecha = $fila['Fecha'];
            $cliente = $fila['cliente'];
            $tipo = $fila['tipo'];
            $estatus = $fila['estatus'];
            $vendedor = $fila['vendedor'];
            $total = $fila['Total'];
            $forma_pago = $fila['metodo_pago'];
            $sucursal = $fila['sucursal']; 
            $importe_sin_servicio = $fila['importe_sin_servicio'] ==null ? 0 : floatval($fila['importe_sin_servicio']); 
            $sumatoria_importe_sin_serv += $importe_sin_servicio;
            $sumatoria_total += floatval($total);
            $cliente_strlen = strlen($cliente);
            
            if($cliente_strlen > 33){
                $cliente = substr($cliente, 0, 32);
            }
            $pdf->Cell(15, 6, utf8_decode_($folio), 0, 0, 'L');
            $pdf->Cell(17, 6, utf8_decode_($fecha), 0, 0, 'L');
            $pdf->Cell(52, 6, utf8_decode_($cliente), 0, 0, 'L');
            $pdf->Cell(12, 6, utf8_decode_($tipo), 0, 0, 'L');
            $pdf->Cell(12, 6, utf8_decode_($estatus), 0, 0, 'L');
            $pdf->Cell(30, 6, utf8_decode_($vendedor), 0, 0, 'L');
            $pdf->Cell(16, 6, utf8_decode_($total), 0, 0, 'L');
            $pdf->Cell(14, 6, utf8_decode_($forma_pago), 0, 0, 'L');
            $pdf->Cell(15, 6, utf8_decode_($sucursal), 0, 0, 'L');
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
                $pdf->Cell(17, 8, utf8_decode_('Fecha'), 0, 0, 'L');
                $pdf->Cell(52, 8, utf8_decode_('Cliente'), 0, 0, 'L');
                $pdf->Cell(12, 8, utf8_decode_('Tipo'), 0, 0, 'L');
                $pdf->Cell(12, 8, utf8_decode_('Estatus'), 0, 0, 'L');
                $pdf->Cell(30, 8, utf8_decode_('Vendedor'), 0, 0, 'L');
                $pdf->Cell(14, 8, utf8_decode_('Total'), 0, 0, 'L');
                $pdf->Cell(16, 8, utf8_decode_('Forma pago'), 0, 0, 'L');
                $pdf->Cell(15, 8, utf8_decode_('Sucursal'), 0, 0, 'L');

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
                $sumatoria_total_ft =  number_format($sumatoria_total, 2, '.', ',');
                $sumatoria_importe_sin_serv_ft = number_format($sumatoria_importe_sin_serv, 2, '.', ',');
             
                $pdf->RoundedRect(143, $altura_el_ultima_pagina +70, 55, 20, 2, '34', '');
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
                $pdf->Cell(10, 8, utf8_decode_($numero_datos_filtrados), 0, 0, 'L');
            }
            $k ++;
        }  

        $sumatoria_total_ft =  number_format($sumatoria_total, 2, '.', ',');
        $sumatoria_importe_sin_serv_ft = number_format($sumatoria_importe_sin_serv, 2, '.', ',');
        if(!$add_page){
            $pdf->SetDrawColor(218, 223, 230);
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
            $pdf->Cell(10, 8, utf8_decode_($numero_datos_filtrados), 0, 0, 'L');
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

    $fecha_inicial = isset($_GET['fecha_inicial']) ? $_GET['fecha_inicial'] : '';
    $mes = isset($_GET['mes']) ? $_GET['mes'] : '';
    $year = isset($_GET['year']) ? $_GET['year'] : '';
    $sucursal = isset($_GET['id_sucursal']) ? $_GET['id_sucursal'] : '';

    $fecha_final = isset($_GET['fecha_final']) ? $_GET['fecha_final'] : '';
    $asesor = isset($_GET['filtro_asesor']) ? $_GET['filtro_asesor'] : [];
    $vendedor = isset($_GET['vendedor']) ? $_GET['vendedor'] : [];
    $cliente = isset($_GET['cliente']) ? $_GET['cliente'] : '';
    $folio = isset($_GET['folio']) ? $_GET['folio'] : '';

    
    $tipo = 'Normal,Apartado,Pedido';
    $estatus = 'Pagado';

    // Define la consulta SQL base
    $sql = "SELECT DISTINCT v.*, concat(u.nombre, ' ', u.apellidos) vendedor, c.Nombre_Cliente as cliente, 
    (SELECT SUM(dv.Importe) FROM detalle_venta dv WHERE dv.Unidad = 'pieza' AND dv.id_Venta = v.id) AS 'importe_sin_servicio' 
    FROM ventas v INNER JOIN usuarios u ON v.id_Usuarios = u.id INNER JOIN clientes c ON v.id_Cliente = c.id WHERE 1=1";

    $sql_creditos = "SELECT DISTINCT v.*, concat(u.nombre, ' ', u.apellidos) vendedor, c.Nombre_Cliente as cliente, 
    (SELECT SUM(dv.Importe) FROM detalle_venta dv WHERE dv.Unidad = 'pieza' AND dv.id_Venta = v.id) AS 'importe_sin_servicio' 
    FROM ventas v INNER JOIN usuarios u ON v.id_Usuarios = u.id INNER JOIN clientes c ON v.id_Cliente = c.id 
    INNER JOIN creditos cr ON v.id = cr.id_venta INNER JOIN abonos a ON a.id_credito = cr.id WHERE 1=1 AND v.estatus ='Pagado' AND a.estado = 1"; 



    if (!empty($mes)) { 
        //$sucursal_ = $con->real_escape_string($sucursal)
        $mes_anterior = intval($mes) - 1;
        $sql .= " AND MONTH(v.fecha) = " . $mes . "";
        $sql_creditos .= " AND MONTH(a.fecha) = ". $mes ." AND (MONTH(v.fecha) = " . $mes . " OR MONTH(v.fecha) = ".$mes_anterior.")";
    }
    if (!empty($year)) { 
        //$sucursal_ = $con->real_escape_string($sucursal);
        $sql .= " AND YEAR(v.fecha) = " . $year . "";
        $sql_creditos .= " AND YEAR(a.fecha) = " . $year . "";
    }


    // Filtra por sucursal si está definida
    if (!empty($sucursal)) {
        $sucursal_ = $con->real_escape_string($sucursal);
        $sql .= " AND v.id_sucursal = '" . $sucursal_ . "'";
        $sql_creditos .= " AND v.id_sucursal = '" . $sucursal_ . "'";
    }

    // Filtra por vendedor si está definido
    if (!empty($vendedor)) {
        $array_vend = explode(",", $vendedor);
        $vendedores_ids =  implode(",", array_map(function ($valor) use ($con, $sanitizacion) {
            return $sanitizacion($valor, $con);
        }, $array_vend));
        $sql .= " AND v.id_Usuarios IN (" . $vendedores_ids . ")";
    }

    if (!empty($asesor)) {
        
        $array_ases = explode(",", $asesor);
        $asesores_ids =  implode(",", array_map(function($valor) use ($con, $sanitizacion){
            return $sanitizacion($valor, $con);
        }, $array_ases));
        $sql .= " AND c.id_asesor IN (" . $asesores_ids. ")";
        $sql_creditos .= " AND c.id_asesor IN (" . $asesores_ids. ")";
    }

    // Filtra por tipo de venta si está definido
    if (!empty($tipo)) {
        $array_tipo = explode(",", $tipo);
        $tipo_ids =  implode(",", array_map(function ($valor) use ($con, $sanitizacion) {
            return $sanitizacion($valor, $con);
        }, $array_tipo));
        $sql .= " AND v.tipo IN (" . $tipo_ids . ")";
    }

    // Filtra por estatus si está definido (múltiples valores)
    if (!empty($estatus)) {
        $array_est = explode(",", $estatus);
        $estatus_ids =  implode(",", array_map(function($valor) use ($con, $sanitizacion){
            return $sanitizacion($valor, $con);
        }, $array_est));
        $sql .= " AND v.estatus IN (" . $estatus_ids .")";
    } 
    
    //Ejecutamos la quert
    $sql .= " ORDER BY v.id DESC";

    $stmt = $con->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    //print_r($sql_creditos);
  
    $stmt = $con->prepare($sql_creditos);
    $stmt->execute();
    $result = $stmt->get_result();
    $data_creditos = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    $data_combinada = array_merge($data, $data_creditos);
    
    $total_resultados = count($data_combinada);
    if($total_resultados > 0) {
        $estatus = true;
        $mensaje = 'Se encontrarón resultados';
    } else {
        $data = [];
        $estatus = true;
        $mensaje = 'No se encontrarón resultados';
    }

    $res = array('query' => $sql, 'post' => $_POST, 'data' => $data_combinada, 'estatus' => $estatus, 'mensaje' => $mensaje, 'numero_resultados' => $total_resultados);
    return $res;

}


cuerpoTabla();
