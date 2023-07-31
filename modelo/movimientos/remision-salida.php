

<?php
session_start();
include '../conexion.php';
$con = $conectando->conexion(); 
global $con;

$folio =  $_GET["id"];
$idMov = $_GET["id"];
global $folio;

$ID = $con->prepare("SELECT id, descripcion, mercancia, fecha, hora, usuario, tipo, sucursal FROM movimientos WHERE id = ?");
$ID->bind_param('i', $idMov);
$ID->execute();
$ID->bind_result($id_mov, $descripcion_mov, $cantidad_movida, $fecha_mov, $hora_mov, $usuario, $tipo, $sucursal_id);
$ID->fetch();
$ID->close();
 
global $id_mov;
global $descripcion_mov;
global $cantidad_movida;
global $fecha_mov;
global $hora_mov;
global $usuario;
global $tipo;
global $sucursal;

$ID = $con->prepare("SELECT code, nombre, calle, numero, colonia, ciudad, estado, pais, Telefono, RFC, CP  FROM sucursal WHERE id = ?");
$ID->bind_param('i', $sucursal_id);
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


require('../../src/vendor/fpdf/fpdf.php');
//require('../../../vistas/plugins/fpdf/rounded_rect2.php');



if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}



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


      $id_mov = $GLOBALS["id_mov"];
      $descripcion_mov= $GLOBALS["descripcion_mov"]; 
      $cantidad_movida= $GLOBALS["cantidad_movida"];
      $fecha_mov= $GLOBALS["fecha_mov"];
      $hora_mov= $GLOBALS["hora_mov"];
      $usuario=$GLOBALS["usuario"];


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
    $this->Cell(170,4,utf8_decode('Folio'),0,0,'R');
    $this->Ln(4);
    $this->SetDrawColor(253,144,138);
    $this->SetLineWidth(0.5);
    /* $this->Line(166,15,183,15); */
    $this->Ln(1.7);
    $this->Cell(155.5,10,utf8_decode(''),0,0,'L', false);
    $this->SetFillColor(197, 203, 212);
    
    $this->RoundedRect(166, 20, 17, 7, 2, '1234', 'DF');
    $this->Cell(18,6,utf8_decode($_GET["id"]),0,0,'C');

   

    
    $this->SetDrawColor(135, 134, 134);
    $this->SetTextColor(36, 35, 28);
    
    
    // Movernos a la derecha
    
    // Título
    $this->Ln(15);
    $this->SetFont('Arial','B',10);
    //$this->Cell(30,10,"'",0,0, 'C');
    $this->Cell(108.3,10,$titulo_sucursal,0,0, 'L');
    $this->SetFont('Exo2-Bold','B',12);
    //Seteando titulo
    $tipo_remision = $GLOBALS["tipo"];
    $tipo_remision = intval($tipo_remision);
    switch ($tipo_remision) {
        case 1:
            $titulo_remision ="Remisión de salida";
        break;

        case 2:
            $titulo_remision ="Remisión de entrada";
        break;

        case 3:
            $titulo_remision ="Actualización de stock";
        break;

        case 4:
            $titulo_remision ="Ingreso de llanta al catalogo";
        break;

        
        default:
        $titulo_remision =$tipo_remision;
        break;
    }
    global $tipo_remision;

    
    $this->Cell(60,10,utf8_decode($titulo_remision),0,0,'C');
    $this->Ln(5);
   
    $estatus = "Reporte";
    $this->SetFont('Arial','',9);
    $this->Cell(32,10,utf8_decode($direccion . " "),0,0,'L', false);
    $this->Cell(88,10,utf8_decode($colonia . ", " . $cp),0,0,'L', false);
    $this->SetFont('Arial','B',9);
    $this->Cell(25,10,utf8_decode("Fecha emisión: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(50,10,utf8_decode($fecha_mov),0,0,'L', false);
    $this->Ln(4);

   
    $this->Cell(21,10,utf8_decode($ciudad.","),0,0,'L', false);
    $this->Cell(18,10,utf8_decode($estado.","),0,0,'L', false);
    $this->Cell(81,10,utf8_decode($pais),0,0,'L', false);
    $this->SetFont('Arial','B',9);
    $this->Cell(25,10,utf8_decode("Hora emisión: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(50,10,utf8_decode($hora_mov),0,0,'L', false);
    $this->Ln(4);

    $this->SetFont('Arial','B',9);
    $this->Cell(15,10,utf8_decode("Telefono: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(105,10,utf8_decode($telefono),0,0,'L', false);
    $this->SetFont('Arial','B',9);
    $this->Cell(25,10,utf8_decode("Sucursal: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(50,10,utf8_decode($sucursal_nombre),0,0,'L', false);
    $this->Ln(4);

    $this->SetFont('Arial','B',9);
    $this->Cell(15,10,utf8_decode("RFC: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(105,10,utf8_decode($rfc),0,0,'L', false);  
    $this->SetFont('Arial','B',9);
    $this->Cell(25,10,utf8_decode("Correo: "),0,0,'L', false);
    $this->SetFont('Arial','',9);
    $this->Cell(50,10,utf8_decode("karlasanchezr@gmail.com"),0,0,'L', false);

   

    $this->Ln(10);
    
    $this->SetFont('Exo2-Bold','B',12);
    $this->Cell(50,10,utf8_decode("Movimiento:"),0,0,'L', false);
    $this->Ln(12);
    $this->SetDrawColor(253,144,138);
    $this->SetFillColor(255,255,255);//(235, 238, 242);
    

    $H1 = $this->GetY();
    $this->SetFont('Arial','B',10);
    $this->Cell(3,3,'',0,0,'L', false);
    $this->Cell(48,3,utf8_decode("Descripcion: "),0,0,'L', false);
    $this->SetFont('Arial','',10);
    $this->MultiCell(130,3,utf8_decode($descripcion_mov),0,0,'L', false); //$descripcion_mov
    $this->Ln(5);
    $this->SetFont('Arial','B',10);
    $this->Cell(3,3,'',0,0,'L', false);
    $this->Cell(47.5,3,utf8_decode("Llantas movidas: "),0,0,'L', false);
    $this->SetFont('Arial','',10);
    $this->Multicell(130,3,utf8_decode($cantidad_movida),0,0,'L', false);
    $this->Ln(5);
    $this->SetFont('Arial','B',10);
    $this->Cell(3,3,'',0,0,'L', false);
    $this->Cell(47.5,3,utf8_decode("Usuario:"),0,0,'L', false);
    $this->SetFont('Arial','',10);
    $this->Cell(30,3,utf8_decode($usuario),0,0,'L', false);
    $this->Ln(11);
   /*  $this->SetFont('Arial','B',10);
    $this->Cell(3,3,'',0,0,'L', false);
    $this->Cell(47.5,3,utf8_decode("Direccion: "),0,0,'L', false);
    $this->SetFont('Arial','',10);
    $this->Cell(30,3,utf8_decode($direccion_cliente),0,0,'L', false);
    $this->Ln(15); */

    $altura = $this->GetY();
    $nH = $altura - $H1;
   //RoundedRect($left, $top, $width, $height, $radius, $corners = '1234', $style = '')
   $this->RoundedRect(10, 71, 186, $nH, 2, '34');
   $this->Ln(6);


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


 //Aqui justifico


 
}

// Creación del objeto de la clase heredada

function cuerpoTabla(){
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Exo2-Bold','B',10);
    
    $pdf->SetDrawColor(135, 134, 134);
    $pdf->SetTextColor(36, 35, 28);
    
    $pdf->Cell(19,8,utf8_decode("Cantidad"),0,0,'C');  
    $pdf->Cell(45,8,utf8_decode("Descripción"),0,0, 'L');
    $pdf->Cell(35,8,utf8_decode("Modelo"),0,0, 'L');
    $pdf->Cell(30,8,utf8_decode("Marca"),0,0, 'C');
    $pdf->Cell(30,8,utf8_decode("Ubicación"),0,0, 'C');
    $pdf->Cell(30,8,utf8_decode("Destino"),0,0, 'L');
    $pdf->Ln(0);
    //$pdf->Line(11,81,196,81);
    $line_height =$pdf->GetY();
    $line_height = $line_height +8.2;
    $pdf->Ln(11);
    
    $tipo_remision = $GLOBALS["tipo"];
    
    
    $pdf->SetDrawColor(1, 1, 1);
    $pdf->SetLineWidth(0);

    
    $pdf->SetFont('Arial','',10);

    $conexion = $GLOBALS["con"];
    $id_mov = $GLOBALS["id_mov"];

    $total = 0;
    $stmt=$conexion->prepare("SELECT COUNT(*) total FROM historial_detalle_cambio WHERE id_movimiento = ?");
    $stmt->bind_param('i',$id_mov);
    $stmt->execute();
    $stmt->bind_result($total);
    $stmt->fetch();
    $stmt->close();

    if($total == 0){
    
        
       
    echo "Error al consultar el movimiento";

    }else if($total > 0){ 

        $detalle = $conexion->prepare("SELECT * FROM historial_detalle_cambio WHERE id_movimiento = ?");
        $detalle->bind_param('i', $id_mov);
        $detalle->execute();
        $resultado = $detalle->get_result(); 
        $detalle->close(); 


        $pdf->SetFillColor(255,255,255);
        $ejeY = $line_height;
        $k=1;

      

        while($fila = $resultado->fetch_assoc()) {
            $cantidad = $fila["cantidad"];
            $id_llanta = $fila["id_llanta"];
            $id_ubicacion= $fila["id_ubicacion"];
            $id_destino = $fila["id_destino"];

            $descripcion_llanta =""; $marca_llanta=""; $modelo_llanta="";
            $tyre=$conexion->prepare("SELECT Descripcion, Marca, Modelo FROM llantas WHERE id= ?");
            $tyre->bind_param('i',$id_llanta);
            $tyre->execute();
            $tyre->bind_result($descripcion_llanta, $marca_llanta, $modelo_llanta);
            $tyre->fetch();
            $tyre->close();

            if($tipo_remision == 4){
                $nombre_ubicacion = "NA";
                $nombre_destino = "NA";
            }else{
                $nombre_ubicacion ="";
                $sucu_ubi=$conexion->prepare("SELECT nombre FROM sucursal WHERE id= ?");
                $sucu_ubi->bind_param('i',$id_ubicacion);
                $sucu_ubi->execute();
                $sucu_ubi->bind_result($nombre_ubicacion);
                $sucu_ubi->fetch();
                $sucu_ubi->close();
    
    
                $nombre_destino ="";
                $sucu_dest=$conexion->prepare("SELECT nombre FROM sucursal WHERE id= ?");
                $sucu_dest->bind_param('i',$id_destino);
                $sucu_dest->execute();
                $sucu_dest->bind_result($nombre_destino);
                $sucu_dest->fetch();
                $sucu_dest->close();
            }

          

            

          $caracteres = mb_strlen($descripcion_llanta);
            
            if ($caracteres < 25) {
              
                $pdf->Cell(19,10,$cantidad,0,0,'C',1);
                $pdf->MultiCell(48,5, utf8_decode($descripcion_llanta),0,1,'L',1); //$descripcion
                $pdf->SetY($ejeY);
                $ejeY = $ejeY + 15;
                $pdf->SetX(77);
                $pdf->Cell(41,10, utf8_decode($modelo_llanta),0,0,'C',1);
                $pdf->Cell(28,10, utf8_decode($marca_llanta),0,0,'C',1);
                $pdf->Cell(23,10,utf8_decode($nombre_ubicacion),0,0, 'C',1);
                $pdf->Cell(30,10,utf8_decode($nombre_destino),0,0, 'C',1);
                $pdf->Ln(15);
          
            }else if ($caracteres > 25 && $caracteres < 45) {
                
                $pdf->Cell(19,10,$cantidad,0,0,'C',1);
                $pdf->MultiCell(48,5, utf8_decode($descripcion_llanta),0,0,'L',1);
                $pdf->SetY($ejeY);
                $ejeY = $ejeY + 15;
                $pdf->SetX(77);
                $pdf->Cell(41,10, utf8_decode($modelo_llanta),0,0,'L',1);
                $pdf->Cell(28,10, utf8_decode($marca_llanta),0,0,'L',1);
                $pdf->Cell(23,10,utf8_decode($nombre_ubicacion),0,0, 'L',1);
                $pdf->Cell(30,10,utf8_decode($nombre_destino),0,0, 'L',1);
                $pdf->Ln(15);
          
            }else{
                $pdf->Cell(19,12,$cantidad,0,0,'C',1);
                $pdf->MultiCell(58,6, utf8_decode($descripcion_llanta),0,'L',1);
                $pdf->SetY($ejeY);
                $ejeY = $ejeY + 15;
                $pdf->SetX(77);
                $pdf->Cell(41,12, utf8_decode($marca_llanta),0,0,'C',1);
                $pdf->Cell(28,12, utf8_decode($marca_llanta),0,0,'C',1);
                $pdf->Cell(23,12,utf8_decode($nombre_ubicacion),0,0, 'C',1);
                $pdf->Cell(30,12,utf8_decode($nombre_destino),0,0, 'C',1);
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
            
            $pdf->Cell(19,8,utf8_decode("Cantidad"),0,0);  
            $pdf->Cell(55,8,utf8_decode("Concepto"),0,0, 'C');
            $pdf->Cell(30,8,utf8_decode("Modelo"),0,0, 'C');
            $pdf->Cell(30,8,utf8_decode("Marca"),0,0, 'C');
            $pdf->Cell(30,8,utf8_decode("Ubicación"),0,0, 'C');
            $pdf->Cell(30,8,utf8_decode("Destino"),0,0, 'C');
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
    //Dibujando cuadro de detalle gris y linea narjando divisora
    $pdf->RoundedRect(9.9, $line_height, 188, $nueva_altura, 2, '34', '');
    $pdf->SetDrawColor(253, 144, 138);
    $pdf->SetLineWidth(0.5);
    $pdf->Line(10,$line_height,198,$line_height);
        
    }

    $pdf->Ln(26);

  
    $pdf->SetFont('Exo2-Bold','B',12);
    //Subtotal
    $pdf->Cell(132,6,'Condiciones y comentarios',0,0);
    $pdf->SetFont('Exo2-Bold','B',10);
  
    $pdf->Ln(6.5);
    $pdf->SetFont('Arial','',10);
    //Subtotal
    /* $pdf->Cell(132,6, utf8_decode($GLOBALS["comentario"]),0,0); */
    $pdf->Ln(4.5);
    $pdf->MultiCell(70,6, utf8_decode(""),0,0,'L',0);
    $pdf->Ln(4.5);
    $pdf->SetFont('Arial','',8);
    $pdf->MultiCell(150,3, utf8_decode("Remisión de salida por la cantidad de llantas mostradas en el presente documento."),0,0,'C',0);
    
    $ejeY = $ejeY +17;
    $pdf->SetY($ejeY);
    $pdf->SetX(142);
    
    //Subtotal

    $pdf->SetTextColor(1, 1, 1);
    $pdf->SetFont('Arial','',10);
    $pdf->Cell(193,6,utf8_decode("Recibido"),0,0,'C'); 
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
    $pdf->Cell(180,8,utf8_decode($formatTotal),0,0,'L',1);
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
    $pdf->MultiCell(140,6,utf8_decode($observacion),0,'L',1);
    $pdf->Ln(52); */

  /*   $pdf->SetTextColor(0, 32, 77);
    $pdf->SetFont('Arial','B',5);
    $text = 'GARANTÍA DE UN AÑO CONTRA DEFECTO DE FABRICA A PARTIR DE ESTA FECHA';
    $text2 = 'FAVOR DE PRESENTAR ESTE COMPROBANTE DE VENTA PARA HACER VALIDO LA GARANTÍA';
    $pdf->Cell(189,6,utf8_decode($text),0,0,'L');
    $pdf->Ln(2);
    $pdf->Cell(189,6,utf8_decode($text2),0,0,'L'); */
   
    
/*     $pdf->Ln(10);

    $pdf->SetTextColor(1, 1, 1);
    $pdf->SetFont('Arial','B',10);
   // $pdf->Cell(185,6,utf8_decode("Gracias por su compra"),0,0,'C');
    $pdf->Ln(18);
    $pdf->Line(78,268,130,268);
    $pdf->SetTextColor(1, 1, 1);
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(193,6,utf8_decode("Recibido"),0,0,'C'); */
    
   /*  $pdf->Image('../../../vistas/dist/img/QR_code.png',20,238,35); */
    $pdf->SetDrawColor(253, 144, 138);
    $pdf->SetLineWidth(1);
    $pdf->Line(10,285,200,285);

    $pdf->Output("Cotizacion F" . $_GET["id"] .".pdf", "I");
}

cuerpoTabla();









?>


