<?php

session_start();
include '../conexion.php';
$con= $conectando->conexion(); 
$id_usuario = intval($_SESSION['id_usuario']); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}


if (isset($_POST)) {
    
    $page = $_POST['page'] ?? 1; // Número de página actual (1 si no se especifica)

    // Parámetros de paginación
    $resultadosPorPagina = 10;

    // Calcular el offset
    $offset = ($page - 1) * $resultadosPorPagina;

   if(isset($_POST["searchTerm"])){
    $term = $_POST["searchTerm"];
    $termX = '%'.$term .'%';
    $query= "SELECT COUNT(*) FROM clientes WHERE Nombre_Cliente LIKE ?"; // WHERE id_asesor = ? OR id_autorizado = ?
    $resultado = $con->prepare($query);
    $resultado->bind_param('s', $termX);
    $resultado->execute();
    $resultado->bind_result($total_clientes);
    $resultado->fetch();
    $resultado->close();

   
    if($total_clientes > 0){
        $query="SELECT cl.*  FROM clientes cl WHERE cl.Nombre_Cliente LIKE '%$term%' LIMIT $resultadosPorPagina OFFSET $offset"; // WHERE id_asesor = ? OR id_autorizado = ?

        $resultado = $con->query($query);
    
        while($fila = $resultado->fetch_assoc()){
        $id= $fila["id"];
        $nombre = $fila["Nombre_Cliente"];
        $telefono = $fila["Telefono"];
        $direccion = $fila["Direccion"];
        $correo = $fila["Correo"];
        $credito = $fila["Credito"];
        $credito_vencido = $fila["credito_vencido"];
        $rfc = $fila["RFC"];
      
    
        $data[] = array("id" => $id, "nombre"=>$nombre, "telefono" => $telefono,
                        "direccion" => $direccion, "correo"=>$correo, "credito"=>$credito,   "rfc"=>$rfc, 'credito_vencido'=>$credito_vencido);
    }

    $response = array(
        'results' => $data, // Array de resultados obtenidos de la consulta SQL
        'post'=> $_POST,
        'total_count'=>$total_clientes,
        'pagination' => array(
          'page'=> $page,
          'offset'=> $offset,
          'paginas_per_page'=> $resultadosPorPagina,
          'more' => count($data) == $resultadosPorPagina // Verificar si hay más resultados disponibles
        ));

    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}else{
    echo "Nada";
};

   }else{

    $query= "SELECT COUNT(*) FROM clientes";
    $resultado = $con->prepare($query);
    //$resultado->bind_param('ii', $id_usuario, $id_usuario);
    $resultado->execute();
    $resultado->bind_result($total_clientes);
    $resultado->fetch();
    $resultado->close();
    if($total_clientes > 0){

        $querySuc = "SELECT * FROM clientes cl LIMIT $resultadosPorPagina OFFSET $offset"; // WHERE id_asesor = ?  OR id_autorizado = ?
        $respon = $con->query($querySuc);
    
        while ($fila = $respon->fetch_assoc()) {
            $id= $fila["id"];
            $nombre = $fila["Nombre_Cliente"];
            $telefono = $fila["Telefono"];
            $direccion = $fila["Direccion"];
            $correo = $fila["Correo"];
            $credito = $fila["Credito"];
            $credito_vencido = $fila["credito_vencido"];
            $rfc = $fila["RFC"];
            $data[] = array("id" => $id, "nombre"=>$nombre, "telefono" => $telefono,
                    "direccion" => $direccion, "correo"=>$correo, "credito"=>$credito,   "rfc"=>$rfc,
                'credito_vencido' => $credito_vencido);
        }
        $response = array(
            'results' => $data, // Array de resultados obtenidos de la consulta SQL
            'post'=> $_POST,
            'total_count'=>$total_clientes,
            'pagination' => array(
              'page'=> $page,
              'offset'=> $offset,
              'paginas_per_page'=> $resultadosPorPagina,
              'more' => count($data) == $resultadosPorPagina // Verificar si hay más resultados disponibles
            ));
    
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }else{
        echo "Nada";
    };
    

   /*  while($fila = $resultado->fetch_assoc()){
    $id= $fila["id"];
    $nombre = $fila["Nombre_Cliente"];
    $telefono = $fila["Telefono"];
    $direccion = $fila["Direccion"];
    $correo = $fila["Correo"];
    $credito = $fila["Credito"];
    $rfc = $fila["RFC"];
  

    $data[] = array("id" => $id, "nombre"=>$nombre, "telefono" => $telefono,
                    "direccion" => $direccion, "correo"=>$correo, "credito"=>$credito,   "rfc"=>$rfc);

                  
} */

//echo json_encode($data, JSON_UNESCAPED_UNICODE); 
   }
    
 

}else{
    print_r("No se pudo establecer una conexión");
}


?>