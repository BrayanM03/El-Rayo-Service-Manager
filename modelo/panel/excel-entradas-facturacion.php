<?php
// Limpiar cualquier salida previa para evitar corrupción de archivo
if (ob_get_length()) ob_end_clean();

include '../conexion.php';
$con = $conectando->conexion();

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

require_once '../../vendor/phpoffice/phpspreadsheet/samples/Bootstrap.php'; 

date_default_timezone_set("America/Matamoros");
session_start(); 

// --- PARÁMETROS DINÁMICOS ---
$fecha_inicio = $_GET['f1'] ?? date('Y-m-d');
$fecha_final  = $_GET['f2'] ?? date('Y-m-d');
$id_sucursal_reporte = $_GET['sucursal'] ?? $_SESSION['id_sucursal'];

$id_usuario_actual = $_SESSION['id_usuario'];
$arreglo_permisos_ids = [7, 1, 11, 26, 29];

if (!in_array($id_usuario_actual, $arreglo_permisos_ids)) {
    $id_sucursal_reporte = $_SESSION['id_sucursal'];
}

$spreadsheet = new Spreadsheet();

$totales_metodos = [
    'efectivo' => 0,
    'tarjeta' => 0,
    'transferencia' => 0,
    'cheque' => 0,
    'sin_definir' => 0
];

function prepararHoja($hoja, $titulo, $headers) {
    $hoja->setCellValue('A1', $titulo);
    $hoja->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    $col = 'A';
    foreach ($headers as $texto) {
        $hoja->setCellValue($col.'3', $texto);
        $hoja->getColumnDimension($col)->setAutoSize(true);
        $col++;
    }
    $ultimaCol = chr(ord($col) - 1);
    $hoja->getStyle("A3:{$ultimaCol}3")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('28A745');
    $hoja->getStyle("A3:{$ultimaCol}3")->getFont()->getColor()->setRGB('FFFFFF');
}

// --- 1. VENTAS CONTADO ---
$hojaVentas = $spreadsheet->getActiveSheet();
$hojaVentas->setTitle("Ventas Contado");
prepararHoja($hojaVentas, "VENTAS DE CONTADO", ['Cliente', 'Folio', 'Total', 'Efectivo', 'Tarjeta', 'Transf.', 'Cheque', 'Sin definir', 'Fecha', 'Tipo', 'Estatus']);

$sqlVentas = "SELECT c.Nombre_Cliente, v.id, v.Total, v.pago_efectivo, v.pago_tarjeta, v.pago_transferencia, v.pago_cheque, v.pago_sin_definir, v.comentario, v.tipo, v.estatus, v.Fecha 
              FROM ventas v 
              INNER JOIN clientes c ON v.id_Cliente = c.id 
              WHERE v.Fecha BETWEEN ? AND ? AND v.estatus != 'Cancelada' 
              AND v.id_sucursal = ? AND v.tipo = 'Normal'";
$stmt = $con->prepare($sqlVentas);
$stmt->bind_param("ssi", $fecha_inicio, $fecha_final, $id_sucursal_reporte);
$stmt->execute();
$resVentas = Arreglo_Get_Result($stmt);

$r = 4;
foreach ($resVentas as $v) {
    $hojaVentas->setCellValue('A'.$r, $v['Nombre_Cliente']);
    $hojaVentas->setCellValue('B'.$r, "RAY".$v['id']);
    $hojaVentas->setCellValue('C'.$r, $v['Total']);
    $hojaVentas->setCellValue('D'.$r, $v['pago_efectivo']);
    $hojaVentas->setCellValue('E'.$r, $v['pago_tarjeta']);
    $hojaVentas->setCellValue('F'.$r, $v['pago_transferencia']);
    $hojaVentas->setCellValue('G'.$r, $v['pago_cheque']);
    $hojaVentas->setCellValue('H'.$r, $v['pago_sin_definir']);
    $hojaVentas->setCellValue('I'.$r, $v['Fecha']);
    $hojaVentas->setCellValue('J'.$r, $v['tipo']);
    $hojaVentas->setCellValue('K'.$r, $v['estatus']);
    $hojaVentas->setCellValue('L'.$r, $v['comentario']);
    
    $totales_metodos['efectivo'] += (float)$v['pago_efectivo'];
    $totales_metodos['tarjeta'] += (float)$v['pago_tarjeta'];
    $totales_metodos['transferencia'] += (float)$v['pago_transferencia'];
    $totales_metodos['cheque'] += (float)$v['pago_cheque'];
    $totales_metodos['sin_definir'] += (float)($v['pago_sin_definir'] ?? 0);
    $r++;
}

// --- 2. PROCESAR ABONOS ---
$tablas_abonos = [
    ['Creditos', 'abonos', 'id_credito', 'creditos', 'ABONOS CRÉDITO'],
    ['Apartados', 'abonos_apartados', 'id_apartado', 'apartados', 'ABONOS APARTADO'],
    ['Pedidos', 'abonos_pedidos', 'id_pedido', 'pedidos', 'ABONOS PEDIDO']
];

