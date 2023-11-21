<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 
date_default_timezone_set("America/Matamoros");

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if (!$con) {
    echo "maaaaal";
}

$fecha = date("Y-m-d");
$hora = date("h:i a");
$metodo ="Sin definir";
$usuario = $_SESSION["nombre"];

if(isset($_POST)){

    $id_abono = $_POST["id_abono"];

    $sql = "SELECT id_credito, abono, sucursal, id_sucursal FROM abonos WHERE id=?";
    $res = $con->prepare($sql);
    $res->bind_param('i', $id_abono);
    $res->execute();
    $res->bind_result($id_credito, $abono_restaurar, $sucursal, $id_sucursal);
    $res->fetch();
    $res->close();

    $sql = "SELECT restante, pagado FROM creditos WHERE id=?";
    $res = $con->prepare($sql);
    $res->bind_param('i', $id_credito);
    $res->execute();
    $res->bind_result($restante, $pagado);
    $res->fetch();
    $res->close();

    $abono_restaurar = doubleval($abono_restaurar);
    $restante = doubleval($restante);
    $pagado = doubleval($pagado);
    $nuevo_restante = $restante + $abono_restaurar;
    $nuevo_pagado =  $pagado - $abono_restaurar;

 /*    if($nuevo_pagado == 0){
        $estado = 0;
        $sql = "INSERT INTO abonos(id, id_credito, fecha, hora, abono, metodo_pago, usuario, estado, sucursal, id_sucursal) VALUES(null,?,?,?,?,?,?,?,?,?)";
        $res = $con->prepare($sql);
        $res->bind_param('issssssss', $id_credito, $fecha, $hora, $nuevo_pagado, $metodo, $usuario, $estado, $sucursal, $id_sucursal);
        $res->execute();
        $res->close();
    } */





    $query = "UPDATE creditos SET pagado =?, restante = ? WHERE id= ?";
    $r = $con->prepare($query);
    $r->bind_param('ddi', $nuevo_pagado, $nuevo_restante, $id_credito);
    $r->execute();
    $r->close();

    $borrar_credito= $con->prepare("DELETE FROM abonos WHERE id = ?");
    $borrar_credito->bind_param('i', $id_abono);
    $borrar_credito->execute();
    $borrar_credito->close();
    
   print_r(1); 

}

?>