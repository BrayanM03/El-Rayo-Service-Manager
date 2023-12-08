<?php
session_start();
date_default_timezone_set("America/Matamoros");
include '../conexion.php';
$con= $conectando->conexion(); 

if (!$con) {
    echo "maaaaal";
}
$id_usuario = $_SESSION["id_usuario"]; 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if (isset($_POST)) {


        $codigo = $_POST["codigo"];
        $sucursal_id = $_POST["sucursal_id"];
    
         $editar_llanta= $con->prepare("DELETE FROM inventario WHERE id_Llanta = ? AND id_sucursal = ?");
         $editar_llanta->bind_param('ii', $codigo, $sucursal_id);
         $editar_llanta->execute();
         $editar_llanta->close();

         $traerdatadenew = "SELECT Descripcion FROM llantas WHERE id = ?";
         $result = $con->prepare($traerdatadenew);
         $result->bind_param('i',$codigo);
         $result->execute();
         $result->bind_result($descripcion_llanta);
         $result->fetch();
         $result->close(); 

         $sql = "SELECT nombre FROM sucursal WHERE id= ?";
         $res = $con->prepare($sql);
         $res->bind_param('s', $sucursal_id);
         $res->execute();
         $res->bind_result($sucursal);
         $res->fetch();
         $res->close(); 
        $descripcion_movimiento = "Se eliminó una llanta del inventario fisico de " . $sucursal;

       
        $fecha = date("Y-m-d");   
        $hora =date("h:i a");   
        $usuario = $_SESSION["nombre"] . " " . $_SESSION["apellidos"];

      //Registramos el movimiento
         $insertar_movimi = "INSERT INTO movimientos(id, descripcion, mercancia, fecha, hora, usuario, id_usuario)
         VALUES(null,?,?,?,?,?,?)";
         $resultado = $con->prepare($insertar_movimi);                     
         $resultado->bind_param('ssssss', $descripcion_movimiento, $descripcion_llanta, $fecha, $hora, $usuario, $id_usuario);
         $resultado->execute();
         $resultado->close(); 
         
        print_r(1);



}else{
    print_r(2);
}


?>