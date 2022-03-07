<?php
session_start();
include '../conexion.php';
$con= $conectando->conexion(); 

if (!isset($_SESSION['id_usuario'])) {
    header("Location:../../login.php");
}



    $numero_fotos_subidas =  count($_FILES);
    $marca = $_POST["marca"];
    //Validamos si se mandaron fotos o no
    if ($numero_fotos_subidas == 0) {
       // print_r("Data:" . $_POST["año"] . " " . $_POST["marca"] . " " . $_POST["modelo"] . " ");
       //$marca_sin_espacios = preg_replace('/\s+/', '_', $marca);
        
       $nombre = "help";
        $query = "INSERT INTO marcas(id, Nombre, Imagen) VALUES (null,?,?)";
        $resultado = $con->prepare($query);
        $resultado->bind_param('ss', $marca, $nombre);
        $resultado->execute();
        $resultado->close();

        print_r(json_encode(  
            [
                'message' => 'Se subieron 0 fotos con exito. :)',
                'status' => http_response_code(200),
                'type' => 0
                //$data
            ]));

    }else{

       
        $marca_sin_espacios = preg_replace('/\s+/', '_', $marca);
        $query = "INSERT INTO marcas(id, Nombre, Imagen) VALUES (null,?,?)";
        $resultado = $con->prepare($query);
        $resultado->bind_param('ss', $marca, $marca_sin_espacios);
        $resultado->execute();
        $resultado->close();

     $count = 0; //Creamos un contador para contar las subidas
     $data = (!file_exists('database.json') ? [] : json_decode( file_get_contents('database.json'))); //verificamos si existe nuestro JSON donde se almacenara la info de la update
 
     $writable = fopen('database.json', 'w'); //Lo creamos
 
 
     //Aqui recorremos la variable global FILES done verificamos si se movio el archivo a la carpeta unica del cliente
     foreach($_FILES as $key => $file){
 
         if (!move_uploaded_file($file['tmp_name'], '../../src/img/logos/'. $file['name'])) {
             
             return print_r( json_encode(['message' => 'No fue posible subir los archivos', 'status' => http_response_code(500)] ));
 
         }else{

            rename('../../src/img/logos/'. $file['name'], '../../src/img/logos/'. $marca_sin_espacios .'.jpg');

         }
 
         //Agregamos el id y el nombre del archivo img al arreglo $data
        /*  array_push($data, [
             'id'=>$key,
             'file_name' => $marca_sin_espacios
         ]); */
 
         $count++; //Incrementamos el contador

     }
 
     //Vericiamos que el contador sea igual a la longitud de FILES
     if( $count == count($_FILES)){
         fwrite($writable, json_encode($data));
         fclose($writable);
 
         //Retornamos el JSON con la informacion de la subida
         return print_r(json_encode(  
             [
                 'message' => 'Se subieron ' . $count . ' fotos con exito. :)',
                 'status' => http_response_code(200),
                 'type' => 1
                 //$data
             ]
         ));
     }

    }
 
    

    ?>