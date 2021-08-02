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

  if($_POST){
    $estatus =5;
    $suc_pedro = "Pedro";
    $ganancia_pedro_sql = $con->prepare("SELECT COUNT(*) FROM `creditos` INNER JOIN ventas ON creditos.id_venta = ventas.id WHERE creditos.estatus <> ? AND ventas.id_Sucursal = ? AND YEAR(ventas.Fecha) =?");
    $ganancia_pedro_sql->bind_param('sss', $estatus, $suc_pedro, $año);
    $ganancia_pedro_sql->execute();
    $ganancia_pedro_sql->bind_result($ganancia_pedro);
    $ganancia_pedro_sql->fetch();
    $ganancia_pedro_sql->close();

    $suc_sendero = "Sendero";
    $ganancia_pedro_sql = $con->prepare("SELECT COUNT(*) FROM `creditos` INNER JOIN ventas ON creditos.id_venta = ventas.id WHERE creditos.estatus <> ? AND ventas.id_Sucursal = ? AND YEAR(ventas.Fecha) =?");
    $ganancia_pedro_sql->bind_param('sss', $estatus,  $suc_sendero, $año);
    $ganancia_pedro_sql->execute();
    $ganancia_pedro_sql->bind_result($ganancia_sendero);
    $ganancia_pedro_sql->fetch();
    $ganancia_pedro_sql->close();

    $data = array("creditos_pedro" => $ganancia_pedro, "creditos_sendero" => $ganancia_sendero);
        

        if (isset($data)) {
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }else{
            print_r("Sin datos");
        }

  }


  ?>