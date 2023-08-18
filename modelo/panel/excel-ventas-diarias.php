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
$fecha = $_GET['fecha'];
$index =0;
$spreadsheet = new SpreadSheet();
$spreadsheet->getProperties()->setCreator("Ricardo Reyna")->setTitle("creditos vencidos");
$count=0;
$spreadsheet->setActiveSheetIndex($count);
$hoja_activa = $spreadsheet->getActiveSheet();

$hoja_activa->setTitle("Reporte de venta diaria");


$consultaSucursal = "SELECT nombre FROM sucursal WHERE id = ?";
$resp = $con->prepare($consultaSucursal);
$resp->bind_param('s', $id_sucursal);
$resp->execute();
$resp->bind_result($nombre_sucursal);
$resp->fetch();
$resp->close();

        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('LogoRayo');
        $drawing->setDescription('Logo');
        $drawing->setPath('../../src/img/logo.jpg'); // put your path and image here
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(40);
        $drawing->setWidth(80);
        $drawing->setHeight(63);
        $drawing->setWorksheet($hoja_activa);

//$hoja_activa->mergeCells("A1:B1");
        $hoja_activa->mergeCells("B1:G1");        
        $hoja_activa->setCellValue('B1', 'Reporte de ventas diarias de '.$nombre_sucursal . ' | Llantera el rayo ');
        $hoja_activa->getStyle('B1')->getFont()->setBold(true);
        $hoja_activa->getStyle('B1')->getFont()->setSize(16);
        $hoja_activa->getRowDimension('1')->setRowHeight(50);
        $hoja_activa->getStyle('B1')->getAlignment()->setHorizontal('center');
        $hoja_activa->getStyle('B1')->getAlignment()->setVertical('center');
        $hoja_activa->getStyle('B1')->getAlignment()->setHorizontal('center');
        $hoja_activa->getStyle('B1')->getAlignment()->setVertical('center');
        $hoja_activa->setCellValue('H1', "Fecha: ". $fecha);


