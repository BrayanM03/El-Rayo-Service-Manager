<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

date_default_timezone_set("America/Matamoros");
  $hora = date("h:i a");
  $fecha = date("Y-m-d"); 
  $mes = date("m");
  $año = date("Y");
  $semana = (date("W")-1);
  $hoy = date("w") - 1;

  //Medida mas vendida en el mes
  $medida_mes = "SELECT CASE WHEN l.Proporcion = 0 THEN CONCAT(l.Ancho, '', 'R', l.Diametro) ELSE CONCAT(l.Ancho, '/', l.Proporcion, 'R', l.Diametro) END AS medida, 
  SUM(dv.Cantidad) AS total_vendido FROM detalle_venta dv INNER JOIN ventas v ON v.id = dv.id_Venta INNER JOIN llantas l ON l.id = dv.id_Llanta 
  WHERE MONTH(v.Fecha) = ? AND YEAR(v.Fecha) = ? GROUP BY l.Ancho, l.Proporcion, l.Diametro ORDER BY total_vendido DESC LIMIT 10";
  $stmt = $con->prepare($medida_mes);
  $stmt->bind_param('ss', $mes, $año);
  $stmt->execute();
  $resultado = $stmt->get_result();
  $stmt->free_result();

  $arreglo_general = array();
  $arreglo_general['medidas'] = array();
  $arreglo_general['cantidades'] = array();
  
  foreach ($resultado as $key => $value) {
      $cantidad = intval($value['total_vendido']);
      $medida = $value['medida'];
      array_push( $arreglo_general['medidas'], $medida);
      array_push($arreglo_general['cantidades'], $cantidad);
    }
  
  echo json_encode($arreglo_general);