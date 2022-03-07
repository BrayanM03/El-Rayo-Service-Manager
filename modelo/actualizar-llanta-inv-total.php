<?php
session_start();
include 'conexion.php';
$con= $conectando->conexion(); 

if (!$con) {
    echo "maaaaal";
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if (isset($_POST)) {

    
        $codigo = $_POST["codigo"]; 

        $e_ancho       = $_POST["ancho"];
        $e_alto        = $_POST["alto"];
        $e_rin         = $_POST["rin"];
        $e_modelo      = $_POST["modelo"];
        $e_fecha       = $_POST["fecha"];
        $e_costo       = $_POST["costo"];
        $e_precio      = $_POST["precio"];
        $e_mayorista   = $_POST["mayorista"];
        $e_descripcion = $_POST["descripcion"];
        $e_marca       = $_POST["marca"];
    
         $editar_llanta= $con->prepare("UPDATE llantas SET Ancho = ?, Proporcion = ?, Diametro = ?, Descripcion = ?, Marca = ?, Modelo = ?,precio_Inicial = ?, precio_Venta = ?, precio_Mayoreo = ?, Fecha = ? WHERE id = ?");
         $editar_llanta->bind_param('iissssdddsi', $e_alto, $e_ancho, $e_rin, $e_descripcion, $e_marca, $e_modelo, $e_costo, $e_precio, $e_mayorista, $e_fecha, $codigo);
         $editar_llanta->execute();
         $editar_llanta->close();
         
        print_r(1);



}else{
    print_r(2);
}


?>