//cabezera
                    
                        
                $hoja_activa->mergeCells("B3:C3");
                $hoja_activa->mergeCells("E3:K3");
                $hoja_activa->getColumnDimension('B')->setWidth(40);
                $hoja_activa->getStyle('B3:C3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('0088ff');
                $hoja_activa->getStyle('B3:C3')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
               

        $venta_total = obtenerVentaTotal($con, $id_sucursal, $fecha, "Normal", "Pagado");
        $utilidad_total = obtenerUtilidadTotal($con, $id_sucursal, $fecha, "Normal", "Pagado");


        $venta_metodo_efectivo = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, "Normal", "Pagado", "Efectivo");
        $venta_metodo_tarjeta = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, "Normal", "Pagado", "Tarjeta");
        $venta_metodo_cheque = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, "Normal", "Pagado", "Cheque");
        $venta_metodo_transferencia = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, "Normal", "Pagado", "Transferencia");
        $venta_metodo_por_definir = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, "Normal", "Pagado", "Por definir");

        $utilidad_metodo_efectivo = obtenerUtilidadMetodoPago($con, $id_sucursal, $fecha, "Normal", "Pagado", "Efectivo");
        $utilidad_metodo_tarjeta = obtenerUtilidadMetodoPago($con, $id_sucursal, $fecha, "Normal", "Pagado", "Tarjeta");
        $utilidad_metodo_cheque = obtenerUtilidadMetodoPago($con, $id_sucursal, $fecha, "Normal", "Pagado", "Cheque");
        $utilidad_metodo_transferencia = obtenerUtilidadMetodoPago($con, $id_sucursal, $fecha, "Normal", "Pagado", "Transferencia");
        $utilidad_metodo_por_definir = obtenerUtilidadMetodoPago($con, $id_sucursal, $fecha, "Normal", "Pagado", "Por definir");


        
        $estatus = 'Pagado';
        $tipo = 'Normal';
        $tipo_apartado = 'Apartado';
        $consultaVentas = "SELECT COUNT(*) FROM ventas WHERE id_sucursal=? AND fecha = ? AND estatus =? AND (tipo =? OR tipo =?)";
        $resp = $con->prepare($consultaVentas);
        $resp->bind_param('sssss', $id_sucursal, $fecha, $estatus, $tipo, $tipo_apartado);
        $resp->execute();
        $resp->bind_result($numero_ventas);
        $resp->fetch();
        $resp->close();
   

                $hoja_activa->getColumnDimension('F')->setWidth(40);
                $hoja_activa->setCellValue('B3', "Ventas totales");
                //$hoja_activa->setCellValue('F3', "Ganancias totales	");
                $hoja_activa->getStyle('B3:G3')->getFont()->setBold(true);
                $hoja_activa->setCellValue('B4', "Monto total de la venta:");
                $hoja_activa->setCellValue('C4', "$". $venta_total);
                $hoja_activa->setCellValue('B5', "Venta total en efectivo:");
                $hoja_activa->setCellValue('C5', "$". $venta_metodo_efectivo);
                $hoja_activa->setCellValue('B6', "Venta total en tarjeta:");  
                $hoja_activa->setCellValue('C6', "$". $venta_metodo_tarjeta);            
                $hoja_activa->setCellValue('B7', "Venta total en cheque:");
                $hoja_activa->setCellValue('C7', "$". $venta_metodo_cheque);
                $hoja_activa->setCellValue('B8', "Venta total en transferencia:");
                $hoja_activa->setCellValue('C8', "$". $venta_metodo_transferencia);
                $hoja_activa->setCellValue('B9', "Venta total sin definir:");
                $hoja_activa->setCellValue('C9', "$". $venta_metodo_por_definir);
                $hoja_activa->setCellValue('B10', "Ventas realizadas");
                $hoja_activa->setCellValue('C10', $numero_ventas);
                
                //Headers de los abonos de los apartados
                $hoja_activa->getStyle('E3:K3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('36c200');
                $hoja_activa->getStyle('E4:K4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4b4d4b');
                $hoja_activa->getStyle('E3:K3')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $hoja_activa->getStyle('E4:K4')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

                $hoja_activa->getColumnDimension('F')->setWidth(40);
                $hoja_activa->getColumnDimension('G')->setWidth(18);
                $hoja_activa->getColumnDimension('H')->setWidth(18);
                $hoja_activa->getColumnDimension('I')->setWidth(18);
                $hoja_activa->getColumnDimension('J')->setWidth(18);
                $hoja_activa->getColumnDimension('K')->setWidth(18);

                $hoja_activa->setCellValue('E3', 'Abonos realizados por apartado');
                $hoja_activa->setCellValue('E4', 'ID apartado');
                $hoja_activa->setCellValue('F4', 'Cliente');
                $hoja_activa->setCellValue('G4', 'Pago efectivo');
                $hoja_activa->setCellValue('H4', 'Pago tarjeta');
                $hoja_activa->setCellValue('I4', 'Pago transferencia');
                $hoja_activa->setCellValue('J4', 'Pago cheque');
                $hoja_activa->setCellValue('K4', 'Pago sin definir');

                $select_count = "SELECT COUNT(*) FROM abonos_apartados WHERE fecha = ? AND id_sucursal = ?";
                $re = $con->prepare($select_count);
                $re->bind_param('si', $fecha, $id_sucursal);
                $re->execute();
                $re->bind_result($numero_abonos);
                $re->fetch();
                $re->close();

                if($numero_abonos > 0){
                    $select_abono = "SELECT * FROM abonos_apartados WHERE fecha = '$fecha' AND id_sucursal = $id_sucursal";
                    $resp_abono = mysqli_query($con, $select_abono);
                    $index_ab = 5;
                    while ($fila = mysqli_fetch_array($resp_abono)) {
                        $id_apartado = $fila['id_apartado'];
                        $select_apa = "SELECT COUNT(*) FROM apartados WHERE id = ?";
                        $re_ = $con->prepare($select_apa);
                        $re_->bind_param('s', $id_apartado);
                        $re_->execute();
                        $re_->bind_result($numero_apartados);
                        $re_->fetch();
                        $re_->close();

                        if($numero_apartados>0){
                            $select_ap = "SELECT * FROM apartados WHERE id = $id_apartado";
                            $resp_ap = mysqli_query($con, $select_ap);
                            
                            while ($fila_ap = mysqli_fetch_array($resp_ap)) {
                                $id_cliente = $fila_ap['id_cliente'];
                                $select_cliente = "SELECT Nombre_Cliente FROM clientes WHERE id = ?";
                                $re__ = $con->prepare($select_cliente);
                                $re__->bind_param('i', $id_cliente);
                                $re__->execute();
                                $re__->bind_result($nombre_cliente);
                                $re__->fetch();
                                $re__->close();
                            }
                        }
                        $hoja_activa->setCellValue('E'.$index_ab, $fila['id_apartado']);
                        $hoja_activa->setCellValue('F'.$index_ab, $nombre_cliente);
                        $hoja_activa->setCellValue('G'.$index_ab, $fila['pago_efectivo']);
                        $hoja_activa->setCellValue('H'.$index_ab, $fila['pago_tarjeta']);
                        $hoja_activa->setCellValue('I'.$index_ab, $fila['pago_transferencia']);
                        $hoja_activa->setCellValue('J'.$index_ab, $fila['pago_cheque']);
                        $hoja_activa->setCellValue('K'.$index_ab, $fila['pago_sin_definir']);
                        $index_ab++;
                    }

                    $hoja_activa
                    ->getStyle('G5:K'.$index_ab)
                    ->getNumberFormat()
                    ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

                }else{
                    $hoja_activa->mergeCells('E5:K5');
                    $hoja_activa->setCellValue('E5', "Sin abonos realizados");
                    $hoja_activa->getStyle('E5:K5')->getAlignment()->setHorizontal('center');
                    $hoja_activa->getStyle('E5:K5')->getAlignment()->setVertical('center');
                }
                
                $hoja_activa->getStyle('A'.$index.':G'.$index)->getAlignment()->setHorizontal('center');
                $hoja_activa->getStyle('A'.$index.':G'.$index)->getAlignment()->setVertical('center');


                if($numero_abonos > 6){
                    $index=12 + $numero_abonos + 1;
                }else{
                    $index=12;
                }

                $hoja_activa->getColumnDimension('A')->setWidth(8);
                $hoja_activa->setCellValue('A'.$index, "#");
                $hoja_activa->getColumnDimension('B')->setWidth(40);
                $hoja_activa->setCellValue('B'.$index, "Nombre del cliente");
                $hoja_activa->getColumnDimension('C')->setWidth(18);
                $hoja_activa->setCellValue('C'.$index, 'Fecha');
                $hoja_activa->getColumnDimension('D')->setWidth(18);
                $hoja_activa->setCellValue('D'.$index, 'Total');
                $hoja_activa->getColumnDimension('E')->setWidth(18);
                $hoja_activa->setCellValue('E'.$index, 'Sucursal');
                $hoja_activa->getColumnDimension('F')->setWidth(40);
                $hoja_activa->setCellValue('F'.$index, 'Vendedor');
                $hoja_activa->getColumnDimension('G')->setWidth(18);
                $hoja_activa->setCellValue('G'.$index, 'Folio');
                $hoja_activa->getColumnDimension('H')->setWidth(20);
                $hoja_activa->setCellValue('H'.$index, 'Tipo');
                $hoja_activa->getColumnDimension('I')->setWidth(20);
                $hoja_activa->setCellValue('I'.$index, 'Metodo pago');
                $hoja_activa->getColumnDimension('J')->setWidth(20);
                $hoja_activa->setCellValue('J'.$index, 'Pago Efectivo');
                $hoja_activa->getColumnDimension('K')->setWidth(20);
                $hoja_activa->setCellValue('K'.$index, 'Pago Tarjeta');
                $hoja_activa->getColumnDimension('L')->setWidth(20);
                $hoja_activa->setCellValue('L'.$index, 'Pago Cheque');
                $hoja_activa->getColumnDimension('M')->setWidth(20);
                $hoja_activa->setCellValue('M'.$index, 'Pago Transferencia');
                $hoja_activa->getColumnDimension('N')->setWidth(20);
                $hoja_activa->setCellValue('N'.$index, 'Pago por definir');
                $hoja_activa->getColumnDimension('O')->setWidth(40);
                $hoja_activa->setCellValue('O'.$index, 'Comentario');

                $hoja_activa->getStyle('A'.$index.':O'.$index)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('007bcc');
                $hoja_activa->getStyle('A'.$index.':O'.$index)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $hoja_activa->getStyle('A'.$index.':O'.$index)->getFont()->setBold(true);
                $hoja_activa->getRowDimension('2')->setRowHeight(20);
                $hoja_activa->getStyle('A'.$index.':O'.$index)->getAlignment()->setHorizontal('center');
                $hoja_activa->getStyle('A'.$index.':O'.$index)->getAlignment()->setVertical('center');

                $index++;

                if($numero_ventas > 0){
                    $traer_venta = "SELECT * FROM ventas WHERE id_sucursal = '$id_sucursal' AND fecha = '$fecha' AND estatus ='$estatus' AND (tipo ='$tipo' OR tipo ='$tipo_apartado')";
                    $respo = mysqli_query($con, $traer_venta);

                    $contador = 1;
                    while ($fila = mysqli_fetch_array($respo)) {
                        $id_venta = $fila["id"];
                        $id_cliente = $fila["id_Cliente"];
                        $id_usuario = $fila["id_Usuarios"];
                        $sucursal = $fila["sucursal"];
                        $tipo = $fila["tipo"];
                        $metodo_pag = $fila["metodo_pago"];
                        $pago_efectivo = $fila["pago_efectivo"];
                        $pago_tarjeta = $fila["pago_tarjeta"];
                        $pago_cheque = $fila["pago_cheque"];
                        $pago_transferencia = $fila["pago_transferencia"];
                        $pago_por_definir = $fila["pago_sin_definir"];
                        $comentario = $fila["comentario"];
                        if($tipo== 'Apartado'){
                            $total_actual = floatval($pago_efectivo) + floatval($pago_tarjeta) + floatval($pago_cheque) + floatval($pago_transferencia) + floatval($pago_por_definir);
                        }else{
                            $total_actual = $fila["Total"];
                        }
                        
                        $consultaCliente = "SELECT Nombre_Cliente FROM clientes WHERE id =?";
                        $resp = $con->prepare($consultaCliente);
                        $resp->bind_param('s', $id_cliente);
                        $resp->execute();
                        $resp->bind_result($nombre_cliente);
                        $resp->fetch();
                        $resp->close();

                        $consultaUsuario = "SELECT nombre, apellidos FROM usuarios WHERE id =?";
                        $resp = $con->prepare($consultaUsuario);
                        $resp->bind_param('s', $id_usuario);
                        $resp->execute();
                        $resp->bind_result($nombre,$apellido);
                        $resp->fetch();
                        $resp->close();

                        
                $hoja_activa->getColumnDimension('A')->setWidth(8);
                $hoja_activa->setCellValue('A'.$index, $contador);
                $hoja_activa->getColumnDimension('B')->setWidth(40);
                $hoja_activa->setCellValue('B'.$index, $nombre_cliente);
                $hoja_activa->getColumnDimension('C')->setWidth(18);
                $hoja_activa->setCellValue('C'.$index, $fecha);
                $hoja_activa->getColumnDimension('D')->setWidth(18);
                $hoja_activa->setCellValue('D'.$index, "$".$total_actual);
                $hoja_activa->getColumnDimension('E')->setWidth(18);
                $hoja_activa->setCellValue('E'.$index, $sucursal);
                $hoja_activa->getColumnDimension('F')->setWidth(40);
                $hoja_activa->setCellValue('F'.$index, $nombre . " " .$apellido);
                $hoja_activa->getColumnDimension('G')->setWidth(18);
                $hoja_activa->setCellValue('G'.$index, "RAY".$id_venta);
                $hoja_activa->getColumnDimension('H')->setWidth(20);
                $hoja_activa->setCellValue('H'.$index, $tipo);
                $hoja_activa->getColumnDimension('I')->setWidth(20);
                $hoja_activa->setCellValue('I'.$index, $metodo_pag);
                $hoja_activa->getColumnDimension('J')->setWidth(20);
                $hoja_activa->setCellValue('J'.$index, "$".$pago_efectivo);
                $hoja_activa->getColumnDimension('K')->setWidth(20);
                $hoja_activa->setCellValue('K'.$index, "$".$pago_tarjeta);
                $hoja_activa->getColumnDimension('L')->setWidth(20);
                $hoja_activa->setCellValue('L'.$index, "$".$pago_cheque);
                $hoja_activa->getColumnDimension('M')->setWidth(20);
                $hoja_activa->setCellValue('M'.$index, "$".$pago_transferencia);
                $hoja_activa->getColumnDimension('N')->setWidth(20);
                $hoja_activa->setCellValue('N'.$index, "$".$pago_por_definir);
                $hoja_activa->getColumnDimension('O')->setWidth(40);
                $hoja_activa->setCellValue('O'.$index, $comentario);


                $index++;
                $contador++;
               

                    }
                }else{
                    $hoja_activa->mergeCells('A'.$index.':O'.$index);
                    $hoja_activa->setCellValue('A'.$index, "Sin ventas realizadas");
                    $hoja_activa->getStyle('A'.$index.':O'.$index)->getAlignment()->setHorizontal('center');
                    $hoja_activa->getStyle('A'.$index.':O'.$index)->getAlignment()->setVertical('center');
                }


                //Creando hoja de reporte de creditos -----
                $spreadsheet->createSheet();
                            
                $spreadsheet->setActiveSheetIndex(1);
                $hoja_activa = $spreadsheet->getActiveSheet();
                $hoja_activa->setTitle("Reporte de venta de creditos");

               
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('LogoRayo');
        $drawing->setDescription('Logo');
        $drawing->setPath('../../src/img/logo.jpg'); // put your path and image here
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(40);
        $drawing->setWidth(80);
        $drawing->setHeight(63);
        $drawing->setWorksheet($hoja_activa);
//$hoja_activa->mergeCells("A1:B1");
        $hoja_activa->mergeCells("B1:G1");        
        $hoja_activa->setCellValue('B1', 'Reporte de ventas a credito diarias de '. $nombre_sucursal . ' | Llantera el rayo ');
        $hoja_activa->getStyle('B1')->getFont()->setBold(true);
        $hoja_activa->getStyle('B1')->getFont()->setSize(16);
        $hoja_activa->getRowDimension('1')->setRowHeight(50);
        $hoja_activa->getStyle('B1')->getAlignment()->setHorizontal('center');
        $hoja_activa->getStyle('B1')->getAlignment()->setVertical('center');
        $hoja_activa->getStyle('B1')->getAlignment()->setHorizontal('center');
        $hoja_activa->getStyle('B1')->getAlignment()->setVertical('center');
        $hoja_activa->setCellValue('H1', "Fecha: ". $fecha);


//cabezera
                    
                        
                $hoja_activa->mergeCells("B3:C3");
                $hoja_activa->mergeCells("F3:G3");
                $hoja_activa->getColumnDimension('B')->setWidth(40);
                $hoja_activa->getStyle('B3:C3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('0088ff');
                $hoja_activa->getStyle('B3:C3')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                //$hoja_activa->getStyle('F3:G3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('36c200');
                $hoja_activa->getStyle('F3:G3')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);


        $venta_total = obtenerVentaTotal($con, $id_sucursal, $fecha, "Credito", "Abierta");
        $utilidad_total = obtenerUtilidadTotal($con, $id_sucursal, $fecha, "Credito", "Abierta");


        $venta_metodo_efectivo = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, "Credito", "Abierta", "Efectivo");
        $venta_metodo_tarjeta = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, "Credito", "Abierta", "Tarjeta");
        $venta_metodo_cheque = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, "Credito", "Abierta", "Cheque");
        $venta_metodo_transferencia = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, "Credito", "Abierta", "Transferencia");
        $venta_metodo_por_definir = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, "Credito", "Abierta", "Por definir");

        $utilidad_metodo_efectivo = obtenerUtilidadMetodoPago($con, $id_sucursal, $fecha, "Credito", "Abierta", "Efectivo");
        $utilidad_metodo_tarjeta = obtenerUtilidadMetodoPago($con, $id_sucursal, $fecha, "Credito", "Abierta", "Tarjeta");
        $utilidad_metodo_cheque = obtenerUtilidadMetodoPago($con, $id_sucursal, $fecha, "Credito", "Abierta", "Cheque");
        $utilidad_metodo_transferencia = obtenerUtilidadMetodoPago($con, $id_sucursal, $fecha, "Credito", "Abierta", "Transferencia");
        $utilidad_metodo_por_definir = obtenerUtilidadMetodoPago($con, $id_sucursal, $fecha, "Credito", "Abierta", "Por definir");


        
        $estatus = 'Pagado';
        $tipo = 'Normal';
        $consultaVentas = "SELECT COUNT(*) FROM ventas WHERE id_sucursal =? AND fecha = ? AND estatus =? AND (tipo =? OR tipo =?)";
        $resp = $con->prepare($consultaVentas);
        $resp->bind_param('sssss', $id_sucursal, $fecha, $estatus, $tipo, $tipo_apartado);
        $resp->execute();
        $resp->bind_result($numero_ventas);
        $resp->fetch();
        $resp->close();

                $hoja_activa->getColumnDimension('F')->setWidth(40);
                $hoja_activa->setCellValue('B3', "Ventas totales");
                //$hoja_activa->setCellValue('F3', "Ganancias totales	");
                $hoja_activa->getStyle('B3:G3')->getFont()->setBold(true);
                $hoja_activa->setCellValue('B4', "Monto total de la venta:");
                $hoja_activa->setCellValue('C4', "$". $venta_total);
                $hoja_activa->setCellValue('B5', "Venta total en efectivo:");
                $hoja_activa->setCellValue('C5', "$". $venta_metodo_efectivo);
                $hoja_activa->setCellValue('B6', "Venta total en tarjeta:");  
                $hoja_activa->setCellValue('C6', "$". $venta_metodo_tarjeta);            
                $hoja_activa->setCellValue('B7', "Venta total en cheque:");
                $hoja_activa->setCellValue('C7', "$". $venta_metodo_cheque);
                $hoja_activa->setCellValue('B8', "Venta total en transferencia:");
                $hoja_activa->setCellValue('C8', "$". $venta_metodo_transferencia);
                $hoja_activa->setCellValue('B9', "Venta total sin definir:");
                $hoja_activa->setCellValue('C9', "$". $venta_metodo_por_definir);
                $hoja_activa->setCellValue('B10', "Ventas realizadas");
                $hoja_activa->setCellValue('C10', $numero_ventas);
                
               /* $hoja_activa->setCellValue('F4', "Ganancia total de la venta:");
                $hoja_activa->setCellValue('G4', "$". $utilidad_total);
                $hoja_activa->setCellValue('F5', "Ganancia total en efectivo:");
                $hoja_activa->setCellValue('G5', "$". $utilidad_metodo_efectivo);
                $hoja_activa->setCellValue('F6', "Ganancia total en tarjeta:");
                $hoja_activa->setCellValue('G6', "$". $utilidad_metodo_tarjeta);
                $hoja_activa->setCellValue('F7', "Ganancia total en cheque:");
                $hoja_activa->setCellValue('G7', "$". $utilidad_metodo_cheque);              
                $hoja_activa->setCellValue('F8', "Ganancia total en transferencia:");
                $hoja_activa->setCellValue('G8', "$". $utilidad_metodo_transferencia);
                $hoja_activa->setCellValue('F9', "Ganancia total sin definir:");
                $hoja_activa->setCellValue('G9', "$". $utilidad_metodo_por_definir);
                
                $hoja_activa->getStyle('A'.$index.':G'.$index)->getAlignment()->setHorizontal('center');
                $hoja_activa->getStyle('A'.$index.':G'.$index)->getAlignment()->setVertical('center');*/


                $index=12;
                //Tabla de abonos
                $hoja_activa->mergeCells("A12:M12");        
                $hoja_activa->setCellValue('A12', "Abonos realizados");
                $hoja_activa->getStyle('A12:M12')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('950033');
                $hoja_activa->getStyle('A12:M12')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                 
                $hoja_activa->getStyle('A12:M12')->getAlignment()->setHorizontal('center');
                $hoja_activa->getStyle('A12:M12')->getAlignment()->setVertical('center');

                $index++;
                $hoja_activa->getColumnDimension('A')->setWidth(8);
                $hoja_activa->setCellValue('A'.$index, "#");
                $hoja_activa->getColumnDimension('B')->setWidth(40);
                $hoja_activa->setCellValue('B'.$index, "Nombre del cliente");
                $hoja_activa->getColumnDimension('C')->setWidth(18);
                $hoja_activa->setCellValue('C'.$index, 'Fecha');
                $hoja_activa->getColumnDimension('D')->setWidth(18);
                $hoja_activa->setCellValue('D'.$index, 'Abono');
                $hoja_activa->getColumnDimension('E')->setWidth(18);
                $hoja_activa->setCellValue('E'.$index, 'Sucursal');
                $hoja_activa->getColumnDimension('F')->setWidth(40);
                $hoja_activa->setCellValue('F'.$index, 'Vendedor');
                $hoja_activa->getColumnDimension('G')->setWidth(18);
                $hoja_activa->setCellValue('G'.$index, 'Credito');
                $hoja_activa->getColumnDimension('H')->setWidth(18);
                $hoja_activa->setCellValue('H'.$index, 'Metodo pago');
                $hoja_activa->getColumnDimension('I')->setWidth(18);
                $hoja_activa->setCellValue('I'.$index, 'Pago Efectivo');
                $hoja_activa->getColumnDimension('J')->setWidth(18);
                $hoja_activa->setCellValue('J'.$index, 'Pago Tarjeta');
                $hoja_activa->getColumnDimension('K')->setWidth(18);
                $hoja_activa->setCellValue('K'.$index, 'Pago Transferencia');
                $hoja_activa->getColumnDimension('L')->setWidth(18);
                $hoja_activa->setCellValue('L'.$index, 'Pago Cheque');
                $hoja_activa->getColumnDimension('M')->setWidth(18);
                $hoja_activa->setCellValue('M'.$index, 'Pago por definir');

                $hoja_activa->getStyle('A'.$index.':M'.$index)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('950033');
                $hoja_activa->getStyle('A'.$index.':M'.$index)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $hoja_activa->getStyle('A'.$index.':M'.$index)->getFont()->setBold(true);
                $hoja_activa->getRowDimension('2')->setRowHeight(20);
                $hoja_activa->getStyle('A'.$index.':M'.$index)->getAlignment()->setHorizontal('center');
                $hoja_activa->getStyle('A'.$index.':M'.$index)->getAlignment()->setVertical('center');
                $index++;

                $abonos_hoy = obtenerClientesqueAbonaron($con, $id_sucursal, $fecha);
                //echo json_encode($abonos_hoy, JSON_UNESCAPED_UNICODE);
                $contador2 = 1;
               
                if($abonos_hoy == 0){
                    $hoja_activa->mergeCells('A'.$index.':H'.$index);
                    $hoja_activa->setCellValue('A'.$index, 'Sin abonos realizados'); 
                             
                    $hoja_activa->getStyle('A'.$index.':H'.$index)->getAlignment()->setHorizontal('center');
                    $hoja_activa->getStyle('A'.$index.':H'.$index)->getAlignment()->setVertical('center');

                    $index++;
                    $contador2++; 
                }else{
                    foreach ($abonos_hoy as $key => $value) {
                        # code...
                            
                            $id_venta = !empty($value["id"]) ? $value["id"] : 0;
                            $cliente = $value["cliente"];
                            $fecha = $value["fecha"];
                            $abono = $value["abono"];
                            $sucursal = $value["sucursal"];
                            $usuario = $value["usuario"];
                            $id_credito = $value["id_credito"];
                            $metodo = $value["metodo"];
                            $pago_efectivo = $value["pago_efectivo"];
                            $pago_tarjeta = $value["pago_tarjeta"];
                            $pago_transferencia = $value["pago_transferencia"];
                            $pago_cheque = $value["pago_cheque"];
                            $pago_por_definir = $value["pago_sin_definir"];
                            
                            $hoja_activa->getColumnDimension('A')->setWidth(8);
                            $hoja_activa->setCellValue('A'.$index, $contador2);
                            $hoja_activa->getColumnDimension('B')->setWidth(40);
                            $hoja_activa->setCellValue('B'.$index, $cliente);
                            $hoja_activa->getColumnDimension('C')->setWidth(18);
                            $hoja_activa->setCellValue('C'.$index, $fecha);
                            $hoja_activa->getColumnDimension('D')->setWidth(18);
                            $hoja_activa->setCellValue('D'.$index, $abono);
                            $hoja_activa->getColumnDimension('E')->setWidth(18);
                            $hoja_activa->setCellValue('E'.$index, $sucursal);
                            $hoja_activa->getColumnDimension('F')->setWidth(40);
                            $hoja_activa->setCellValue('F'.$index, $usuario);
                            $hoja_activa->getColumnDimension('G')->setWidth(18);
                            $hoja_activa->setCellValue('G'.$index, "CRED".$id_credito);     
                            $hoja_activa->getColumnDimension('H')->setWidth(18);
                            $hoja_activa->setCellValue('H'.$index, $metodo);  
                            $hoja_activa->getColumnDimension('I')->setWidth(18);
                            $hoja_activa->setCellValue('I'.$index, "$".$pago_efectivo);
                            $hoja_activa->getColumnDimension('J')->setWidth(18);
                            $hoja_activa->setCellValue('J'.$index, "$".$pago_tarjeta);
                            $hoja_activa->getColumnDimension('K')->setWidth(18);
                            $hoja_activa->setCellValue('K'.$index, "$".$pago_cheque);
                            $hoja_activa->getColumnDimension('L')->setWidth(18);
                            $hoja_activa->setCellValue('L'.$index, "$".$pago_transferencia);
                            $hoja_activa->getColumnDimension('M')->setWidth(18);
                            $hoja_activa->setCellValue('M'.$index, "$".$pago_por_definir);      
                            $hoja_activa->getStyle('A'.$index.':M'.$index)->getAlignment()->setHorizontal('center');
                            $hoja_activa->getStyle('A'.$index.':M'.$index)->getAlignment()->setVertical('center');
    
                            $index++;
                            $contador2++;
                            
                        }
                }
                
                $index++;
                $index++;
                $hoja_activa->mergeCells("A". $index .":P". $index ."");        
                $hoja_activa->setCellValue('A'. $index , "Ventas realizadas a credito");
                $hoja_activa->getStyle('A'.$index.':P'.$index)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('007bcc');
                $hoja_activa->getStyle('A'.$index.':P'.$index)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $hoja_activa->getStyle('A'.$index.':P'.$index)->getFont()->setBold(true);
                $hoja_activa->getStyle('A'.$index.':P'.$index)->getAlignment()->setHorizontal('center');
                $hoja_activa->getStyle('A'.$index.':P'.$index)->getAlignment()->setVertical('center');
                $index++;
                $hoja_activa->getColumnDimension('A')->setWidth(8);
                $hoja_activa->setCellValue('A'.$index, "#");
                $hoja_activa->getColumnDimension('B')->setWidth(40);
                $hoja_activa->setCellValue('B'.$index, "Nombre del cliente");
                $hoja_activa->getColumnDimension('C')->setWidth(18);
                $hoja_activa->setCellValue('C'.$index, 'Fecha');
                $hoja_activa->getColumnDimension('D')->setWidth(18);
                $hoja_activa->setCellValue('D'.$index, 'Total');
                $hoja_activa->getColumnDimension('E')->setWidth(18);
                $hoja_activa->setCellValue('E'.$index, 'Sucursal');
                $hoja_activa->getColumnDimension('F')->setWidth(40);
                $hoja_activa->setCellValue('F'.$index, 'Vendedor');
                $hoja_activa->getColumnDimension('G')->setWidth(18);
                $hoja_activa->setCellValue('G'.$index, 'Estatus');
                $hoja_activa->getColumnDimension('H')->setWidth(18);
                $hoja_activa->setCellValue('H'.$index, 'Folio');
                $hoja_activa->getColumnDimension('I')->setWidth(20);
                $hoja_activa->setCellValue('I'.$index, 'Tipo');
                $hoja_activa->getColumnDimension('J')->setWidth(20);
                $hoja_activa->setCellValue('J'.$index, 'Metodo pago');
                $hoja_activa->getColumnDimension('K')->setWidth(20);
                $hoja_activa->setCellValue('K'.$index, 'Pago Efectivo');
                $hoja_activa->getColumnDimension('L')->setWidth(20);
                $hoja_activa->setCellValue('L'.$index, 'Pago Tarjeta');
                $hoja_activa->getColumnDimension('M')->setWidth(20);
                $hoja_activa->setCellValue('M'.$index, 'Pago Transferencia');
                $hoja_activa->getColumnDimension('N')->setWidth(20);
                $hoja_activa->setCellValue('N'.$index, 'Pago Cheque');
                $hoja_activa->getColumnDimension('O')->setWidth(20);
                $hoja_activa->setCellValue('O'.$index, 'Pago por Definir');
                $hoja_activa->getColumnDimension('O')->setWidth(40);
                $hoja_activa->setCellValue('P'.$index, 'Comentario');

                $hoja_activa->getStyle('A'.$index.':P'.$index)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('007bcc');
                $hoja_activa->getStyle('A'.$index.':P'.$index)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $hoja_activa->getStyle('A'.$index.':P'.$index)->getFont()->setBold(true);
                $hoja_activa->getRowDimension('2')->setRowHeight(20);
                $hoja_activa->getStyle('A'.$index.':P'.$index)->getAlignment()->setHorizontal('center');
                $hoja_activa->getStyle('A'.$index.':P'.$index)->getAlignment()->setVertical('center');

                $index++;


                $estatuscred = "Abierta";
                $tipocred = "Credito";
                $numero_ventas_cred =0;
                $consultaCred = "SELECT COUNT(*) FROM ventas WHERE id_sucursal=? AND fecha = ? AND estatus =? AND (tipo =? OR tipo = ?)";
                $respc = $con->prepare($consultaCred);
                $respc->bind_param('sssss', $id_sucursal, $fecha, $estatuscred, $tipocred, $tipo_apartado);
                $respc->execute();
                $respc->bind_result($numero_ventas_cred);
                $respc->fetch();
                $respc->close();


                if($numero_ventas_cred > 0){
                    $traer_venta = "SELECT * FROM ventas WHERE id_sucursal = '$id_sucursal' AND fecha = '$fecha' AND estatus ='$estatuscred' AND tipo ='$tipocred'";
                    $respo = mysqli_query($con, $traer_venta);

                    $contador = 1;
                    while ($fila = mysqli_fetch_array($respo)) {
                        $id_venta = $fila["id"];
                        $id_cliente = $fila["id_Cliente"];
                        $id_usuario = $fila["id_Usuarios"];
                        $sucursal = $fila["sucursal"];
                        $pago_efectivo = $fila["pago_efectivo"];
                        $pago_tarjeta = $fila["pago_tarjeta"];
                        $pago_cheque = $fila["pago_cheque"];
                        $pago_transferencia = $fila["pago_transferencia"];
                        $pago_por_definir = $fila["pago_sin_definir"];
                        $total_actual = $fila["Total"];
                        $metodo_p = $fila["metodo_pago"];
                        $venta_estatus = $fila["estatus"];
                        $comentario = $fila["comentario"];
                        
                        $consultaCliente = "SELECT Nombre_Cliente FROM clientes WHERE id =?";
                        $resp = $con->prepare($consultaCliente);
                        $resp->bind_param('s', $id_cliente);
                        $resp->execute();
                        $resp->bind_result($nombre_cliente);
                        $resp->fetch();
                        $resp->close();

                        $consultaUsuario = "SELECT nombre, apellidos FROM usuarios WHERE id =?";
                        $resp = $con->prepare($consultaUsuario);
                        $resp->bind_param('s', $id_usuario);
                        $resp->execute();
                        $resp->bind_result($nombre, $apellido);
                        $resp->fetch();
                        $resp->close();

                        
                $hoja_activa->getColumnDimension('A')->setWidth(8);
                $hoja_activa->setCellValue('A'.$index, $contador);
                $hoja_activa->getColumnDimension('B')->setWidth(40);
                $hoja_activa->setCellValue('B'.$index, $nombre_cliente);
                $hoja_activa->getColumnDimension('C')->setWidth(18);
                $hoja_activa->setCellValue('C'.$index, $fecha);
                $hoja_activa->getColumnDimension('D')->setWidth(18);
                $hoja_activa->setCellValue('D'.$index, "$".$total_actual);
                $hoja_activa->getColumnDimension('E')->setWidth(18);
                $hoja_activa->setCellValue('E'.$index, $sucursal);
                $hoja_activa->getColumnDimension('F')->setWidth(40);
                $hoja_activa->setCellValue('F'.$index, $nombre . " ".$apellido);
                $hoja_activa->getColumnDimension('G')->setWidth(18);
                $hoja_activa->setCellValue('G'.$index, $venta_estatus);
                $hoja_activa->getColumnDimension('H')->setWidth(18);
                $hoja_activa->setCellValue('H'.$index, "RAY".$id_venta);
                $hoja_activa->getColumnDimension('I')->setWidth(20);
                $hoja_activa->setCellValue('I'.$index, $tipocred);
                $hoja_activa->getColumnDimension('J')->setWidth(20);
                $hoja_activa->setCellValue('J'.$index, $metodo_p);
                $hoja_activa->getColumnDimension('K')->setWidth(20);
                $hoja_activa->setCellValue('K'.$index, "$".$pago_efectivo);
                $hoja_activa->getColumnDimension('L')->setWidth(20);
                $hoja_activa->setCellValue('L'.$index, "$".$pago_tarjeta);
                $hoja_activa->getColumnDimension('M')->setWidth(20);
                $hoja_activa->setCellValue('M'.$index, "$".$pago_cheque);
                $hoja_activa->getColumnDimension('N')->setWidth(20);
                $hoja_activa->setCellValue('N'.$index, "$".$pago_transferencia);
                $hoja_activa->getColumnDimension('O')->setWidth(20);
                $hoja_activa->setCellValue('O'.$index, "$".$pago_por_definir);
                $hoja_activa->getColumnDimension('P')->setWidth(40);
                $hoja_activa->setCellValue('P'.$index, $comentario);

                $index++;
                $contador++;

                }
                }else{
                    $hoja_activa->mergeCells('A'.$index.':P'.$index);
                    $hoja_activa->setCellValue('A'.$index, 'Sin ventas realizadas'); 
                             
                    $hoja_activa->getStyle('A'.$index.':P'.$index)->getAlignment()->setHorizontal('center');
                    $hoja_activa->getStyle('A'.$index.':P'.$index)->getAlignment()->setVertical('center');

                    $index++;
                 
                }

        //Creando hoja de reporte de totales -----
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(2);
        $hoja_activa = $spreadsheet->getActiveSheet();
        $hoja_activa->setTitle('Reporte de montos finales');
        $hoja_activa->mergeCells('B2:C3');
        $hoja_activa->mergeCells('D2:E2');
        $hoja_activa->mergeCells('D3:E3'); 
        $hoja_activa->getStyle('D2:E3')->getAlignment()->setHorizontal('center');
        $hoja_activa->getStyle('D2:E3')->getAlignment()->setVertical('center');
        $hoja_activa->getStyle('D2:E3')->getAlignment()->setHorizontal('center');
        $hoja_activa->getStyle('D4:E9')->getAlignment()->setVertical('center');
        $hoja_activa->getRowDimension('2')->setRowHeight(50);
        $hoja_activa->getRowDimension('3')->setRowHeight(30);
        $hoja_activa->getColumnDimension('D')->setWidth(50);
        $hoja_activa->getColumnDimension('E')->setWidth(30);
        $hoja_activa->setCellValue('D4', 'Pagado en efectivo');
        $hoja_activa->setCellValue('D5', 'Pagado en tarjeta');
        $hoja_activa->setCellValue('D6', 'Pagado en transferencia');
        $hoja_activa->setCellValue('D7', 'Pagado en cheque');
        $hoja_activa->setCellValue('D8', 'Pagado en sin definir');

        $hoja_activa->getStyle('D9')->getFont()->setSize(18);
        $hoja_activa->setCellValue('D9', 'Total final');
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('LogoRayo');
        $drawing->setDescription('Logo');
        $drawing->setPath('../../src/img/logo.jpg'); // put your path and image here
        $drawing->setCoordinates('B3');
        $drawing->setOffsetX(40);
        $drawing->setWidth(80);
        $drawing->setHeight(63);
        $drawing->setWorksheet($hoja_activa);

        $normal_metodo_efectivo = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, 'Normal', 'Pagado', 'Efectivo');
        $normal_metodo_tarjeta = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, 'Normal', 'Pagado', 'Tarjeta');
        $normal_metodo_cheque = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, 'Normal', 'Pagado', 'Cheque');
        $normal_metodo_transferencia = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, 'Normal', 'Pagado', 'Transferencia');
        $normal_metodo_por_definir = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, 'Normal', 'Pagado', 'Por definir');
        
        $credito_metodo_efectivo = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, 'Credito', 'Abierta', 'Efectivo');
        $credito_metodo_tarjeta = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, 'Credito', 'Abierta', 'Tarjeta');
        $credito_metodo_cheque = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, 'Credito', 'Abierta', 'Cheque');
        $credito_metodo_transferencia = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, 'Credito', 'Abierta', 'Transferencia');
        $credito_metodo_por_definir = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, 'Credito', 'Abierta', 'Por definir');
        
        $final_metodo_efectivo = $normal_metodo_efectivo + $credito_metodo_efectivo;
        $final_metodo_tarjeta = $normal_metodo_tarjeta + $credito_metodo_tarjeta;
        $final_metodo_transferencia = $normal_metodo_transferencia + $credito_metodo_transferencia;
        $final_metodo_cheque = $normal_metodo_cheque + $credito_metodo_cheque;
        $final_metodo_por_definir = $normal_metodo_por_definir + $credito_metodo_por_definir;
        
        $final_corte = $final_metodo_efectivo + $final_metodo_tarjeta + $final_metodo_transferencia + $final_metodo_cheque + $final_metodo_por_definir;

        $hoja_activa->getStyle('D2')->getFont()->setBold(true);
        $hoja_activa->getStyle('D2')->getFont()->setSize(16);
        $hoja_activa->setCellValue('D2', 'Reporte de montos finales');
        $hoja_activa->setCellValue('D3', $fecha . '  - Sucursal: ' . $nombre_sucursal);

        $hoja_activa->setCellValue('E4', $final_metodo_efectivo);
        $hoja_activa->setCellValue('E5', $final_metodo_tarjeta);
        $hoja_activa->setCellValue('E6', $final_metodo_transferencia);
        $hoja_activa->setCellValue('E7', $final_metodo_cheque);
        $hoja_activa->setCellValue('E8', $final_metodo_por_definir);
        $hoja_activa->getStyle('E9')->getFont()->setSize(18);
        $hoja_activa->setCellValue('E9', $final_corte);

        $hoja_activa->getRowDimension('4')->setRowHeight(25);
        $hoja_activa->getRowDimension('5')->setRowHeight(25);
        $hoja_activa->getRowDimension('6')->setRowHeight(25);
        $hoja_activa->getRowDimension('7')->setRowHeight(25);
        $hoja_activa->getRowDimension('8')->setRowHeight(25);
        $hoja_activa->getRowDimension('9')->setRowHeight(35);
        $hoja_activa
                ->getStyle('B2:F12')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('ffffff');

        $hoja_activa
                ->getStyle('D3')
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB('fee135');

        $hoja_activa->getStyle('B2:F12')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)
        ->setColor(new Color('18171c'));
        $hoja_activa->getStyle('D3:E3')->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)
        ->setColor(new Color('18171c'));
        $hoja_activa->getStyle('D4:E8')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_THIN)
        ->setColor(new Color('18171c'));

        $hoja_activa->getStyle('E4:E9')->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte de venta diaria '. $nombre_sucursal .' '. $fecha .'.xlsx"');
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


