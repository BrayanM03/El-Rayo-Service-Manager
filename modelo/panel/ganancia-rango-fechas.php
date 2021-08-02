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
  $semana = date("W");
  $hoy = date("w") - 1;

  if($_POST){

    $fecha_inicial = $_POST["fecha_inicial"];
    $fecha_final = $_POST["fecha_final"];
    $estatus ="Cancelada";
    $ganancia_rango_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE YEAR(Fecha) = ? AND Fecha BETWEEN ? AND ? AND estatus <> ?");
    $ganancia_rango_sql->bind_param('ssss', $año, $fecha_inicial, $fecha_final, $estatus);
    $ganancia_rango_sql->execute();
    $ganancia_rango_sql->bind_result($ganancia_rango);
    $ganancia_rango_sql->fetch();
    $ganancia_rango_sql->close();

    if($ganancia_rango == null){
        $ganancia_rango = 0;
    } 

    $suc = "Pedro";
    $ganancia_rango_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE YEAR(Fecha) = ? AND Fecha BETWEEN ? AND ? AND estatus <> ? AND id_Sucursal =?");
    $ganancia_rango_sql->bind_param('sssss', $año, $fecha_inicial, $fecha_final, $estatus, $suc);
    $ganancia_rango_sql->execute();
    $ganancia_rango_sql->bind_result($ganancia_rango_pedro);
    $ganancia_rango_sql->fetch();
    $ganancia_rango_sql->close();

    
    if($ganancia_rango_pedro == null){
      $ganancia_rango_pedro = 0;
  } 

    $suc = "Sendero";
    $ganancia_rango_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE YEAR(Fecha) = ? AND Fecha BETWEEN ? AND ? AND estatus <> ? AND id_Sucursal =?");
    $ganancia_rango_sql->bind_param('sssss', $año, $fecha_inicial, $fecha_final, $estatus, $suc);
    $ganancia_rango_sql->execute();
    $ganancia_rango_sql->bind_result($ganancia_rango_sendero);
    $ganancia_rango_sql->fetch();
    $ganancia_rango_sql->close();

    if($ganancia_rango_sendero == null){
        $ganancia_rango_sendero = 0;
    } 

    
    $data = array("ganancia_rango" => $ganancia_rango, "ganancia_rango_pedro" => $ganancia_rango_pedro, "ganancia_rango_sendero"=> $ganancia_rango_sendero);


if (isset($data)) {
 echo json_encode($data, JSON_UNESCAPED_UNICODE);
}else{
 print_r("Sin datos");
}

  }