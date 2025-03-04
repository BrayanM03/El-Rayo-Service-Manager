<?php
include '../conexion.php';
$con = $conectando->conexion();
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!$con) {
    echo "maaaaal";
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}
date_default_timezone_set("America/Matamoros");


use PhpOffice\PhpSpreadsheet\helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\SpreadSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

require_once '../../vendor/phpoffice/phpspreadsheet/samples/Bootstrap.php'; 

date_default_timezone_set("America/Matamoros");
$year = $_GET['year'];
$mes = $_GET['mes'];
$id_asesor = $_GET['id_asesor'];
$spreadsheet = new SpreadSheet();
$spreadsheet->getProperties()->setCreator("Ricardo Reyna")->setTitle("reporte_comisiones");
$count=0;
$spreadsheet->setActiveSheetIndex($count);
$hoja_activa = $spreadsheet->getActiveSheet();

$hoja_activa->setTitle("Reporte de comisiones");


$consultaAsesor = "SELECT nombre, apellidos, id_sucursal, comision_venta, comision_credito FROM usuarios WHERE id = ?";
$resp = $con->prepare($consultaAsesor);
$resp->bind_param('s', $id_asesor);
$resp->execute();
$resp->bind_result($nombre_asesor, $apellido_asesor, $id_sucursal, $porcentaje_comision, $porcentaje_comision_credito);
$resp->fetch();
$resp->close();

$consultaSucursal = "SELECT nombre FROM sucursal WHERE id = ?";
$resp = $con->prepare($consultaSucursal);
$resp->bind_param('s', $id_sucursal);
$resp->execute();
$resp->bind_result($nombre_sucursal);
$resp->fetch();
$resp->close();
if(empty($porcentaje_comision)){$porcentaje_comision=0;}
if(empty($porcentaje_comision_credito)){$porcentaje_comision_credito=0;}

switch ($mes) {
    case '01':
        $mes_letra = 'Enero';
        break;
    case '02':
        $mes_letra = 'Febrero';
        break;
    case '03':
        $mes_letra = 'Marzo';
        break;
    case '04':
        $mes_letra = 'Abril';
        break;
    case '05':
        $mes_letra = 'Mayo';
        break;    
    case '06':
        $mes_letra = 'Junio';
        break;
    case '07':
        $mes_letra = 'Julio';
        break;
    case '08':
        $mes_letra = 'Agosto';
        break;    
    case '09':
        $mes_letra ='Septiembre';
        break;
    case '10':
        $mes_letra ='Octubre';
        break;
    case '11':
        $mes_letra ='Noviembre';
        break;
    case '12':
        $mes_letra ='Diciembre';
        break;
    
    default:
        $mes_letra = 'Sin definir';
        break;
}

