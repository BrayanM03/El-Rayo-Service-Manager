<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 
date_default_timezone_set("America/Matamoros");
include 'obtener-utilidad-abono.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if (!$con) {
    echo "maaaaal";
}


if(isset($_POST)){

    $id_credito = $_POST["id-credito"];
    $fecha = date("Y-m-d");
    $hora = date("h:i a");
    $metodos = $_POST["metodo"];
    
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
        $metodo_pago = $value['metodo'];
        $monto_total = $pago_efectivo + $pago_tarjeta + $pago_transferencia + $pago_cheque + $pago_sin_definir;
       
        if($key != count($metodos) - 1) {
          // Este código se ejecutará para todos menos el último
          $desc_metodos .= $metodo_pago . ", ";
        }else{
          $desc_metodos .= $metodo_pago . ". ";
        }
      }
      $metodos_str = count($metodos) > 1 ? 'Mixto' : $desc_metodos;
     //Obtenemos estatus del credito
     $obtenerStatus = "SELECT estatus FROM creditos WHERE id = ?";
     $stmt = $con->prepare($obtenerStatus);
     $stmt->bind_param('i', $id_credito);
     $stmt->execute();
     $stmt->bind_result($estatus);
     $stmt->fetch(); 
     $stmt->close();
  
     if ($estatus !== 5) {
        $traerdata = "SELECT pagado, restante, total, id_venta FROM creditos WHERE id = ?";
        $result = $con->prepare($traerdata);
        $result->bind_param('i',$id_credito);
        $result->execute();
        $result->bind_result($pagado, $restante, $total, $id_venta_otro);
        $result->fetch();
        $result->close();

        $traerdata = "SELECT id_sucursal FROM ventas WHERE id = ?";
        $result = $con->prepare($traerdata);
        $result->bind_param('i',$id_venta_otro);
        $result->execute();
        $result->bind_result($id_sucursal);
        $result->fetch();
        $result->close();

        $querySuc = "SELECT nombre FROM sucursal WHERE id =?";
        $resp=$con->prepare($querySuc);
        $resp->bind_param('i', $id_sucursal);
        $resp->execute();
        $resp->bind_result($sucursal);
        $resp->fetch();
        $resp->close();
    
        $comproba = (double)$monto_total + (double)$pagado;
    
    
        if($comproba > $total){
            print_r(1);
        }else{
    
            if ($comproba == $total) {
                $estado = 1; //Creditos pagado
            }else{
                $estado = 0; //Aun sin pagar
            }

            include '../helpers/verificar-hora-corte.php';
            
            $insertar_abono = "INSERT INTO abonos(id, id_credito, fecha, hora, abono, metodo_pago, pago_efectivo, pago_tarjeta, pago_transferencia, pago_cheque, pago_sin_definir, usuario, estado, sucursal, id_sucursal, fecha_corte, hora_corte) VALUES(null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $resultado = $con->prepare($insertar_abono);  
            $resultado->bind_param('isssssssssssssss', $id_credito, $fecha, $hora, $monto_total, $metodos_str, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_sin_definir, $usuario, $estado, $sucursal, $id_sucursal, $fecha_corte, $hora_corte);
            $resultado->execute();
            $error_credito = $resultado->error;
            if($error_credito){
                print_r($error_credito);
            }
            if ($resultado == true) {
              
            $resultado->close();
            $id_abono = $con->insert_id;
            insertarUtilidadAbono($id_abono, $con);
            $pagado_update = (double)$pagado + (double)$monto_total;
            $restante_update = (double)$restante - (double)$monto_total;
           
    
            $actualizar = "UPDATE creditos SET pagado = ?, restante = ?, estatus= 2 WHERE id = ?";
            $res = $con->prepare($actualizar);
            $res->bind_param('ddi', $pagado_update, $restante_update, $id_credito);
            $res->execute();
            $res->close();
            
        
            $traerdatadenew = "SELECT pagado, restante, total FROM creditos WHERE id = ?";
            $result = $con->prepare($traerdatadenew);
            $result->bind_param('i',$id_credito);
            $result->execute();
            $result->bind_result($pagado2, $restante2, $total2);
            $result->fetch();
            $result->close();
    
            //En este if actualizamos el estatus del credito en caso de que se haya pagado completamente
            if($pagado2 == $total2){
    
                $actualizar2 = "UPDATE creditos SET estatus= 3 WHERE id = ?";
                $res2 = $con->prepare($actualizar2);
                $res2->bind_param('i', $id_credito);
                $res2->execute();
                $res2->close();

                $actualizar2 = "SELECT id_cliente, id_venta FROM creditos WHERE id = ?";
                $res2 = $con->prepare($actualizar2);
                $res2->bind_param('i', $id_credito);
                $res2->execute();
                $res2->bind_result($id_cliente, $id_venta);
                $res2->fetch();
                $res2->close();

                $new_status = "Pagado";
                $actualizar2 = "UPDATE ventas SET estatus = ? WHERE id = ?";
                $res2 = $con->prepare($actualizar2);
                $res2->bind_param('si', $new_status, $id_venta);
                $res2->execute();
                $res2->close();

                $consulta2 = "SELECT COUNT(*) FROM creditos  WHERE estatus = 4 AND id_cliente =?";
                $totrs = $con->prepare($consulta2);
                $totrs->bind_param('i', $id_cliente);
                $totrs->execute();
                $totrs->bind_result($total_creditos_vencidos);
                $totrs->fetch();
                $totrs->close();

                if($total_creditos_vencidos ==0){
                    $actualizar_cliente = "UPDATE clientes SET credito_vencido = 0 WHERE id = ?";
                    $res2 = $con->prepare($actualizar_cliente);
                    $res2->bind_param('i', $id_cliente);
                    $res2->execute();
                    $res2->close();
                }
            

    
                $data = array("pagado_nuevo"=> $pagado2, "restante_nuevo"=>$restante2);
    
            }else{
    
                $data = array("pagado_nuevo"=> $pagado2, "restante_nuevo"=>$restante2);
            }
        
            
        
            echo json_encode($data, JSON_UNESCAPED_UNICODE); 

            }else{
                echo "Error";
                $resultado->close();
            }
            
    
        }
     }else if($estatus == 5){
         print_r(6);
     }
   
   

    
  

    
 

  
    
}


?>