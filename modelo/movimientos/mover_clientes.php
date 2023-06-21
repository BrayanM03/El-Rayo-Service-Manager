<?php


date_default_timezone_set("America/Matamoros");


if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

function InsertarMovimiento($accion, $descripcion, $con){
    $fecha = date("Y-m-d");
    $hora = date("H:i:s");
    

    $usuario_id = $_SESSION["id_usuario"];
    $usuario_nombre = $_SESSION["nombre"]. " " .$_SESSION["apellidos"];
    $id_sucursal = $_SESSION["id_sucursal"];
    $nombre_sucursal = traer_sucursal($id_sucursal, $con);

    $query = "INSERT INTO movimientos_clientes(id, descripcion, fecha, id_usuario, nombre_completo, hora, accion, id_sucursal, sucursal_nombre)
    VALUES(null, ?,?,?,?,?,?,?,?)";

    $resp = $con->prepare($query);
    $resp->bind_param('ssisssis', $descripcion, $fecha, $usuario_id, $usuario_nombre, $hora, $accion, $id_sucursal, $nombre_sucursal);
    $resp->execute();
    $resp->close();
}

function traer_sucursal($id_sucursal, $con){
    $total_suc = 0;
    $querySuc = "SELECT COUNT(*) FROM sucursal WHERE id = ?";
    $resp=$con->prepare($querySuc);
    $resp->bind_param('i', $id_sucursal);
    $resp->execute();
    $resp->bind_result($total_suc);
    $resp->fetch();
    $resp->close();

    if($total_suc>0){
        $querySuc = "SELECT * FROM sucursal WHERE id = $id_sucursal";
        $resp = mysqli_query($con, $querySuc);

        while ($row = $resp->fetch_assoc()){
            $id = $row['id'];
            if($id !==1){
                $nombre = $row['nombre'];
            }
           
            }

            return $nombre;
    }
}

function traerAsesor($id_asesor, $con){
    $total_suc = 0;
    $querySuc = "SELECT COUNT(*) FROM usuarios WHERE id = ?";
    $resp=$con->prepare($querySuc);
    $resp->bind_param('i', $id_asesor);
    $resp->execute();
    $resp->bind_result($total_suc);
    $resp->fetch();
    $resp->close();

    if($total_suc>0){
        $querySuc = "SELECT * FROM usuarios WHERE id !=1 AND id = $id_asesor";
        $resp = mysqli_query($con, $querySuc);

        while ($row = $resp->fetch_assoc()){
            $id = $row['id'];
            if($id !==1){
                $nombre = $row['nombre'] ." ". $row['apellidos'];
            }
           
            }

            return $nombre;
    }
}

function traerCliente($id_cliente, $con){
    $total_suc = 0;
    $querySuc = "SELECT COUNT(*) FROM clientes WHERE id = ?";
    $resp=$con->prepare($querySuc);
    $resp->bind_param('i', $id_cliente);
    $resp->execute();
    $resp->bind_result($total_suc);
    $resp->fetch();
    $resp->close();

    if($total_suc>0){
        $querySuc = "SELECT * FROM clientes WHERE id = $id_cliente";
        $resp = mysqli_query($con, $querySuc);

        while ($row = $resp->fetch_assoc()){
            $id = $row['id'];
            if($id !==1){
                $nombre = $row['Nombre_Cliente'];
            }
           
            }

            return $nombre;
    }
}



?>