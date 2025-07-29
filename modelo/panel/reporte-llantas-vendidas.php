
<?php
include '../conexion.php';
date_default_timezone_set("America/Matamoros");

$con= $conectando->conexion(); 
include '../helpers/response_helper.php';

if(empty($_POST['fecha_inicio']) && $_POST['fecha_final']){
    responder(false, 'Selecciona una o mas sucursales', []);
}
$fecha_inicio = $_POST['fecha_inicio']== '' ?  $fecha_final :$_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_final']=='' ? $fecha_inicio : $_POST['fecha_final'];
$ids_sucursales= $_POST['sucursal'];
$placeholders = implode(',', array_fill(0, count($ids_sucursales), '?'));
$types = 'ss' . str_repeat('i', count($ids_sucursales));
$params = array_merge([$fecha_inicio, $fecha_fin], $ids_sucursales);

$query= "SELECT COUNT(*) FROM ventas WHERE (Fecha BETWEEN ? AND ?) AND estatus != 'Cancelada' AND id_sucursal IN ($placeholders)";
$stmt = $con->prepare($query);
$stmt->bind_param( $types,
...array_map(function($v) { return $v; }, $params));
$stmt->execute();
$stmt->bind_result($ventas_realizadas);
$stmt->fetch();
$stmt->close();

if($ventas_realizadas>0){
    $query = "SELECT l.Descripcion, l.Marca, dv.Cantidad, v.id as ray, s.nombre as sucursal, concat(u.nombre, ' ', u.apellidos) as vendedor,
    c.Nombre_cliente, v.Fecha, v.hora FROM detalle_venta dv INNER JOIN ventas v ON dv.id_venta = v.id 
    INNER JOIN llantas l ON l.id = dv.id_llanta
    INNER JOIN sucursal s ON s.id = v.id_sucursal
    INNER JOIN clientes c ON c.id = v.id_cliente INNER JOIN usuarios u ON u.id = v.id_Usuarios WHERE 
    dv.Unidad = 'Pieza' AND v.estatus != 'Cancelada' AND (v.Fecha BETWEEN ? AND ?) AND v.id_sucursal IN ($placeholders)";
       $stmt = $con->prepare($query);
       $stmt->bind_param( $types,
       ...array_map(function($v) { return $v; }, $params));
       $stmt->execute();
       $resultado = $stmt->get_result();
    
       $Data = [];
       while ($fila = $resultado->fetch_assoc()) {
            $Data[]= $fila;
       }

    responder(true, 'Se encontrarón ventas', [], $Data);

}else{
    responder(false, 'No se encontrarón ventas', []);
}

?>
