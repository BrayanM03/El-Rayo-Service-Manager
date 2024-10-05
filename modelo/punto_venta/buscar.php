<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!$con) {
    echo "Problemas con la conexion";
} 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

$ancho = $_POST['ancho'];
$alto = isset($_POST['alto']) ? $_POST['alto'] : 0;
$diametro = $_POST['diametro'];
$filtros = isset($_POST['filtros']) == true ? $_POST['filtros'] : [];

$count = "SELECT count(*) FROM llantas l INNER JOIN inventario i ON l.id = i.id_Llanta WHERE i.Stock > 0 
AND l.Ancho = ? AND l.Proporcion = ? AND l.Diametro = ?";


if(count($filtros) > 0) {
    $aplicacion_ids = [];
    $sucursal_ids = [];
    $tipo_vehiculo_ids = [];

    foreach ($filtros as $value) {
        $categoria = $categoria = substr($value, 0, -5);
        $filtro_id = substr($value, strrpos($value, '_') + 1);

        if($categoria == 'aplicacion') {
            $aplicacion_ids[] = $filtro_id;
        }
        if($categoria == 'sucursal') {
            $sucursal_ids[] = $filtro_id;
        }
        if($categoria == 'tipo_vehiculo') {
            $tipo_vehiculo_ids[] = $filtro_id;
        }
    }

    // Usamos implode para unir los IDs sin comas al final
    $aplicacion_ids_str = implode(', ', $aplicacion_ids);
    $sucursal_ids_str = implode(', ', $sucursal_ids);
    $tipo_vehiculo_ids_str = implode(', ', $tipo_vehiculo_ids);

    //placeholders
    $placeholders_aplicacion = implode(',', array_fill(0, count($aplicacion_ids), '?'));
    $placeholders_sucursal = implode(',', array_fill(0, count($sucursal_ids), '?'));
    $placeholders_tipo_vehiculo = implode(',', array_fill(0, count($tipo_vehiculo_ids), '?'));
    $types_aplicacion = implode('', array_fill(0, count($aplicacion_ids), 'i'));
    $types_sucursal = implode('', array_fill(0, count($sucursal_ids), 'i'));
    $types_tipo_vehiculo = implode('', array_fill(0, count($tipo_vehiculo_ids), 'i'));
    $types_ = $types_aplicacion.$types_tipo_vehiculo.$types_sucursal;
   
    if($placeholders_aplicacion){
        $count .= ' AND l.id_aplicacion IN (' . $placeholders_aplicacion . ')';
    }
    if($placeholders_tipo_vehiculo){
        $count .= ' AND l.id_tipo_vehiculo IN (' . $placeholders_tipo_vehiculo . ')';
    }
    if($placeholders_sucursal){
        $count .= ' AND i.id_sucursal IN (' . $placeholders_sucursal.')';
    }

}
$types = 'ddd';
$stmt  = $con->prepare($count);
if(count($filtros) > 0) {
    $types.=$types_;
    // Inicializamos los arrays vacíos si no están definidos
    $aplicacion_ids = $aplicacion_ids ?? [];
    $sucursal_ids = $sucursal_ids ?? [];
    $tipo_vehiculo_ids = $tipo_vehiculo_ids ?? [];

    // Unimos los arreglos en uno solo
    $combined_ids = array_merge($aplicacion_ids, $tipo_vehiculo_ids, $sucursal_ids);

    // Agregamos las variables adicionales al array
    $params = array_merge([$ancho, $alto, $diametro], $combined_ids);
  
    $stmt->bind_param($types,...$params);

}else{

    $stmt->bind_param($types, $ancho, $alto, $diametro);
}
$stmt->execute();
$stmt->bind_result($total);
$stmt->fetch();
$stmt->close();
/* print_r($count);
print_r($types);
print_r($params);
print_r($total);
die(); */

if($total > 0) {
    $sel = "SELECT l.*, i.Stock, i.Codigo, s.nombre, li.url_principal, li.url_frontal, li.url_perfil, li.url_piso, l.promocion, l.precio_promocion FROM llantas l 
    INNER JOIN inventario i ON l.id = i.id_Llanta 
    INNER JOIN sucursal s ON s.id = i.id_sucursal 
    LEFT JOIN llantas_imagenes li ON l.id = li.id_llanta WHERE i.Stock > 0 
    AND l.Ancho = ? AND l.Proporcion = ? AND l.Diametro = ?";

    if(count($filtros) > 0) {
        if($placeholders_aplicacion){
            $sel .= ' AND l.id_aplicacion IN (' . $placeholders_aplicacion . ')';
        }
        if($placeholders_tipo_vehiculo){
            $sel.= ' AND l.id_tipo_vehiculo IN (' . $placeholders_tipo_vehiculo . ')';
        }
        if($placeholders_sucursal){
            $sel .= ' AND i.id_sucursal IN (' . $placeholders_sucursal.')';
        }
        $res = $con->prepare($sel);
        $res->bind_param($types,...$params);
    }else{
        $res = $con->prepare($sel);
        $res->bind_param('ddd', $ancho, $alto, $diametro);
    }
    
    $res->execute();
    $resultado = $res->get_result();
    $res->free_result();
    $res->close();
    $resultado_f=[];
    while ($value = $resultado->fetch_assoc()) { 
        $resultado_f[]= $value;
    }

    $mensaje = 'Se encontró esa medida';
    $response =  array('estatus'=>true, 'mensaje'=>$mensaje, 'datos' => $resultado_f);
    echo json_encode($response);
}else{
    $mensaje = 'No se encontró esa medida';
    $response =  array('estatus'=>false, 'mensaje'=>$mensaje);
    echo json_encode($response);
}
?>