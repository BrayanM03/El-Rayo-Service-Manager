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
       
    
    $query="SELECT creditos.id, creditos.id_cliente, creditos.pagado, creditos.restante, creditos.total, creditos.estatus, creditos.fecha_inicio, creditos.fecha_final, creditos.plazo, clientes.Nombre_Cliente FROM creditos INNER JOIN clientes ON creditos.id_cliente = clientes.id";

    $resultado = mysqli_query($con, $query);

    while($fila = $resultado->fetch_assoc()){
    $id = $fila["id"];
    $cliente = $fila["Nombre_Cliente"];
    $pagado = $fila["pagado"];
    $restante = $fila["restante"];
    $total = $fila["total"];
    $estatus = $fila["estatus"];
    $fecha_inicio = $fila["fecha_inicio"];
    $fecha_final = $fila["fecha_final"];
    $plazo = $fila["plazo"];

    $data["data"][] = array("id" => $id, "fecha_inicial"=>$fecha_inicio,"fecha_final"=>$fecha_final, "restante" => $restante,
                    "pagado" => $pagado, "cliente"=>$cliente, "total"=>$total, "plazo"=>$plazo, "estatus"=>$estatus);

                  
}

echo json_encode($data, JSON_UNESCAPED_UNICODE);  

}else{
    print_r("No se pudo establecer una conexión");
}


?>