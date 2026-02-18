<?php
include '../conexion.php';
$con = $conectando->conexion();

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

require_once '../../vendor/phpoffice/phpspreadsheet/samples/Bootstrap.php'; 

date_default_timezone_set("America/Matamoros");
session_start(); 
$id_sucursal_usuario = $_SESSION['id_sucursal'];
$id_usuario_actual = $_SESSION['id_usuario'];

$arreglo_permisos_ids = [7, 1, 11, 26, 29, 21];

// Verificamos si el usuario tiene permiso global
$tiene_permiso_total = in_array($id_usuario_actual, $arreglo_permisos_ids);

// Construimos la cláusula extra para el SQL
$filtro_sucursal = "";
if (!$tiene_permiso_total) {
    // Si no tiene permiso, forzamos a que solo vea su sucursal
    $filtro_sucursal = " AND s.id = $id_sucursal_usuario ";
}

$spreadsheet = new Spreadsheet();

// --- CONFIGURACIÓN DE COLORES POR SUCURSAL ---
$mapa_colores = [
    1  => 'FFD9E1F2', 2  => 'FFE2EFDA', 3  => 'FFFFF2CC', 
    5  => 'FFFBE4E1', 6  => 'FFFFE6CC'
];
$color_default = 'FFF2F2F2';

