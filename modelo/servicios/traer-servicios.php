<?php

session_start();
include '../conexion.php';
$con = $conectando->conexion();

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

$id_usuario = $_POST['id_usuario'];


$queryChange = "SELECT COUNT(*) FROM servicios";
$resps = $con->prepare($queryChange);
$resps->execute();
$resps->bind_result($total_servicios);
$resps->fetch();
$resps->close();

if ($total_servicios > 0) {
    $querySuc = "SELECT * FROM servicios ORDER BY id DESC";
    $respon = mysqli_query($con, $querySuc);

    while ($rows = $respon->fetch_assoc()) {
        $id = $rows['id'];
        $codigo = $rows["codigo"];
        $descripcion= $rows["descripcion"];
        $precio = $rows["precio"];
        $estatus = $rows["estatus"];
        $img = $rows["img"];

       

        $data[] = array("id"=>$id, "codigo"=> $codigo, 
                        "descripcion"=> $descripcion, 
                        "precio"=> $precio,
                        "estatus"=> $estatus, 
                        "img"=> $img);

    }

   echo json_encode($data, JSON_UNESCAPED_UNICODE);
}else{
    $data = array("id"=>false, "mensaje"=> "Sin datos");
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}


