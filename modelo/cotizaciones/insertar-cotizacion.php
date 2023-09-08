<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php"); 
}


if(isset($_POST)){

  date_default_timezone_set("America/Matamoros");
  $hora = date("h:i a");
  $fecha= date("Y-m-d");

    //Variables para el historial venta
  if ($_POST['comentario'] == "") {
    $comentario = "Sin comentario"; 
  }else{
    $comentario = $_POST['comentario'];
    
  }

    $sucursal = $_SESSION['sucursal'];
   
    $id_sucursal = $_SESSION['id_sucursal'];
    $idUser =   $_SESSION['id_usuario'];
    $usuario = $_SESSION['nombre'] . ' ' . $_SESSION['apellidos'];
    $cliente =  $_POST["cliente"];
    $total =    $_POST["total"];
    $tipo_cotizacion = $_POST["tipo_cotizacion"];
    $estatus = 'Activo';

    if($tipo_cotizacion ==1){
      $tabla = 'cotizaciones';
      $tabla_detalle = 'detalle_cotizacion';
      $queryInsertar = "INSERT INTO $tabla (id, Fecha, id_Sucursal, sucursal, id_Usuarios, id_Cliente, Total, estatus, hora, comentario) VALUES (null,?,?,?,?,?,?,?,?,?)";
      $resultado = $con->prepare($queryInsertar);
      $resultado->bind_param('sssssssss', $fecha, $id_sucursal, $sucursal, $idUser, $cliente , $total, $estatus,$hora, $comentario);
      $resultado->execute();
      $id_Cotizacion = $con->insert_id;
      $resultado->close();
    }else{
      $pago_efectivo = 0;
      $pago_transferencia = 0;
      $pago_tarjeta = 0;
      $pago_cheque = 0;
      $pago_sin_definir = 0;
      $monto_total_abono = 0;
      foreach ($_POST['metodos_pago'] as $key => $value) {
        $metodo_id = isset($value['id_metodo']) ? $value['id_metodo'] : $key;
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
        $monto_total_abono += $value['monto'];
        $metodo_pago = $value['metodo'];
        $desc_metodos = '';
        $restante = floatval($total) -floatval($monto_total_abono);
        if($key != count($_POST["metodos_pago"]) - 1) {
            // Este código se ejecutará para todos menos el último
            $desc_metodos .= $metodo_pago . ", ";
        } else {
            $desc_metodos .= $metodo_pago . ". ";
        }
    }
        $tabla = 'pedidos';
        $tipo = 'Pedido';
        $tabla_detalle = 'detalle_pedido';
        $queryInsertar = "INSERT INTO $tabla (id, fecha_inicio, id_sucursal, sucursal, abonado, restante, id_usuario, id_cliente, total, estatus, hora_inicio, tipo, comentario) VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?)";
        $resultado = $con->prepare($queryInsertar);
        $resultado->bind_param('ssssssssssss', $fecha, $id_sucursal, $sucursal, $monto_total_abono, $restante,  $idUser, $cliente , $total, $estatus, $hora, $tipo, $comentario);
        $resultado->execute();
        $erro = $resultado->error;
        print_r($erro);
        $id_pedido = $con->insert_id;
        $resultado->close();
    }
    $estatus = "OK";

   
    $datos = $_POST['data'];
   // $info_producto_individual = json_decode($datos);  
   $info_producto_individual = $datos;

   
    

        foreach ($info_producto_individual as $key => $value) { 
          
          $validacion = is_numeric($key);

          
          if($validacion){
            
            
            $id_llanta = $value["codigo"];
            $cantidad = $value["cantidad"];
            $precio_unitario = $value["precio"];
            $importe = $value["importe"];
            $modelo= $value["modelo"];
            
 
              $unidad = "pieza";
              if($tipo_cotizacion ==1){
                $queryInsertar = "INSERT INTO $tabla_detalle (id, id_Llanta, id_Cotiza, Cantidad, Unidad, precio_Unitario, Importe) VALUES (null,?,?,?,?,?,?)";
           
                $resultado = $con->prepare($queryInsertar);
                $resultado->bind_param('ssssss',$id_llanta, $id_Cotizacion, $cantidad, $unidad, $precio_unitario, $importe);
                $resultado->execute();
                $resultado->close();
            
  
              $vaciarTabla = "DELETE FROM detalle_nueva_cotizacion WHERE id_usuario = $idUser AND tipo = $tipo_cotizacion";
  
              $consulta = mysqli_query($con, $vaciarTabla);
              }else{
                $queryInsertar = "INSERT INTO $tabla_detalle (id, id_Llanta, id_pedido, Cantidad, Unidad, precio_Unitario, Importe) VALUES (null,?,?,?,?,?,?)";
           
                $resultado = $con->prepare($queryInsertar);
                $resultado->bind_param('ssssss',$id_llanta, $id_pedido, $cantidad, $unidad, $precio_unitario, $importe);
                $resultado->execute();
                $resultado->close();

              $vaciarTabla = "DELETE FROM detalle_nueva_cotizacion WHERE id_usuario = $idUser AND tipo = $tipo_cotizacion";
              $consulta = mysqli_query($con, $vaciarTabla);
              }
              

          }else{
            echo "";
          }

        }

        if($tipo_cotizacion != 1){
          if($total === $monto_total_abono){
            $estado = 0;
          }  else{
            $estado =1;
          }

          if($monto_total_abono > 0){
            $queryInsertarAbono = "INSERT INTO abonos_pedidos(id_pedido, fecha, hora, abono, metodo_pago, pago_efectivo, pago_tarjeta, pago_transferencia, pago_cheque, pago_sin_definir, usuario, id_usuario, estado, sucursal, id_sucursal, credito) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,0)";
            $resultado = $con->prepare($queryInsertarAbono);
            $resultado->bind_param('sssssssssssssss', $id_pedido, $fecha, $hora, $monto_total_abono, $metodo_pago, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_cheque, $pago_sin_definir, $usuario, $idUser, $estado, $sucursal, $id_sucursal);
            $resultado->execute();
            $erro = $resultado->error;
            print_r($erro);
            $resultado->close();
          }
          print_r($id_pedido);
        }else{
          print_r($id_Cotizacion);
        }

      
  
}





?>