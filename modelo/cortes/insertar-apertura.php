<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


date_default_timezone_set("America/Matamoros");
  $hora = date("h:i a");
  $fecha = date("Y-m-d"); 

$sucursal = $_POST["sucursal"];
$usuario = $_SESSION['nombre'];
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

$apertura = $_POST["apertura"];

               $insertar = $con->prepare("INSERT INTO apertura(id, monto, fecha, usuario, sucursal) VALUES(null,?,?,?,?)");
               
               if ($insertar) {
                $insertar->bind_param('sdss', $apertura, $fecha, $usuario, $sucursal);
                $insertar->execute();
                $insertar->close();
               }else{
                   echo "Algo salio mal suc.";
               }
            

               $actualizar = $con->prepare("UPDATE sucursal SET corte = 0 WHERE id = ?");
               
               if ($actualizar == true) {
                $actualizar->bind_param('s',$id_sucursal);
                $actualizar->execute();
                $actualizar->close();
               }else{
                   echo "Algo salio mal.";
                   $actualizar->close();
               }

               //Iniciando apertura
               $actualizar = $con->prepare("UPDATE sucursal SET apertura = ? WHERE id = ?");
               
               if ($actualizar) {
                $actualizar->bind_param('ss', $apertura, $id_sucursal);
                $actualizar->execute();
                $actualizar->close();
               }else{
                   echo "Algo salio mal. ";
               }
              
               print_r(1);
            

?>