<?php
    session_start();
    include '../conexion.php';
    $con= $conectando->conexion(); 
    
    date_default_timezone_set("America/Matamoros");

    if (!$con) {
        echo "Problemas con la conexion";
    }

    if (!isset($_SESSION['id_usuario'])) {
        header("Location:../login.php");
    }

    $id_usuario = $_SESSION["id_usuario"];
    $rol = $_SESSION["rol"];

    $fecha_hoy = date("Y-m-d");
  //Actualizamos el estatus de los credtios que ya esta vencidos
    $estatusvencido = 4;
    $res = 0.00;
    $update = "UPDATE `creditos` SET estatus = ? WHERE pagado <> total AND restante <> ? AND DATE(fecha_final) <= ?";
    $result = $con->prepare($update);
    $result->bind_param('sss', $estatusvencido, $res, $fecha_hoy);
    $result->execute();
    $result->close();

    $traer = "SELECT * FROM `creditos` WHERE estatus =?";
    $result = $con->prepare($traer);
    if ($result) {
     
    $result->bind_param('s', $estatusvencido);
    $result->execute();
    $array_resultados = $result->get_result();
    $result->close();
    
    while($fila = $array_resultados->fetch_assoc()){
        
        $cliente_id = $fila["id_cliente"];
        $pagado = $fila["pagado"];
        $restante = $fila["restante"];
        $fecha_inicio = $fila["fecha_inicio"];
        $fecha_final = $fila["fecha_final"];
        $id_cred = $fila["id"]; 

        $sql_customer_name = "SELECT Nombre_Cliente FROM `clientes` WHERE id = ?";
        $result = $con->prepare($sql_customer_name);
        $result->bind_param('s', $cliente_id);
        $result->execute();
        $result->bind_result($cliente);
        $result->fetch();
        $result->close();

        $arreglo[] = array("cliente"=> $cliente, "pagado"=> $pagado, "restante"=> $restante, "fecha_inicio"=>$fecha_inicio, "fecha_final"=> $fecha_final, "id_cred"=> $id_cred);

        
    }

    echo json_encode($arreglo, JSON_UNESCAPED_UNICODE);


    }


    ?>