$hoja_activa->mergeCells("A1:H1");
$hoja_activa->setCellValue('A1', 'Reporte de comisiones  de: '. $nombre_asesor . ' '. $apellido_asesor .'  | Llantera el rayo ');
$hoja_activa->mergeCells("A2:D2");
$hoja_activa->mergeCells("E2:H2");
$hoja_activa->setCellValue('A2', 'Sucursal: '.$nombre_sucursal);
$hoja_activa->setCellValue('E2', 'Mes de '. $mes_letra);
$hoja_activa->getStyle('A1:H2')->getAlignment()->setHorizontal('center');
$hoja_activa->getStyle('A1:H2')->getAlignment()->setVertical('center');
$hoja_activa->getRowDimension('1')->setRowHeight(30);
$hoja_activa->getStyle('A3:H3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('dfe6f2');
$hoja_activa->getStyle('A1:H1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('007bcc');
$hoja_activa->getStyle('A1:H1')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE); 

                $index=3;

                $hoja_activa->getColumnDimension('A')->setWidth(8);
                $hoja_activa->setCellValue('A'.$index, "Folio");
                $hoja_activa->getColumnDimension('B')->setWidth(17);
                $hoja_activa->setCellValue('B'.$index, "Fecha");
                $hoja_activa->getColumnDimension('C')->setWidth(50);
                $hoja_activa->setCellValue('C'.$index, 'Cliente');
                $hoja_activa->getColumnDimension('D')->setWidth(18);
                $hoja_activa->setCellValue('D'.$index, 'Tipo');
                $hoja_activa->getColumnDimension('E')->setWidth(18);
                $hoja_activa->setCellValue('E'.$index, 'Estatus');
                $hoja_activa->getColumnDimension('F')->setWidth(25);
                $hoja_activa->setCellValue('F'.$index, 'Vendedor');
                $hoja_activa->getColumnDimension('G')->setWidth(20);
                $hoja_activa->setCellValue('G'.$index, 'Total');
                $hoja_activa->getColumnDimension('H')->setWidth(20);
                $hoja_activa->setCellValue('H'.$index, 'Sucursal');
              

         
                $hoja_activa->getStyle('A'.$index.':H'.$index)->getFont()->setBold(true);
                $hoja_activa->getRowDimension('2')->setRowHeight(20);
                $hoja_activa->getStyle('A'.$index.':H'.$index)->getAlignment()->setHorizontal('center');
                $hoja_activa->getStyle('A'.$index.':H'.$index)->getAlignment()->setVertical('center');

                
               // $comisiones =  obtenerComisiones($con, $id_sucursal, $fecha_inicio, $fecha_final);
               $comisiones = informacionFiltros($con);
                /* echo json_encode($comisiones);
                die(); */

                $bandera = 0;
                $sumatoria =0;
                $sumatoria_ventas = 0;
                $sumatoria_creditos = 0;
               
if(count($comisiones['data']) > 0) {

    $index++;
    
            foreach ($comisiones['data'] as $key => $value) {
                
                $folio = $value['id'];
                $fecha = $value['Fecha'];
                $cliente = $value['cliente'];
                $tipo = $value['tipo'];
                $estatus = $value['estatus'];
                $vendedor = $value['vendedor'];
                $importe_sin_servicio = $value['importe_sin_servicio'];
                $sucursal = $value['sucursal'];
               
                if($importe_sin_servicio !=null){
                    
                    if($tipo=='Credito' && $bandera==0){
                        $bandera=1;
                        $index++;
                        $hoja_activa->setCellValue('F'.$index, 'Sumatoria ventas sin serv');
                        $hoja_activa->setCellValue('G'.$index, $sumatoria_ventas);
                        $index++;
                        $comision_venta = ($sumatoria_ventas * floatval($porcentaje_comision))/100;
                        $hoja_activa->setCellValue('F'.$index, 'Ganacia comisión ('.$porcentaje_comision.'%): ');
                        $hoja_activa->setCellValue('G'.$index, $comision_venta);
                        $hoja_activa->getStyle('F'.($index-1).':F'.$index)->getFont()->setBold(true);

                        $sumatoria_creditos=0;
                        $index+=3;
                        $hoja_activa->mergeCells("A$index:H$index");
                        $hoja_activa->setCellValue('A'.$index,'Ventas de creditos pagadas asesor: '.$nombre_asesor . ' ' . $apellido_asesor .' | Llantera el rayo ');
                        $hoja_activa->mergeCells('A'.($index+1).':D'.($index+1));
                        $hoja_activa->mergeCells('E'.($index+1).':J'.($index+1));
                        $hoja_activa->setCellValue('A'.($index+1), 'Sucursal: '.$nombre_sucursal);
                        $hoja_activa->setCellValue('E'.($index+1), 'Mes de '. $mes_letra);
                        $hoja_activa->getStyle('A'.($index).':J'.($index+1))->getAlignment()->setHorizontal('center');
                        $hoja_activa->getStyle('A'.$index.':J'.($index+1))->getAlignment()->setVertical('center');
                        $hoja_activa->getRowDimension($index)->setRowHeight(30);
                        $hoja_activa->getStyle('A'.($index+2).':J'.($index+2))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('dfe6f2');
                        $hoja_activa->getStyle('A'.($index).':J'.($index))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('007bcc');
                        $hoja_activa->getStyle('A'.$index.':J'.$index)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE); 

                        $index+=2;

                        $hoja_activa->getColumnDimension('A')->setWidth(8);
                        $hoja_activa->setCellValue('A'.$index, "Folio");
                        $hoja_activa->getColumnDimension('B')->setWidth(17);
                        $hoja_activa->setCellValue('B'.$index, "Fecha Apertura");
                        $hoja_activa->getColumnDimension('C')->setWidth(17);
                        $hoja_activa->setCellValue('C'.$index, "Fecha ult. abono");
                        $hoja_activa->setCellValue('D'.$index, 'Cliente');
                        $hoja_activa->getColumnDimension('E')->setWidth(18);
                        $hoja_activa->setCellValue('E'.$index, 'Tipo');
                        $hoja_activa->getColumnDimension('F')->setWidth(18);
                        $hoja_activa->setCellValue('F'.$index, 'Estatus');
                        $hoja_activa->getColumnDimension('G')->setWidth(25);
                        $hoja_activa->setCellValue('G'.$index, 'Vendedor');
                        $hoja_activa->getColumnDimension('H')->setWidth(20);
                        $hoja_activa->setCellValue('H'.$index, 'Total');
                        $hoja_activa->getColumnDimension('I')->setWidth(20);
                        $hoja_activa->setCellValue('I'.$index, 'Sucursal');
                        $hoja_activa->getColumnDimension('J')->setWidth(20);
                        $hoja_activa->setCellValue('J'.$index, 'Dias transcurridos pago');
         
                        $hoja_activa->getStyle('A'.$index.':J'.$index)->getFont()->setBold(true);
                        $hoja_activa->getRowDimension('2')->setRowHeight(20);
                        $hoja_activa->getStyle('A'.$index.':J'.$index)->getAlignment()->setHorizontal('center');
                        $hoja_activa->getStyle('A'.$index.':J'.$index)->getAlignment()->setVertical('center');
                        $index++;
                       
                    }else{

                        if($bandera==0){
                            $sumatoria_ventas += floatval($value['importe_sin_servicio']);
                        }
                    }
                    
                    
                    $hoja_activa->getColumnDimension('A')->setWidth(8);
                    $hoja_activa->setCellValue('A'.$index, $folio);
                    $hoja_activa->getColumnDimension('B')->setWidth(17);
                    $hoja_activa->setCellValue('B'.$index, $fecha);
                    if($bandera==0){
                        $hoja_activa->getColumnDimension('C')->setWidth(50);
                        $hoja_activa->setCellValue('C'.$index, $cliente);
                        $hoja_activa->getColumnDimension('D')->setWidth(18);
                        $hoja_activa->setCellValue('D'.$index, $tipo);
                        $hoja_activa->getColumnDimension('E')->setWidth(18);
                        $hoja_activa->setCellValue('E'.$index, $estatus);
                        $hoja_activa->getColumnDimension('F')->setWidth(25);
                        $hoja_activa->setCellValue('F'.$index, $vendedor);
                        $hoja_activa->getColumnDimension('G')->setWidth(20);
                        $hoja_activa->setCellValue('G'.$index, $importe_sin_servicio);
                        $hoja_activa->getColumnDimension('H')->setWidth(20);
                        $hoja_activa->setCellValue('H'.$index, $sucursal);
                        $hoja_activa->getStyle('A'.$index.':H'.$index)->getAlignment()->setHorizontal('center');
                        $hoja_activa->getStyle('A'.$index.':H'.$index)->getAlignment()->setVertical('center');
                        //$hoja_activa->getStyle('A'.$index.':F'.$index)->getFont()->setBold(true);
                        $hoja_activa->getStyle('A'.$index.':H'.$index)->getFont()->setSize(12);
                        $hoja_activa->getStyle('A'.$index.':H'.$index)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);
                        
                    }else{

                        $fecha_inicio_ = new DateTime($value['Fecha']);
                        $fecha_ultimo_abono_ = new DateTime($value['fecha_ultimo_abono']);

                        // Obtener la diferencia en días entre las dos fechas
                        $intervalo = $fecha_inicio_->diff($fecha_ultimo_abono_);
                        $dias_transcurridos = $intervalo->days;
                        /* print_r($dias_transcurridos);
                        die(); */
                        // Verificar si no han pasado más de 45 días desde la última vez que se abonó
                        $fecha_ultimo_abono = $value['fecha_ultimo_abono'];
                        //print_r($value['Fecha'] . ' * ' . $value['fecha_ultimo_abono'] . ' = '.$dias_transcurridos .' dias transcurridos<br>');
                        //print_r($dias_transcurridos .' - ' );

                        $fecha_ultimo_abono = $value['fecha_ultimo_abono'];
                        if ($dias_transcurridos <= 45) {
                        $hoja_activa->getColumnDimension('C')->setWidth(17);
                        $hoja_activa->setCellValue('C'.$index, $fecha_ultimo_abono);
                        $hoja_activa->setCellValue('D'.$index, $cliente);
                        $hoja_activa->getColumnDimension('E')->setWidth(18);
                        $hoja_activa->setCellValue('E'.$index, $tipo);
                        $hoja_activa->getColumnDimension('F')->setWidth(18);
                        $hoja_activa->setCellValue('F'.$index, $estatus);
                        $hoja_activa->getColumnDimension('G')->setWidth(25);
                        $hoja_activa->setCellValue('G'.$index, $vendedor);
                        $hoja_activa->getColumnDimension('H')->setWidth(20);
                        $hoja_activa->setCellValue('H'.$index, $importe_sin_servicio);
                        $hoja_activa->getColumnDimension('I')->setWidth(20);
                        $hoja_activa->setCellValue('I'.$index, $sucursal);
                        $hoja_activa->getColumnDimension('J')->setWidth(20);
                        $hoja_activa->setCellValue('J'.$index, $dias_transcurridos . ' dias');
                        $hoja_activa->getStyle('A'.$index.':J'.$index)->getAlignment()->setHorizontal('center');
                        $hoja_activa->getStyle('A'.$index.':J'.$index)->getAlignment()->setVertical('center');
                        //$hoja_activa->getStyle('A'.$index.':F'.$index)->getFont()->setBold(true);
                        $hoja_activa->getStyle('A'.$index.':J'.$index)->getFont()->setSize(12);
                        $hoja_activa->getStyle('A'.$index.':J'.$index)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);
                        
                        if($dias_transcurridos>30){
                            $hoja_activa->getStyle('A'.($index).':J'.($index))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('ffc302');
                            //$hoja_activa->getStyle('A'.$index.':J'.$index)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                        }
                        }else{
                            $index--;
                            //echo "Han pasado más de 45 días desde el último abono.";
                        }
                    }
                    $sumatoria_creditos += intval($importe_sin_servicio);
                    $index++;
                }
            }
            
            if($bandera == 0) {
                $sumatoria_creditos =0;
                $comision_creditos =0;
            }else{
                $index++;
                $hoja_activa->setCellValue('F'.$index, 'Sumatoria creditos pagados sin serv');
                $hoja_activa->setCellValue('G'.$index, $sumatoria_creditos);
                $index++;
                $comision_creditos = ($sumatoria_creditos * floatval($porcentaje_comision_credito))/100;
                $hoja_activa->setCellValue('F'.$index, 'Ganacia comisión creditos ('.$porcentaje_comision_credito.'%): ');
                $hoja_activa->setCellValue('G'.$index, $comision_creditos);
                $hoja_activa->getStyle('F'.($index-1).':F'.$index)->getFont()->setBold(true);  
            }
            

            $index+=2;
            $sumatoria_total = $sumatoria_ventas + $sumatoria_creditos;
            $hoja_activa->setCellValue('F'.$index, 'Sumatoria total sin serv');
            $hoja_activa->setCellValue('G'.$index, $sumatoria_total);
            $index++;

            $comision_venta = ($sumatoria_ventas * floatval($porcentaje_comision))/100;
            $comision_total = $comision_venta + $comision_creditos;
            $hoja_activa->setCellValue('F'.$index, 'Total comisión: (Venta:'.$porcentaje_comision.'%)(Credito: '.$porcentaje_comision_credito.'): ');
            $hoja_activa->setCellValue('G'.$index, $comision_total);
            $hoja_activa->getStyle('F'.($index-1).':F'.$index)->getFont()->setBold(true);

            $hoja_activa->getStyle('C4:C'.$index)->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
            $hoja_activa->getStyle('F4:H'.$index)->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
      
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte de comisiones sucursal '. $nombre_sucursal.'.xlsx"');
        header('Cache-Control: max-age=0'); 

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        
        $writer->save('php://output');
        
 //Funcion que emulara el get_result-----------------*
 
 function Arreglo_Get_Result( $Statement ) {
    $RESULT = array();
    $Statement->store_result();
    for ( $i = 0; $i < $Statement->num_rows; $i++ ) {
        $Metadata = $Statement->result_metadata();
        $PARAMS = array();
        while ( $Field = $Metadata->fetch_field() ) {
            $PARAMS[] = &$RESULT[ $i ][ $Field->name ];
        }
        call_user_func_array( array( $Statement, 'bind_result' ), $PARAMS );
        $Statement->fetch();
    } 
    return $RESULT;
}


