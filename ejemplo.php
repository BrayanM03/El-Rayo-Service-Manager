
    <?php
    
    include 'modelo/conexion.php';
    $con= $conectando->conexion(); 

    if ($con) {
        echo "Todo bien";
    }else{
        echo "todo mal";
    }
    
    ?>
