<?php

include '../conexion.php';
$con = $conectando->conexion();
session_start();

if (!$con) {
    echo "maaaaal";
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}
date_default_timezone_set("America/Matamoros");
$id_sucursal = $_GET['id_sucursal'];
$fecha = $_GET['fecha'];

$venta_total = obtenerVentaTotal($con, $id_sucursal, $fecha, "Normal", "Pagado");
$utilidad_total = obtenerUtilidadTotal($con, $id_sucursal, $fecha, "Normal", "Pagado");


$venta_metodo_efectivo = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, "Normal", "Pagado", "Efectivo");
$venta_metodo_tarjeta = obtenerVentaMetodoPago($con, $id_sucursal, $fecha, "Normal", "Pagado", "Tarjeta");
$utilidad_metodo_efectivo = obtenerUtilidadMetodoPago($con, $id_sucursal, $fecha, "Normal", "Pagado", "Efectivo");
$utilidad_metodo_cheque = obtenerUtilidadMetodoPago($con, $id_sucursal, $fecha, "Normal", "Pagado", "Cheque");

print_r("<b>Venta total:</b>" . $venta_total . "<br/>");
print_r("<b>Utilidad total: </b>" . $utilidad_total . "<br/>");

print_r("<b>Venta efectivo:</b>" . $venta_metodo_efectivo . "<br/>");
print_r("<b>Venta tarjeta:</b>" . $venta_metodo_tarjeta . "<br/>");
print_r("<b>Utilidad efectivo: </b>" . $utilidad_metodo_efectivo . "<br/>");
print_r("<b>Utilidad tarjeta: </b>" . $utilidad_metodo_tarjeta . "<br/>");

