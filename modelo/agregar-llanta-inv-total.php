<?php
    
    include 'conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (isset($_POST)) {
      $respuesta =  $_POST["mayorista"];
      $nulo = null;


      $query = "INSERT INTO llantas (Ancho, Proporcion, Diametro, Descripcion, Marca, Modelo, precio_Inicial, precio_Venta, precio_Mayoreo, Fecha, id_Sucursal) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
      $resultado = $con->prepare($query);
      $resultado->bind_param(
          'iissssiiisi',
          $_POST['ancho'],
          $_POST['alto'],
          $_POST['rin'],
          $_POST['descripcion'],
          $_POST['marca'],
          $_POST['modelo'],
          $_POST['costo'],
          $_POST['precio'],
          $_POST['mayorista'],
          $_POST['fecha'],
          $nulo

      );

      $resultado->execute();

      if ($resultado->error) {
          
          print_r("error" . $resultado->error);
      }else{
        print_r(1);
       
      }
        
    }else{
        print_r("Error al conectar");
    }
    ?>