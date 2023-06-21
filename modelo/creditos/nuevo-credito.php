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
    $id_cliente = $_POST["id_cliente"];
   $id_sucursal = $_POST["sucursal_id"]; 

  $querySuc = "SELECT nombre FROM sucursal WHERE id =?";
  $resp=$con->prepare($querySuc);
  $resp->bind_param('i', $id_sucursal);
  $resp->execute();
  $resp->bind_result($sucursal);
  $resp->fetch();
  $resp->close();
   
    $plazo = $_POST["plazo"];
    $importe_total = $_POST["importe"];

    $restante = $_POST["restante"];
    $hora = date("h:i a");
    $metodo = $_POST["metodo_pago"];
    $metodos_pago = $_POST["arreglo_metodos"];
    
  
    $monto_efectivo = 0;
    $monto_tarjeta = 0;
    $monto_transferencia = 0;
    $monto_cheque = 0;
    $monto_sin_definir = 0;
    $metodo_pago = count($metodo) > 1 ? "Mixto": $metodos_pago[$metodo[0]]["metodo"];
    $monto_total = 0;
    foreach ($metodos_pago as $key => $value) {
        $clave = $value["clave"];
        $monto_efectivo = in_array(0, $metodo) && $clave == 0 ? $value["monto"]: $monto_efectivo +=0;
        $monto_tarjeta = in_array(1, $metodo) && $clave == 1 ? $value["monto"]: $monto_tarjeta+=0;
        $monto_transferencia = in_array(2, $metodo) && $clave == 2 ? $value["monto"] : $monto_transferencia+=0;
        $monto_cheque = in_array(3, $metodo) && $clave == 3 ? $value["monto"]: $monto_cheque+=0;
        $monto_sin_definir = in_array(4, $metodo) && $clave == 4 ? $value["monto"]: $monto_sin_definir+=0;
        $monto_total = $monto_efectivo + $monto_tarjeta + $monto_transferencia + $monto_cheque + $monto_sin_definir;
    }

    $usuario = $_SESSION["nombre"];

    if ($monto_total == 0) {
        $estatus = 0;
    }else{
        $estatus = 1;
    }
    
   

    if($_POST["fecha"] == ""){
        $fecha_inicio = date("Y-m-d");
    }else{
        
        $fecha_inicio = $_POST["fecha"];
    }
    
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

        default:
            # code...
        break;
    }

    $sql = "SELECT id FROM ventas ORDER BY id DESC LIMIT 1";
    $resultados = mysqli_query($con, $sql);
    
    if(!$resultados){

      echo "no se pudo realizar la consulta";

    }else{
      $dato =  mysqli_fetch_array($resultados, MYSQLI_ASSOC);

        
        $id_Venta = $dato["id"];
    }


    $insertar_credito = "INSERT INTO creditos(id_cliente, pagado, restante, total, estatus, fecha_inicio, fecha_final, plazo, id_venta)
                         VALUES(?,?,?,?,?,?,?,?,?)";
    $resultado = $con->prepare($insertar_credito);                     
    $resultado->bind_param('idddissii', $id_cliente, $monto_total, $restante, $importe_total ,$estatus, $fecha_inicio, $fecha_limite, $plazo, $id_Venta);
    $resultado->execute();
    $resultado->close();

    $sql = "SELECT id FROM creditos ORDER BY id DESC LIMIT 1";
    $resultado2 = mysqli_query($con, $sql);
    if ($resultado2) {
        $dato =  mysqli_fetch_array($resultado2, MYSQLI_ASSOC);
        $id_credito = $dato["id"];
            //print_r($id_credito.'-'. $fecha_inicio.'-'.$hora.'-'.$abono.'-'.$metodo_pago.'-'.$monto_efectivo.'-'.$monto_tarjeta.'-'.$monto_transferencia.'-'.$monto_cheque.'-'.$monto_sin_definir.'-'.$usuario.'-'.$estado.'-'.$sucursal.'-'.$id_sucursal);
            $estado = $restante == 0 ? 1:0;
            $queryInsertar = "INSERT INTO abonos (id, id_credito, fecha, hora, abono, metodo_pago, pago_efectivo, pago_tarjeta, pago_transferencia, pago_cheque, pago_sin_definir, usuario, estado, sucursal, id_sucursal) VALUES (null,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $resultado = $con->prepare($queryInsertar);
            $resultado->bind_param('sssssdddddsiss',$id_credito, $fecha_inicio, $hora, $monto_total, $metodo_pago, $monto_efectivo, $monto_tarjeta, $monto_transferencia, $monto_cheque, $monto_sin_definir, $usuario, $estado, $sucursal, $id_sucursal);
            $resultado->execute();
            $resultado->close();
    }else{
        print_r("valio queso");
    }
}


?>