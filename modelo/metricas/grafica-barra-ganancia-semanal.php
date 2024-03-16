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


  if($_POST){

    
    $estatus ="Cancelada";
    $Lunes = 0;
    $Martes = 1;
    $Miercoles = 2;
    $Jueves = 3;
    $Viernes= 4;
    $Sabado = 5;
    $Domingo ="6";
    $dias_semanas = array(['nombre'=>'ganancia_lunes', 'dia'=>0], ['nombre'=>'ganancia_martes', 'dia'=>1], ['nombre'=>'ganancia_miercoles', 'dia'=>2],
    ['nombre'=>'ganancia_jueves', 'dia'=>3], ['nombre'=>'ganancia_viernes', 'dia'=>4], ['nombre'=>'ganancia_sabado', 'dia'=>5], ['nombre'=>'ganancia_domingo', 'dia'=>6]);
    $ingreso_semana=[];
    foreach($dias_semanas as $element){
        $ingreso_apartado=0;
        $ingreso_credito=0;
        $ingreso_normal=0;
        $ingreso_pedido =0;
        $dia = $element['dia'];
        $nombre_ = $element['nombre'];
        $venta_normal = $con->prepare("SELECT SUM(v.total) FROM ventas v WHERE WEEK(v.Fecha) = ? AND YEAR(v.Fecha) = ? AND v.estatus <> ? AND v.tipo ='Normal' AND WEEKDAY(v.Fecha) =?");
        $venta_credito = $con->prepare("SELECT SUM(a.abono) FROM creditos c INNER JOIN abonos a ON c.id = a.id_credito WHERE WEEK(a.fecha) = ? AND YEAR(a.fecha) = ? AND WEEKDAY(a.fecha) =? AND c.estatus != 4");
        $venta_pedido =$con->prepare("SELECT SUM(a.abono) FROM pedidos p INNER JOIN abonos_pedidos a ON p.id = a.id_pedido WHERE WEEK(a.fecha) = ? AND YEAR(a.fecha) = ? AND WEEKDAY(a.fecha) =? AND p.estatus != 'Cancelado'");
        $venta_apartado = $con->prepare("SELECT SUM(a.abono) FROM apartados ap INNER JOIN abonos_apartados a ON ap.id = a.id_apartado WHERE WEEK(a.fecha) = ? AND YEAR(a.fecha) = ? AND WEEKDAY(a.fecha) =? AND ap.estatus != 'Cancelado'");

        $venta_normal->bind_param('ssss', $semana, $año, $estatus, $dia);
        $venta_normal->execute();
        $venta_normal->bind_result($ingreso_normal);
        $venta_normal->fetch();
        $venta_normal->close();

        $venta_credito->bind_param('sss', $semana, $año, $dia);
        $venta_credito->execute();
        $venta_credito->bind_result($ingreso_credito);
        $venta_credito->fetch();
        $venta_credito->close();

        $venta_pedido->bind_param('sss', $semana, $año, $dia);
        $venta_pedido->execute();
        $venta_pedido->bind_result($ingreso_pedido);
        $venta_pedido->fetch();
        $venta_pedido->close();

        $venta_apartado->bind_param('sss', $semana, $año, $dia);
        $venta_apartado->execute();
        $venta_apartado->bind_result($ingreso_apartado);
        $venta_apartado->fetch();
        $venta_apartado->close();
        $ingreso_dia = $ingreso_normal + $ingreso_apartado + $ingreso_credito + $ingreso_pedido;
       $ingreso_semana =  array_merge($ingreso_semana, array($nombre_=>$ingreso_dia));
    }
        /*$data = array("ganancia_lunes" => $ganancia_Lunes, "ganancia_martes" => $ganancia_Martes, "ganancia_miercoles" => $ganancia_Miercoles,
        "ganancia_jueves" => $ganancia_Jueves, "ganancia_viernes" => $ganancia_Viernes,"ganancia_sabado" => $ganancia_Sabado, "ganancia_domingo" => $ganancia_Domingo, "ganancia_semanal" => $ganancia_Semanal, "ganancia_hoy"=> $ganancia_Hoy, "ventas_hoy"=> $ventas_Hoy);*/

        if (isset($ingreso_semana)) {
            echo json_encode($ingreso_semana, JSON_UNESCAPED_UNICODE);
        }else{
            print_r("Sin datos");
        }
    

  }

  /*SELECT Total, Fecha, (ELT(WEEKDAY(Fecha) + 1, 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo')) AS DIA_SEMANA
FROM ventas WHERE WEEK(Fecha) = 26 AND YEAR(Fecha) =2021 AND WEEKDAY(Fecha) =0*/


?>