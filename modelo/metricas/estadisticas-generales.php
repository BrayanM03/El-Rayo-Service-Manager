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

    $entrada_normal_mes=0;
    $entrada_credito_mes=0;
    $entrada_apartado_mes=0;
    $entrada_pedido_mes=0;
    $estatus ="Cancelada";

    //Codigo que abarga la ganancia del mes

    $entrada_normal_mes_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE MONTH(fecha_corte) = ? AND YEAR(fecha_corte) = ? AND estatus <> ? AND (tipo = 'Normal')");
    $entrada_credito_mes_sql = $con->prepare("SELECT SUM(a.abono) FROM creditos c INNER JOIN `abonos` a ON a.id_credito = c.id WHERE MONTH(a.fecha_corte) = ? AND YEAR(a.fecha_corte) = ? AND c.estatus != 4");
    $entrada_pedido_mes_sql = $con->prepare("SELECT SUM(a.abono) FROM pedidos p INNER JOIN abonos_pedidos a ON p.id = a.id_pedido WHERE MONTH(a.fecha) = ? AND YEAR(a.fecha) = ? AND p.estatus <> 'Cancelado'");
    $entrada_apartado_mes_sql = $con->prepare("SELECT SUM(a.abono) FROM `apartados` ap INNER JOIN abonos_apartados a ON ap.id = a.id_apartado WHERE MONTH(a.fecha) = ? AND YEAR(a.fecha) = ? AND ap.estatus <> 'Cancelado'");

    $entrada_normal_mes_sql->bind_param('sss', $mes, $año, $estatus);
    $entrada_normal_mes_sql->execute();
    $entrada_normal_mes_sql->bind_result($entrada_normal_mes);
    $entrada_normal_mes_sql->fetch();
    $entrada_normal_mes_sql->close();

    $entrada_credito_mes_sql->bind_param('ss', $mes, $año);
    $entrada_credito_mes_sql->execute();
    $entrada_credito_mes_sql->bind_result($entrada_credito_mes);
    $entrada_credito_mes_sql->fetch();
    $entrada_credito_mes_sql->close();

    $entrada_pedido_mes_sql->bind_param('ss', $mes, $año);
    $entrada_pedido_mes_sql->execute();
    $entrada_pedido_mes_sql->bind_result($entrada_pedido_mes);
    $entrada_pedido_mes_sql->fetch();
    $entrada_pedido_mes_sql->close();

    $entrada_apartado_mes_sql->bind_param('ss', $mes, $año);
    $entrada_apartado_mes_sql->execute();
    $entrada_apartado_mes_sql->bind_result($entrada_apartado_mes);
    $entrada_apartado_mes_sql->fetch();
    $entrada_apartado_mes_sql->close();
           
    $ganancia_mes = floatval($entrada_apartado_mes) + floatval($entrada_credito_mes) + floatval($entrada_pedido_mes) + floatval($entrada_normal_mes);
    
    if($ganancia_mes == null){
            $ganancia_mes = 0;
      }
   
    //Codigo que abarca la ganancia del año
    $entrada_normal_año=0; $entrada_apartado_año=0; $entrada_pedido_año=0; $entrada_credito_año=0;
    $entrada_normal_año_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE YEAR(fecha_corte) = ? AND estatus <> ? AND tipo = 'Normal'");
    $entrada_credito_año_sql = $con->prepare("SELECT SUM(a.abono) FROM creditos c INNER JOIN `abonos` a ON a.id_credito = c.id WHERE YEAR(a.fecha_corte) = ? AND c.estatus != 4");
    $entrada_pedido_año_sql = $con->prepare("SELECT SUM(a.abono) FROM pedidos p INNER JOIN abonos_pedidos a ON p.id = a.id_pedido WHERE YEAR(a.fecha_corte) = ? AND p.estatus <> 'Cancelado'");
    $entrada_apartado_año_sql = $con->prepare("SELECT SUM(a.abono) FROM `apartados` ap INNER JOIN abonos_pedidos a ON ap.id = a.id_pedido WHERE YEAR(fecha_corte) = ? AND estatus <> 'Cancelado'");

    $entrada_normal_año_sql->bind_param('ss', $año, $estatus);
    $entrada_normal_año_sql->execute();
    $entrada_normal_año_sql->bind_result($entrada_normal_año);
    $entrada_normal_año_sql->fetch();
    $entrada_normal_año_sql->close();

    $entrada_credito_año_sql->bind_param('s', $año);
    $entrada_credito_año_sql->execute();
    $entrada_credito_año_sql->bind_result($entrada_credito_año);
    $entrada_credito_año_sql->fetch();
    $entrada_credito_año_sql->close();

    $entrada_pedido_año_sql->bind_param('s', $año);
    $entrada_pedido_año_sql->execute();
    $entrada_pedido_año_sql->bind_result($entrada_pedido_año);
    $entrada_pedido_año_sql->fetch();
    $entrada_pedido_año_sql->close();

    $entrada_apartado_año_sql->bind_param('s', $año);
    $entrada_apartado_año_sql->execute();
    $entrada_apartado_año_sql->bind_result($entrada_apartado_año);
    $entrada_apartado_año_sql->fetch();
    $entrada_apartado_año_sql->close();
    
    $ganancia_anual = $entrada_normal_año + $entrada_apartado_año + $entrada_pedido_año + $entrada_credito_año;
    if($ganancia_anual == null){
        $ganancia_anual = 0;
      }
       
      
    $total_ventas_sql = $con->prepare("SELECT COUNT(*) ventas FROM `ventas` WHERE YEAR(fecha_corte) = ? AND estatus <> ?");
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