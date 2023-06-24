<?php
include '../conexion.php';
$con = $conectando->conexion();
session_start();

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
$id_sucursal = $_GET['id_sucursal'];
$fecha_inicio = $_GET['fecha_inicio'];
$fecha_final = $_GET['fecha_final'];
$spreadsheet = new SpreadSheet();
$spreadsheet->getProperties()->setCreator("Ricardo Reyna")->setTitle("reporte_comisiones");
$count=0;
$spreadsheet->setActiveSheetIndex($count);
$hoja_activa = $spreadsheet->getActiveSheet();

$hoja_activa->setTitle("Reporte de comisiones");


$consultaSucursal = "SELECT nombre FROM sucursal WHERE id = ?";
$resp = $con->prepare($consultaSucursal);
$resp->bind_param('s', $id_sucursal);
$resp->execute();
$resp->bind_result($nombre_sucursal);
$resp->fetch();
$resp->close();

$hoja_activa->mergeCells("A1:F1");
$hoja_activa->setCellValue('A1', 'Reporte de comisiones  de '.$nombre_sucursal . ' | Llantera el rayo ');
$hoja_activa->mergeCells("A2:D2");
$hoja_activa->mergeCells("E2:F2");
$hoja_activa->setCellValue('A2', 'Sucursal: '.$nombre_sucursal);
$hoja_activa->setCellValue('E2', 'Fechas - Desde: '. $fecha_inicio . " - Hasta: " . $fecha_final);
$hoja_activa->getStyle('A1:F2')->getAlignment()->setHorizontal('center');
$hoja_activa->getStyle('A1:F2')->getAlignment()->setVertical('center');
$hoja_activa->getRowDimension('1')->setRowHeight(30);
$hoja_activa->getStyle('A3:F3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('dfe6f2');
$hoja_activa->getStyle('A1:F1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('007bcc');
$hoja_activa->getStyle('A1:F1')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE); 

                $index=3;

                $hoja_activa->getColumnDimension('A')->setWidth(8);
                $hoja_activa->setCellValue('A'.$index, "#");
                $hoja_activa->getColumnDimension('B')->setWidth(40);
                $hoja_activa->setCellValue('B'.$index, "Nombre del vendedor");
                $hoja_activa->getColumnDimension('C')->setWidth(18);
                $hoja_activa->setCellValue('C'.$index, 'Comisión');
                $hoja_activa->getColumnDimension('D')->setWidth(18);
                $hoja_activa->setCellValue('D'.$index, 'Numero de ventas');
                $hoja_activa->getColumnDimension('E')->setWidth(18);
                $hoja_activa->setCellValue('E'.$index, 'Rango de fechas');
                $hoja_activa->getColumnDimension('F')->setWidth(20);
                $hoja_activa->setCellValue('F'.$index, 'Porcentaje de comisión');
              

         
                $hoja_activa->getStyle('A'.$index.':F'.$index)->getFont()->setBold(true);
                $hoja_activa->getRowDimension('2')->setRowHeight(20);
                $hoja_activa->getStyle('A'.$index.':F'.$index)->getAlignment()->setHorizontal('center');
                $hoja_activa->getStyle('A'.$index.':F'.$index)->getAlignment()->setVertical('center');

                
                $comisiones =  obtenerComisiones($con, $id_sucursal, $fecha_inicio, $fecha_final);
               /*  print_r($comisiones);
                die(); */

if($comisiones > 0) {

    $index++;
    $contador = 0;
            foreach ($comisiones as $key => $value) {
                
                $nombre = $value["nombre"];
                $comision = $value["comision"];
                $numero_ventas = $value["numero_ventas"];
                $total_venta = $value["total_venta"];
                $porcentaje_comision = isset($value["porcentaje_comision"]) ? $value["porcentaje_comision"] : 0;

                $hoja_activa->setCellValue('A'.$index, $contador);
                $hoja_activa->setCellValue('B'.$index, $nombre);
                $hoja_activa->setCellValue('C'.$index, $comision);
                $hoja_activa->setCellValue('D'.$index, $numero_ventas);
                $hoja_activa->setCellValue('F'.$index, $porcentaje_comision . "%");
                $hoja_activa->getStyle('A'.$index.':F'.$index)->getAlignment()->setHorizontal('center');
                $hoja_activa->getStyle('A'.$index.':F'.$index)->getAlignment()->setVertical('center');
                //$hoja_activa->getStyle('A'.$index.':F'.$index)->getFont()->setBold(true);
                $hoja_activa->getStyle('A'.$index.':F'.$index)->getFont()->setSize(12);
                $hoja_activa->getStyle('A'.$index.':F'.$index)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);
                
                $index++;
                $contador++;

            }
            $hoja_activa->getStyle('C4:C'.$index)->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte de venta diaria '. $nombre_sucursal .' '. $fecha_inicio .' - '. $fecha_final.'.xlsx"');
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
            $id_usuario = $usuario["id"];
            $nombre = $usuario["nombre"];
            $apellidos = $usuario["apellidos"];
            $porcentaje_comision = $usuario["comision"];
            $nombre_completo = $nombre . " " . $apellidos;
            $comision = obtenerComision($con, $fecha_inicio, $fecha_final, $id_usuario, $porcentaje_comision);
            $numero_ventas = $comision["numero_ventas"];
            $total_venta = $comision["total_venta"];
            $comision = $comision["comision"];
            $comisiones[$index] = array("nombre"=>$nombre_completo, "comision"=>$comision, "numero_ventas"=>$numero_ventas, "total_venta"=>$total_venta, "porcentaje_comision"=>$porcentaje_comision);
           // print_r($comisiones[$index]);
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

   
        $consulta = "SELECT id, Total FROM ventas WHERE Fecha BETWEEN ? AND ? AND id_Cliente IN ($placeholders) AND estatus = 'Pagado'";
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
        $numero_ventas = count($comision_data);
        foreach($comision_data as $comision) {
            $id_venta = $comision["id"];
            $total_venta += (double)$comision["Total"];
            
        }
        $comision_asesor = $total_venta * ($porcentaje_comision/100);

        $res = array('numero_ventas'=>$numero_ventas, 'comision'=>$comision_asesor, 'total_venta'=>$total_venta);
    }else{
        $res = array('numero_ventas'=>0, 'comision'=>0, 'total_venta'=>0);
    }
    return $res;
}