foreach ($tablas_abonos as $tabla) {
    $hoja = $spreadsheet->createSheet();
    $hoja->setTitle($tabla[0]);
    prepararHoja($hoja, $tabla[4], ['Folio Original', 'Cliente', 'Abono Total', 'Efectivo', 'Tarjeta', 'Transf.', 'Cheque', 'Sin definir', 'Fecha']);
    
    if ($tabla[0] == 'Creditos') {
        $sql = "SELECT a.*, c.Nombre_Cliente, x.id as folio_origen 
                FROM abonos a 
                INNER JOIN creditos x ON x.id = a.id_credito
                INNER JOIN ventas v ON v.id = x.id_venta
                INNER JOIN clientes c ON v.id_Cliente = c.id
                WHERE a.fecha BETWEEN ? AND ? AND v.id_sucursal = ? AND v.estatus != 'Cancelada'";
    } else {
        $estatus_cancelado = $tabla[3] == 'pedidos' ? 'Cancelado' : 'Cancelada'; //Andele mijo por no cambiar valores en la BD
        $sql = "SELECT a.*, c.Nombre_Cliente, x.id as folio_origen 
                FROM {$tabla[1]} a 
                INNER JOIN {$tabla[3]} x ON x.id = a.{$tabla[2]}
                INNER JOIN clientes c ON x.id_cliente = c.id
                WHERE a.fecha BETWEEN ? AND ? AND x.id_sucursal = ? AND x.estatus != '$estatus_cancelado'";
               
    }
    
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssi", $fecha_inicio, $fecha_final, $id_sucursal_reporte);
    $stmt->execute();
    $res = Arreglo_Get_Result($stmt);
    
    $r = 4;
    foreach ($res as $a) {
        $hoja->setCellValue('A'.$r, $a['folio_origen']);
        $hoja->setCellValue('B'.$r, $a['Nombre_Cliente']);
        $hoja->setCellValue('C'.$r, $a['abono']);
        $hoja->setCellValue('D'.$r, $a['pago_efectivo']);
        $hoja->setCellValue('E'.$r, $a['pago_tarjeta']);
        $hoja->setCellValue('F'.$r, $a['pago_transferencia']);
        $hoja->setCellValue('G'.$r, $a['pago_cheque'] ?? 0);
        $hoja->setCellValue('H'.$r, $a['pago_sin_definir'] ?? 0);
        $hoja->setCellValue('I'.$r, $a['fecha']);
        
        $totales_metodos['efectivo'] += (float)($a['pago_efectivo'] ?? 0);
        $totales_metodos['tarjeta'] += (float)($a['pago_tarjeta'] ?? 0);
        $totales_metodos['transferencia'] += (float)($a['pago_transferencia'] ?? 0);
        $totales_metodos['cheque'] += (float)($a['pago_cheque'] ?? 0);
        $totales_metodos['sin_definir'] += (float)($a['pago_sin_definir'] ?? 0);
        $r++;
    }
}

// --- 3. RESUMEN FINAL ---
$hojaResumen = $spreadsheet->createSheet();
$hojaResumen->setTitle("Resumen Final");
$hojaResumen->setCellValue('B2', 'REPORTE DE ENTRADAS - DESGLOSE DE MÉTODOS');
$hojaResumen->setCellValue('B4', 'MÉTODO DE PAGO');
$hojaResumen->setCellValue('C4', 'TOTAL ACUMULADO');

$filas = [
    ['Efectivo', $totales_metodos['efectivo']],
    ['Tarjeta', $totales_metodos['tarjeta']],
    ['Transferencia', $totales_metodos['transferencia']],
    ['Cheques', $totales_metodos['cheque']],
    ['Sin definir', $totales_metodos['sin_definir']]
];

$r = 5;
foreach ($filas as $fila) {
    $hojaResumen->setCellValue('B'.$r, $fila[0]);
    $hojaResumen->setCellValue('C'.$r, $fila[1]);
    $r++;
}

$hojaResumen->setCellValue('B11', 'TOTAL ENTRADAS');
$hojaResumen->setCellValue('C11', array_sum($totales_metodos));
$hojaResumen->getStyle('C5:C11')->getNumberFormat()->setFormatCode('$#,##0.00');

// --- DESCARGA ---
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Reporte_Entradas.xlsx"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit;

function Arreglo_Get_Result($Statement) {
    $RESULT = array();
    $Statement->store_result();
    $Metadata = $Statement->result_metadata();
    if (!$Metadata) return [];
    $fields = $Metadata->fetch_fields();
    $temp = array();
    $PARAMS = array();
    foreach ($fields as $field) { $PARAMS[] = &$temp[$field->name]; }
    call_user_func_array(array($Statement, 'bind_result'), $PARAMS);
    while ($Statement->fetch()) {
        $row = array();
        foreach ($temp as $key => $val) { $row[$key] = $val; }
        $RESULT[] = $row;
    }
    return $RESULT;
}