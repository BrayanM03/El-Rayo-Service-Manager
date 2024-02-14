
<?php
/* include '../conexion.php';
$con= $conectando->conexion();  */
//'2021-06-01' AND '2021-12-31' Junio 2021 a Diciembre 2021
//'2022-01-01' AND '2022-06-30' Enero 2022 a Junio 2022
//'2022-07-01' AND '2022-12-31' Julio 2022 a Diciembre 2022
//'2023-01-01' AND '2023-06-30' Enero 2023 a Junio 2023
//'2023-07-01' AND '2023-12-31' Julio 2023 a Diciembre 2023
//'2024-01-01' AND '2024-02-06' Enero 2024 a Febrero 2024
/* $query = "SELECT * FROM abonos WHERE fecha BETWEEN '2024-01-01' AND '2024-02-14'";
$stmt = $con->prepare($query);
$stmt->execute();
$ventas_general =$stmt->get_result();
$stmt->close();
foreach($ventas_general as $fila){
    $id_abono = $fila['id'];
    $re = insertarUtilidadAbono($id_abono, $con);
}
echo json_encode($re); */ 
//$u = insertarUtilidadAbono(690, $con);

function insertarUtilidadAbono($id_abono, $con){
    $utilidad_credito=0;
    $total_a_pagar=0;
    $abono =0;
    $sel = "SELECT a.abono, v.utilidad, c.total FROM abonos a INNER JOIN creditos c ON c.id = a.id_credito INNER JOIN ventas v ON v.id = c.id_venta WHERE a.id = ?";
    $stmt = $con->prepare($sel);
    $stmt->bind_param('s',$id_abono);
    $stmt->execute();
    $stmt->bind_result($abono, $utilidad_credito, $total_a_pagar);
    $stmt->fetch();
    $stmt->close();
    $porcentaje_utilidad = calcular_porcentaje_utilidad($abono, $total_a_pagar, $utilidad_credito);
    if($porcentaje_utilidad>0){
        $porcentaje_utilidad = floatval(str_replace(',', '', $porcentaje_utilidad));
       
        $upd = "UPDATE abonos SET utilidad = ? WHERE id =?";
        $stmt = $con->prepare($upd);
        $stmt->bind_param('ss', $porcentaje_utilidad, $id_abono);
        $stmt->execute();
        $stmt->close();
    }
}

function calcular_porcentaje_utilidad($abono, $total, $utilidad) {
    // Calcula el porcentaje del abono con respecto al total
    if($total <=0|| $abono <=0 || $utilidad == null){
        return 0;
    }else{
        if($utilidad > 0){
            $porcentaje_abono = ($abono / $total) * 100;
            // Calcula el monto correspondiente al porcentaje del abono en la utilidad
            $porcentaje_utilidad = ($porcentaje_abono / 100) * $utilidad;
            
            return number_format($porcentaje_utilidad,2);
        }else{
            return 0;
        }
    }
}
?>