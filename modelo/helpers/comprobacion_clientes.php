<?php

function comprobar_clientes($id, $con){
    $cantidad_clientes=0;
    $count = 'SELECT COUNT(*) FROM clientes WHERE id = ?';
    $stmt = $con->prepare($count);
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $stmt->bind_result($cantidad_clientes);
    $stmt->fetch();
    $stmt->close();

    if($cantidad_clientes==1){
        $sel = 'SELECT * FROM clientes WHERE id = ?';
        $stmt = $con->prepare($sel);
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $data = $stmt->get_result();
        $stmt->free_result();
        $response=array();
        foreach ($data as $key => $value) {
            $response = $value;
        }
       
        return $response;
    }else{
        return [];
    }


};


?>