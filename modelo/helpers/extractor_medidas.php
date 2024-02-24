<?php

// Función para extraer las medidas de la descripción de la llanta

include '../conexion.php';
$con= $conectando->conexion(); 
function extraerMedidas($descripcion) {
    // Dividir la descripción en partes usando el espacio como delimitador
    $partes = explode(" ", $descripcion);
    
    // Inicializar variables para ancho y alto
    $ancho = null;
    $alto = null;
    $rin = null;
    
    // Iterar sobre las partes para encontrar las medidas
    foreach ($partes as $parte) {
      
        // Verificar si la parte tiene el formato de una medida (número/númeroRnúmero)
        
        if (preg_match("/(\d+)\/(\d+)R(\d+)/", $parte, $matches)) {
            // Encontramos un patrón de medida
            $ancho = $matches[1];
            $alto = $matches[2];
            $rin = $matches[3];
            break; // Terminamos el bucle una vez que encontramos las medidas
        }/* elseif (preg_match('/(\d+-\d+-\d+)/', $parte, $matches)) {
            // Verifica si hay coincidencia con el patrón para el formato "CAMARA"
            print_r($matches);
            $especificacion = $matches[1];
            
            $partes = explode('-', $especificacion);
            $ancho = $partes[0];
            $altura = $partes[1];
            $diametro = $partes[2];
            break;
        }elseif (preg_match('/LLANTA (\d+x\d+\.\d+R\d+)/', $parte, $matches)) {
            // Verifica si hay coincidencia con el patrón para el formato alternativo
            $especificacion = $matches[1];
            
            $partes = explode('x', $especificacion);
            $ancho = $partes[0];
            $diametro = substr($partes[1], 0, 2); // Tomamos los primeros dos dígitos
            
            break;
        } else{
           
        } */
    }
    
    return array($ancho, $alto, $rin);
}

// Ejemplo de descripciones de llantas
$descripciones = [
    "LLANTA 245/35R20 ROYAL PERFORMANCE 95W",
    "CAMARA 16-9-26 HARVESTKING",
    "LLANTA 205/65R15 ULTIMAPRO UP1 95H",
    "LLANTA 33X12.50R22 RUGGED CONTENDER 109Q"
];

$select = "SELECT * FROM llantas";
$stmt = $con->prepare($select);
$stmt->execute();
$ree = $stmt->get_result();
$stmt->free_result();
$stmt->close();
$llantas_con_error=[];
// Iterar sobre las descripciones
foreach ($ree as $row) {
    $descripcion = $row['Descripcion'];
    list($ancho, $alto, $rin) = extraerMedidas($descripcion);
    $ancho_query = $row['Ancho'];
    $alto_query = $row['Proporcion'];
    $rin_query = $row['Diametro'];
    $codigo = $row['id'];
    // Verificar si las medidas son consistentes
    if ($ancho != null && $alto != null && $rin != null) {
        if($ancho_query!= $ancho || $alto_query!= $alto || $rin_query != $rin) {
            $llantas_con_error[] ="Ancho: $ancho_query, Alto: $alto_query, Rin: $rin_query  $descripcion || ------ CODIGO: $codigo";
        }
    } else {
        //echo "No se pudieron extraer las medidas para la descripción: $descripcion\n";
    }
}
echo json_encode($llantas_con_error);
?>
