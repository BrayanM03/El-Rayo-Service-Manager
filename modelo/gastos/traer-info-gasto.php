<?php
session_start();
include '../conexion.php';
$con = $conectando->conexion(); 
$id_gasto = $_POST['id'];
$count = "SELECT COUNT(*) FROM gastos WHERE id = ?";
$res = $con->prepare($count);
$res->bind_param('s', $id_gasto);
$res->execute();
$res->bind_result($total_gastos);
$res->fetch();
$res->close();

if($total_gastos>0){
    $sel = "SELECT g.*, gfp.monto as monto FROM gastos g 
    INNER JOIN gastos_formas_pago gfp ON gfp.id_gasto = g.id
    INNER JOIN formas_pago fp ON gfp.id_forma_pago = fp.id WHERE g.id = ?";
    $res = $con->prepare($sel);
    $res->bind_param('s', $id_gasto);
    $res->execute();
    $resultado_gasto = $res->get_result();  
    $res->close();
    while($fila = $resultado_gasto->fetch_assoc()){
        $data = $fila;
    }

    $res = array('estatus'=> true, 'mensaje'=> 'Se encontraron datos', 'data'=>$data);

}else{
    $res = array('estatus'=> false, 'mensaje'=> 'El folio no coincide con el gasto');
}

echo json_encode($res);

