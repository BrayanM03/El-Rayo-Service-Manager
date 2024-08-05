
<?php

session_start();
include '../../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../../login.php");
}

$id_llanta = $_POST['id_llanta'];
$precio_promocion = $_POST['precio_promocion'];

$set = "SELECT count(*) FROM llantas WHERE id = ?";
$stmt = $con->prepare($set);
$stmt->bind_param('s', $id_llanta);
$stmt->execute();
$stmt->bind_result($total_llantas);
$stmt->fetch();
$stmt->close();

if($total_llantas>0){
    $upd = "UPDATE llantas SET promocion=1, precio_promocion=? WHERE id = ?";
    $stmt = $con->prepare($upd);
    $stmt->bind_param('ss', $precio_promocion, $id_llanta);
    $stmt->execute();
    $stmt->close();

    $response = array('estatus' => true, 'mensaje' => 'Promoción agregada');
}else{
    $response = array('estatus' => false, 'mensaje' => 'No existe esa llanta con esa llanta en el catalogo');
}

echo json_encode($response);

?>
