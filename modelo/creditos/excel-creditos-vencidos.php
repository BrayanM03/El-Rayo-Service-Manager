<?php
include '../conexion.php';
$con = $conectando->conexion();

use PhpOffice\PhpSpreadsheet\helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\SpreadSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

require_once '../../vendor/phpoffice/phpspreadsheet/samples/Bootstrap.php'; 

date_default_timezone_set("America/Matamoros");
session_start(); 

$spreadsheet = new SpreadSheet();
$spreadsheet->getProperties()->setCreator("Alvaro M")->setTitle("creditos vencidos");
$count=0;
$spreadsheet->setActiveSheetIndex($count);
$hoja_activa = $spreadsheet->getActiveSheet();

$hoja_activa->setTitle("creditos vencidos");

//$hoja_activa->mergeCells("A1:B1");
        $hoja_activa->mergeCells("B1:G1");
        $hoja_activa->setCellValue('B1', 'Reporte total de creditos vencidos | Llantera el rayo');
        $hoja_activa->getStyle('B1')->getFont()->setBold(true);
        $hoja_activa->getStyle('B1')->getFont()->setSize(16);
        $hoja_activa->getRowDimension('1')->setRowHeight(50);
        $hoja_activa->getStyle('B1')->getAlignment()->setHorizontal('center');
        $hoja_activa->getStyle('B1')->getAlignment()->setVertical('center');
        $hoja_activa->getStyle('B1')->getAlignment()->setHorizontal('center');
        $hoja_activa->getStyle('B1')->getAlignment()->setVertical('center');

$index=3;
//cabezera

                $hoja_activa->getColumnDimension('A')->setWidth(8);
                $hoja_activa->setCellValue('A'.$index, "#");
                $hoja_activa->getColumnDimension('B')->setWidth(40);
                $hoja_activa->setCellValue('B'.$index, "Nombre del cliente");
                $hoja_activa->getColumnDimension('C')->setWidth(18);
                $hoja_activa->setCellValue('C'.$index, 'Pagado');
                $hoja_activa->getColumnDimension('D')->setWidth(18);
                $hoja_activa->setCellValue('D'.$index, 'Restante');
                $hoja_activa->getColumnDimension('E')->setWidth(18);
                $hoja_activa->setCellValue('E'.$index, 'Total');
                $hoja_activa->getColumnDimension('F')->setWidth(22);
                $hoja_activa->setCellValue('F'.$index, 'Estatus');
                $hoja_activa->getColumnDimension('G')->setWidth(18);
                $hoja_activa->setCellValue('G'.$index, 'Fecha de inicio');
                $hoja_activa->getColumnDimension('H')->setWidth(20);
                $hoja_activa->setCellValue('H'.$index, 'Fecha final');
                $hoja_activa->getColumnDimension('I')->setWidth(20);
                $hoja_activa->setCellValue('I'.$index, 'plazo');
                $hoja_activa->getColumnDimension('J')->setWidth(15);
                $hoja_activa->setCellValue('J'.$index, 'Venta');
                $hoja_activa->getColumnDimension('K')->setWidth(25);
                $hoja_activa->setCellValue('K'.$index, 'Sucursal');

                $hoja_activa->getStyle('A'.$index.':K'.$index)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('007bcc');
                $hoja_activa->getStyle('A'.$index.':K'.$index)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $hoja_activa->getStyle('A'.$index.':K'.$index)->getFont()->setBold(true);
                $hoja_activa->getRowDimension('2')->setRowHeight(20);
                $hoja_activa->getStyle('A'.$index.':K'.$index)->getAlignment()->setHorizontal('center');
                $hoja_activa->getStyle('A'.$index.':K'.$index)->getAlignment()->setVertical('center');

                $index++;

$consulta ="SELECT COUNT(*) FROM creditos WHERE estatus=4";
$res =$con->prepare($consulta);

$res->execute();
$res->bind_result($total_creditos);
$res->fetch();
$res->close();
//print_r("el total de creditos son: ".$total_creditos);

if($total_creditos>0)
{
    $traer_creditos = "SELECT c.*,s.nombre as nombre_sucursal, v.hora FROM creditos c
    LEFT JOIN ventas v ON v.id = c.id_venta INNER JOIN sucursal s ON v.id_sucursal =s.id WHERE c.estatus=4";
    $resp = $con->prepare($traer_creditos);
    $resp->execute();
    $respuesta = Arreglo_Get_Result($resp);
    $resp->close();
    
    $contador=1;
    foreach ($respuesta as $key => $value) {
        //$data[]=$value;
        $cliente_id=$value["id_cliente"];
        $pagado=$value["pagado"];
        $restante=$value["restante"];
        $sucursal = $value["nombre_sucursal"];
        $total=$value["total"];
       // $estatus=$value["estatus"];
       $estatus="Vencido";
       $plazo_numerico=$value["plazo"];
            switch ($plazo_numerico) {
               case '1':
                 $plazo="7 dias";
                break;
                case '2':
                    $plazo="15 dias";
                break;
                 case '3':
                     $plazo="1 mes";
                            
                 break;

                 case '4':
                 $plazo="1 año";
                 break;

                 case '5':
                    $plazo="7 dias";
                    break;

                case '6':
                    $plazos = '1 día';
                break;

                default:
                $plazo="Sin definir";
                    # code...
                    break;
            }

        $fecha_inicio=$value["fecha_inicio"] . ' ' . $value['hora'];
        $fecha_final=$value["fecha_final"]. ' ' . $value['hora'];
        
        $id_venta=$value["id_venta"];

        $consulta ="SELECT Nombre_Cliente FROM clientes WHERE id=?";
        $res =$con->prepare($consulta);
        $res->bind_param("i",$cliente_id);
        $res->execute();
        $res->bind_result($nombre_cliente);
        $res->fetch();
        $res->close();

        $hoja_activa->getColumnDimension('A')->setWidth(8);
        $hoja_activa->setCellValue('A'.$index, $contador);
        $hoja_activa->getColumnDimension('B')->setWidth(40);
        $hoja_activa->setCellValue('B'.$index, $nombre_cliente);
        $hoja_activa->getColumnDimension('C')->setWidth(18);
        $hoja_activa->setCellValue('C'.$index,"$". $pagado);
        $hoja_activa->getColumnDimension('D')->setWidth(18);
        $hoja_activa->setCellValue('D'.$index, "$". $restante);
        $hoja_activa->getColumnDimension('E')->setWidth(18);
        $hoja_activa->setCellValue('E'.$index, "$". $total);
        $hoja_activa->getColumnDimension('F')->setWidth(22);
        $hoja_activa->setCellValue('F'.$index, $estatus);
        $hoja_activa->getColumnDimension('G')->setWidth(18);
        $hoja_activa->setCellValue('G'.$index, $fecha_inicio);
        $hoja_activa->getColumnDimension('H')->setWidth(20);
        $hoja_activa->setCellValue('H'.$index, $fecha_final);
        $hoja_activa->getColumnDimension('I')->setWidth(20);
        $hoja_activa->setCellValue('I'.$index, $plazo);
        $hoja_activa->getColumnDimension('J')->setWidth(15);
        $hoja_activa->setCellValue('J'.$index, $id_venta);
        $hoja_activa->getColumnDimension('K')->setWidth(25);
        $hoja_activa->setCellValue('K'.$index, $sucursal);

        $contador++;
        $index++;
        //echo "Nombre: " .$nombre_cliente."---correo: " .$correo. 
        

    }
    //echo json_encode($respuesta);


}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte de creditos vencidos.xlsx"');
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
?>