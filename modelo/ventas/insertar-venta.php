<?php

session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}




if(isset($_POST["venta"])){

    $data = json_decode($_POST['venta']);

    $queryInsertar = "INSERT INTO ventas (id, Fecha, id_Sucursal, id_Usuarios, id_Cliente, Cantidad, Total) VALUES (null,?,?,?,?,?,?)";
    $resultado = $con->prepare($query);
    $resultado->bind_param('siiiid', $fecha, $sucursal, $idUser, $cliente, $cantidad ,$total );
    $resultado->execute();
    $resultado->close();    


    // usar esta consulta:  select * from ventas ORDER BY id DESC LIMIT 1 para escoger el ultimo id

    /*$queryInsertar = "INSERT INTO detalle_venta (id, id_Venta, id_Llanta, Cantidad, Unidad, precio_Unitario, Importe, estatus) VALUES (null,?,?,?,?,?,?,?)";
    $resultado = $con->prepare($query);
    $resultado->bind_param('sisi', $code, $codigo, $sucursal,$stock );
    $resultado->execute();
    $resultado->close();*/

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}





?>