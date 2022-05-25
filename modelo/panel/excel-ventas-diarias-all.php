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
session_start(); 
$fecha = $_GET['fecha'];

$spreadsheet = new SpreadSheet();
$spreadsheet->getProperties()->setCreator("Ricardo Reyna")->setTitle("reporte");
$count=0;
$spreadsheet->setActiveSheetIndex($count);
$hoja_activa = $spreadsheet->getActiveSheet();

$hoja_activa->setTitle("Reporte de venta diaria");



//$hoja_activa->mergeCells("A1:B1");
        $hoja_activa->mergeCells("B1:G1");        
        $hoja_activa->setCellValue('B1', 'Reporte de ventas diarias de todas las sucursales | Llantera el rayo ');
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
                $hoja_activa->getStyle('F3:G3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('36c200');
                $hoja_activa->getStyle('F3:G3')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);


        $venta_total = obtenerVentaTotal($con, $fecha, "Normal", "Pagado");
     
        $utilidad_total = obtenerUtilidadTotal($con, $fecha, "Normal", "Pagado");


        $venta_metodo_efectivo = obtenerVentaMetodoPago($con, $fecha, "Normal", "Pagado", "Efectivo");
        $venta_metodo_tarjeta = obtenerVentaMetodoPago($con,  $fecha, "Normal", "Pagado", "Tarjeta");
        $venta_metodo_cheque = obtenerVentaMetodoPago($con, $fecha, "Normal", "Pagado", "Cheque");
        $venta_metodo_transferencia = obtenerVentaMetodoPago($con, $fecha, "Normal", "Pagado", "Transferencia");
        $venta_metodo_por_definir = obtenerVentaMetodoPago($con, $fecha, "Normal", "Pagado", "Por definir");

        $utilidad_metodo_efectivo = obtenerUtilidadMetodoPago($con, $fecha, "Normal", "Pagado", "Efectivo");
        $utilidad_metodo_tarjeta = obtenerUtilidadMetodoPago($con, $fecha, "Normal", "Pagado", "Tarjeta");
        $utilidad_metodo_cheque = obtenerUtilidadMetodoPago($con, $fecha, "Normal", "Pagado", "Cheque");
        $utilidad_metodo_transferencia = obtenerUtilidadMetodoPago($con, $fecha, "Normal", "Pagado", "Transferencia");
        $utilidad_metodo_por_definir = obtenerUtilidadMetodoPago($con, $fecha, "Normal", "Pagado", "Por definir");


        
        $estatus = "Pagado";
        $tipo = "Normal";
        $consultaVentas = "SELECT COUNT(*) FROM ventas WHERE fecha = ? AND estatus =? AND tipo =?";
        $resp = $con->prepare($consultaVentas);
        $resp->bind_param('sss',$fecha, $estatus, $tipo);
        $resp->execute();
        $resp->bind_result($numero_ventas);
        $resp->fetch();
        $resp->close();
        

                $hoja_activa->getColumnDimension('F')->setWidth(40);
                $hoja_activa->setCellValue('B3', "Ventas totales");
                $hoja_activa->setCellValue('F3', "Ganancias totales	");
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
                
                $hoja_activa->setCellValue('F4', "Ganancia total de la venta:");
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
                $hoja_activa->getStyle('A'.$index.':G'.$index)->getAlignment()->setVertical('center');


                $index=12;

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

                $hoja_activa->getStyle('A'.$index.':I'.$index)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('007bcc');
                $hoja_activa->getStyle('A'.$index.':I'.$index)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $hoja_activa->getStyle('A'.$index.':I'.$index)->getFont()->setBold(true);
                $hoja_activa->getRowDimension('2')->setRowHeight(20);
                $hoja_activa->getStyle('A'.$index.':I'.$index)->getAlignment()->setHorizontal('center');
                $hoja_activa->getStyle('A'.$index.':I'.$index)->getAlignment()->setVertical('center');

                $index++;


                if($numero_ventas > 0){
                    $traer_venta = "SELECT * FROM ventas WHERE fecha = '$fecha' AND estatus ='$estatus' AND tipo ='$tipo'";
                    $respo = mysqli_query($con, $traer_venta);

                    $contador = 1;
                    while ($fila = mysqli_fetch_array($respo)) {
                        $id_venta = $fila["id"];
                        $id_cliente = $fila["id_Cliente"];
                        $id_usuario = $fila["id_Usuarios"];
                        $sucursal = $fila["sucursal"];
                        $metodo_pag = $fila["metodo_pago"];
                        $total_actual = $fila["Total"];
                        
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

                $index++;
                $contador++;


                    }
                }


                //Creando hoja de reporte de creditos -----
                $spreadsheet->createSheet();
                            
                $spreadsheet->setActiveSheetIndex(1);
                $hoja_activa = $spreadsheet->getActiveSheet();
                $hoja_activa->setTitle("Reporte de venta de creditos");

