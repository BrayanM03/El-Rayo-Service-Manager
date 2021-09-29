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
    $metodo = $_POST["metodo_pago"];

    switch ($metodo) {
        case '0':
            $metodo_pago = "Efectivo";
            break;

            case '1':
                $metodo_pago = "Tarjeta";
                break;
                
                case '2':
                    $metodo_pago = "Transferencia";
                    break;

                    case '3':
                        $metodo_pago = "Cheque";
                        break;

                        case '4':
                            $metodo_pago = "Sin definir";
                            break;
        
        default:
            # code...
            break;
    }

    $usuario = $_SESSION["nombre"];

    if ($abono == 0) {
        $estatus = 0;
    }else{
        $estatus = 1;
    }
    
   

    if($_POST["fecha"] == ""){
        $fecha_inicio = date("Y-m-d");
    }else{
        
        $fecha_inicio = $_POST["fecha"];
    }
    
    $fecha = date($fecha_inicio);

    switch ($plazo) {
        case '1':
            $fecha_limite = date("Y-m-d",strtotime($fecha ."+ 7 days")); 
        break;
        
        case '2':
            $fecha_limite = date("Y-m-d",strtotime($fecha ."+ 15 days")); 
        break;
        
        case '3':
            $fecha_limite = date("Y-m-d",strtotime($fecha ."+ 1 month")); 
        break;
        
        case '4':
            $fecha_limite = date("Y-m-d",strtotime($fecha ."+ 1 year")); 
        break;
        
        case '5':
            $fecha_limite = date("Y-m-d",strtotime($fecha ."+ 7 days")); 
        break;

        default:
            # code...
        break;
    }

    $sql = "SELECT id FROM ventas ORDER BY id DESC LIMIT 1";
    $resultados = mysqli_query($con, $sql);
    
    if(!$resultados){

      echo "no se pudo realizar la consulta";

    }else{
      $dato =  mysqli_fetch_array($resultados, MYSQLI_ASSOC);

        
        $id_Venta = $dato["id"];
    }


   
    $insertar_credito = "INSERT INTO creditos(id_cliente, pagado, restante, total, estatus, fecha_inicio, fecha_final, plazo, id_venta)
                         VALUES(?,?,?,?,?,?,?,?,?)";
    $resultado = $con->prepare($insertar_credito);                     
    $resultado->bind_param('idddissii', $id_cliente, $abono, $restante, $importe_total ,$estatus, $fecha_inicio, $fecha_limite, $plazo, $id_Venta);
    $resultado->execute();
    $resultado->close();

    $sql = "SELECT id FROM creditos ORDER BY id DESC LIMIT 1";
    $resultado2 = mysqli_query($con, $sql);

    if ($resultado2) {
        $dato =  mysqli_fetch_array($resultado2, MYSQLI_ASSOC);
        $id_credito = $dato["id"];
        
            $queryInsertar = "INSERT INTO abonos (id, id_credito, fecha, hora, abono, metodo_pago, usuario) VALUES (null,?,?,?,?,?,?)";
            $resultado = $con->prepare($queryInsertar);
            $resultado->bind_param('ssssss',$id_credito, $fecha_inicio, $hora, $abono, $metodo_pago, $usuario);
            $resultado->execute();
            $resultado->close();
    }else{
        print_r("valio queso");
    }
}


?>