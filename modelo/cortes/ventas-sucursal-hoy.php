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
    $sucursalP = "Pedro";
    $sucursalS = "Sendero";



               $ganancia_domingo_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND estatus <> ? AND WEEKDAY(Fecha) =? AND id_Sucursal =?");
               $ganancia_domingo_sql->bind_param('sssss', $semana, $año, $estatus, $hoy, $sucursalP);
               $ganancia_domingo_sql->execute();
               $ganancia_domingo_sql->bind_result($ganancia_pedro);
               $ganancia_domingo_sql->fetch();
               $ganancia_domingo_sql->close();

               if($ganancia_pedro == null){
                   $ganancia_pedro = 0;
               } 

               
               $ganancia_domingo_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND estatus <> ? AND WEEKDAY(Fecha) =? AND id_Sucursal =?");
               $ganancia_domingo_sql->bind_param('sssss', $semana, $año, $estatus, $hoy, $sucursalS);
               $ganancia_domingo_sql->execute();
               $ganancia_domingo_sql->bind_result($ganancia_sendero);
               $ganancia_domingo_sql->fetch();
               $ganancia_domingo_sql->close();

               if($ganancia_sendero == null){
                   $ganancia_sendero = 0;
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


               $data = array("ganancia_pedro"=> $ganancia_pedro,"ganancia_sendero"=> $ganancia_sendero, "ventas_hoy"=> $ventas_Hoy);
        

        if (isset($data)) {
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }else{
            print_r("Sin datos");
        }
    

  }

  /*SELECT Total, Fecha, (ELT(WEEKDAY(Fecha) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS DIA_SEMANA
FROM ventas WHERE WEEK(Fecha) = 26 AND YEAR(Fecha) =2021 AND WEEKDAY(Fecha) =0*/


?>