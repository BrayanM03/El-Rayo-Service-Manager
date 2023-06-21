<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 
date_default_timezone_set("America/Matamoros");

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if (!$con) {
    echo "maaaaal";
}


if(isset($_POST)){

  
    $id_abono = $_POST["id"];
    $fecha = date("Y-m-d");
    $hora = date("h:i a");
    $metodos = $_POST["metodo"];
    $usuario = $_SESSION["user"];
    $suma_monto_anterior = $_POST["suma_monto_anterior"];
      
    $desc_metodos ='';
    $pago_efectivo=0;
    $pago_transferencia=0;
    $pago_tarjeta=0;
    $pago_cheque=0;
    $pago_sin_definir=0;
    $monto_total = 0;
    $usuario = $_SESSION["nombre"];
    foreach ($metodos as $key => $value) {
        $metodo_id = isset($value['clave']) ? $value['clave']: $key;
        switch ($metodo_id) {
          case 0:
           $pago_efectivo = $value['monto'];
            break;
          
          case 1:
          $pago_tarjeta = $value['monto'];
          break;
          
          case 2:
          $pago_transferencia = $value['monto'];
  
          break;
    
          case 3:
          $pago_cheque = $value['monto'];
          break;
    
          case 4:
          $pago_sin_definir = $value['monto'];
          break;     
          
          default:
            break;
        }
        $monto_pago = $value['monto'];
        $monto_total = $pago_efectivo + $pago_tarjeta + $pago_transferencia + $pago_cheque + $pago_sin_definir;
       if($monto_pago > 0){
        $metodo_pago = $value['metodo'];
        if($key != count($metodos) - 1){
            // Este código se ejecutará para todos menos el último
            $desc_metodos .= $metodo_pago . ", ";
          }else{
            $desc_metodos .= $metodo_pago . "";
          }
       }
       
      }
      $metodos_str = count($metodos) > 1 ? 'Mixto' : $desc_metodos;
    if($monto_total < 0){
        print_r(2);
    }else{
        

        //Obtenemos estatus del credito
     $obtenerStatus = "SELECT id_credito FROM abonos WHERE id = ?";
     $stmt = $con->prepare($obtenerStatus);
     $stmt->bind_param('i', $id_abono);
     $stmt->execute();
     $stmt->bind_result($id_credito);
     $stmt->fetch(); 
     $stmt->close();

     //Obtenemos de los creditos el pagado, el restante y el total
    $obtenerStatus = "SELECT pagado, restante, total FROM creditos WHERE id = ?";
    $stmt = $con->prepare($obtenerStatus);
    $stmt->bind_param('i', $id_credito);
    $stmt->execute();
    $stmt->bind_result($pagado, $restante, $total_a_pagar);
    $stmt->fetch(); 
    $stmt->close();

  

    $nuevo_pagado = ((double)$pagado - (double)$suma_monto_anterior) + (double)$monto_total;
    $nuevo_restante = ((double)$total_a_pagar - $nuevo_pagado);

    
  

    if($nuevo_restante < 0 ){
        print_r(0);
    }else{
        

        $actualizar = "UPDATE abonos SET fecha = ?, hora = ?, abono= ?, metodo_pago = ?, pago_efectivo = ?, pago_tarjeta = ?, pago_transferencia = ?, pago_cheque = ?, pago_sin_definir = ?, usuario = ? WHERE id = ?";
        $res = $con->prepare($actualizar);
        $res->bind_param('ssdsdddddsi', $fecha, $hora, $monto_total, $metodos_str, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_sin_definir, $usuario, $id_abono);
        $res->execute();
        $res->close();

        $obtenerSumatoria = "SELECT SUM(abono) AS sumtotal FROM abonos WHERE id_credito= ?";
        $response = $con->prepare($obtenerSumatoria);
        $response->bind_param('i', $id_credito); 
        $response->execute();
        $response->bind_result($totalAbonos);
        $response->fetch(); 
        $response->close();


        $actualizar = "UPDATE creditos SET pagado = ?, restante = ? WHERE id = ?";
        $res = $con->prepare($actualizar);
        $res->bind_param('ddi', $totalAbonos, $nuevo_restante, $id_credito);
        $res->execute();
        $res->close();

        //Volvemos a traer los datos de creditos ya actualizados
        $obtenerStatus = "SELECT restante FROM creditos WHERE id = ?";
        $stmt = $con->prepare($obtenerStatus);
        $stmt->bind_param('i', $id_credito);
        $stmt->execute();
        $stmt->bind_result($restante_actualizado);
        $stmt->fetch(); 
        $stmt->close(); 

        //Si el restante esta en 0 actualizamos el estatus a pagado
        if($restante_actualizado ==0){
        $new_stat = 3;
        $actualizar = "UPDATE creditos SET estatus = ? WHERE id = ?";
        $res = $con->prepare($actualizar);
        $res->bind_param('di', $new_stat, $id_credito);
        $res->execute();
        $res->close();
        }else{
            $new_stat = 2;
            $actualizar = "UPDATE creditos SET estatus = ? WHERE id = ?";
            $res = $con->prepare($actualizar);
            $res->bind_param('di', $new_stat, $id_credito);
            $res->execute();
            $res->close();
        }

        //Se inserta movimiento

        $data = array("nuevo_pagado"=>$nuevo_pagado, "nuevo_restante"=>$nuevo_restante);
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
       

    }

   /* 

    
    

    

        /*  */

    /*

     */ 

    

 

    }

        
}


?>