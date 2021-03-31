<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if(isset($_POST)){

   

   
    $codigo_llanta = $_POST['codigos']; 
    $fecha = $_POST['fecha'];
    $sucursal = $_SESSION['sucursal'];
    $idUser = $_SESSION['id_usuario'];
    $cliente = "Publico en General";
    $cantidad = $_POST['cantidades'];
    $total = $_POST["total"];

      $codigos = json_decode($codigo_llanta); 

      foreach($codigos as $value){
        echo "El codigo es " . $value; 
      } 
      


    /*    
    $queryInsertar = "INSERT INTO ventas (id, Fecha, id_Sucursal, id_Usuarios, id_Cliente, Cantidad, Total) VALUES (null,?,?,?,?,?,?)";
    $resultado = $con->prepare($queryInsertar);
    $resultado->bind_param('siiiid', $fecha, $sucursal, $idUser, $cliente, $cantidad ,$total );
    $resultado->execute();
    $resultado->close();  */

     


    // usar esta consulta:  select * from ventas ORDER BY id DESC LIMIT 1 para escoger el ultimo id

    /*$queryInsertar = "INSERT INTO detalle_venta (id, id_Venta, id_Llanta, Cantidad, Unidad, precio_Unitario, Importe) VALUES (null,?,?,?,?,?,?,?)";
    $resultado = $con->prepare($query);
    $resultado->bind_param('sisi', $code, $codigo, $sucursal,$stock );
    $resultado->execute();
    $resultado->close();*/

   // echo json_encode($codigo_llanta, JSON_UNESCAPED_UNICODE);
}





?>