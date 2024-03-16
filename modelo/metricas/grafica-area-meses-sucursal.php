<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

date_default_timezone_set("America/Matamoros");

  if($_POST){

    $año = $_POST['year'];
    $estatus ="Cancelada";
    $enero = 01;
    $febrero = 02;
    $marzo = 03;
    $abril = 04;
    $mayo= 05;
    $junio = 06;
    $julio = 07;
    $agosto = '08';
    $septiembre = '09';
    $octubre = '10';
    $noviembre = '11';
    $diciembre = '12';

    $suc = $_POST["id_suc"];
    $ganancia_mes_sql = $con->prepare("SELECT SUM(CASE WHEN v.tipo = 'Credito' THEN COALESCE(a.abono, 0) ELSE v.Total END) AS Total_Ingresos FROM ventas v 
    LEFT JOIN creditos c ON v.id = c.id_venta LEFT JOIN abonos a ON c.id = a.id_credito 
    WHERE MONTH(v.Fecha) = ? AND YEAR(v.Fecha) = ? AND v.id_sucursal =? AND v.estatus != ?");
               $ganancia_mes_sql->bind_param('ssss', $enero, $año, $suc, $estatus);
               $ganancia_mes_sql->execute();
               $ganancia_mes_sql->bind_result($ganancia_ene);
               $ganancia_mes_sql->fetch();
               $ganancia_mes_sql->close();

               if($ganancia_ene == null){
                   $ganancia_ene = 0;
               } 

               $ganancia_mes_sql = $con->prepare("SELECT SUM(CASE WHEN v.tipo = 'Credito' THEN COALESCE(a.abono, 0) ELSE v.Total END) AS Total_Ingresos FROM ventas v 
               LEFT JOIN creditos c ON v.id = c.id_venta LEFT JOIN abonos a ON c.id = a.id_credito 
               WHERE MONTH(v.Fecha) = ? AND YEAR(v.Fecha) = ? AND v.id_sucursal =? AND v.estatus != ?");
               $ganancia_mes_sql->bind_param('ssss', $febrero, $año, $suc, $estatus);
               $ganancia_mes_sql->execute();
               $ganancia_mes_sql->bind_result($ganancia_feb);
               $ganancia_mes_sql->fetch();
               $ganancia_mes_sql->close();

               if($ganancia_feb == null){
                   $ganancia_feb = 0;
               } 

               $ganancia_mes_sql = $con->prepare("SELECT SUM(CASE WHEN v.tipo = 'Credito' THEN COALESCE(a.abono, 0) ELSE v.Total END) AS Total_Ingresos FROM ventas v 
               LEFT JOIN creditos c ON v.id = c.id_venta LEFT JOIN abonos a ON c.id = a.id_credito 
               WHERE MONTH(v.Fecha) = ? AND YEAR(v.Fecha) = ? AND v.id_sucursal =? AND v.estatus != ?");
               $ganancia_mes_sql->bind_param('ssss', $marzo, $año, $suc, $estatus);
               $ganancia_mes_sql->execute();
               $ganancia_mes_sql->bind_result($ganancia_mar);
               $ganancia_mes_sql->fetch();
               $ganancia_mes_sql->close();

               if($ganancia_mar == null){
                   $ganancia_mar = 0;
               } 

               $ganancia_mes_sql = $con->prepare("SELECT SUM(CASE WHEN v.tipo = 'Credito' THEN COALESCE(a.abono, 0) ELSE v.Total END) AS Total_Ingresos FROM ventas v 
               LEFT JOIN creditos c ON v.id = c.id_venta LEFT JOIN abonos a ON c.id = a.id_credito 
               WHERE MONTH(v.Fecha) = ? AND YEAR(v.Fecha) = ? AND v.id_sucursal =? AND v.estatus != ?");
               $ganancia_mes_sql->bind_param('ssss', $abril, $año, $suc, $estatus);
               $ganancia_mes_sql->execute();
               $ganancia_mes_sql->bind_result($ganancia_abr);
               $ganancia_mes_sql->fetch();
               $ganancia_mes_sql->close();

               if($ganancia_abr == null){
                   $ganancia_abr = 0;
               } 

               $ganancia_mes_sql = $con->prepare("SELECT SUM(CASE WHEN v.tipo = 'Credito' THEN COALESCE(a.abono, 0) ELSE v.Total END) AS Total_Ingresos FROM ventas v 
               LEFT JOIN creditos c ON v.id = c.id_venta LEFT JOIN abonos a ON c.id = a.id_credito 
               WHERE MONTH(v.Fecha) = ? AND YEAR(v.Fecha) = ? AND v.id_sucursal =? AND v.estatus != ?");
               $ganancia_mes_sql->bind_param('ssss', $mayo, $año, $suc, $estatus);
               $ganancia_mes_sql->execute();
               $ganancia_mes_sql->bind_result($ganancia_may);
               $ganancia_mes_sql->fetch();
               $ganancia_mes_sql->close();

               if($ganancia_may == null){
                   $ganancia_may = 0;
               } 

               $ganancia_mes_sql = $con->prepare("SELECT SUM(CASE WHEN v.tipo = 'Credito' THEN COALESCE(a.abono, 0) ELSE v.Total END) AS Total_Ingresos FROM ventas v 
               LEFT JOIN creditos c ON v.id = c.id_venta LEFT JOIN abonos a ON c.id = a.id_credito 
               WHERE MONTH(v.Fecha) = ? AND YEAR(v.Fecha) = ? AND v.id_sucursal =? AND v.estatus != ?");
               $ganancia_mes_sql->bind_param('ssss', $junio, $año, $suc, $estatus);
               $ganancia_mes_sql->execute();
               $ganancia_mes_sql->bind_result($ganancia_jun);
               $ganancia_mes_sql->fetch();
               $ganancia_mes_sql->close();

               if($ganancia_jun == null){
                   $ganancia_jun = 0;
               } 

               $ganancia_mes_sql = $con->prepare("SELECT SUM(CASE WHEN v.tipo = 'Credito' THEN COALESCE(a.abono, 0) ELSE v.Total END) AS Total_Ingresos FROM ventas v 
               LEFT JOIN creditos c ON v.id = c.id_venta LEFT JOIN abonos a ON c.id = a.id_credito 
               WHERE MONTH(v.Fecha) = ? AND YEAR(v.Fecha) = ? AND v.id_sucursal =? AND v.estatus != ?");
               $ganancia_mes_sql->bind_param('ssss', $julio, $año, $suc, $estatus);
               $ganancia_mes_sql->execute();
               $ganancia_mes_sql->bind_result($ganancia_jul);
               $ganancia_mes_sql->fetch();
               $ganancia_mes_sql->close();

               if($ganancia_jul == null){
                   $ganancia_jul = 0;
               } 

               $ganancia_mes_sql = $con->prepare("SELECT SUM(CASE WHEN v.tipo = 'Credito' THEN COALESCE(a.abono, 0) ELSE v.Total END) AS Total_Ingresos FROM ventas v 
               LEFT JOIN creditos c ON v.id = c.id_venta LEFT JOIN abonos a ON c.id = a.id_credito 
               WHERE MONTH(v.Fecha) = ? AND YEAR(v.Fecha) = ? AND v.id_sucursal =? AND v.estatus != ?");
               $ganancia_mes_sql->bind_param('ssss', $agosto, $año, $suc, $estatus);
               $ganancia_mes_sql->execute();
               $ganancia_mes_sql->bind_result($ganancia_ago);
               $ganancia_mes_sql->fetch();
               $ganancia_mes_sql->close();

               if($ganancia_ago == null){
                   $ganancia_ago = 0;
               } 

               $ganancia_mes_sql = $con->prepare("SELECT SUM(CASE WHEN v.tipo = 'Credito' THEN COALESCE(a.abono, 0) ELSE v.Total END) AS Total_Ingresos FROM ventas v 
               LEFT JOIN creditos c ON v.id = c.id_venta LEFT JOIN abonos a ON c.id = a.id_credito 
               WHERE MONTH(v.Fecha) = ? AND YEAR(v.Fecha) = ? AND v.id_sucursal =? AND v.estatus != ?");
               $ganancia_mes_sql->bind_param('ssss', $septiembre, $año, $suc, $estatus);
               $ganancia_mes_sql->execute();
               $ganancia_mes_sql->bind_result($ganancia_sep);
               $ganancia_mes_sql->fetch();
               $ganancia_mes_sql->close();

               if($ganancia_sep == null){
                   $ganancia_sep = 0;
               } 

               $ganancia_mes_sql = $con->prepare("SELECT SUM(CASE WHEN v.tipo = 'Credito' THEN COALESCE(a.abono, 0) ELSE v.Total END) AS Total_Ingresos FROM ventas v 
               LEFT JOIN creditos c ON v.id = c.id_venta LEFT JOIN abonos a ON c.id = a.id_credito 
               WHERE MONTH(v.Fecha) = ? AND YEAR(v.Fecha) = ? AND v.id_sucursal =? AND v.estatus != ?");
               $ganancia_mes_sql->bind_param('ssss', $octubre, $año, $suc, $estatus);
               $ganancia_mes_sql->execute();
               $ganancia_mes_sql->bind_result($ganancia_oct);
               $ganancia_mes_sql->fetch();
               $ganancia_mes_sql->close();

               if($ganancia_oct == null){
                   $ganancia_oct = 0;
               } 

               $ganancia_mes_sql = $con->prepare("SELECT SUM(CASE WHEN v.tipo = 'Credito' THEN COALESCE(a.abono, 0) ELSE v.Total END) AS Total_Ingresos FROM ventas v 
               LEFT JOIN creditos c ON v.id = c.id_venta LEFT JOIN abonos a ON c.id = a.id_credito 
               WHERE MONTH(v.Fecha) = ? AND YEAR(v.Fecha) = ? AND v.id_sucursal =? AND v.estatus != ?");
               $ganancia_mes_sql->bind_param('ssss', $noviembre, $año, $suc, $estatus);
               $ganancia_mes_sql->execute();
               $ganancia_mes_sql->bind_result($ganancia_nov);
               $ganancia_mes_sql->fetch();
               $ganancia_mes_sql->close();

               if($ganancia_nov == null){
                   $ganancia_nov = 0;
               } 


               $ganancia_mes_sql = $con->prepare("SELECT SUM(CASE WHEN v.tipo = 'Credito' THEN COALESCE(a.abono, 0) ELSE v.Total END) AS Total_Ingresos FROM ventas v 
               LEFT JOIN creditos c ON v.id = c.id_venta LEFT JOIN abonos a ON c.id = a.id_credito 
               WHERE MONTH(v.Fecha) = ? AND YEAR(v.Fecha) = ? AND v.id_sucursal =? AND v.estatus != ?");
               $ganancia_mes_sql->bind_param('ssss', $diciembre, $año, $suc, $estatus);
               $ganancia_mes_sql->execute();
               $ganancia_mes_sql->bind_result($ganancia_dic);
               $ganancia_mes_sql->fetch();
               $ganancia_mes_sql->close();

               if($ganancia_dic == null){
                   $ganancia_dic = 0;
               } 

              

               $data = array("ganancia_ene" => $ganancia_ene, "ganancia_feb" => $ganancia_feb, "ganancia_mar" => $ganancia_mar,
               "ganancia_abr" => $ganancia_abr, "ganancia_may" => $ganancia_may,"ganancia_jun" => $ganancia_jun, "ganancia_jul" => $ganancia_jul, "ganancia_ago" => $ganancia_ago,
               "ganancia_sep" => $ganancia_sep, "ganancia_oct" => $ganancia_oct, "ganancia_nov" => $ganancia_nov, "ganancia_dic" => $ganancia_dic);
        

        if (isset($data)) {
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }else{
            print_r("Sin datos");
        }
    

  }

?>
