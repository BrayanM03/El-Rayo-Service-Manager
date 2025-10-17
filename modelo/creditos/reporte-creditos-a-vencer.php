<?php
ob_start(); // inicia el buffer para evitar salidas previas

include '../conexion.php';
$con = $conectando->conexion();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

require '../../vendor/autoload.php'; // ✅ usa autoload, no Bootstrap.php
ini_set('memory_limit', '1024M'); // 1 GB
set_time_limit(0);
date_default_timezone_set("America/Matamoros");
session_start(); 

// --- Configuración base ---
$spreadsheet = new Spreadsheet();
$spreadsheet->getProperties()
    ->setCreator("Alvaro M")
    ->setTitle("Créditos vencidos");

$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle("Créditos vencidos");

// --- Primera fila ---
$sheet->mergeCells("A1:L1");
$sheet->getStyle('A1:L1')->getAlignment()->setHorizontal('center');
$sheet->getStyle('A1:L1')->getAlignment()->setVertical('center');
$tipo = $_GET['tipo'];
if($_GET['tipo']=='p'){
    $sheet->setCellValue('A1', 'Reporte de creditos vencidos entre '. $_GET['fecha_inicial']. ' y '. $_GET['fecha_final'] .' dias');
}else{
    $sheet->setCellValue('A1', 'Reporte de creditos vencidos en '.$tipo.' dias');
    
}

$creditos_avencer = obtenerCreditosVencidos($tipo, $con, $_GET['fecha_inicial'], $_GET['fecha_final']);
$fila=3;
// --- Encabezados ---
$headers = [
    'ID Credito', 'Sucursal', 'Cliente', 'Fecha inicio', 'Fecha Final',
    'Total', 'Pagado', 'Restante', 'Estatus Crédito', 'Estatus Venta', 'Plazo', 'RAY',
];
$estatus_credito = array(0=>'Sin abono', 1=>'Primer abono', 2 => 'Pagando', 3 =>'Finalizado', 4 => 'Vencido' ,5 => 'Cancelado');
$plazos = ['No borrar', '7 dias', '15 dias', '1 mes', '1 año', '7 dias', '1 dias']; //No borrar porque los plazo empieza no en 0
$sheet->fromArray($headers, null, 'A2');

//Establecer anchos
$sheet->getColumnDimension('A')->setWidth(10);
$sheet->getColumnDimension('B')->setWidth(20);
$sheet->getColumnDimension('C')->setWidth(40);
$sheet->getColumnDimension('D')->setWidth(13);
$sheet->getColumnDimension('E')->setWidth(13);
$sheet->getColumnDimension('F')->setWidth(14);
$sheet->getColumnDimension('G')->setWidth(14);
$sheet->getColumnDimension('H')->setWidth(14);
$sheet->getColumnDimension('I')->setWidth(13);
$sheet->getColumnDimension('J')->setWidth(13);
$sheet->getColumnDimension('K')->setWidth(13);
$sheet->getColumnDimension('L')->setWidth(14);

if(count($creditos_avencer)==0){
    $fila = 3;
    
    $sheet->mergeCells("A".$fila.":L".$fila);
    $sheet->setCellValue('A' . $fila, 'No se encontrarón creditos a vencer');

$sheet->getStyle('A1:L1')->getAlignment()->setHorizontal('center');
$sheet->getStyle('A1:L1')->getAlignment()->setVertical('center');

}else{
    foreach ($creditos_avencer as $row) {
        $sheet->setCellValue('A' . $fila, $row['id_credito']);
        $sheet->setCellValue('B' . $fila, $row['sucursal']);
        $sheet->setCellValue('C' . $fila, $row['cliente']);
        $sheet->setCellValue('D' . $fila, $row['fecha_inicio']);
        $sheet->setCellValue('E' . $fila, $row['fecha_final']);
        $sheet->setCellValue('F' . $fila, $row['total']);
        $sheet->setCellValue('G' . $fila, $row['pagado']);
        $sheet->setCellValue('H' . $fila, $row['restante']);
        $sheet->setCellValue('I' . $fila, $estatus_credito[$row['estatus_credito']]);
        $sheet->setCellValue('J' . $fila, $row['estatus_venta']);
        $sheet->setCellValue('K' . $fila, $plazos[$row['plazo']]);
        $sheet->setCellValue('L' . $fila, $row['id_Venta']);
        $fila++;
        }
}
/* echo json_encode($creditos_avencer);
die(); */

/* // Simulación de registros (para probar)
$totalRegistros = 100;
for ($i = 2; $i <= $totalRegistros + 1; $i++) {
    $sheet->setCellValueExplicit('A' . $i, $i - 1, DataType::TYPE_NUMERIC);
    $sheet->setCellValueExplicit('B' . $i, 'Usuario ' . ($i - 1), DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('C' . $i, 'usuario' . ($i - 1) . '@correo.com', DataType::TYPE_STRING);
    $sheet->setCellValueExplicit('D' . $i, date('Y-m-d'), DataType::TYPE_STRING);
} */

// --- Estilos opcionales ---
$sheet->getStyle('A2:L2')->getFill()->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setRGB('007bcc');
$sheet->getStyle('A2:L2')->getFont()->getColor()->setARGB(Color::COLOR_WHITE);
$sheet->getStyle('A2:L2')->getFont()->setBold(true);

// --- Limpieza del buffer antes de enviar ---
if (ob_get_length()) ob_end_clean();

