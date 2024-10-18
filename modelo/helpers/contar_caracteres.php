<?php

// Función para extraer las medidas de la descripción de la llanta

include '../conexion.php';
$con = $conectando->conexion(); 

$select = 'SELECT id, mercancia FROM movimientos WHERE LENGTH(mercancia) > 280';
$stmt = $con->prepare($select);
$stmt->execute();
$ree = $stmt->get_result();
$stmt->free_result();
$stmt->close();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registros con mercancia larga</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mx-auto p-4">
        <h2 class="text-2xl font-bold mb-4">Registros con mercancia larga</h2>
        <table class="table-auto w-full">
            <thead>
                <tr>
                    <th class="px-4 py-2">Folio</th>
                    <th class="px-4 py-2">Mercancia</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ree as $row) { ?>
                <tr>
                    <td class="border px-4 py-2"><?= $row['id'] ?></td>
                    <td class="border px-4 py-2"><?= $row['mercancia'] ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>