//Funciones para obtener utilidad de ventas
function obtenerUtilidadTotal($con, $id_sucursal, $fecha, $tipo, $estatus){

    $total_venta_metodo = 0;
    $consulta = "SELECT SUM(Total) FROM ventas WHERE id_sucursal=? AND fecha = ? AND tipo = ? AND estatus = ?";
    $res = $con->prepare($consulta);
    $res->bind_param("ssss", $id_sucursal, $fecha, $tipo, $estatus);
    $res->execute();
    $res->bind_result($total_venta_metodo);
    $res->fetch();
    $res->close();

    if($total_venta_metodo == null || $total_venta_metodo == ""){
        return 0;
    }else{

        
    $lista_ventas = mysqli_query($con, "SELECT * FROM ventas WHERE id_sucursal='$id_sucursal' AND 
                                                                   fecha = '$fecha' AND 
                                                                   tipo = '$tipo' AND 
                                                                   estatus = '$estatus'");
        //Iteramos sobre el arreglo de las ventas 
        $importe_acumulado =0; 
        $costo_acumulado =0;                                                            
        while ($row = mysqli_fetch_array($lista_ventas)){

        $id_venta = intval($row["id"]);

        $lista_llantas = mysqli_query($con, "SELECT * FROM detalle_venta WHERE id_Venta = '$id_venta'");

        //Iteramos sobre el arreglo de los detalles de venta

        while ($fila = mysqli_fetch_array($lista_llantas)){
        $costo_llanta = 0;
        $id_llanta = intval($fila["id_Llanta"]);
        $cantidad_llantas = intval($fila["Cantidad"]);
        $precio_unitario = floatval($fila["precio_Unitario"]);
        $total_importe = floatval($fila["Importe"]);
        $importe_acumulado =$importe_acumulado + $total_importe;
        
        $consulta = "SELECT precio_Inicial FROM llantas WHERE id=?";
        $resp = $con->prepare($consulta);
        $resp->bind_param("s", $id_llanta);
        $resp->execute();
        $resp->bind_result($costo_llanta);
        $resp->fetch();
        $resp->close();

        $costo_total = $costo_llanta * $cantidad_llantas;

        echo $costo_total . " -- <br/>";

        $costo_total = floatval($costo_total);
        $costo_acumulado = $costo_acumulado + $costo_total;

        }


        }
    $utilidad_total = $importe_acumulado - $costo_acumulado;    /* 
    print_r("importe total desde 1era consulta:" . $total_venta_metodo . "<br/>");*/
    return $utilidad_total;
    }

}

function obtenerUtilidadMetodoPago($con, $id_sucursal, $fecha, $tipo, $estatus, $metodo_pago){
    $total_venta_metodo = 0;
    $consulta = "SELECT SUM(Total) FROM ventas WHERE id_sucursal=? AND fecha = ? AND tipo = ? AND estatus = ? AND metodo_pago =?";
    $res = $con->prepare($consulta);
    $res->bind_param("sssss", $id_sucursal, $fecha, $tipo, $estatus, $metodo_pago);
    $res->execute();
    $res->bind_result($total_venta_metodo);
    $res->fetch();
    $res->close();

    if($total_venta_metodo == null || $total_venta_metodo == ""){
        return 0;
    }else{

        
    $lista_ventas = mysqli_query($con, "SELECT * FROM ventas WHERE id_sucursal='$id_sucursal' AND 
                                                                   fecha = '$fecha' AND 
                                                                   tipo = '$tipo' AND 
                                                                   estatus = '$estatus' AND 
                                                                   metodo_pago = '$metodo_pago'");
        //Iteramos sobre el arreglo de las ventas 
        $importe_acumulado =0; 
        $costo_acumulado =0;                                                            
        while ($row = mysqli_fetch_array($lista_ventas)){

        $id_venta = intval($row["id"]);

        $lista_llantas = mysqli_query($con, "SELECT * FROM detalle_venta WHERE id_Venta = '$id_venta'");

        //Iteramos sobre el arreglo de los detalles de venta

        while ($fila = mysqli_fetch_array($lista_llantas)){
        $costo_llanta = 0;
        $id_llanta = intval($fila["id_Llanta"]);
        $cantidad_llantas = intval($fila["Cantidad"]);
        $precio_unitario = floatval($fila["precio_Unitario"]);
        $total_importe = floatval($fila["Importe"]);
        $importe_acumulado =$importe_acumulado + $total_importe;
        
        $consulta = "SELECT precio_Inicial FROM llantas WHERE id=?";
        $resp = $con->prepare($consulta);
        $resp->bind_param("s", $id_llanta);
        $resp->execute();
        $resp->bind_result($costo_llanta);
        $resp->fetch();
        $resp->close();

        $costo_total = $costo_llanta * $cantidad_llantas;

        echo $costo_total . " -- <br/>";

        $costo_total = floatval($costo_total);
        $costo_acumulado = $costo_acumulado + $costo_total;

        }


        }
    $utilidad_total = $importe_acumulado - $costo_acumulado;    /* 
    print_r("importe total desde 1era consulta:" . $total_venta_metodo . "<br/>");
    print_r("costo total desde iteraciones: " .$costo_acumulado . "<br/>"); */
    return $utilidad_total;
    }
    
}

//Funciones para obtener ganancias
function obtenerVentaTotal($con, $id_sucursal, $fecha, $tipo, $estatus){
    $total_venta = 0;
    $consulta = "SELECT SUM(Total) FROM ventas WHERE id_sucursal=? AND fecha = ? AND tipo = ? AND estatus = ?";
    $res = $con->prepare($consulta);
    $res->bind_param("ssss", $id_sucursal, $fecha, $tipo, $estatus);
    $res->execute();
    $res->bind_result($total_venta);
    $res->fetch();
    $res->close();

    if($total_venta == "" || $total_venta ==null){
        $total_venta = 0;
    }
    return $total_venta;
}

function obtenerVentaMetodoPago($con, $id_sucursal, $fecha, $tipo, $estatus, $metodo_pago){
    $total_venta = 0;
    $consulta = "SELECT SUM(Total) FROM ventas WHERE id_sucursal=? AND fecha = ? AND tipo = ? AND estatus = ? AND metodo_pago =?";
    $res = $con->prepare($consulta);
    $res->bind_param("sssss", $id_sucursal, $fecha, $tipo, $estatus, $metodo_pago);
    $res->execute();
    $res->bind_result($total_venta);
    $res->fetch();
    $res->close();

    if($total_venta == "" || $total_venta ==null){
        $total_venta = 0;
    }
    return $total_venta;
}

?>