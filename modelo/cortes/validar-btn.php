<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

$sucursal = $_POST["sucursal"];

switch ($sucursal) {
    case 'Pedro':
        $id_sucursal = 1;
        break;
    
        case 'Sendero':
            $id_sucursal = 2;
            break;
        
    default:
        # code...
        break;
}

$comprobar = $con->prepare("SELECT corte FROM `sucursal` WHERE id =?");
               $comprobar->bind_param('s', $id_sucursal);
               $comprobar->execute();
               $comprobar->bind_result($valor_cort);
               $comprobar->fetch();
               $comprobar->close();

               print_r($valor_cort);
            

?>