//Funcion para obtener comisiones
function obtenerComisiones($con, $id_sucursal, $fecha_inicio, $fecha_final){

    $total_venta_metodo = 0;
    $consulta = "SELECT * FROM usuarios WHERE id_sucursal=?";
    $res = $con->prepare($consulta);
    $res->bind_param("s", $id_sucursal);
    $res->execute();
    $usuarios_arreglo = Arreglo_Get_Result($res);
    $res->fetch();
    $res->close();

    if(count($usuarios_arreglo)>0){
        $index = 0;
        $comisiones = array();
        foreach($usuarios_arreglo as $usuario){ 
            $id_usuario = $usuario['id'];
            $nombre = $usuario['nombre'];
            $apellidos = $usuario['apellidos'];
            $porcentaje_comision = $usuario['comision'];
            $nombre_completo = $nombre . ' ' . $apellidos;
            $comision = obtenerComision($con, $fecha_inicio, $fecha_final, $id_usuario, $porcentaje_comision);
            $numero_ventas = $comision['numero_ventas'];
            $total_venta = $comision['total_venta'];
            $comision_valor = $comision['comision'];
            $total_pagado_contado = $comision['total_pagado_contado'];
            $total_pagado_credito = $comision['total_pagado_credito'];
            $comisiones[$index] = array('nombre'=>$nombre_completo, 
                                        'comision'=>$comision_valor, 
                                        'numero_ventas'=>$numero_ventas, 
                                        'total_venta'=>$total_venta, 
                                        'porcentaje_comision'=>$porcentaje_comision,
                                        'total_pagado_contado'=>$total_pagado_contado,
                                        'total_pagado_credito'=>$total_pagado_credito);
                                        
            $index++;
        }
        return $comisiones;
    }else{
        
        return null;
    }
}


