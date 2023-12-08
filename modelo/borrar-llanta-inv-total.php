<?php

include 'conexion.php';
$con= $conectando->conexion(); 
session_start();
if (!$con) {
    echo "maaaaal";
}

if (isset($_POST)) {


        $codigo = $_POST["codigo"];
$id_usuario = $_SESSION["id_usuario"];

        $tray_descp= $con->prepare("SELECT Descripcion FROM llantas WHERE id = ?");
        $tray_descp->bind_param('i', $codigo);
        $tray_descp->execute();
        $tray_descp->bind_result($descripcion_llanta);
        $tray_descp->fetch();
        $tray_descp->close();
    
         $editar_llanta= $con->prepare("DELETE FROM llantas WHERE id = ?");
         $editar_llanta->bind_param('i', $codigo);
         $editar_llanta->execute();
         $editar_llanta->close();

         $editar_llanta= $con->prepare("DELETE FROM inventario WHERE id = ?");
         $editar_llanta->bind_param('i', $codigo);
         $editar_llanta->execute();
         $editar_llanta->close();
         
        if($editar_llanta){
            print_r(1);
        }else{
            print_r(2);
        }

        //Registrando los movimientos

      $descripcion_movimiento = "Se borró una llanta de la base de datos del catalogo.";
       
      $fecha = date("Y-m-d");   
      $hora =date("h:i a");  
      $id_usuario = $_SESSION["id_usuario"]; 
      $usuario = $_SESSION["nombre"] . " " . $_SESSION["apellidos"];

      $tipo = 5; //Borrado de llanta del catalogo
      $sucursal = "No aplica";
    //Registramos el movimiento
       $insertar_movimi = "INSERT INTO movimientos(id, descripcion, mercancia, fecha, hora, usuario, tipo, sucursal, id_usuario)
       VALUES(null,?,?,?,?,?,?,?,?)";
       $resultado = $con->prepare($insertar_movimi);                     
       $resultado->bind_param('ssssssss', $descripcion_movimiento, $descripcion_llanta, $fecha, $hora, $usuario, $tipo, $sucursal, $id_usuario);
       $resultado->execute();
       $resultado->close(); 

       //LAST ID MOVIMIENTO
      $rs = mysqli_query($con, "SELECT MAX(id) AS id FROM movimientos");
      if ($fila = mysqli_fetch_row($rs)) {
      $id_movimiento = trim($fila[0]);
      }

       $sucursal_id = $_SESSION['id_sucursal'];
       $stock = 0;
       $stock_actual_s = 0;
       $stock_total = 0;
       $id_usuario = $_SESSION['id_usuario'];
       //Ingresando info al detalle la
       $insertar = "INSERT INTO historial_detalle_cambio(id, 
            id_llanta, 
            id_ubicacion, 
            id_destino, 
            cantidad, 
            id_usuario,
            id_movimiento,
            stock_destino_actual,
            stock_destino_anterior,
            usuario_emisor,
            usuario_receptor) VALUES(null, ?,?,?,?,?,?,?,?,?,?)";
            $result = $con->prepare($insertar);
            $result->bind_param('ssssssssss',$codigo, $sucursal_id, $sucursal_id, $stock, $id_usuario, $id_movimiento, $stock_total, $stock_actual_s, $id_usuario, $id_usuario);
            $result->execute();
            $err = $con->error;
            $result->close();
}else{
    print_r(2);
}



?>