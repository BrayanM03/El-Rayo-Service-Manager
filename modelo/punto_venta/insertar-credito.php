<?php
include '../creditos/obtener-utilidad-abono.php';

function insertarCredito($con, $id_cliente, $id_sucursal, $monto_efectivo, $monto_tarjeta, $monto_transferencia, $monto_cheque, $monto_sin_definir, $descripcion_forma_pago, $importe, $id_venta){

    $pagare = isset($_POST['pagare']) == true ? $_POST['pagare']: null;
    $sucursal='';
    $querySuc = "SELECT nombre FROM sucursal WHERE id =?";
    $resp=$con->prepare($querySuc);
    $resp->bind_param('i', $id_sucursal);
    $resp->execute();
    $resp->bind_result($sucursal);
    $resp->fetch();
    $resp->close();
    
      $hora = date("h:i a");
      $plazo = $_POST["plazo"];
      //$metodos_pago = $_POST["arreglo_metodos"];
 
      $monto_total = $monto_efectivo + $monto_tarjeta + $monto_transferencia + $monto_cheque + $monto_sin_definir;
      
      $restante = $importe - $monto_total;
      /* print_r($_POST);
      die(); */
      /* $metodo_pago = count($metodo) > 1 ? "Mixto": $metodos_pago[0]["metodo"];//$metodos_pago[$metodo[0]]["metodo"];
      $monto_total = 0;
      foreach ($metodos_pago as $key => $value) {
          $clave = $value["clave"];
          $monto_recibido = !empty($value["monto"]) ? (double)$value['monto']:0;
          $monto_efectivo = in_array('Efectivo', $metodo) && $clave == 'Efectivo' ? $monto_recibido: $monto_efectivo +=0;
          $monto_tarjeta = in_array('Tarjeta', $metodo) && $clave == 'Tarjeta' ? $monto_recibido: $monto_tarjeta+=0;
          $monto_transferencia = in_array('Transferencia', $metodo) && $clave == 'Transferencia' ? $monto_recibido : $monto_transferencia+=0;
          $monto_cheque = in_array('Cheque', $metodo) && $clave == 'Cheque' ? $monto_recibido: $monto_cheque+=0;
          $monto_deposito = in_array('Deposito', $metodo) && $clave == 'Deposito' ? $monto_recibido: $monto_deposito+=0;
          $monto_sin_definir = in_array('Sin definir', $metodo) && $clave == 'Sin definir' ? $monto_recibido: $monto_sin_definir+=0;
          $monto_total = $monto_efectivo + $monto_tarjeta + $monto_transferencia + $monto_cheque + $monto_sin_definir;
      } */
  
      $usuario = $_SESSION["nombre"] . ' ' . $_SESSION['apellidos'];
      $id_usuario = $_SESSION['id_usuario'];
  
      if ($monto_total == 0) {
          $estatus = 0;
      }else{
          $estatus = 1;
      }
      
     
      $fecha_inicio = date("Y-m-d");
     
      
      $fecha = date($fecha_inicio);
  
      switch ($plazo) {
          case '1':
              $fecha_limite = date("Y-m-d",strtotime($fecha ."+ 7 days")); 
          break;
          
          case '2':
              $fecha_limite = date("Y-m-d",strtotime($fecha ."+ 15 days")); 
          break;
          
          case '3':
              $fecha_limite = date("Y-m-d",strtotime($fecha ."+ 1 month")); 
          break;
          
          case '4':
              $fecha_limite = date("Y-m-d",strtotime($fecha ."+ 1 year"));  
          break;
          
          case '5':
              $fecha_limite = date("Y-m-d",strtotime($fecha ."+ 7 days")); 
          break;
  
          case '6':
              $fecha_limite = date("Y-m-d",strtotime($fecha ."+ 1 days")); 
          break;
  
          default:
              # code...
          break;
      }
  
    
  
  
      $insertar_credito = "INSERT INTO creditos(id_cliente, pagado, restante, total, estatus, fecha_inicio, fecha_final, plazo, id_venta, pagare)
                           VALUES(?,?,?,?,?,?,?,?,?,?)";
      $resultado = $con->prepare($insertar_credito);                     
      $resultado->bind_param('idddissiii', $id_cliente, $monto_total, $restante, $importe ,$estatus, $fecha_inicio, $fecha_limite, $plazo, $id_venta, $pagare);
      $resultado->execute();
      $id_credito = $resultado->insert_id;
      $resultado->close();

              //print_r($id_credito.'-'. $fecha_inicio.'-'.$hora.'-'.$abono.'-'.$metodo_pago.'-'.$monto_efectivo.'-'.$monto_tarjeta.'-'.$monto_transferencia.'-'.$monto_cheque.'-'.$monto_sin_definir.'-'.$usuario.'-'.$estado.'-'.$sucursal.'-'.$id_sucursal);
              if($monto_total >0){
                  $estado = $restante == 0 ? 1:0;
                  include '../helpers/verificar-hora-corte.php';
                  $queryInsertar = "INSERT INTO abonos (id, id_credito, fecha, hora, abono, metodo_pago, pago_efectivo, pago_tarjeta, pago_transferencia, pago_cheque, pago_deposito, pago_sin_definir, usuario, id_usuario, estado, sucursal, id_sucursal, fecha_corte, hora_corte) VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                  $resultado = $con->prepare($queryInsertar);
                  $resultado->bind_param('sssssddddddsisssss',$id_credito, $fecha_inicio, $hora, $monto_total, $descripcion_forma_pago, $monto_efectivo, $monto_tarjeta, $monto_transferencia, $monto_cheque, $monto_deposito, $monto_sin_definir, $usuario, $id_usuario, $estado, $sucursal, $id_sucursal, $fecha_corte, $hora_corte);
                  $resultado->execute();
                  $id_abono = $con->insert_id;
                  $resultado->close();
                 // print_r($id_abono);
                  insertarUtilidadAbono($id_abono, $con);
                  
              }
              
     
};

 


?>