function obtenerComision($con, $fecha_inicio, $fecha_final, $id_usuario, $porcentaje_comision){
    $comision = 0;
    $consulta = "SELECT id, Nombre_Cliente, id_asesor FROM clientes WHERE id_asesor = ?";
    $res = $con->prepare($consulta);
    $res->bind_param("s", $id_usuario);
    $res->execute();
    $arreglo_id_clientes = Arreglo_Get_Result($res);
    $res->close();
    
    if($arreglo_id_clientes != null && count($arreglo_id_clientes)>0){
        $ids_clientes = array_merge(array_column($arreglo_id_clientes, 'id'), [0]);
        $id_clientes_implode = implode(',', $ids_clientes);
        $placeholders = implode(',', array_fill(0, count($ids_clientes), '?'));

   
        $consulta = "SELECT id, Total, tipo FROM ventas WHERE Fecha BETWEEN ? AND ? AND id_Cliente IN ($placeholders) AND estatus = 'Pagado'";
        $res = $con->prepare($consulta);
        // Añade dos elementos adicionales al array de parámetros para la fecha_inicio y fecha_final
        $params = array_merge([$fecha_inicio, $fecha_final], $ids_clientes);

        // Añade los tipos de datos de los parámetros
        $tipos = str_repeat('s', count($params));
        $res->bind_param($tipos, ...$params);
        $res->execute();
        $comision_data = Arreglo_Get_Result($res);
        $res->close();
        
        $comision_asesor =0;
        $total_venta = 0;
        $total_pagado_contado = 0;
        $total_pagado_credito = 0;
        $numero_ventas = count($comision_data);
        
        foreach($comision_data as $comision) {
            $unidad = "";
            $id_venta = $comision["id"];
            $tipo_venta = $comision["tipo"];
            $consulta_unidad = "SELECT Unidad, Importe FROM detalle_venta WHERE id_Venta = ?";
            $resu = $con->prepare($consulta_unidad);
            $resu->bind_param("s", $id_venta);
            $resu->execute();
            $unidad_data = Arreglo_Get_Result($resu);
            $resu->close();
            
            if($unidad_data != null && count($unidad_data)>0){
                foreach($unidad_data as $unidad_el){
                    $unidad = $unidad_el["Unidad"];
                    
                    if($unidad == "pieza"){
                        $total_venta += (double)$unidad_el["Importe"];
                        if($tipo_venta == "Normal") {
                            $total_pagado_contado += (float)$unidad_el["Importe"];
                        }else if($tipo_venta == "Credito"){
                            $total_pagado_credito += (float)$unidad_el["Importe"];
                        }
                    }else{
                        $total_venta += 0;

                    }
                }
            }
            //$total_venta += (double)$comision["Total"];
            
        }
        
        $comision_asesor = $total_venta * ($porcentaje_comision/100);

        $res = array('numero_ventas'=>$numero_ventas, 'comision'=>$comision_asesor, 'total_venta'=>$total_venta, 'total_pagado_contado'=>$total_pagado_contado, 'total_pagado_credito'=>$total_pagado_credito);
    }else{
        $res = array('numero_ventas'=>0, 'comision'=>0, 'total_venta'=>0, 'total_pagado_contado'=>0, 'total_pagado_credito'=>0);
    }
    return $res;
}

