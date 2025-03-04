<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}

require_once('../movimientos/mover_clientes.php');

function obtenerNombreCampo($key)
{
    // Mapea los nombres de campo según tus necesidades
    switch ($key) {
        case 'Nombre_Cliente':
            return 'Nombre';
        case 'Telefono':
            return 'Teléfono';
        case 'Direccion':
            return 'Dirección';
            // Agrega los demás casos según tus campos
        default:
            return $key;
    }
}

if($_POST){

    $id = $_POST["id"];
    $nombre    = $_POST["nombre"];  
    $credito   = $_POST["credito"];   
    $telefono  = $_POST["telefono"];   
    $correo    = $_POST["correo"];   
    $rfc       = $_POST["rfc"];   
    $direccion = $_POST["direccion"];   
    $latitud   = $_POST["latitud"];  
    $longitud  = $_POST["longitud"];
    $asesor    = $_POST["asesor"];
    $tipo_cliente = $_POST["tipo_cliente"];

    $select = "SELECT * FROM clientes WHERE id = ?";
    $ress = $con->prepare($select);
    $ress->bind_param('i', $id);
    $ress->execute();
    $resultado = $ress->get_result();
    $cambios = '';

    $mapaIndices = array(
        'id'=> 'id',
        'Nombre_Cliente' => 'nombre',
        'Telefono' => 'telefono',
        'Direccion' => 'direccion',
        'Correo' => 'correo',
        'Credito' => 'credito',
        'RFC' => 'rfc',
        'Latitud' => 'latitud',
        'Longitud' => 'longitud',
        'id_asesor' => 'asesor'
        // Agrega los demás mapeos según tus necesidades
    );

    while ($row = $resultado->fetch_assoc()) {
        $plantilla = "Se realiza actualización de: \n";
            foreach ($row as $key => $value) {
                
                if (isset($mapaIndices[$key]) && $_POST[$mapaIndices[$key]] != $value) {
                    // Obtén los nombres de campo y valor antes y después del cambio
                    $campo = obtenerNombreCampo($key);
                    $valorAnterior = $value;
                    $valorNuevo = $_POST[$mapaIndices[$key]];
        
                    // Agrega el cambio a la variable $cambios
                    if($key == 'id_asesor'){
                        $asesor_anterior = traerAsesor($valorAnterior,$con);
                        $asesor_nuevo = traerAsesor($valorNuevo,$con);
                        $cambios .= "- $campo: Se modificó '$asesor_anterior' por '$asesor_nuevo'\n";

                    }else{
                        $cambios .= "- $campo: Se modificó '$valorAnterior' por '$valorNuevo'\n";
                    }
                }
            }
    }
    
    if($cambios != ''){
        InsertarMovimiento("actualizacion", $cambios, $con);
    }
   

    
    $update_cliente = "UPDATE clientes SET Nombre_Cliente = ?, Telefono = ?, Direccion = ?, Correo = ?, Credito = ?, RFC = ?, Latitud = ?, Longitud = ?, tipo_cliente =?, id_asesor = ? WHERE id = ?";
    $resultado = $con->prepare($update_cliente);
    $resultado->bind_param('ssssssssssi', $nombre, $telefono, $direccion, $correo, $credito, $rfc, $latitud, $longitud, $tipo_cliente, $asesor, $id);
    $resultado->execute();
    
    if ($resultado) {
       
        print_r(1);
    }else {
        print_r(0);
    }
   
}


?>
