<?php
    session_start();
    date_default_timezone_set("America/Matamoros");
    include 'conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (isset($_POST)) {
      $respuesta =  $_POST["mayorista"];
      $nulo = null;
      $fecha = date("Y-m-d"); 

      $query = "INSERT INTO llantas (Ancho, Proporcion, Diametro, Descripcion, Marca, Modelo, precio_Inicial, precio_Venta, precio_Mayoreo, Fecha) VALUES (?,?,?,?,?,?,?,?,?,?)";
      $resultado = $con->prepare($query);
      $resultado->bind_param(
          'ssssssssss',
          $_POST['ancho'],
          $_POST['alto'],
          $_POST['rin'],
          $_POST['descripcion'],
          $_POST['marca'],
          $_POST['modelo'],
          $_POST['costo'],
          $_POST['precio'],
          $_POST['mayorista'],
          $fecha 

      );

      $resultado->execute();
      $resultado->close(); 

      //LAST ID
      $rs = mysqli_query($con, "SELECT MAX(id) AS id FROM llantas");
      if ($rowss = mysqli_fetch_row($rs)) {
      $id_llanta = trim($rowss[0]);
      }

      $descripcion_movimiento = "Se agregó un nueva llanta al registro de la base de datos del catalogo.";
      $descripcion_llanta = $_POST["descripcion"];
       
      $fecha = date("Y-m-d");   
      $hora =date("h:i a");   
      $usuario = $_SESSION["nombre"] . " " . $_SESSION["apellidos"];

      $tipo = 4; //Ingreso nuevo al catalogo
      $sucursal = "No aplica";
    //Registramos el movimiento
       $insertar_movimi = "INSERT INTO movimientos(id, descripcion, mercancia, fecha, hora, usuario, tipo, sucursal)
       VALUES(null,?,?,?,?,?,?,?)";
       $resultado = $con->prepare($insertar_movimi);                     
       $resultado->bind_param('sssssss', $descripcion_movimiento, $descripcion_llanta, $fecha, $hora, $usuario, $tipo, $sucursal);
       $resultado->execute();
       $resultado->close(); 

       //LAST ID MOVIMIENTO
      $rs = mysqli_query($con, "SELECT MAX(id) AS id FROM movimientos");
      if ($fila = mysqli_fetch_row($rs)) {
      $id_movimiento = trim($fila[0]);
      }

       $sucursal_id = $_SESSION['id_sucursal'];
       $stock = "NA";
       $stock_actual_s = "NA";
       $stock_total = "NA";
       $codigo = $id_llanta;
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
            stock_destino_anterior) VALUES(null, ?,?,?,?,?,?,?,?)";
            $result = $con->prepare($insertar);
            $result->bind_param('ssssssss',$codigo, $sucursal_id, $sucursal_id, $stock, $id_usuario, $id_movimiento, $stock_total, $stock_actual_s);
            $result->execute();
            $result->close();
       
       
      

     print_r(1);
        
    }else{
        print_r("Error al conectar");
    }
    ?>