<?php
session_start();
date_default_timezone_set("America/Matamoros");
include 'conexion.php';
$con= $conectando->conexion(); 

if (!$con) {
    echo "maaaaal";
}

if (isset($_POST)) {


        $codigo = $_POST["codigo"];
    
         $editar_llanta= $con->prepare("DELETE FROM inventario_mat2 WHERE id_Llanta = ?");
         $editar_llanta->bind_param('i', $codigo);
         $editar_llanta->execute();
         $editar_llanta->close();
         
        
         $traerdatadenew = "SELECT Descripcion FROM llantas WHERE id = ?";
         $result = $con->prepare($traerdatadenew);
         $result->bind_param('i',$codigo);
         $result->execute();
         $result->bind_result($descripcion_llanta);
         $result->fetch();
         $result->close(); 

        $sucursal = "Sendero";  
        $descripcion_movimiento = "Se eliminó una llanta del inventario fisico de " . $sucursal;

       
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
    print_r(2);
}


?>