// --- Cabeceras HTTP ---
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Reporte de creditos a vencer.xlsx"');
header('Cache-Control: max-age=0');

// --- Guardar en salida ---
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->setPreCalculateFormulas(false);
$writer->save('php://output');
exit;

function obtenerCreditosVencidos($tipo, $con, $fecha_inicio = 0, $fecha_final = 0){

    $id_sucursal_sesion = $_SESSION['id_sucursal'];
    $rol = $_SESSION['rol'];
    // Validar tipo
    $dias = 0;
    switch ($tipo) {
        case 1: $dias = 1; break;
        case 7: $dias = 7; break;
        case 15: $dias = 15; break;
        default: $dias = 7; // Valor por defecto
    }

    // Query SQL
    $query = "
        SELECT 
            c.id AS id_credito,
            s.nombre as sucursal,
            cl.Nombre_Cliente as cliente,
            c.id_Venta,
            c.plazo,
            c.fecha_inicio,
            c.fecha_final,
            c.estatus AS estatus_credito,
            v.id AS id_venta,
            v.estatus AS estatus_venta,
            v.total,
            c.pagado,
            c.restante,
            v.fecha AS fecha_venta
        FROM creditos c
        INNER JOIN ventas v ON c.id_Venta = v.id
        INNER JOIN clientes cl ON cl.id = v.id_cliente
        INNER JOIN sucursal s ON s.id = v.id_sucursal
        WHERE v.estatus != 'Cancelada'
          AND c.estatus NOT IN (3, 4, 5)
    ";

    if ($rol != 1 && $id_sucursal_sesion != 7) {
        $query .= ' AND v.id_sucursal = ?';
    }
    
    // Filtro por tipo
    if ($tipo == 'p' && $fecha_inicio != 0 && $fecha_final != 0) {
        $query .= " AND DATE(c.fecha_final) BETWEEN ? AND ? ORDER BY c.fecha_final ASC";
    
        if ($rol != 1 && $id_sucursal_sesion != 7) {
            // Rol distinto de 1 → incluye sucursal
            $stmt = $con->prepare($query);
            $stmt->bind_param("ssi", $fecha_inicio, $fecha_final, $id_sucursal_sesion);
        } else {
            // Rol 1 → sin filtro de sucursal
            $stmt = $con->prepare($query);
            $stmt->bind_param("ss", $fecha_inicio, $fecha_final);
        }
    
    } else {
        $query .= " AND DATE(c.fecha_final) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY) ORDER BY c.fecha_final ASC";
        
        if ($rol != 1 && $id_sucursal_sesion != 7) {
            // Rol distinto de 1 → incluye sucursal
            $stmt = $con->prepare($query);
            $stmt->bind_param("ii", $dias, $id_sucursal_sesion);
        } else {
            // Rol 1 → sin filtro de sucursal
            $stmt = $con->prepare($query);
            $stmt->bind_param("i", $dias);
        }
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $creditos = [];
    while ($row = $result->fetch_assoc()) {
        $creditos[] = $row;
    }

    $stmt->close();
    $con->close();

    return $creditos;
    
}

function obtenerCreditosVencidosXX($tipo, $con, $fecha_inicio = 0, $fecha_final = 0) {
   
    $id_sucursal_sesion = $_SESSION['id_sucursal'];
    // Validar tipo
    $dias = 0;
    switch ($tipo) {
        case 1: $dias = 1; break;
        case 7: $dias = 7; break;
        case 15: $dias = 15; break;
        default: $dias = 7; // Valor por defecto
    }

    // Query SQL
    $query = "
        SELECT 
            c.id AS id_credito,
            s.nombre as sucursal,
            cl.Nombre_Cliente as cliente,
            c.id_Venta,
            c.plazo,
            c.fecha_inicio,
            c.fecha_final,
            c.estatus AS estatus_credito,
            v.id AS id_venta,
            v.estatus AS estatus_venta,
            v.total,
            c.pagado,
            c.restante,
            v.fecha AS fecha_venta
        FROM creditos c
        INNER JOIN ventas v ON c.id_Venta = v.id
        INNER JOIN clientes cl ON cl.id = v.id_cliente
        INNER JOIN sucursal s ON s.id = v.id_sucursal
        WHERE v.estatus != 'Cancelada'
          AND c.estatus NOT IN (3, 4, 5)
    ";

    if($id_sucursal_sesion == 1 ){
        $query .=  ' AND v.id_sucursal = ?';
    }

       // Filtro por tipo
       if ($tipo == 'p' && $fecha_inicio != 0 && $fecha_final != 0) {
        // Personalizado
        $query .= " AND DATE(c.fecha_final) BETWEEN ? AND ? ORDER BY c.fecha_final ASC";
        $stmt = $con->prepare($query);
        $stmt->bind_param("ssi", $fecha_inicio, $fecha_final, $id_sucursal_sesion);
    } else {
        // Automático por rango de días
        $query .= " AND DATE(c.fecha_final) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY) ORDER BY c.fecha_final ASC";
        
        $stmt = $con->prepare($query);
        $stmt->bind_param("ii", $dias, $id_sucursal_sesion);
    }

  
    $stmt->execute();
    $result = $stmt->get_result();

    $creditos = [];
    while ($row = $result->fetch_assoc()) {
        $creditos[] = $row;
    }

    $stmt->close();
    $con->close();

    return $creditos;
}

?>
