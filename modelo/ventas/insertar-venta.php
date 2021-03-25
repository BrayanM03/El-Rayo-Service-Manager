<?php

if(isset($_POST["venta"])){

    $data = json_decode($_POST['venta']);

   /* $query = "INSERT INTO ventas (id, Fecha, id_Llanta, Sucursal, Stock) VALUES (null,?,?,?,?)";
    $resultado = $con->prepare($query);
    $resultado->bind_param('sisi', $code, $codigo, $sucursal,$stock );
    $resultado->execute();
    $resultado->close();*/

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}


?>