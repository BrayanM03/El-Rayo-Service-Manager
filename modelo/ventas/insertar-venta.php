<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if(isset($_POST)){

   

    //Variables para el historial venta
    $codigo_llanta = $_POST['codigos']; 
    $fecha = $_POST['fecha'];
    $sucursal = $_SESSION['sucursal'];
    $idUser = $_SESSION['id_usuario'];
    $cliente = 1;
    $cantidad = $_POST['cantidades'];
    $total = $_POST["total"];
    $estatus = "Activo";
    $unidad = "pieza";
      
    $codigos = json_decode($codigo_llanta); 
    $cantidades = json_decode($cantidad);
    $count = count($codigos);
      
      $suma = array_sum($cantidades);
      


   
    /*$queryInsertar = "INSERT INTO ventas (id, Fecha, id_Sucursal, id_Usuarios, id_Cliente, Cantidad, Total, estatus) VALUES (null,?,?,?,?,?,?,?)";
    $resultado = $con->prepare($queryInsertar);
    $resultado->bind_param('ssiiids', $fecha, $sucursal, $idUser, $cliente, $suma ,$total, $estatus);
    $resultado->execute();
    $resultado->close();*/

    $sql = "SELECT id FROM ventas ORDER BY id DESC LIMIT 1";
    $resultado = mysqli_query($con, $sql);
    
    if(!$resultado){
      echo "no se pudo realizar la consulta";

    }else{
      while ($dato=mysqli_fetch_assoc($resultado)) {
        $arreglo[] = $dato;

        //editar esta parte

        foreach ($codigos as $key => $value) {
          
          $query_mostrar = $con->prepare("SELECT precio_Inicial, Codigo FROM llantas l, pedro, sendero INNER JOIN inventario_mat1 pedro
                                           ON pedro.id_Llanta = l.id INNER JOIN inventario_mat2 sendero ON sendero.id_Llanta = l.id");
          $query_mostrar->bind_param('s', $value);
          $query_mostrar->execute();
          $query_mostrar->store_result(); 

/*
          $queryInsertar = "INSERT INTO detalle_venta (id, id_Venta, id_Llanta, Cantidad, Unidad, precio_Unitario, Importe) VALUES (null,?,?,?,?,?,?,?)";
          $resultado = $con->prepare($query);
          $resultado->bind_param('sisi',$arreglo[0], $value, $suma, $unidad, $precio_unitario, $importe);
          $resultado->execute();
          $resultado->close();*/

        }

    /*usar esta consulta:  select * from ventas ORDER BY id DESC LIMIT 1 para escoger el ultimo id

          

   // echo json_encode($codigo_llanta, JSON_UNESCAPED_UNICODE);*/

    }

    
  }
  
}





?>