<?php
 session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!$con) {
    echo "maaaaal";
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}
date_default_timezone_set("America/Matamoros");

if (isset($_POST)) {

    $id_llanta = $_POST['id_llanta'];
    $costo = $_POST['costo'];
    $precio = $_POST['precio'];
    $precio_lista = $_POST['precio_lista'];
    $mayoreo  = $_POST['mayoreo'];

    //Actualizamos stock de la llanta
    $editar_llanta = $con->prepare("UPDATE llantas SET precio_Inicial = ?, precio_Venta = ?, precio_Mayoreo = ?, precio_lista = ?
     WHERE id = ?");
    $editar_llanta->bind_param('ddddi', $costo, $precio, $mayoreo, $precio_lista, $id_llanta);
    $editar_llanta->execute();
    $editar_llanta->close();

    $comprobar= "SELECT precio_Inicial, precio_Venta, precio_Mayoreo, precio_lista FROM llantas WHERE id =?";
    $res = $con->prepare($comprobar);
    $res->bind_param('i', $id_llanta);
    $res->execute();
    $res->bind_result($costo_actualizado, $precio_actualizado, $mayoreo_actualizado, $precio_lista_actualizado);
    $res->fetch();
    $res->close();

    $data = array('estatus'=> true,'mensaje' => 'Precios actualizados correctamente', 
    'costo'=>$costo_actualizado, 'precio'=>$precio_actualizado, 'mayoreo'=>$mayoreo_actualizado,
    'precio_lista'=>$precio_lista_actualizado);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}else{
    $data = array('estatus'=> false,'mensaje' =>'No hay una solicitud post');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}


?>