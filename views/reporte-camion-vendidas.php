<div class="row mt-3">
    <div class="col-12">
        <h4 class="mb-3 mt-3">Conteo de llantas vendidas este año <?= date('Y')?></h4>
        <table class="table table-bordered">

        <thead class="text-white text-center">
            <tr class="bg-dark">
                <th rowspan="2">#</th>
                <th rowspan="2">Sucursal</th>
                <th rowspan="2">Mes</th>

                <th colspan="3">Auto</th>
                
                <th colspan="3">Camion</th>
                
                <th rowspan="2">Total</th>
            </tr>
            
            <tr style="background-color:#858796; color:white; border: 1px solid black !important;">
                <th>Contado</th>
                <th>Credito</th>
                <th>Total auto</th>

                <th>Contado</th>
                <th>Credito</th>
                <th>Total Camion</th> 
                
                </tr>
        </thead>

            <tbody style="font-weight: lighter !important;">

            <?php
    $meses = ['no borrar','Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $year = date('Y');
    $mes_actual = date('n'); // <-- mes actual (1 a 12)

    $consultar = "CALL llantas_vendidas_por_mes(?)";
    $resp = $con->prepare($consultar);
    $resp->bind_param('s',$year);
    $resp->execute();
    $resultado = $resp->get_result();
    $cont = 0;

    if($resultado->num_rows > 0){
        while ($data = $resultado->fetch_assoc()) {
            $cont++;

            $total_auto = intval($data['auto_contado']) + intval($data['auto_credito']);
            $total_camion = intval($data['camion_contado']) + intval($data['camion_credito']);
            $total_pz = $total_auto + $total_camion;

            // ✅ Si el mes coincide con el actual, aplica color de fondo
            $color_fila = ($data['mes'] == $mes_actual) ? 'style="background-color: #ffeeba;"' : ''; 
            // Puedes cambiar el color (#ffeeba es amarillo claro)

            echo '
                <tr '.$color_fila.'>
                    <th>'.$cont.'</th>
                    <th>'.$data['nombre'].'</th>
                    <th>'.$meses[$data['mes']].'</th>
                    <th>'.$data['auto_contado'].'</th>
                    <th>'.$data['auto_credito'].'</th>
                    <th>'.$total_auto.'</th>
                    <th>'.$data['camion_contado'].'</th>
                    <th>'.$data['camion_credito'].'</th>
                    <th>'.$total_camion.'</th>
                    <th>'.$total_pz.'</th>
                </tr>';
        }
        $resp->close();
    } else {
        echo '
            <tr style="background-color:white">
                <th colspan="10" class="text-center p-3">Sin datos</th>
            </tr>';
    }
?>


            </tbody>

        </table>
    </div>
</div>