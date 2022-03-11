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
  $estatus ="Pagado";
  $sucursal = $_POST["sucursal_id"];
  $unidad = "pieza";

  $id_usuario = $_SESSION['id_usuario'];

  if($_POST){

    //Obtener ganancia



      $ventas_total_hoy_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND estatus = ? AND WEEKDAY(Fecha) =? AND id_sucursal =?");
               $ventas_total_hoy_sql->bind_param('sssss', $semana, $año, $estatus, $hoy, $sucursal);
               $ventas_total_hoy_sql->execute();
               $ventas_total_hoy_sql->bind_result($venta_total);
               $ventas_total_hoy_sql->fetch();
               $ventas_total_hoy_sql->close();

               if($venta_total == null){
                $venta_total = 0;
                }

                $costo_acumulado = traer_ganancia($con, $semana, $año, $tipo, $estatus, $hoy, $sucursal, $unidad);
                $ganancia_total = $venta_total - $costo_acumulado;

                $ganancia_transferencia = ganancia_meotodo_pago("Transferencia", $con, $semana, $año, $tipo, $estatus, $hoy, $sucursal, $unidad);
                $ganancia_efectivo = ganancia_meotodo_pago("Efectivo", $con, $semana, $año, $tipo, $estatus, $hoy, $sucursal, $unidad);
                $ganancia_tarjeta = ganancia_meotodo_pago("Tarjeta", $con, $semana, $año, $tipo, $estatus, $hoy, $sucursal, $unidad);
                $ganancia_cheque = ganancia_meotodo_pago("Cheque", $con, $semana, $año, $tipo, $estatus, $hoy, $sucursal, $unidad); 
                $ganancia_sin_definir = ganancia_meotodo_pago("Cheque", $con, $semana, $año, $tipo, $estatus, $hoy, $sucursal, $unidad);

                //Obtener total de creditos realizados
                $tipoCred = "Credito";
                $ganancia_domingo_sql = $con->prepare("SELECT COUNT(*) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND tipo = ? AND WEEKDAY(Fecha) = ? AND id_sucursal =?");
                $ganancia_domingo_sql->bind_param('sssss', $semana,  $año,  $tipoCred, $hoy, $sucursal);
                $ganancia_domingo_sql->execute();
                $ganancia_domingo_sql->bind_result($creditos_realizados);
                $ganancia_domingo_sql->fetch();
                $ganancia_domingo_sql->close();
 
                if($creditos_realizados == null){
                    $creditos_realizados = 0;
                }

                 //Obtener total de ventas realizados
                $ventas_total_hoy_sql = $con->prepare("SELECT COUNT(*) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND estatus = ? AND WEEKDAY(Fecha) =? AND id_sucursal =?");
               $ventas_total_hoy_sql->bind_param('sssss', $semana, $año, $estatus, $hoy, $sucursal);
               $ventas_total_hoy_sql->execute();
               $ventas_total_hoy_sql->bind_result($ventas_realizadas);
               $ventas_total_hoy_sql->fetch();
               $ventas_total_hoy_sql->close();

               if($venta_total == null){
                $venta_total = 0;
                }


                //Consultamos la apertura de datos
                $traer_apertura = $con->prepare("SELECT apertura FROM sucursal WHERE id =?");
                $traer_apertura->bind_param('i',$sucursal);
                $traer_apertura->execute();
                $traer_apertura->bind_result($apertura);
                $traer_apertura->fetch();
                $traer_apertura->close();
               $apertura = floatval($apertura);
            
                //Insertamos la informacion en la tabla de historial de cortes
                $query = "INSERT INTO cortes (id, usuario, id_sucursal, fecha, hora, total_venta, total_ganancia, ganancia_transferencia, ganancia_efectivo, ganancia_tarjeta, ganancia_cheque, ganancia_sin_definir, creditos_realizados, ventas_realizadas, apertura) VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                $resultado = $con->prepare($query);
                $resultado->bind_param('sssssssssssssd', $id_usuario, $sucursal, $fecha, $hora, $venta_total, $ganancia_total, $ganancia_transferencia, $ganancia_efectivo, $ganancia_tarjeta, $ganancia_cheque, $ganancia_sin_definir, $creditos_realizados, $ventas_realizadas, $apertura);
                $resultado->execute();
                $resultado->close(); 

               

                $query = "UPDATE sucursal SET corte = 1 WHERE id =?";
                $resultado = $con->prepare($query);
                $resultado->bind_param('s', $sucursal);
                $resultado->execute();
                $resultado->close(); 

                $query = "UPDATE sucursal SET apertura = 0 WHERE id =?";
                $resultado = $con->prepare($query);
                $resultado->bind_param('s', $sucursal);
                $resultado->execute();
                $resultado->close(); 
                
                print_r(1);
               /*   $data[] = array("costo_acumulado"=> $costo_acumulado,"ganancia_total"=> $ganancia_total, "ganancia_transferencia"=> $ganancia_transferencia,
                "ganancia_efectivo"=> $ganancia_efectivo,  "ganancia_tarjeta"=> $ganancia_tarjeta, "ganancia_cheque"=> $ganancia_cheque,  "ganancia_sin_definir"=> $ganancia_sin_definir);
                
                echo json_encode($data, JSON_UNESCAPED_UNICODE);  */
    

  }//puto


  //Funciones para calcular la ganancia

  function traer_ganancia($con, $semana, $año, $tipo, $estatus, $hoy, $sucursal, $unidad){
      
    

    $traer_id = $con->prepare("SELECT id FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND tipo = ? AND estatus = ? AND WEEKDAY(Fecha) =? AND id_sucursal =?");
    $traer_id->bind_param('ssssss', $semana, $año, $tipo, $estatus, $hoy, $sucursal);
    $traer_id->execute();
    $resultado = $traer_id->get_result();
     $costo_acumulado = 0;
     if($resultado->num_rows < 1){
         //echo "sin valores"; 
         $traer_id->close();
     }else{
         while($fila = $resultado->fetch_assoc()){
         $id_venta = $fila["id"];

         $traer_idLlanta = $con->prepare("SELECT id_Llanta, Cantidad FROM `detalle_venta` WHERE id_Venta = ? AND Unidad = ?");
         $traer_idLlanta->bind_param('ss', $id_venta, $unidad);
         $traer_idLlanta->execute();
         $array_id_llanta = $traer_idLlanta->get_result();

         if($array_id_llanta->num_rows < 1){
            // echo "sin valores"; 
         }else{
             while($fila2 = $array_id_llanta->fetch_assoc()){

             $id_llanta = $fila2["id_Llanta"];
             $cantidad = $fila2["Cantidad"];
             
             $traer_Costo = $con->prepare("SELECT precio_Inicial FROM `llantas` WHERE id = ?");
             $traer_Costo->bind_param('s', $id_llanta);
             $traer_Costo->execute();
             $array_costos = $traer_Costo->get_result();
             if($array_costos->num_rows < 1){
                 //echo "sin valores"; 
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


     return $costo_acumulado;

   
    
 }
  }

  function ganancia_meotodo_pago($metodo, $con, $semana, $año, $tipo, $estatus, $hoy, $sucursal, $unidad){
      
  

    $traer_id = $con->prepare("SELECT id FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND tipo = ? AND estatus = ? AND WEEKDAY(Fecha) =? AND id_sucursal =? AND metodo_pago = ?");
    $traer_id->bind_param('sssssss', $semana, $año, $tipo, $estatus, $hoy, $sucursal, $metodo);
    $traer_id->execute();
    $resultado = $traer_id->get_result();
     $costo_acumulado = 0;
     if($resultado->num_rows < 1){
         //echo "sin valores"; 
         return 0;
         $traer_id->close();
     }else{
         while($fila = $resultado->fetch_assoc()){
         $id_venta = $fila["id"];

         $traer_idLlanta = $con->prepare("SELECT id_Llanta, Cantidad FROM `detalle_venta` WHERE id_Venta = ? AND Unidad = ?");
         $traer_idLlanta->bind_param('ss', $id_venta, $unidad);
         $traer_idLlanta->execute();
         $array_id_llanta = $traer_idLlanta->get_result();

         if($array_id_llanta->num_rows < 1){
            // echo "sin valores";    
            
            $costo_metodo = 0;
         }else{
             while($fila2 = $array_id_llanta->fetch_assoc()){

             $id_llanta = $fila2["id_Llanta"];
             $cantidad = $fila2["Cantidad"];
             
             $traer_Costo = $con->prepare("SELECT precio_Inicial FROM `llantas` WHERE id = ?");
             $traer_Costo->bind_param('s', $id_llanta);
             $traer_Costo->execute();
             $array_costos = $traer_Costo->get_result();
             if($array_costos->num_rows < 1){
                 //echo "sin valores"; 
             }else{
                 while($fila3 = $array_costos->fetch_assoc()){
                     $costo_unitario = $fila3["precio_Inicial"];
                     $costo_total = $costo_unitario * $cantidad;
                     $costo_metodo = $costo_acumulado + $costo_total;
                    
 
             }

             $traer_Costo->close();
             
         }
         };
         $traer_idLlanta->close();
         
     }
     }
   
    $traer_id->close();

    $venta_total_metodo =0;
    $ventas_total_hoy_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND estatus = ? AND WEEKDAY(Fecha) =? AND id_sucursal =?  AND metodo_pago = ?");
    $ventas_total_hoy_sql->bind_param('ssssss', $semana, $año, $estatus, $hoy, $sucursal, $metodo);
    $ventas_total_hoy_sql->execute();
    $ventas_total_hoy_sql->bind_result($venta_total_metodo);
    $ventas_total_hoy_sql->fetch();
    $ventas_total_hoy_sql->close();
    
    if($venta_total_metodo == null){
        $venta_total_metodo = 0;
    }
  
    $ganancia_metodo = $venta_total_metodo - $costo_metodo;
    return $ganancia_metodo;
    
 }
  }


?>