// --- FUNCIÓN PARA DAR FORMATO A CABECERAS ---
function formatearCabecera($hoja, $titulo) {
    $hoja->mergeCells("B1:G1");
    $hoja->setCellValue('B1', $titulo);
    $hoja->getStyle('B1')->getFont()->setBold(true)->setSize(16);
    $hoja->getRowDimension('1')->setRowHeight(50);
    $hoja->getStyle('B1')->getAlignment()->setHorizontal('center')->setVertical('center');

    $columnas = [
        'A' => ['#', 8], 'B' => ['Nombre del cliente', 40], 'C' => ['Pagado', 18],
        'D' => ['Restante', 18], 'E' => ['Total', 18], 'F' => ['Estatus', 22],
        'G' => ['Fecha de inicio', 18], 'H' => ['Fecha final', 20], 'I' => ['Plazo', 20],
        'J' => ['Venta', 15], 'K' => ['Sucursal', 25], 'L' => ['Asesor', 40]
    ];

    foreach ($columnas as $col => $info) {
        $hoja->getColumnDimension($col)->setWidth($info[1]);
        $hoja->setCellValue($col.'3', $info[0]);
    }

    $hoja->getStyle('A3:L3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('007bcc');
    $hoja->getStyle('A3:L3')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
    $hoja->getStyle('A3:L3')->getAlignment()->setHorizontal('center');
}

// --- FUNCIÓN PARA LLENAR DATOS ---
function llenarDatos($hoja, $datos, $mapa_colores, $color_default) {
    $index = 4;
    $contador = 1;
    $total_monto = 0;

    foreach ($datos as $value) {
        $plazo = match($value["plazo"]) {
            '1', '5' => "7 dias",
            '2' => "15 dias",
            '3' => "1 mes",
            '4' => "1 año",
            '6' => "1 día",
            default => "Sin definir"
        };

        $estatus_texto = ($value['estatus'] == 4) ? "Vencido" : "Activo";
        
        $hoja->setCellValue('A'.$index, $contador);
        $hoja->setCellValue('B'.$index, $value['cliente']);
        $hoja->setCellValue('C'.$index, "$". number_format($value["pagado"], 2));
        $hoja->setCellValue('D'.$index, "$". number_format($value["restante"], 2));
        $hoja->setCellValue('E'.$index, "$". number_format($value["total"], 2));
        $hoja->setCellValue('F'.$index, $estatus_texto);
        $hoja->setCellValue('G'.$index, $value["fecha_inicio"].' '.$value['hora']);
        $hoja->setCellValue('H'.$index, $value["fecha_final"].' '.$value['hora']);
        $hoja->setCellValue('I'.$index, $plazo);
        $hoja->setCellValue('J'.$index, $value["id_venta"]);
        $hoja->setCellValue('K'.$index, $value["nombre_sucursal"]);
        $hoja->setCellValue('L'.$index, $value["asesor"]);

        $color = $mapa_colores[$value['id_sucursal']] ?? $color_default;
        $hoja->getStyle("A$index:L$index")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($color);

        $total_monto += $value["restante"];
        $contador++;
        $index++;
    }
    return $total_monto;
}

// --- PROCESAMIENTO DE DATOS ---

// 1. Créditos Vencidos (Estatus 4)
$queryVencidos = "SELECT c.*, s.nombre as nombre_sucursal, s.id as id_sucursal, v.hora, cl.Nombre_Cliente as cliente, CONCAT(u.nombre,' ',u.apellidos) as asesor 
                  FROM creditos c LEFT JOIN ventas v ON v.id = c.id_venta 
                  INNER JOIN clientes cl ON cl.id = v.id_Cliente 
                  INNER JOIN sucursal s ON v.id_sucursal = s.id 
                  INNER JOIN usuarios u ON cl.id_asesor = u.id 
                  WHERE c.estatus = 4 $filtro_sucursal ORDER BY s.nombre ASC, c.id_venta ASC";
$resV = $con->prepare($queryVencidos);
$resV->execute();
$datosVencidos = Arreglo_Get_Result($resV);

// 2. Créditos Activos (Excepto 3 y 5)
$queryActivos = "SELECT c.*, s.nombre as nombre_sucursal, s.id as id_sucursal, v.hora, cl.Nombre_Cliente as cliente, CONCAT(u.nombre,' ',u.apellidos) as asesor 
                 FROM creditos c LEFT JOIN ventas v ON v.id = c.id_venta 
                 INNER JOIN clientes cl ON cl.id = v.id_Cliente 
                 INNER JOIN sucursal s ON v.id_sucursal = s.id 
                 INNER JOIN usuarios u ON cl.id_asesor = u.id 
                 WHERE c.estatus NOT IN (3, 5, 4) $filtro_sucursal ORDER BY s.nombre ASC, c.id_venta ASC";
$resA = $con->prepare($queryActivos);
$resA->execute();
$datosActivos = Arreglo_Get_Result($resA);

// --- CREACIÓN DE HOJAS ---

// Hoja 1: Vencidos
$hoja1 = $spreadsheet->getActiveSheet();
$hoja1->setTitle("Vencidos");
formatearCabecera($hoja1, "Reporte de Créditos Vencidos");
$sumaVencidos = llenarDatos($hoja1, $datosVencidos, $mapa_colores, $color_default);

// Hoja 2: Activos
$hoja2 = $spreadsheet->createSheet();
$hoja2->setTitle("Activos");
formatearCabecera($hoja2, "Reporte de Créditos Activos (Vigentes)");
$sumaActivos = llenarDatos($hoja2, $datosActivos, $mapa_colores, $color_default);

// Hoja 3: Balance
$hoja3 = $spreadsheet->createSheet();
$hoja3->setTitle("Balance Comparativo");
$hoja3->setCellValue('B2', 'RESUMEN DE CARTERA');
$hoja3->getStyle('B2')->getFont()->setBold(true)->setSize(14);

$hoja3->setCellValue('B4', 'Concepto');
$hoja3->setCellValue('C4', 'Monto Total (Restante)');
$hoja3->setCellValue('D4', 'Cantidad de Créditos');

$hoja3->setCellValue('B5', 'Créditos Vencidos');
$hoja3->setCellValue('C5', $sumaVencidos);
$hoja3->setCellValue('D5', count($datosVencidos));

$hoja3->setCellValue('B6', 'Créditos Activos');
$hoja3->setCellValue('C6', $sumaActivos);
$hoja3->setCellValue('D6', count($datosActivos));

$hoja3->setCellValue('B8', 'TOTAL CARTERA');
$hoja3->setCellValue('C8', $sumaVencidos + $sumaActivos);
$hoja3->getStyle('B8:C8')->getFont()->setBold(true);

// Estilo de moneda para el balance
$hoja3->getStyle('C5:C8')->getNumberFormat()->setFormatCode('$#,##0.00');
$hoja3->getColumnDimension('B')->setWidth(30);
$hoja3->getColumnDimension('C')->setWidth(25);

// --- SALIDA ---
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Reporte_General_Creditos.xlsx"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');

function Arreglo_Get_Result($Statement) {
    $RESULT = array();
    $Statement->store_result();
    $Metadata = $Statement->result_metadata();
    $fields = $Metadata->fetch_fields();
    $PARAMS = array();
    $temp = array();
    foreach ($fields as $field) {
        $PARAMS[] = &$temp[$field->name];
    }
    call_user_func_array(array($Statement, 'bind_result'), $PARAMS);
    while ($Statement->fetch()) {
        $row = array();
        foreach ($temp as $key => $val) {
            $row[$key] = $val;
        }
        $RESULT[] = $row;
    }
    return $RESULT;
}
?>