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

    $tipo = "Normal";
    $estatus ="Cancelada";
    $unidad = "pieza";

    //Declarando funciones
    function obtenerVentaTotal($con, $semana, $año, $tipo, $estatus, $hoy, $sucursal){

        $enc =0;
        $traer_id = $con->prepare("SELECT COUNT(*) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND tipo = ? AND estatus <> ? AND WEEKDAY(Fecha) =? AND id_sucursal =?");
        $traer_id->bind_param('ssssss', $semana, $año, $tipo, $estatus, $hoy, $sucursal);
        $traer_id->execute();
        $traer_id->bind_result($enc);
        $traer_id->fetch();
        $traer_id->close();

        $venta_hoy=0;
        if($enc>0){
           
            $traer = "SELECT SUM(Total) total FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND tipo = ? AND estatus <> ? AND WEEKDAY(Fecha) =? AND id_sucursal =?";
            $res = $con->prepare($traer);
            $res->bind_param('ssssss', $semana, $año, $tipo, $estatus, $hoy, $sucursal);
            $res->execute();
            $res->bind_result($venta_hoy);
            $res->fetch();
            $res->close();

           return $venta_hoy;
    }else{
        return $venta_hoy;
    }
}


    function obtenerGananciaTotal($con, $semana, $año, $tipo, $estatus, $hoy, $sucursal){

        $enc =0;
        $traer_id = $con->prepare("SELECT COUNT(*) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND tipo = ? AND estatus <> ? AND WEEKDAY(Fecha) =? AND id_sucursal =?");
        $traer_id->bind_param('ssssss', $semana, $año, $tipo, $estatus, $hoy, $sucursal);
        $traer_id->execute();
        $traer_id->bind_result($enc);
        $traer_id->fetch();
        $traer_id->close();

        $ganancia_hoy= 0;
      
       
        if($enc>0){

            $query = "SELECT * FROM `ventas` WHERE WEEK(Fecha) = '$semana' AND YEAR(Fecha) = '$año' AND tipo = '$tipo' AND estatus <> '$estatus' AND WEEKDAY(Fecha) ='$hoy' AND id_sucursal ='$sucursal'";
            
            $respuesta = mysqli_query($con, $query);

           
            
            while($fila = $respuesta->fetch_assoc()){
                $id_venta = $fila["id"];
                //echo json_encode("venta id ". $id_venta);
                $enc2 = 0;
                $traer_id = $con->prepare("SELECT COUNT(*) FROM `detalle_venta` WHERE id_Venta = ?");
                $traer_id->bind_param('i', $id_venta);
                $traer_id->execute();
                $traer_id->bind_result($enc2);
                $traer_id->fetch();
                $traer_id->close();
                
                if($enc2>0){
                    $traer = "SELECT * FROM `detalle_venta` WHERE id_Venta = '$id_venta'";
                    $result = mysqli_query($con, $traer);

                    $sumatoria = 0;
                    while($fila2 = $result->fetch_assoc()){
                        $id_detalle_vta = $fila2['id'];
                        $id_llanta = $fila2["id_Llanta"];
                        $cantidad = $fila2["Cantidad"];
                        $importe = $fila2["Importe"];

                        $costo = 0;
                        $traer_llanta = $con->prepare("SELECT precio_Inicial FROM `llantas` WHERE id = ?");
                        $traer_llanta->bind_param('i', $id_llanta);
                        $traer_llanta->execute();
                        $traer_llanta->bind_result($costo);
                        $traer_llanta->fetch();
                        $traer_llanta->close();

                        $costo_total_individual = $costo * $cantidad;

                        $ganancia_llanta  = floatval($importe) - floatval($costo_total_individual);
                        $sumatoria = $sumatoria + $ganancia_llanta;

                       // print_r("detalle de ". $sucursal ." con id ". $id_detalle_vta ." es: ". $sumatoria." ");
                    }
                   
                    $ganancia_hoy = $ganancia_hoy + $sumatoria;
                   
                }else{
                    $mnsj ="";
                    $response = array("estatus"=> false, $mnsj =>"Hubo un error, no hay detalle de venta que coincida con el id_venta, muy extraño...");

                }

             }

           return $ganancia_hoy;
    }else{
        return $ganancia_hoy;
    }

    }

//-------------------------ITERANDO SOBRE LAS SUCURSALES ACTUALES---------------------------------------//

    $traer = "SELECT COUNT(*) FROM sucursal";
    $r=$con->prepare($traer);
    $r->execute();
    $r->bind_result($total_sucursales);
    $r->fetch();
    $r->close();

  

    if($total_sucursales>0){

        $traer = "SELECT * FROM sucursal";
        $resp = mysqli_query($con, $traer);
        while($fila = $resp->fetch_assoc()){
            //Empezamos a recorrer sucursales
            $id_sucursal = $fila["id"];
            $venta_total = obtenerVentaTotal($con, $semana, $año, $tipo, $estatus, $hoy, $id_sucursal);
            $ganancia_total = obtenerGananciaTotal($con, $semana, $año, $tipo, $estatus, $hoy, $id_sucursal);
            $response[]=array("id" => $id_sucursal, 
                              "venta_hoy"=>$venta_total,
                              "ganancia_hoy"=>$ganancia_total);
        }

    }else{
        $response = array("estatus"=> false, $mnsj =>"No encontramos una sucursal");
    }

        echo json_encode($response, JSON_UNESCAPED_UNICODE);

    



               
  

}

    ?>