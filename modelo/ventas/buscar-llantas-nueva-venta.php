<?php

include '../conexion.php';
$con = $conectando->conexion();

if (!$con) {
    echo "maaaaal";
}

if (isset($_POST["searchTerm"])) {
    $term = $_POST["searchTerm"];
    $parametro = "%$term%";
    $id_user_sucursal = $_POST["id_sucursal"];
    $user_rol = $_POST["rol"];

   // print_r($rol_usr);
/*     if ($user_rol != 1) {
        $query_mostrar = $con->prepare("SELECT COUNT(*) total FROM llantas l INNER JOIN inventario 
            ON inventario.id_Llanta = l.id 
            WHERE  l.Ancho LIKE ?
            OR inventario.Codigo LIKE ? 
            OR l.Proporcion LIKE ? 
            OR l.Diametro LIKE ?
            OR l.Modelo LIKE ? 
            OR l.Marca LIKE ? 
            OR l.Descripcion LIKE ? AND inventario.id_sucursal =?");

        $query_mostrar->bind_param('ssssssss', $parametro, $parametro, $parametro, $parametro, $parametro, $parametro, $parametro, $id_user_sucursal);
        $query_mostrar->execute();
        $query_mostrar->bind_result($total);
        $query_mostrar->fetch();
        $query_mostrar->close();


        if ($total > 0) {

            $sqlTraerLlanta = "SELECT l.*, inv.id, inv.Codigo, inv.Sucursal, inv.id_sucursal, inv.Stock FROM llantas l INNER JOIN inventario inv
  ON inv.id_Llanta = l.id WHERE inv.id_sucursal = '$id_user_sucursal' AND (l.Ancho LIKE '%$term%'
  OR inv.Codigo LIKE '%$term%'
  OR l.Proporcion LIKE '%$term%'  
  OR l.Diametro LIKE '%$term%'
  OR l.Modelo LIKE '%$term%'  
  OR l.Marca LIKE '%$term%' 
  OR l.Descripcion LIKE '%$term%')";
            $result = mysqli_query($con, $sqlTraerLlanta);
            while ($datas = mysqli_fetch_array($result)) {

                $llantasEncontradas[]  = $datas;
            } 
            echo json_encode($llantasEncontradas, JSON_UNESCAPED_UNICODE);
        }
    }else if($user_rol == 1){ *///Los usuarios con provilegios nivel 1 pueden vender de cualquier sucursal
        $query_mostrar = $con->prepare("SELECT COUNT(*) total FROM llantas l INNER JOIN inventario 
        ON inventario.id_Llanta = l.id 
        WHERE  l.Ancho LIKE ?
        OR inventario.Codigo LIKE ? 
        OR l.Proporcion LIKE ? 
        OR l.Diametro LIKE ?
        OR l.Modelo LIKE ? 
        OR l.Marca LIKE ? 
        OR l.Descripcion LIKE ?");

    $query_mostrar->bind_param('sssssss', $parametro, $parametro, $parametro, $parametro, $parametro, $parametro, $parametro);
    $query_mostrar->execute();
    $query_mostrar->bind_result($total);
    $query_mostrar->fetch();
    $query_mostrar->close();


    if ($total > 0) {

        $sqlTraerLlanta = "SELECT l.*, inv.id, inv.Codigo, inv.Sucursal, inv.id_sucursal, inv.Stock FROM llantas l INNER JOIN inventario inv
ON inv.id_Llanta = l.id WHERE l.Ancho LIKE '%$term%'
OR inv.Codigo LIKE '%$term%'
OR l.Proporcion LIKE '%$term%'  
OR l.Diametro LIKE '%$term%'
OR l.Modelo LIKE '%$term%'  
OR l.Marca LIKE '%$term%' 
OR l.Descripcion LIKE '%$term%'";
        $result = mysqli_query($con, $sqlTraerLlanta);
        while ($datas = mysqli_fetch_array($result)) {

            $llantasEncontradas[]  = $datas;
        } //Aqui termina de buscar en una sucursal y valida si hay en otra

        //echo json_encode($_POST);
       echo json_encode($llantasEncontradas, JSON_UNESCAPED_UNICODE);
    }
   // }
} else {
    print_r("Error al conectar");
}
