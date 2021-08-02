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

    
    $estatus ="Cancelada";
    $Lunes = 0;
    $Martes = 1;
    $Miercoles = 2;
    $Jueves = 3;
    $Viernes= 4;
    $Sabado = 5;
    $Domingo = 6;

    $ganancia_lunes_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND estatus <> ? AND WEEKDAY(Fecha) =?");
               $ganancia_lunes_sql->bind_param('ssss', $semana, $año, $estatus, $Lunes);
               $ganancia_lunes_sql->execute();
               $ganancia_lunes_sql->bind_result($ganancia_Lunes);
               $ganancia_lunes_sql->fetch();
               $ganancia_lunes_sql->close();

               if($ganancia_Lunes == null){
                   $ganancia_Lunes = 0;
               } 



               $ganancia_martes_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND estatus <> ? AND WEEKDAY(Fecha) =?");
               $ganancia_martes_sql->bind_param('ssss', $semana, $año, $estatus, $Martes);
               $ganancia_martes_sql->execute();
               $ganancia_martes_sql->bind_result($ganancia_Martes);
               $ganancia_martes_sql->fetch();
               $ganancia_martes_sql->close();

               if($ganancia_Martes == null){
                   $ganancia_Martes = 0;
               } 

               

               $ganancia_miercoles_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND estatus <> ? AND WEEKDAY(Fecha) =?");
               $ganancia_miercoles_sql->bind_param('ssss', $semana, $año, $estatus, $Miercoles);
               $ganancia_miercoles_sql->execute();
               $ganancia_miercoles_sql->bind_result($ganancia_Miercoles);
               $ganancia_miercoles_sql->fetch();
               $ganancia_miercoles_sql->close();

               if($ganancia_Miercoles == null){
                   $ganancia_Miercoles = 0;
               } 

               $ganancia_jueves_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND estatus <> ? AND WEEKDAY(Fecha) =?");
               $ganancia_jueves_sql->bind_param('ssss', $semana, $año, $estatus, $Jueves);
               $ganancia_jueves_sql->execute();
               $ganancia_jueves_sql->bind_result($ganancia_Jueves);
               $ganancia_jueves_sql->fetch();
               $ganancia_jueves_sql->close();

               if($ganancia_Jueves == null){
                   $ganancia_Jueves = 0;
               } 


               $ganancia_viernes_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND estatus <> ? AND WEEKDAY(Fecha) =?");
               $ganancia_viernes_sql->bind_param('ssss', $semana, $año, $estatus, $Viernes);
               $ganancia_viernes_sql->execute();
               $ganancia_viernes_sql->bind_result($ganancia_Viernes);
               $ganancia_viernes_sql->fetch();
               $ganancia_viernes_sql->close();

               if($ganancia_Viernes == null){
                   $ganancia_Viernes = 0;
               } 


               $ganancia_sabado_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND estatus <> ? AND WEEKDAY(Fecha) =?");
               $ganancia_sabado_sql->bind_param('ssss', $semana, $año, $estatus, $Sabado);
               $ganancia_sabado_sql->execute();
               $ganancia_sabado_sql->bind_result($ganancia_Sabado);
               $ganancia_sabado_sql->fetch();
               $ganancia_sabado_sql->close();

               if($ganancia_Sabado == null){
                   $ganancia_Sabado = 0;
               } 


               $ganancia_domingo_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND estatus <> ? AND WEEKDAY(Fecha) =?");
               $ganancia_domingo_sql->bind_param('ssss', $semana, $año, $estatus, $Domingo);
               $ganancia_domingo_sql->execute();
               $ganancia_domingo_sql->bind_result($ganancia_Domingo);
               $ganancia_domingo_sql->fetch();
               $ganancia_domingo_sql->close();

               if($ganancia_Domingo == null){
                   $ganancia_Domingo = 0;
               } 

               
               $ganancia_domingo_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND estatus <> ?");
               $ganancia_domingo_sql->bind_param('sss', $semana, $año, $estatus);
               $ganancia_domingo_sql->execute();
               $ganancia_domingo_sql->bind_result($ganancia_Semanal);
               $ganancia_domingo_sql->fetch();
               $ganancia_domingo_sql->close();

               if($ganancia_Semanal == null){
                   $ganancia_Semanal = 0;
               } 

               $ganancia_domingo_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND estatus <> ? AND WEEKDAY(Fecha) =?");
               $ganancia_domingo_sql->bind_param('ssss', $semana, $año, $estatus, $hoy);
               $ganancia_domingo_sql->execute();
               $ganancia_domingo_sql->bind_result($ganancia_Hoy);
               $ganancia_domingo_sql->fetch();
               $ganancia_domingo_sql->close();

               if($ganancia_Hoy == null){
                   $ganancia_Hoy = 0;
               } 

               $ganancia_domingo_sql = $con->prepare("SELECT COUNT(*) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND estatus <> ? AND WEEKDAY(Fecha) =?");
               $ganancia_domingo_sql->bind_param('ssss', $semana, $año, $estatus, $hoy);
               $ganancia_domingo_sql->execute();
               $ganancia_domingo_sql->bind_result($ventas_Hoy);
               $ganancia_domingo_sql->fetch();
               $ganancia_domingo_sql->close();

               if($ventas_Hoy == null){
                   $ventas_Hoy = 0;
               } 


               $data = array("ganancia_lunes" => $ganancia_Lunes, "ganancia_martes" => $ganancia_Martes, "ganancia_miercoles" => $ganancia_Miercoles,
               "ganancia_jueves" => $ganancia_Jueves, "ganancia_viernes" => $ganancia_Viernes,"ganancia_sabado" => $ganancia_Sabado, "ganancia_domingo" => $ganancia_Domingo, "ganancia_semanal" => $ganancia_Semanal, "ganancia_hoy"=> $ganancia_Hoy, "ventas_hoy"=> $ventas_Hoy);
        

        if (isset($data)) {
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }else{
            print_r("Sin datos");
        }
    

  }

  /*SELECT Total, Fecha, (ELT(WEEKDAY(Fecha) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS DIA_SEMANA
FROM ventas WHERE WEEK(Fecha) = 26 AND YEAR(Fecha) =2021 AND WEEKDAY(Fecha) =0*/


?>