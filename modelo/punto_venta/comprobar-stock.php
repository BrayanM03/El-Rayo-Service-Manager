<?php

    include '../conexion.php';
    require_once '../catalogo/Catalogo.php';
    include '../helpers/response_helper.php';

    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    
    if (isset($_POST)) {
        $cantidad= $_POST['cantidad'];
        $id_llanta = $_POST['id_llanta']; 
        $id_sucursal = $_POST['id_sucursal'];
        $catalogo = new Catalogo($con);
        $comprobacion = $catalogo->comprobarStock($id_llanta, $id_sucursal, $cantidad);
        if(!$comprobacion['estatus']){
            responder(false, $comprobacion['mensaje'], 'warning', [], true, true);
        }else{
            responder(true, $comprobacion['mensaje'], 'success', [], true, true);
        }
    }else{
        responder(false,  "No hay solicitud post", 'danger', [], true);

    }