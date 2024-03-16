<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

date_default_timezone_set("America/Matamoros");
  $hora = date("h:i a");
  $fecha = date("Y-m-d"); 
  $mes = date("m");
  $año = date("Y");

  if($_POST){
    $estatus ="Cancelada";

    $traer="SELECT COUNT(*) FROM sucursal";
    $res= $con->prepare($traer);
    $res->execute();
    $res->bind_result($tot);
    $res->fetch();
    $res->close();

    $ventas_totales = [];
    if($tot > 0){

        $query = "SELECT * FROM sucursal";
        $resp = mysqli_query($con, $query);

        while ($fila = $resp->fetch_assoc()) {
            $id = $fila["id"];
            $nombre_suc = $fila["nombre"];


            $ganancia_pedro_sql = $con->prepare("SELECT COUNT(*) FROM `ventas` WHERE id_sucursal = ? AND YEAR(Fecha) = ? AND estatus <> ?");
            $ganancia_pedro_sql->bind_param('sss', $id, $año, $estatus);
            $ganancia_pedro_sql->execute();
            $ganancia_pedro_sql->bind_result($numero_ventas);
            $ganancia_pedro_sql->fetch();
            $ganancia_pedro_sql->close();


            if($numero_ventas == null){
                $numero_ventas =0;
            }else{
                $numero_ventas = floatval($numero_ventas);
            }

            $tarer_colores = $con->prepare("SELECT color_out, color_hover FROM `colores_sucursales` WHERE id_suc = ?");
            $tarer_colores->bind_param('i', $id);
            $tarer_colores->execute();
            $tarer_colores->bind_result($background, $hover);
            $tarer_colores->fetch();
            $tarer_colores->close();

            $ventas_totales[] = array("id"=>$id,
                                      "sucursal" => $nombre_suc, 
                                      "numero_ventas" => $numero_ventas,
                                      "color_back"=>$background,
                                      "color_hover"=>$hover);

        }

    }

        if (isset($ventas_totales)) {
            echo json_encode($ventas_totales, JSON_UNESCAPED_UNICODE);
        }else{
            print_r("Sin datos");
        }

  }


  ?>