//$hoja_activa->mergeCells("A1:B1");
        $hoja_activa->mergeCells("B1:G1");        
        $hoja_activa->setCellValue('B1', 'Reporte de ventas a credito diarias de todas las sucursales | Llantera el rayo ');
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
                $hoja_activa->getStyle('F3:G3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('36c200');
                $hoja_activa->getStyle('F3:G3')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);


        $venta_total = obtenerVentaTotal($con, $fecha, "Credito", "Abierta");
        $utilidad_total = obtenerUtilidadTotal($con, $fecha, "Credito", "Abierta");


        $venta_metodo_efectivo = obtenerVentaMetodoPago($con, $fecha, "Credito", "Abierta", "Efectivo");
        $venta_metodo_tarjeta = obtenerVentaMetodoPago($con, $fecha, "Credito", "Abierta", "Tarjeta");
        $venta_metodo_cheque = obtenerVentaMetodoPago($con, $fecha, "Credito", "Abierta", "Cheque");
        $venta_metodo_transferencia = obtenerVentaMetodoPago($con, $fecha, "Credito", "Abierta", "Transferencia");
        $venta_metodo_por_definir = obtenerVentaMetodoPago($con, $fecha, "Credito", "Abierta", "Por definir");

        $utilidad_metodo_efectivo = obtenerUtilidadMetodoPago($con, $fecha, "Credito", "Abierta", "Efectivo");
        $utilidad_metodo_tarjeta = obtenerUtilidadMetodoPago($con, $fecha, "Credito", "Abierta", "Tarjeta");
        $utilidad_metodo_cheque = obtenerUtilidadMetodoPago($con, $fecha, "Credito", "Abierta", "Cheque");
        $utilidad_metodo_transferencia = obtenerUtilidadMetodoPago($con, $fecha, "Credito", "Abierta", "Transferencia");
        $utilidad_metodo_por_definir = obtenerUtilidadMetodoPago($con, $fecha, "Credito", "Abierta", "Por definir");


        
        $estatus = "Pagado";
        $tipo = "Normal";
        $consultaVentas = "SELECT COUNT(*) FROM ventas WHERE fecha = ? AND estatus =? AND tipo =?";
        $resp = $con->prepare($consultaVentas);
        $resp->bind_param('sss', $fecha, $estatus, $tipo);
        $resp->execute();
        $resp->bind_result($numero_ventas);
        $resp->fetch();
        $resp->close();

                $hoja_activa->getColumnDimension('F')->setWidth(40);
                $hoja_activa->setCellValue('B3', "Ventas totales");
                $hoja_activa->setCellValue('F3', "Ganancias totales	");
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
                
                $hoja_activa->setCellValue('F4', "Ganancia total de la venta:");
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
                $hoja_activa->getStyle('A'.$index.':G'.$index)->getAlignment()->setVertical('center');


                $index=12;
                //Tabla de abonos
                $hoja_activa->mergeCells("A12:H12");        
                $hoja_activa->setCellValue('A12', "Abonos realizados");
                $hoja_activa->getStyle('A12:H12')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('950033');
                $hoja_activa->getStyle('A12:H12')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                 
                $hoja_activa->getStyle('A12:H12')->getAlignment()->setHorizontal('center');
                $hoja_activa->getStyle('A12:H12')->getAlignment()->setVertical('center');

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

                $hoja_activa->getStyle('A'.$index.':H'.$index)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('950033');
                $hoja_activa->getStyle('A'.$index.':H'.$index)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $hoja_activa->getStyle('A'.$index.':H'.$index)->getFont()->setBold(true);
                $hoja_activa->getRowDimension('2')->setRowHeight(20);
                $hoja_activa->getStyle('A'.$index.':H'.$index)->getAlignment()->setHorizontal('center');
                $hoja_activa->getStyle('A'.$index.':H'.$index)->getAlignment()->setVertical('center');
                $index++;

                $abonos_hoy = obtenerClientesqueAbonaron($con, $fecha);
                //echo json_encode($abonos_hoy, JSON_UNESCAPED_UNICODE);
                $contador2 = 1;

                foreach ($abonos_hoy as $key => $value) {
                    # code...
                        
                        $id_venta = $value["id"];
                        $cliente = $value["cliente"];
                        $fecha = $value["fecha"];
                        $abono = $value["abono"];
                        $sucursal = $value["sucursal"];
                        $usuario = $value["usuario"];
                        $id_credito = $value["id_credito"];
                        $metodo = $value["metodo"];
                        
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
                $hoja_activa->getStyle('A'.$index.':H'.$index)->getAlignment()->setHorizontal('center');
                $hoja_activa->getStyle('A'.$index.':H'.$index)->getAlignment()->setVertical('center');

                $index++;
                $contador2++;
                        
                    }
                

                
                $index++;
                $index++;
                $hoja_activa->mergeCells("A". $index .":I". $index ."");        
                $hoja_activa->setCellValue('A'. $index , "Ventas realizados");
                $hoja_activa->getStyle('A'.$index.':I'.$index)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('007bcc');
                $hoja_activa->getStyle('A'.$index.':I'.$index)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $hoja_activa->getStyle('A'.$index.':I'.$index)->getFont()->setBold(true);
                $hoja_activa->getStyle('A'.$index.':I'.$index)->getAlignment()->setHorizontal('center');
                $hoja_activa->getStyle('A'.$index.':I'.$index)->getAlignment()->setVertical('center');
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
                $hoja_activa->setCellValue('G'.$index, 'Folio');
                $hoja_activa->getColumnDimension('H')->setWidth(20);
                $hoja_activa->setCellValue('H'.$index, 'Tipo');
                $hoja_activa->getColumnDimension('I')->setWidth(20);
                $hoja_activa->setCellValue('I'.$index, 'Metodo pago');

                $hoja_activa->getStyle('A'.$index.':I'.$index)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('007bcc');
                $hoja_activa->getStyle('A'.$index.':I'.$index)->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $hoja_activa->getStyle('A'.$index.':I'.$index)->getFont()->setBold(true);
                $hoja_activa->getRowDimension('2')->setRowHeight(20);
                $hoja_activa->getStyle('A'.$index.':I'.$index)->getAlignment()->setHorizontal('center');
                $hoja_activa->getStyle('A'.$index.':I'.$index)->getAlignment()->setVertical('center');

                $index++;


                $estatuscred = "Abierta";
                $tipocred = "Credito";
                if($numero_ventas > 0){
                    $traer_venta = "SELECT * FROM ventas WHERE fecha = '$fecha' AND estatus ='$estatuscred' AND tipo ='$tipocred'";
                    $respo = mysqli_query($con, $traer_venta);

                    $contador = 1;
                    while ($fila = mysqli_fetch_array($respo)) {
                        $id_venta = $fila["id"];
                        $id_cliente = $fila["id_Cliente"];
                        $id_usuario = $fila["id_Usuarios"];
                        $sucursal = $fila["sucursal"];
                        $total_actual = $fila["Total"];
                        $metodo_p = $fila["metodo_pago"];
                        
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
                $hoja_activa->setCellValue('G'.$index, "RAY".$id_venta);
                $hoja_activa->getColumnDimension('H')->setWidth(20);
                $hoja_activa->setCellValue('H'.$index, $tipocred);
                $hoja_activa->getColumnDimension('I')->setWidth(20);
                $hoja_activa->setCellValue('I'.$index, $metodo_p);

                $index++;
                $contador++;


                    }
                }





        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte de venta diaria. '. $fecha .'.xlsx"');
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
function obtenerUtilidadTotal($con, $fecha, $tipo, $estatus){

    $total_venta_metodo = 0;
    $consulta = "SELECT SUM(Total) FROM ventas WHERE fecha = ? AND tipo = ? AND estatus = ?";
    $res = $con->prepare($consulta);
    $res->bind_param("sss", $fecha, $tipo, $estatus);
    $res->execute();
    $res->bind_result($total_venta_metodo);
    $res->fetch();
    $res->close();

    if($total_venta_metodo == null || $total_venta_metodo == ""){
        return 0;
    }else{

        
    $lista_ventas = mysqli_query($con, "SELECT * FROM ventas WHERE fecha = '$fecha' AND 
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
    $utilidad_total = $importe_acumulado - $costo_acumulado;    /* 
    print_r("importe total desde 1era consulta:" . $total_venta_metodo . "<br/>");*/
    return $utilidad_total;
    }

}

function obtenerUtilidadMetodoPago($con, $fecha, $tipo, $estatus, $metodo_pago){
    $total_venta_metodo = 0;
    $consulta = "SELECT SUM(Total) FROM ventas WHERE fecha = ? AND tipo = ? AND estatus = ? AND metodo_pago =?";
    $res = $con->prepare($consulta);
    $res->bind_param("ssss", $fecha, $tipo, $estatus, $metodo_pago);
    $res->execute();
    $res->bind_result($total_venta_metodo);
    $res->fetch();
    $res->close();

    if($total_venta_metodo == null || $total_venta_metodo == ""){
        return 0;
    }else{

        
    $lista_ventas = mysqli_query($con, "SELECT * FROM ventas WHERE fecha = '$fecha' AND 
                                                                   tipo = '$tipo' AND 
                                                                   estatus = '$estatus' AND 
                                                                   metodo_pago = '$metodo_pago'");
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
    $utilidad_total = $importe_acumulado - $costo_acumulado;    /* 
    print_r("importe total desde 1era consulta:" . $total_venta_metodo . "<br/>");
    print_r("costo total desde iteraciones: " .$costo_acumulado . "<br/>"); */
    return $utilidad_total;
    }
    
}

//Funciones para obtener ganancias
function obtenerVentaTotal($con, $fecha, $tipo, $estatus){
    $total_venta = 0;
    $consulta = "SELECT SUM(Total) FROM ventas WHERE fecha = ? AND tipo = ? AND estatus = ?";
    $res = $con->prepare($consulta);
    $res->bind_param("sss", $fecha, $tipo, $estatus);
    $res->execute();
    $res->bind_result($total_venta);
    $res->fetch();
    $res->close();

    if($total_venta == "" || $total_venta ==null){
        $total_venta = 0;
    }
    return $total_venta;
}

function obtenerVentaMetodoPago($con, $fecha, $tipo, $estatus, $metodo_pago){
    $total_venta = 0;
    $consulta = "SELECT SUM(Total) FROM ventas WHERE fecha = ? AND tipo = ? AND estatus = ? AND metodo_pago =?";
    $res = $con->prepare($consulta);
    $res->bind_param("ssss", $fecha, $tipo, $estatus, $metodo_pago);
    $res->execute();
    $res->bind_result($total_venta);
    $res->fetch();
    $res->close();

    if($total_venta == "" || $total_venta ==null){
        $total_venta = 0;
    }
    return $total_venta;
}


function obtenerClientesqueAbonaron($con, $fecha)
{
    $traer_id = $con->prepare("SELECT * FROM `abonos` WHERE fecha =?");
    $traer_id->bind_param('s', $fecha);
    $traer_id->execute();
    $resultado = $traer_id->get_result();
    $traer_id->close();

    if ($resultado->num_rows < 1) {
        return $resultado->num_rows;
    } else {
        while ($fila = $resultado->fetch_assoc()) {
            $id_cliente="";
            $id_credito = $fila["id_credito"];
            $abono = $fila["abono"];
            $sucurs = $fila["sucursal"];
            $metodo_pago = $fila["metodo_pago"];
            $usuario = $fila["usuario"];
            $id_credito = $fila["id_credito"];

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

            $arreglo[] = array("id_credito"=>$id_credito, "metodo"=>$metodo_pago, "cliente"=>$cliente, "abono"=> $abono, "fecha"=> $fecha, "sucursal"=> $sucurs, "usuario"=>$usuario);
        };
    
        return $arreglo;
    }
}


?>