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

    
    $estatus ="Cancelada";
    $ganancia_mes_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE MONTH(Fecha) = ? AND YEAR(Fecha) = ? AND estatus <> ?");
               $ganancia_mes_sql->bind_param('sss', $mes, $año, $estatus);
               $ganancia_mes_sql->execute();
               $ganancia_mes_sql->bind_result($ganancia_mes);
               $ganancia_mes_sql->fetch();
               $ganancia_mes_sql->close();

               if($ganancia_mes == null){
                 $ganancia_mes = 0;
               }

               $ganancia_año_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE YEAR(Fecha) = ? AND estatus <> ?");
               $ganancia_año_sql->bind_param('ss', $año, $estatus);
               $ganancia_año_sql->execute();
               $ganancia_año_sql->bind_result($ganancia_anual);
               $ganancia_año_sql->fetch();
               $ganancia_año_sql->close();  

               
               if($ganancia_anual == null){
                $ganancia_anual = 0;
              }
               
               $total_ventas_sql = $con->prepare("SELECT COUNT(*) ventas FROM `ventas` WHERE YEAR(Fecha) = ? AND estatus <> ?");
               $total_ventas_sql->bind_param('ss', $año, $estatus);
               $total_ventas_sql->execute();
               $total_ventas_sql->bind_result($total_venta);
               $total_ventas_sql->fetch();
               $total_ventas_sql->close(); 

               
               if($total_venta == null){
                $total_venta = 0;
              }

               $finalizado = 3;
               $cancelado = 5;
               $creditos_pendientes_sql = $con->prepare("SELECT COUNT(*) FROM `creditos` WHERE estatus <> ? AND estatus <> ?");
               $creditos_pendientes_sql->bind_param('ss', $finalizado, $cancelado);
               $creditos_pendientes_sql->execute();
               $creditos_pendientes_sql->bind_result($creditos_pendientes);
               $creditos_pendientes_sql->fetch();
               $creditos_pendientes_sql->close(); 

               $data = array("ganancia_mes" => $ganancia_mes, "ganancia_anual" => $ganancia_anual,  "total_venta" => $total_venta, "creditos_pendientes" => $creditos_pendientes);
        

        if (isset($data)) {
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }else{
            print_r("Sin datos");
        }
    

  }

?>