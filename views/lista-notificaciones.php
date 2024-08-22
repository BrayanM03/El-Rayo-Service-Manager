<div class="card mb-3">
    <div class="list-group">
        <span href="#" class="list-group-item">
            <b>Lista de notificaciones</b>
        </span>
        <?php
        // Variables de paginación
        $notificaciones_por_pagina = 10; // Número de notificaciones por página
        $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $offset = ($pagina_actual - 1) * $notificaciones_por_pagina;

        $select_count = 'SELECT COUNT(*) FROM notificaciones_usuarios WHERE id_usuario = ?';
        $stmt = $con->prepare($select_count);
        $stmt->bind_param('s', $_SESSION['id_usuario']);
        $stmt->execute();
        $stmt->bind_result($notifications_count);
        $stmt->fetch();
        $stmt->close();

        $select_count = 'SELECT COUNT(*) FROM notificaciones_usuarios WHERE id_usuario = ? AND estatus_vista =0';
        $stmt = $con->prepare($select_count);
        $stmt->bind_param('s', $_SESSION['id_usuario']);
        $stmt->execute();
        $stmt->bind_result($notifications_vista_count);
        $stmt->fetch();
        $stmt->close();

        if ($notifications_count == 0) {
        ?>
            <a href="" class="list-group-item list-group-item-action">Sin notificaciones</a>

            <?php } else {
            $select_count = 'SELECT n.*, nu.estatus_abierta, nu.id as nu_id FROM notificaciones_usuarios nu INNER JOIN notificaciones n
                             ON nu.id_notificacion = n.id WHERE nu.id_usuario = ? ORDER BY n.id DESC LIMIT ?,?';
            $stmt = $con->prepare($select_count);
            $stmt->bind_param('sii', $_SESSION['id_usuario'], $offset, $notificaciones_por_pagina);
            $stmt->execute();
            $response = Arreglo_Get_Result($stmt);
            $stmt->close();

            foreach ($response as $key => $value) {
                // Convertir la fecha a un objeto DateTime
                $fecha_notificacion = $value['fecha'];
                $dateTime = new DateTime($fecha_notificacion);
                $id_notificacion = $value['id'];
                $nu_id = $value['nu_id'];
                $class_estatus_abierta = $value['estatus_abierta'] == 0 ? 'font-weight-bold' : '';
                // Formatear la fecha
                $fechaFormateada = $dateTime->format('l j \d\e F Y');
                $fechaFormateada = str_replace(array_keys($diaSemana), array_values($diaSemana), $fechaFormateada);
                $fechaFormateada = str_replace(array_keys($meses), array_values($meses), $fechaFormateada);

            ?>
                <a class="list-group-item list-group-item-action d-flex align-items-center" href="./modelo/configuraciones/configuracion_notificaciones/abrir_notificacion.php?visto=1&abierto=1&id_nu=<?= $nu_id; ?>&id_notificacion=<?= $id_notificacion ?>">
                    <div class="mr-3">
                        <div class="icon-circle bg-danger">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                    </div>
                    <div>
                        <div class="small text-gray-500"><?= $fechaFormateada . ', ' . $value['hora']; ?></div>
                        <span class="<?= $class_estatus_abierta; ?>"><?= $value['mensaje']; ?></span>
                        <?php if ($value['estatus_abierta'] == 0) { ?>
                            <span class="badge badge-lg badge-warning">Nuevo</span>
                        <?php }; ?>
                    </div>
                </a>
            <?php
            }

            if($notifications_count>0){
// Calcular el número total de páginas
$total_paginas = ceil($notifications_count / $notificaciones_por_pagina);

            }
            
            ?>

        <?php } ?>

    </div>
</div>

<?php       if($notifications_count>0){?>
<nav aria-label="Page navigation ml-auto border">
    <ul class="pagination">
        <?php if ($pagina_actual > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?pagina=<?= $pagina_actual - 1; ?>" aria-label="Anterior">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <li class="page-item <?= $i == $pagina_actual ? 'active' : ''; ?>">
                <a class="page-link" href="?pagina=<?= $i; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($pagina_actual < $total_paginas): ?>
            <li class="page-item">
                <a class="page-link" href="?pagina=<?= $pagina_actual + 1; ?>" aria-label="Siguiente">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
<?php }?>