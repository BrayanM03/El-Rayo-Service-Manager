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

  $tipo = "Normal";
  $tipoCred = "Credito";
  $estatus ="Pagado";
  $estatusAbierta = "Abierta";
  $sucursal = "Pedro";
  $unidad = "pieza";

  $id_usuario = $_SESSION['id_usuario'];

    $ganancia_efectivo = ganancia_meotodo_pago("efectivo", $con,  $semana, $año, $tipo, $estatus, $hoy, $sucursal, $unidad);
    //print_r($ganancia_efectivo);
    echo json_encode($ganancia_efectivo, JSON_UNESCAPED_UNICODE);
    
  function ganancia_meotodo_pago($metodo, $con, $semana, $año, $tipo, $estatus, $hoy, $sucursal, $unidad){
      
  

    $traer_id = $con->prepare("SELECT id FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND tipo = ? AND estatus = ? AND WEEKDAY(Fecha) =? AND id_Sucursal =? AND metodo_pago = ?");
    $traer_id->bind_param('sssssss', $semana, $año, $tipo, $estatus, $hoy, $sucursal, $metodo);
    $traer_id->execute();
    $resultado = $traer_id->get_result();
   
     $totalGananciaServicio = 0;
     $costo_metodo = 0;
    
     if($resultado->num_rows < 1){
         //echo "sin valores"; 
         return 0;
         $traer_id->close();
     }else{
        $costo_acumulado = 0;

         while($fila = $resultado->fetch_assoc()){
         $id_venta = $fila["id"];

         $traer_idLlanta = $con->prepare("SELECT id_Llanta, Cantidad FROM `detalle_venta` WHERE id_Venta = ? AND Unidad = ?");
         $traer_idLlanta->bind_param('ss', $id_venta, $unidad);
         $traer_idLlanta->execute();
         $array_id_llanta = $traer_idLlanta->get_result();

         if($array_id_llanta->num_rows < 1){
           
                            $servicio = "servicio";
                            $traer_servicio = $con->prepare("SELECT * FROM `detalle_venta` WHERE id_Venta = ? AND Unidad = ?");
                            $traer_servicio->bind_param('ss', $id_venta, $servicio);
                            $traer_servicio->execute();
                            $array_servicio = $traer_servicio->get_result();
                            
                            if($array_servicio->num_rows < 1){

                            }else{
                                while($row = $array_servicio->fetch_assoc()){
                                $importe = $row["Importe"];
                                $totalGananciaServicio = $totalGananciaServicio + $importe;
                                }
                            }
         }else{
          
             while($fila2 = $array_id_llanta->fetch_assoc()){
               
             $id_llanta = $fila2["id_Llanta"];
             $cantidad = $fila2["Cantidad"];
            
            
             
             $traer_Costo = $con->prepare("SELECT precio_Inicial FROM `llantas` WHERE id = ?");
             $traer_Costo->bind_param('s', $id_llanta);
             $traer_Costo->execute();
             $array_costos = $traer_Costo->get_result();
             
             if($array_costos->num_rows < 1){
                 
             }else{
              
                 while($fila3 = $array_costos->fetch_assoc()){

                     $costo_unitario = $fila3["precio_Inicial"];
                     $costo_total = $costo_unitario * $cantidad;
                     $costo_acumulado = $costo_acumulado + $costo_total;
                  
 
             }

             $traer_Costo->close();
             
         }
         };
         $traer_idLlanta->close();
         
     }
     }
   
    $traer_id->close();

    $venta_total_metodo = 0;
    $ventas_total_hoy_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND estatus = ? AND WEEKDAY(Fecha) =? AND id_Sucursal =?  AND metodo_pago = ?");
    $ventas_total_hoy_sql->bind_param('ssssss', $semana, $año, $estatus, $hoy, $sucursal, $metodo);
    $ventas_total_hoy_sql->execute();
    $ventas_total_hoy_sql->bind_result($venta_total_metodo);
    $ventas_total_hoy_sql->fetch();
    $ventas_total_hoy_sql->close();
    
    if($venta_total_metodo == null){
        $venta_total_metodo = 0;
    }
    
  
    $ganancia_metodo = $venta_total_metodo - $costo_acumulado;
    return $ganancia_metodo;

    /* if ($costo_metodo == 0) {
        return $totalGananciaServicio;
        
    }else{
        $ganancia_metodo = $venta_total_metodo - $costo_metodo;
        return $ganancia_metodo;
    }  */
    
 }
  }