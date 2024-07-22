<?php
    session_start();
    include '../conexion.php';
    $con= $conectando->conexion(); 
    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    date_default_timezone_set("America/Matamoros");


    function form_moneda($numero_original){
        $numero_formateado = number_format($numero_original, 2, '.', ',');
        return $numero_formateado;
    }
    function form_date($fecha_original){
        $timestamp = strtotime($fecha_original);
        $fecha_formateada = date("d-M-y", $timestamp);
        return $fecha_formateada;
    }

    if (!$con) {
        echo "Problemas con la conexion";
    }

    if (!isset($_SESSION['id_usuario'])) {
        header("Location:../login.php");
    }

    $id_usuario = $_SESSION["id_usuario"];
    $rol = $_SESSION["rol"];

    $fecha_hoy = date("Y-m-d");
    $id_cliente = $_POST["id_cliente"];
  //Actualizamos el estatus de los credtios que ya esta vencidos
    $estatusvencido = 4;
    $res = 0.00;
    /* $update = "UPDATE `creditos` SET estatus = ? WHERE pagado <> total AND restante <> ? AND DATE(fecha_final) <= ?";
    $result = $con->prepare($update);
    $result->bind_param('sss', $estatusvencido, $res, $fecha_hoy);
    $result->execute();
    $result->close(); */

    $traer = "SELECT c.*, v.hora FROM `creditos` as c INNER JOIN ventas v ON c.id_venta = v.id  WHERE c.estatus =? AND c.id_cliente =?";
    $result = $con->prepare($traer);
    if ($result) {
     
    $result->bind_param('ss', $estatusvencido, $id_cliente);
    $result->execute();
    $array_resultados = $result->get_result();
    $result->close();
    
    while($fila = $array_resultados->fetch_assoc()){
        
        $cliente_id = $fila['id_cliente'];
        $pagado = $fila['pagado'];
        $pagado_f = form_moneda($pagado);
        $restante = $fila['restante'];
        $restante_f = form_moneda($restante);

        $total = $fila['total'];
        $total_f = form_moneda($total);

        $fecha_inicio = $fila['fecha_inicio'] .' '. $fila['hora'];
        $fecha_inicio_f = form_date($fecha_inicio);
        $fecha_final = $fila['fecha_final'] .' '. $fila['hora'];
        $fecha_final_f = form_date($fecha_final);
        $id_cred = $fila['id']; 
        $id_venta = $fila['id_venta']; 

        $sql_customer_name = "SELECT Nombre_Cliente FROM `clientes` WHERE id = ?";
        $result = $con->prepare($sql_customer_name);
        $result->bind_param('s', $cliente_id);
        $result->execute();
        $result->bind_result($cliente);
        $result->fetch();
        $result->close();

        $arreglo[] = array('id_venta'=>$id_venta, 'cliente'=> $cliente,'total'=>$total_f, 'pagado'=> $pagado_f, 'restante'=> $restante_f, 'fecha_inicio'=>$fecha_inicio, 'fecha_final'=> $fecha_final, 'id_cred'=> $id_cred);
    }


    

    

    echo json_encode($arreglo, JSON_UNESCAPED_UNICODE);


    }


    ?>