function informacionFiltros($con)
{

    //Funcion que sanitiza entradas
    $sanitizacion = function ($valor, $con) {
        $valor_ = $con->real_escape_string($valor);
        $valor_str = "'" . $valor_ . "'";
        return $valor_str; 
    };

    $mes = isset($_GET['mes']) ? $_GET['mes'] : '';
    $year = isset($_GET['year']) ? $_GET['year'] : '';
    $sucursal = isset($_GET['id_sucursal']) ? $_GET['id_sucursal'] : '';
    $asesor = isset($_GET['id_asesor']) ? $_GET['id_asesor'] : [];
    $vendedor = isset($_GET['vendedor']) ? $_GET['vendedor'] : [];
   

    
    $tipo = 'Normal,Apartado,Pedido';
    $estatus = 'Pagado';

    // Define la consulta SQL base
    $sql = "SELECT DISTINCT v.*, concat(u.nombre, ' ', u.apellidos) vendedor, c.Nombre_Cliente as cliente, 
    (SELECT SUM(dv.Importe) FROM detalle_venta dv WHERE dv.Unidad = 'pieza' AND dv.id_Venta = v.id) AS 'importe_sin_servicio' 
    FROM ventas v INNER JOIN usuarios u ON v.id_Usuarios = u.id INNER JOIN clientes c ON v.id_Cliente = c.id WHERE 1=1";

    $sql_creditos = "SELECT DISTINCT v.*, concat(u.nombre, ' ', u.apellidos) vendedor, c.Nombre_Cliente as cliente, 
    a.fecha AS 'fecha_ultimo_abono',
    (SELECT SUM(dv.Importe) FROM detalle_venta dv WHERE dv.Unidad = 'pieza' AND dv.id_Venta = v.id) AS 'importe_sin_servicio'
    FROM ventas v INNER JOIN usuarios u ON v.id_Usuarios = u.id INNER JOIN clientes c ON v.id_Cliente = c.id 
    INNER JOIN creditos cr ON v.id = cr.id_venta INNER JOIN abonos a ON a.id_credito = cr.id WHERE 1=1 AND v.estatus ='Pagado' AND a.estado = 1"; 



    if (!empty($mes)) {
        //$fecha_limite = date('Y-m-d', strtotime("$fecha_inicial +45 days"));
        if($mes ==1 || $mes ==2){
            if($mes==1){
                $mes_anterior_2 = 11;
            }else if($mes==2){
                $mes_anterior_2 = 12;
            }
        }else{
            $mes_anterior_2 = $mes -2;
        }
       
        $sql .= " AND MONTH(v.fecha) = " . $mes . "";
        $sql_creditos .= " AND MONTH(a.fecha) = ". $mes ." AND MONTH(cr.fecha_inicio) IN (" . $mes_anterior_2 . ", " . ($mes == 1 ? 12 : $mes - 1) . ", " . $mes . ")";
    }
    if (!empty($year)) { 
        if($mes ==1 || $mes ==2){
            $year_credito = $year-1;
            $sql_creditos .= " AND YEAR(v.fecha) BETWEEN " . $year_credito . " AND ". $year;
        }else{
            $year_credito=$year;
            $sql_creditos .= " AND YEAR(v.fecha) = " . $year_credito . "";
        }
        $sql .= " AND YEAR(v.fecha) = " . $year . "";
       
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
        if($_SESSION['id_usuario']==6 || in_array(6, $array_ases)){ //Usuario de aminta se bloquearan las ventas fuera de su sucursal en filtro de asesor
            $sql .= " AND v.id_sucursal = 1 AND c.id_asesor IN (" . $asesores_ids. ")";
            $sql_creditos .= " AND v.id_sucursal = 1 AND c.id_asesor IN (" . $asesores_ids. ")";
        }else{
            $sql .= " AND c.id_asesor IN (" . $asesores_ids. ")";
            $sql_creditos .= " AND c.id_asesor IN (" . $asesores_ids. ")";
        }
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

    print_r($sql_creditos);
    die();
  
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

    $res = array('query' => $sql, 'query_creditos'=> $sql_creditos, 'post' => $_POST, 'data' => $data_combinada, 'estatus' => $estatus, 'mensaje' => $mensaje, 'numero_resultados' => $total_resultados);
    return $res;

}

