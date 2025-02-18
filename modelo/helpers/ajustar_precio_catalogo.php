<?php

// Función para extraer las medidas de la descripción de la llanta

include '../conexion.php';
$con= $conectando->conexion();

/***************************************************
 * 2) DEFINIMOS LOS RANGOS Y LAS SUMAS CORRESPONDIENTES
 *    Formato: [rango_inicial, rango_final, +Lista, +Venta, +Mayoreo]
 ***************************************************/
$rangos = [
    [0,     849,   450, 300, 150],
    [850,   1049,  475, 350, 200],
    [1050,  1349,  550, 400, 250],
    [1350,  1649,  550, 400, 250],
    [1650,  1750,  550, 400, 275],
    [1751,  1995,  600, 450, 300],
    [1996,  2695,  650, 475, 350],
    [2696,  2795,  700, 500, 350],
    [2796,  3395,  800, 550, 400],
    [3496,  4995,  850, 650, 400],
    [4996,  6995,  895, 700, 450],
    [6996,  9995,  1000, 800, 450],
    [9996,  999999999, 3000, 2500, 1300], // Para 9995 en adelante
];

$nuevoLista = redondeoEspecial(1070);
/* print_r($nuevoLista); */
/***************************************************
 * 3) FUNCIÓN PARA REDONDEO ESPECIAL
 *    - 0-25   => termina en .95 (resta 1 al "cien" si es posible)
 *    - 26-69  => termina en .50
 *    - 70-99  => termina en .95
 ***************************************************/
function redondeoEspecial($valor) {
    // 1) Convertimos el float a entero
    //    Puedes usar round() o floor() según tu regla:
    //    - round() redondea .5 hacia arriba
    //    - floor() siempre va hacia abajo
    $valorEntero = (int) round($valor);

    // 2) Sacamos las "centenas" y el "residuo"
    $centenas = (int) floor($valorEntero / 100);
    $residuo  = $valorEntero % 100;

    // 3) Aplicamos la lógica
    if ($residuo >= 0 && $residuo <= 25) {
        // 0–25 => termina en .95 (bajamos 1 centena si es posible)
        if ($centenas > 0) {
            $centenas--;
        }
        return ($centenas * 100) + 95;
    } elseif ($residuo >= 26 && $residuo <= 69) {
        // 26–69 => termina en .50
        return ($centenas * 100) + 50;
    } else {
        // 70–99 => termina en .95
        return ($centenas * 100) + 95;
    }
}

/***************************************************
 * 4) PREPARAMOS EL SELECT PARA LEER LA TABLA "llanta"
 ***************************************************/
$sqlSelect = "SELECT id, Descripcion, precio_Inicial FROM llantas";
$stmtSelect = $con->prepare($sqlSelect);
$stmtSelect->execute();
$result = $stmtSelect->get_result();

if ($result && $result->num_rows > 0) {
    // Procesamos todos los registros

    print_r("
    <table style='border:1px solid gray;'>
  <tr style='border:1px solid gray;'>
    <th>Descripción</th>
    <th>Costo</th>
    <th>Precio lista</th>
    <th>Precio desc.</th>
    <th>Precio mayoreo.</th>
  </tr>
  
    ");

   
    while ($row = $result->fetch_assoc()) {
        $id    = $row['id'];
        $costo = floatval($row['precio_Inicial']);
        $descripcion = $row['Descripcion'];
        // Buscamos qué rango le corresponde
        $sqlUpdate = "
        UPDATE llantas
        SET 
            precio_lista   = ?,
            precio_Venta   = ?,
            precio_Mayoreo = ?
        WHERE id = ?
    ";
    $stmtUpdate = $con->prepare($sqlUpdate);
        foreach ($rangos as $r) {
            list($min, $max, $utilLista, $utilVenta, $utilMayoreo) = $r;
            
            if ($costo >= $min && $costo <= $max) {
                // Calculamos los nuevos precios (antes del redondeo)
                $nuevoLista   = $costo + $utilLista;
                $nuevoVenta   = $costo + $utilVenta;
                $nuevoMayoreo = $costo + $utilMayoreo;

                // Aplicamos el redondeo especial
                $nuevoLista   = redondeoEspecial($nuevoLista);
                $nuevoVenta   = redondeoEspecial($nuevoVenta);
                $nuevoMayoreo = redondeoEspecial($nuevoMayoreo);
                
                // Vinculamos parámetros y ejecutamos el UPDATE
                $stmtUpdate->bind_param("dddi", $nuevoLista, $nuevoVenta, $nuevoMayoreo, $id);
                $stmtUpdate->execute();
  
                // Ya encontramos el rango para este costo, rompemos el ciclo
                break;
            }
        }

        print_r("
            <tr style='border:1px solid gray;'>
                <td>".$descripcion."</td>
                <td>".$costo."</td>
                <td>".$nuevoLista."</td>
                <td>".$nuevoVenta."</td>
                <td>".$nuevoMayoreo."</td>
            </tr>
            ");
             // Cerramos el statement de UPDATE
             $stmtUpdate->close();
            }
      

    print_r("
</table>
    ");

}
?>