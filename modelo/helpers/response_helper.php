<?php
function responder($status, $message, $tipo = 'danger', $data = null, $exit = true, $echo = true) {
    header('Content-Type: application/json');
    if($echo){
        echo json_encode([
            'estatus' => $status,
            'mensaje' => $message,
            'tipo' => $tipo, // Añadir el tipo de mensaje ('warning', 'danger', etc.)
            'data' => $data
        ]);
    }else{
        return json_encode([
            'estatus' => $status,
            'mensaje' => $message,
            'tipo' => $tipo, // Añadir el tipo de mensaje ('warning', 'danger', etc.)
            'data' => $data
        ]);
    }
    
    if($exit==true){
        exit;
    }
}
?>
