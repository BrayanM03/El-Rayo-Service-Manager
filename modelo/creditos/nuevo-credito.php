<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 
date_default_timezone_set("America/Matamoros");

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

if (!$con) {
    echo "maaaaal";
}


if(isset($_POST)){
    $id_cliente = $_POST["id_cliente"];
   
    $plazo = $_POST["plazo"];
    $importe_total = $_POST["importe"];
    $abono = $_POST["abono"];
    $restante = $_POST["restante"];
    $hora = date("h:i a");
    $estatus = 1;
   

    if($_POST["fecha"] == ""){
        $fecha_inicio = date("d-m-Y");
    }else{
        
        $fecha_inicio = $_POST["fecha"];
    }
    
    $fecha = date($fecha_inicio);

    switch ($plazo) {
        case '1':
            $fecha_limite = date("d-m-Y",strtotime($fecha ."+ 7 days")); 
        break;
        
        case '2':
            $fecha_limite = date("d-m-Y",strtotime($fecha ."+ 15 days")); 
        break;
        
        case '3':
            $fecha_limite = date("d-m-Y",strtotime($fecha ."+ 1 month")); 
        break;
        
        case '4':
            $fecha_limite = date("d-m-Y",strtotime($fecha ."+ 1 year")); 
        break;
        
        case '5':
            $fecha_limite = date("d-m-Y",strtotime($fecha ."+ 7 days")); 
        break;

        default:
            # code...
        break;
    }

   
    $insertar_credito = "INSERT INTO creditos(id_cliente, pagado, restante, total, estatus, fecha_inicio, fecha_final, plazo)
                         VALUES(?,?,?,?,?,?,?,?)";
    $resultado = $con->prepare($insertar_credito);                     
    $resultado->bind_param('idddissi', $id_cliente, $abono, $restante, $importe_total ,$estatus, $fecha_inicio, $fecha_limite, $plazo);
    $resultado->execute();
    $resultado->close();

    $sql = "SELECT id FROM creditos ORDER BY id DESC LIMIT 1";
    $resultado2 = mysqli_query($con, $sql);

    if ($resultado2) {
        $dato =  mysqli_fetch_array($resultado2, MYSQLI_ASSOC);
        $id_credito = $dato["id"];
        
            $queryInsertar = "INSERT INTO abonos (id, id_credito, fecha, abono) VALUES (null,?,?,?)";
            $resultado = $con->prepare($queryInsertar);
            $resultado->bind_param('isd',$id_credito,$fecha_inicio, $abono );
            $resultado->execute();
            $resultado->close();
    }else{
        print_r("valio queso");
    }
}


?>