//Funciones para obtener utilidad de ventas
function obtenerUtilidadTotal($con, $id_sucursal, $fecha, $tipo, $estatus){

    $total_venta_metodo = 0;
    $consulta = "SELECT SUM(Total) FROM ventas WHERE id_sucursal=? AND fecha = ? AND tipo = ? AND estatus = ?";
    $res = $con->prepare($consulta);
    $res->bind_param("ssss", $id_sucursal, $fecha, $tipo, $estatus);
    $res->execute();
    $res->bind_result($total_venta_metodo);
    $res->fetch();
    $res->close();

    if($total_venta_metodo == null || $total_venta_metodo == ""){
        return 0;
    }else{

        
    $lista_ventas = mysqli_query($con, "SELECT * FROM ventas WHERE id_sucursal='$id_sucursal' AND 
                                                                   fecha = '$fecha' AND 
                                                                   tipo = '$tipo' AND 
                                                                   estatus = '$estatus'");
        //Iteramos sobre el arreglo de las ventas 
        $importe_acumulado =0; 
        $costo_acumulado =0;                                                            
        while ($row = mysqli_fetch_array($lista_ventas)){

        $id_venta = intval($row["id"]);

        $lista_llantas = mysqli_query($con, "SELECT * FROM detalle_venta WHERE id_Venta = '$id_venta'");

        //Iteramos sobre el arreglo de los detalles de venta

        while ($fila = mysqli_fetch_array($lista_llantas)){
        $costo_llanta = 0;
        $id_llanta = intval($fila["id_Llanta"]);
        $cantidad_llantas = intval($fila["Cantidad"]);
        $precio_unitario = floatval($fila["precio_Unitario"]);
        $total_importe = floatval($fila["Importe"]);
        $importe_acumulado =$importe_acumulado + $total_importe;
        
        $consulta = "SELECT precio_Inicial FROM llantas WHERE id=?";
        $resp = $con->prepare($consulta);
        $resp->bind_param("s", $id_llanta);
        $resp->execute();
        $resp->bind_result($costo_llanta);
        $resp->fetch();
        $resp->close();

        $costo_total = $costo_llanta * $cantidad_llantas;


        $costo_acumulado = $costo_acumulado + $costo_total;

        }


        }
    $utilidad_total = $importe_acumulado - $costo_acumulado;    
    return $utilidad_total;
    }

}

function obtenerUtilidadMetodoPago($con, $id_sucursal, $fecha, $tipo, $estatus, $metodo_pago){
    $total_venta_metodo = 0;
    
    switch($metodo_pago){
        case "Efectivo":
            $col = "pago_efectivo";
            break;
        case "Tarjeta":
            $col = "pago_tarjeta";
            break;
        case "Cheque":
            $col = "pago_cheque";
            break;
        case "Transferencia":
            $col = "pago_transferencia";
            break;
        case "Por definir":
            $col = "pago_sin_definir";
            break;

    }
    $consulta = "SELECT SUM($col) FROM ventas WHERE id_sucursal=? AND fecha = ? AND tipo = ? AND estatus = ?";
    $res = $con->prepare($consulta);
    $res->bind_param("ssss", $id_sucursal, $fecha, $tipo, $estatus);
    $res->execute();
    $res->bind_result($total_venta_metodo);
    $res->fetch();
    $res->close();

    if($total_venta_metodo == null || $total_venta_metodo == ""){
        return 0;
    }else{

        
    $lista_ventas = mysqli_query($con, "SELECT * FROM ventas WHERE id_sucursal='$id_sucursal' AND 
                                                                   fecha = '$fecha' AND 
                                                                   tipo = '$tipo' AND 
                                                                   estatus = '$estatus' AND 
                                                                   $col IS NOT NULL");
        //Iteramos sobre el arreglo de las ventas 
        $importe_acumulado =0; 
        $costo_acumulado =0;                                                            
        while ($row = mysqli_fetch_array($lista_ventas)){

        $id_venta = intval($row["id"]);

        $lista_llantas = mysqli_query($con, "SELECT * FROM detalle_venta WHERE id_Venta = '$id_venta'");

        //Iteramos sobre el arreglo de los detalles de venta

        while ($fila = mysqli_fetch_array($lista_llantas)){
        $costo_llanta = 0;
        $id_llanta = intval($fila["id_Llanta"]);
        $cantidad_llantas = intval($fila["Cantidad"]);
        $precio_unitario = floatval($fila["precio_Unitario"]);
        $total_importe = floatval($fila["Importe"]);
        $importe_acumulado =$importe_acumulado + $total_importe;
        
        $consulta = "SELECT precio_Inicial FROM llantas WHERE id=?";
        $resp = $con->prepare($consulta);
        $resp->bind_param("s", $id_llanta);
        $resp->execute();
        $resp->bind_result($costo_llanta);
        $resp->fetch();
        $resp->close();

        $costo_total = $costo_llanta * $cantidad_llantas;

        $costo_total = $costo_total;
        $costo_acumulado = $costo_acumulado + $costo_total;

        }


        }
    $utilidad_total = $importe_acumulado - $costo_acumulado;  
    return $utilidad_total;
    }
    
}

//Funciones para obtener ganancias
function obtenerVentaTotal($con, $id_sucursal, $fecha, $tipo, $estatus){
    $total_venta = 0;
    $total_venta_apartados = 0;
    if($tipo == "Credito"){
        $consulta = "SELECT SUM(abono) FROM abonos WHERE id_sucursal=? AND fecha = ?";
        $res = $con->prepare($consulta);
        $res->bind_param("ss", $id_sucursal, $fecha);
        $res->execute();
        $res->bind_result($total_venta);
        $res->fetch();
        $res->close();
    }else{
        $consulta = "SELECT SUM(Total) FROM ventas WHERE id_sucursal=? AND fecha = ? AND tipo = ? AND estatus = ?";
        $res = $con->prepare($consulta);
        $res->bind_param("ssss", $id_sucursal, $fecha, $tipo, $estatus);
        $res->execute();
        $res->bind_result($total_venta);
        $res->fetch();
        $res->close();

        $consulta = "SELECT SUM(abono) FROM abonos_apartados WHERE id_sucursal=? AND fecha = ?";
        $res = $con->prepare($consulta);
        $res->bind_param("ss", $id_sucursal, $fecha);
        $res->execute();
        $res->bind_result($total_venta_apartados);
        $res->fetch();
        $res->close();

        $total_venta = $total_venta + $total_venta_apartados;
    }
   

    if($total_venta == "" || $total_venta ==null){
        $total_venta = 0;
    }
    return $total_venta;
}

function obtenerVentaMetodoPago($con, $id_sucursal, $fecha, $tipo, $estatus, $metodo_pago){
    $total_venta = 0;
    $total_venta_apartados = 0;
    switch($metodo_pago){
        case "Efectivo":
            $col = "pago_efectivo";
            break;
        case "Tarjeta":
            $col = "pago_tarjeta";
            break;
        case "Cheque":
            $col = "pago_cheque";
            break;
        case "Transferencia":
            $col = "pago_transferencia";
            break;
        case "Por definir":
            $col = "pago_sin_definir";
            break;

    }

    if($tipo== 'Normal' || $tipo == 'Apartado'){
        $consulta = "SELECT SUM($col) FROM ventas WHERE id_sucursal=? AND fecha = ? AND tipo = ? AND estatus = ?";
        $res = $con->prepare($consulta);
        $res->bind_param("ssss", $id_sucursal, $fecha, $tipo, $estatus);
        $res->execute();
        $res->bind_result($total_venta);
        $res->fetch();
        $res->close();

        $consulta = "SELECT SUM($col) FROM abonos_apartados WHERE id_sucursal=? AND fecha = ?";
        $res = $con->prepare($consulta);
        $res->bind_param("ss", $id_sucursal, $fecha);
        $res->execute();
        $res->bind_result($total_venta_apartados);
        $res->fetch();
        $res->close();

        $total_venta = $total_venta + $total_venta_apartados;
    }else {
        $consulta = "SELECT SUM($col) FROM abonos WHERE id_sucursal=? AND fecha = ?";
        $res = $con->prepare($consulta);
        $res->bind_param("ss", $id_sucursal, $fecha);
        $res->execute();
        $res->bind_result($total_venta);
        $res->fetch();
        $res->close();
    }
    

    if($total_venta == "" || $total_venta ==null){
        $total_venta = 0;
    }
    return $total_venta;
}


function obtenerClientesqueAbonaron($con, $id_sucursal, $fecha)
{
    $traer_id = $con->prepare("SELECT * FROM `abonos` WHERE fecha =? AND id_sucursal =?");
    $traer_id->bind_param('ss', $fecha, $id_sucursal);
    $traer_id->execute();
    $resultado = $traer_id->get_result();
    $traer_id->close();

    if ($resultado->num_rows < 1) {
        return $resultado->num_rows;
    } else {
        while ($fila = $resultado->fetch_assoc()) {
            $id_cliente="";
            $id_credito = $fila['id_credito'];
            $abono = $fila['abono'];
            $sucurs = $fila['sucursal'];
            $metodo_pago = $fila['metodo_pago'];
            $pago_efectivo = isset($fila['pago_efectivo']) ? $fila['pago_efectivo']  :0;
            $pago_tarjeta = isset($fila['pago_tarjeta']) ? $fila['pago_tarjeta']: 0;
            $pago_cheque = isset($fila['pago_cheque']) ? $fila['pago_cheque']: 0;
            $pago_transferencia = isset($fila['pago_transferencia']) ? $fila['pago_transferencia']: 0;
            $pago_por_definir = isset($fila['pago_sin_definir']) ? $fila['pago_sin_definir']: 0;
            $usuario = $fila['usuario'];
            $id_credito = $fila['id_credito'];

            $traer_id = $con->prepare("SELECT id_Cliente FROM `creditos` WHERE id= ?");
            $traer_id->bind_param('s', $id_credito);
            $traer_id->execute();
            $traer_id->bind_result($id_cliente);
            $traer_id->fetch();
            $traer_id->close();

            $cliente="";
            $traer_id = $con->prepare("SELECT Nombre_Cliente FROM `clientes` WHERE id= ?");
            $traer_id->bind_param('s', $id_cliente);
            $traer_id->execute();
            $traer_id->bind_result($cliente);
            $traer_id->fetch();
            $traer_id->close();

            $arreglo[] = array("id_credito"=>$id_credito, 
            "metodo"=>$metodo_pago,
            "pago_efectivo"=>$pago_efectivo,
            "pago_tarjeta"=>$pago_tarjeta,
            "pago_cheque"=>$pago_cheque,
            "pago_transferencia"=>$pago_transferencia,
            "pago_sin_definir"=>$pago_por_definir,
            "cliente"=>$cliente, 
            "abono"=> $abono, "fecha"=> $fecha, "sucursal"=> $sucurs, "usuario"=>$usuario);
        
        };
    
        return $arreglo;
    }
}
