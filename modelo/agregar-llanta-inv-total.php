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

      $descripcion_movimiento = "Se agregó un nueva llanta al registro de la base de datos.";
      $descripcion_llanta = $_POST["descripcion"];
       
      $fecha = date("Y-m-d");   
      $hora =date("h:i a");   
      $usuario = $_SESSION["nombre"] . " " . $_SESSION["apellidos"];

    //Registramos el movimiento
       $insertar_movimi = "INSERT INTO movimientos(id, descripcion, mercancia, fecha, hora, usuario)
       VALUES(null,?,?,?,?,?)";
       $resultado = $con->prepare($insertar_movimi);                     
       $resultado->bind_param('sssss', $descripcion_movimiento, $descripcion_llanta, $fecha, $hora, $usuario);
       $resultado->execute();
       $resultado->close(); 
       
      

     print_r(1);
        
    }else{
        print_r("Error al conectar");
    }
    ?>