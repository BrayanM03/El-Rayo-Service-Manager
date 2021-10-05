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
  $sucursal = $_POST["sucursal"];
  $unidad = "pieza";

  $id_usuario = $_SESSION['id_usuario'];

  if($_POST){

    //Obtener ganancia

      $ventas_total_hoy_sql = $con->prepare("SELECT SUM(Total) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ?  AND tipo = ? AND estatus = ? AND WEEKDAY(Fecha) =? AND id_Sucursal =?");
               $ventas_total_hoy_sql->bind_param('ssssss', $semana, $año, $tipo, $estatus, $hoy, $sucursal);
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

                //Obtener total de ventas
                $ganancia_domingo_sql = $con->prepare("SELECT COUNT(*) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND tipo = ? AND estatus = ? AND WEEKDAY(Fecha) =? AND id_Sucursal =?");
                $ganancia_domingo_sql->bind_param('ssssss', $semana, $año, $tipo, $estatus, $hoy, $sucursal);
                $ganancia_domingo_sql->execute();
                $ganancia_domingo_sql->bind_result($ventas_Hoy);
                $ganancia_domingo_sql->fetch();
                $ganancia_domingo_sql->close();

                if($ventas_Hoy == null){
                    $ventas_Hoy = 0;
                }

                //Obtener total de creditos realizados
                $ganancia_domingo_sql = $con->prepare("SELECT COUNT(*) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND tipo = ? AND WEEKDAY(Fecha) = ? AND id_Sucursal =?");
                $ganancia_domingo_sql->bind_param('sssss', $semana,  $año,  $tipoCred, $hoy, $sucursal);
                $ganancia_domingo_sql->execute();
                $ganancia_domingo_sql->bind_result($creditos_realizados);
                $ganancia_domingo_sql->fetch();
                $ganancia_domingo_sql->close();

                if($creditos_realizados == null){
                    $creditos_realizados = 0;
                }
 
                
                //Obtener creditos pagados
                /* $ganancia_domingo_sql = $con->prepare("SELECT COUNT(*) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND tipo = ? AND estatus = ? AND WEEKDAY(Fecha) = ?");
                $ganancia_domingo_sql->bind_param('sssss', $semana, $año, $tipoCred, $estatus, $hoy);
                $ganancia_domingo_sql->execute();
                $ganancia_domingo_sql->bind_result($creditos_pagados);
                $ganancia_domingo_sql->fetch();
                $ganancia_domingo_sql->close();

                if($creditos_pagados == null){
                    $creditos_pagados = 0;
                } */

                $creditos_pagados = obtenerCreditosPagados($con, $semana, $año, $hoy, $sucursal);

                
               /*  //Obtener pagos de creditos en especifico
                $ganancia_domingo_sql = $con->prepare("SELECT COUNT(*) FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND tipo = ? AND estatus <> ? AND WEEKDAY(Fecha) =?");
                $ganancia_domingo_sql->bind_param('sssss', $semana, $tipoCred, $año, $estatus, $hoy);
                $ganancia_domingo_sql->execute();
                $ganancia_domingo_sql->bind_result($creditos_realizados);
                $ganancia_domingo_sql->fetch();
                $ganancia_domingo_sql->close();

                if($creditos_realizados == null){
                    $creditos_realizados = 0;
                } */

                $abonos = obtenerClientesqueAbonaron($con, $semana, $año, $tipo, $estatus, $hoy, $sucursal, $unidad );
                if ($abonos == 0) {
                    $abonos_realizados = 0;
                }else{

                    $abonos_realizados = count($abonos);
                }
            
              
                 $data = array("costo_acumulado"=> $costo_acumulado,"ganancia_total"=> $ganancia_total, "ganancia_transferencia"=> $ganancia_transferencia,
                "ganancia_efectivo"=> $ganancia_efectivo, "numero_ventas"=>$ventas_Hoy, "venta_total"=>$venta_total,  "ganancia_tarjeta"=> $ganancia_tarjeta, 
                "ganancia_cheque"=> $ganancia_cheque,  "ganancia_sin_definir"=> $ganancia_sin_definir, "creditos_realizados"=> $creditos_realizados, 
                "creditos_pagados"=> $creditos_pagados, "abonos"=> $abonos, "abonos_realizados"=> $abonos_realizados);
                
                echo json_encode($data, JSON_UNESCAPED_UNICODE); 
    

  }


  //Funciones para calcular la ganancia------------------------------------------------------

  function traer_ganancia($con, $semana, $año, $tipo, $estatus, $hoy, $sucursal, $unidad){
      
    

    $traer_id = $con->prepare("SELECT id FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND tipo = ? AND estatus = ? AND WEEKDAY(Fecha) =? AND id_Sucursal =?");
    $traer_id->bind_param('ssssss', $semana, $año, $tipo, $estatus, $hoy, $sucursal);
    $traer_id->execute();
    $resultado = $traer_id->get_result();
     $costo_acumulado = 0;
     if($resultado->num_rows < 1){
         //echo $semana. " ". $año . " " . $hoy; 
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
      
  

    $traer_id = $con->prepare("SELECT id FROM `ventas` WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND tipo = ? AND estatus = ? AND WEEKDAY(Fecha) =? AND id_Sucursal =? AND metodo_pago = ?");
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
  
    $ganancia_metodo = $venta_total_metodo - $costo_metodo;
    return $ganancia_metodo;
    
 }
  }


  function obtenerClientesqueAbonaron($con, $semana, $año, $tipo, $estatus, $hoy, $sucursal, $unidad){

    
    $traer_id = $con->prepare("SELECT id_credito, abono, metodo_pago FROM `abonos` WHERE WEEK(fecha) = ? AND YEAR(fecha) = ? AND WEEKDAY(fecha) =? AND id_Sucursal =?");
    $traer_id->bind_param('ssss', $semana, $año, $hoy, $sucursal);
    $traer_id->execute();
    $resultado = $traer_id->get_result();
    $traer_id->close();

    if($resultado->num_rows < 1){
        
        return $resultado->num_rows;
     }else{
         
         while($fila = $resultado->fetch_assoc()){
        
         $id_cliente="";
         $id_credito = $fila["id_credito"];
         $abono = $fila["abono"];
         $metodo_pago = $fila["metodo_pago"];
         $traer_id = $con->prepare("SELECT id_Cliente FROM `creditos` WHERE id= ?");
         $traer_id->bind_param('s', $id_credito);
         $traer_id->execute(); 
         $traer_id->bind_result($id_cliente);
         $traer_id->fetch();
         $traer_id->close();                                                                                

         $cliente="";
         $traer_id = $con->prepare("SELECT Nombre_Cliente FROM `clientes` WHERE id= ?");
         $traer_id->bind_param('s', $id_cliente);
         $traer_id->execute(); 
         $traer_id->bind_result($cliente);
         $traer_id->fetch();
         $traer_id->close();

         $arreglo[] = array("cliente"=>$cliente, "abono"=> $abono);
         

     };
    
     return $arreglo;

 }


  }

  function obtenerCreditosPagados($con, $semana, $año, $hoy, $sucursal){
    $estado_pagado =1;
    $abonosHoy = "SELECT id_credito FROM abonos WHERE WEEK(Fecha) = ? AND YEAR(Fecha) = ? AND WEEKDAY(Fecha) = ? AND estado = ?  AND id_Sucursal =?";
    $resultado =  $con->prepare($abonosHoy);
    $resultado->bind_param("sssis", $semana, $año, $hoy, $estado_pagado, $sucursal);
    $resultado->execute();
    $arreg = $resultado->get_result();
    $resultado->close();

    if ($arreg->num_rows < 1) {
        $retorno = 0;
    }else{

        $contador = 0;
        while($row = $arreg->fetch_assoc()){
            $contador = $contador + 1;
            $retorno = $contador;
        }

    }

    return $retorno;


}


?>