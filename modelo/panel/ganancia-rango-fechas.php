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

    

  $querySuc = "SELECT COUNT(*) FROM sucursal";
  $resp=$con->prepare($querySuc);
  $resp->execute();
  $resp->bind_result($total_suc);
  $resp->fetch();
  $resp->close();

  if($total_suc>0){
      $querySuc = "SELECT * FROM sucursal";
      $resp = mysqli_query($con, $querySuc);

      $ganancia_por_suc = [];

      while ($row = $resp->fetch_assoc()){
          $suc= $row['id'];
          $nombre = $row['nombre'];

          
    $ganancia_rango_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE YEAR(Fecha) = ? AND Fecha BETWEEN ? AND ? AND estatus <> ? AND id_sucursal =?");
    $ganancia_rango_sql->bind_param('sssss', $año, $fecha_inicial, $fecha_final, $estatus, $suc);
    $ganancia_rango_sql->execute();
    $ganancia_rango_sql->bind_result($ganancia_rango_suc);
    $ganancia_rango_sql->fetch();
    $ganancia_rango_sql->close();

    
    if($ganancia_rango_suc == null){
      $ganancia_rango_suc = 0;
  } 

       $ganancia_por_suc[] = array("id"=>$suc, "nombre"=>$nombre, "ganancia"=>$ganancia_rango_suc);
         
          }
  }
    
    $data = array("ganancia_rango" => $ganancia_rango, "ganancia_suc" => $ganancia_por_suc);


if (isset($data)) {
 echo json_encode($data, JSON_UNESCAPED_UNICODE);
}else{
 print_r("Sin datos");
}

  }