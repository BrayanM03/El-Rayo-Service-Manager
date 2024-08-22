<?php 
include '../../conexion.php';
$con = $conectando->conexion();

$visto = $_GET['visto'];

 
if($visto == 'all'){
    $id_usuario = $_POST['id_usuario'];
    $select_count = 'SELECT COUNT(*) FROM notificaciones_usuarios WHERE id_usuario = ? AND estatus_vista =0';
            $stmt = $con->prepare($select_count);
            $stmt->bind_param('s', $id_usuario);
            $stmt->execute();
            $stmt->bind_result($notifications_vista_count);
            $stmt->fetch();
            $stmt->close();


            if($notifications_vista_count>0){
                $upd = 'UPDATE notificaciones_usuarios SET estatus_vista = 1 WHERE estatus_vista =0 AND id_usuario = ?';
                $stmt = $con->prepare($upd);
                $stmt->bind_param('s', $id_usuario);
                $stmt->execute();
                $stmt->close();
                $response = array('estatus'=>true, 'mensaje'=> 'Notificaciones actualizadas');
            }else{
                $response = array('estatus'=>false, 'mensaje'=> 'No es necesario actualizar');

            }

}else{
    $abierto = $_GET['abierto']; 
    $id_nu = $_GET['id_nu']; 
    $id_notificacion = $_GET['id_notificacion'];
    $select_count = 'SELECT COUNT(*) FROM notificaciones_usuarios nu INNER JOIN notificaciones n ON n.id = nu.id_notificacion WHERE nu.id =?';
    $stmt = $con->prepare($select_count);
    $stmt->bind_param('s', $id_nu);
    $stmt->execute();
    $stmt->bind_result($notifications_vista_count);
    $stmt->fetch();
    $stmt->close();
    
    if($notifications_vista_count>0){
        $select_type = 'SELECT id_credito, categoria FROM notificaciones WHERE id =?';
        $stmt = $con->prepare($select_type);
        $stmt->bind_param('s', $id_notificacion);
        $stmt->execute();
        $stmt->bind_result($id_credito, $categoria_notificacion);
        $stmt->fetch();
        $stmt->close();

        $upd = 'UPDATE notificaciones_usuarios SET estatus_abierta = 1 WHERE id= ?';
        $stmt = $con->prepare($upd);
        $stmt->bind_param('s', $id_nu);
        $stmt->execute();
        $stmt->close();

        if($categoria_notificacion == 'Aviso'){
            header("Location:../../../detalle-credito.php?id_credito=".$id_credito);
        }

    }
}

echo json_encode($response);



?>