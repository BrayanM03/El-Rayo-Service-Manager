<?php


session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if (!$con) {
    echo "maaaaal";
}

if (isset($_POST)) {
       
    
    $query="SELECT ventas.id, ventas.Fecha, ventas.sucursal, ventas.id_sucursal, usuarios.nombre, clientes.Nombre_Cliente, ventas.Cantidad, ventas.Total, ventas.tipo, ventas.estatus FROM ventas INNER JOIN usuarios ON ventas.id_Usuarios = usuarios.id INNER JOIN clientes ON ventas.id_Cliente = clientes.id";

    $resultado = mysqli_query($con, $query);

    while($fila = $resultado->fetch_assoc()){
    $codigo = $fila["id"];
    $fecha = $fila["Fecha"];
    $nombre_suc = $fila["sucursal"];
    $sucursal = $fila["id_sucursal"];
    $vendedor = $fila["nombre"];
    $cliente = $fila["Nombre_Cliente"];
    $cantidad = $fila["Cantidad"];
    $total = $fila["Total"];
    $tipo = $fila["tipo"];
    $estatus = $fila["estatus"];

    $data["data"][] = array("folio" => $codigo, "fecha"=>$fecha,"nombre_suc"=>$nombre_suc, "sucursal" => $sucursal,
                    "vendedor" => $vendedor, "cliente"=>$cliente, "cantidad"=>$cantidad, "total"=>$total, "tipo"=>$tipo, "estatus"=>$estatus);

                  
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);  

}else{
    print_r("No se pudo establecer una conexión");
}


?>