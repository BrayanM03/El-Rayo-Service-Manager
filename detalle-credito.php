
    <?php
    session_start();

    include 'modelo/conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (!isset($_SESSION['id_usuario'])) {
        header("Location:login.php");
    }
    $rol = $_SESSION["rol"];
    if ($rol ==3) {
        header("Location:nueva-venta.php");
    }

    
    ?>
<!DOCTYPE html>
<html lang="es">

<head> 

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="src/img/rayo.svg" />
 
	

    <title>Credito venta </title>

    <!-- Custom fonts for this template-->
    <link rel="stylesheet" href="src/css/inventario.css">
    <link rel="stylesheet" href="src/css/historial-ventas.css">
    <link rel="stylesheet" href="src/css/creditos.css">

    <link href="src/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
        
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.css' rel='stylesheet' />

        


    <!-- Custom styles for this template-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="src/vendor/datatables/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" integrity="sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==" crossorigin="anonymous" />
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="src/vendor/bower_components/select2-bootstrap-theme/dist/select2-bootstrap.css">
  

    <!---Librerias de estilos-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="src/css/menu-vertical.css" rel="stylesheet">
    <link href="src/css/detalle-credito.css" rel="stylesheet">
   

</head>

<body id="page-top"> 

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <?php 
            require_once 'sidebar.php'
        ?>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include 'views/navbar.php'; ?>

                <!-- End of Topbar --> 


                <!-- Begin Page Content -->
                <div class="container-fluid" style="display: flex; justify-content: center; align-items:center; flex-direction:column">

                     <!-- Contenido inventario -->
                     
                     <div class="row">
                            <div class="col-md-12">
                                <h3><b>Detalle de credito</b></h3>
                            </div>
                        </div>
                        <hr>  
                     <!--  <table id="creditos" class="table table-striped">  </table> --> 

                        <?php
                        $id_credito = $_GET['id_credito'];
                            $count = "SELECT COUNT(*) FROM creditos c INNER JOIN ventas v ON v.id = c.id_venta WHERE c.id = ?";
                            $stmt = $con->prepare($count);
                            $stmt->bind_param('s', $id_credito);
                            $stmt->execute();
                            $stmt->bind_result($count_creditos);
                            $stmt->fetch();
                            $stmt->close();

                            if($count_creditos>0){
                                $sel = "SELECT v.*, cl.Nombre_Cliente as cliente, 
                                c.fecha_inicio, c.fecha_final, c.pagado, c.restante, c.total, c.plazo, c.estatus as estatus_credito FROM creditos c INNER JOIN ventas v ON v.id = c.id_venta
                                INNER JOIN clientes cl ON cl.id = v.id_Cliente
                                WHERE c.id = ?";
                                $stmt = $con->prepare($sel);
                                $stmt->bind_param('s', $id_credito);
                                $stmt->execute();
                                $res = Arreglo_Get_Result($stmt);
                                $stmt->fetch();
                                $stmt->close(); 

                                foreach ($res as $key => $value) {
                                    $id_venta = $value['id'];
                                    $cliente = $value['cliente'];
                                    $dateTime_fecha_inicio = new DateTime($value['fecha_inicio']);
                                    $dateTime_fecha_final = new DateTime($value['fecha_final']);

                                    $fechaFormateada_inicio = $dateTime_fecha_inicio->format('l j \d\e F Y');
                                    $fechaFormateada_final = $dateTime_fecha_final->format('l j \d\e F Y');
                                    $fechaFormateada_inicio = str_replace(array_keys($diaSemana), array_values($diaSemana), $fechaFormateada_inicio);
                                    $fechaFormateada_final = str_replace(array_keys($diaSemana), array_values($diaSemana), $fechaFormateada_final);
                                    $fechaFormateada_inicio = str_replace(array_keys($meses), array_values($meses), $fechaFormateada_inicio);
                                    $fechaFormateada_final = str_replace(array_keys($meses), array_values($meses), $fechaFormateada_final);
            
                                    //plazo 
                                    switch ($value['plazo']) {
                                        case '1':
                                        $plazo = '1 semana';
                                        break;
                                        
                                        case '2':
                                        $plazo = '15 dias';
                                        break;
                                        
                                        case '3':
                                        $plazo = '1 mes';
                                        break;
                            
                                        case '4':
                                        $plazo = '1 año';
                                        break;
                            
                                        case '5':
                                        $plazo = 'Sin definir';
                                        break;
                            
                                        case '6':
                                            $plazo = '1 día';
                                        break;
                                    
                                    default:
                                        $plazo = 'No definido';
                                        break;
                                }

                                //Estatus
                                switch ($value['estatus_credito']) {
                                    case "0":
                                      $estatus_credito= '<h3><span class="badge badge-primary">Sin abono</span></h3>';
                                      break;
                        
                                    case "1":
                                      $estatus_credito ='<h3><span class="badge badge-info">Primer abono</span></h3>';
                                      break;
                                    case "2":
                                      $estatus_credito = '<h3><span class="badge badge-warning">Pagando</span></h3>';
                                      break;
                                    case "3":
                                        $estatus_credito = '<h3><span class="badge badge-success">Finalizado</span></h3>';
                                      break;
                                    case "4":
                                        $estatus_credito ='<h3><span class="badge badge-lg badge-danger">Vencido</span></h3>';
                                      break;
                                    case "5":
                                        $estatus_credito = '<h3><span class="badge badge-dark">Cancelada</span></h3>';
                                      break;
                                    default:
                                    $estatus_credito = 'Sin definir';
                                      break;
                                  }
                                //Formatear montos
                                    $abonado = number_format($value['pagado'], 2, '.', ',');
                                    $restante = number_format($value['restante'], 2, '.', ',');
                                    $total = number_format($value['total'], 2, '.', ',');
                                }
                            }
                            
                        ?>
                        <div class="contenedor-detalle-credito">
                            <div class="row">
                               <div class="col-12 col-md-3">
                                   <div class="form-group">
                                       <label  for="">Folio venta:</label>
                                       <input class="form-control" value="<?php echo $id_venta; ?>" type="text" id="cliente">
                                   </div>
                               </div>
                               <div class="col-12 col-md-9">
                                   <div class="form-group">
                                       <label  for="">Cliente:</label>
                                       <input class="form-control" value="<?= $cliente; ?>" type="text" id="cliente">
                                   </div>
                               </div>
                               <div class="col-6 col-md-3">
                                   <div class="form-group">
                                       <label  for="">Fecha inicio</label>
                                       <input class="form-control" value="<?= $fechaFormateada_inicio; ?>" type="text" id="cliente">
                                   </div>
                               </div>
                               <div class="col-6 col-md-3">
                                   <div class="form-group">
                                       <label  for="">Fecha vencimiento</label>
                                       <input class="form-control bg-white" value="<?= $fechaFormateada_final; ?>" disabled type="text" id="cliente">
                                   </div>
                               </div>
                               <div class="col-6 col-md-3">
                                   <div class="form-group">
                                       <label  for="">Plazo</label>
                                       <input class="form-control bg-white" disabled  value="<?= $plazo; ?>" type="text" id="cliente">
                                   </div>
                               </div>
                               <div class="col-6 col-md-3">
                                   <div class="form-group">
                                       <label  for="">Estatus</label><br>
                                       <?= $estatus_credito; ?>
                                      <!--  <input class="form-control"  value="<?= $estatus_credito; ?>" type="text" id="cliente">
                                    --></div>
                               </div>
                               <div class="col-6 col-md-3">
                                   <div class="form-group">
                                       <label  for="">Abonado</label>
                                       <input class="form-control bg-white" disabled value="<?= $abonado; ?>" type="text" id="cliente">
                                   </div>
                               </div>
                               <div class="col-6 col-md-3">
                                   <div class="form-group">
                                       <label  for="">Restante</label>
                                       <input class="form-control bg-white" disabled type="text"  value="<?= $restante; ?>" id="cliente">
                                   </div>
                               </div>
                               <div class="col-6 col-md-3">
                                   <div class="form-group">
                                       <label  for="">Monto total</label>
                                       <input class="form-control"  value="<?= $total; ?>" type="text" id="cliente">
                                   </div>
                               </div>
                               
                            </div>

                            <div class="row p-3">
                                <div class="col-12">
                                    <label for="">Mercancia:</label>
                                </div>
                                <div class="col-12">
                                    <table class="my-5 table table-primary table-bordered table-hover">
                                        <thead>
                                            <th>ID</th>
                                            <th>Descripcion</th>
                                            <th>Marca</th>
                                            <th>Cantidad</th>
                                            <th>P. unit</th>
                                            <th>Importe</th>
                                        </thead>
                                        <tbody>
                                        <?php
                                        if($count_creditos >0){
                                            $count = "SELECT COUNT(*) FROM detalle_venta WHERE id_Venta = ?";
                                            $stmt = $con->prepare($count);
                                            $stmt->bind_param('s', $id_venta);
                                            $stmt->execute();
                                            $stmt->bind_result($count_detalles);
                                            $stmt->fetch();
                                            $stmt->close(); 

                                            if($count_detalles>0){
                                                $count = "SELECT l.*, dv.Cantidad, dv.precio_Unitario, dv.Importe FROM detalle_venta dv INNER JOIN llantas l ON l.id = dv.id_Llanta WHERE dv.id_Venta = ?";
                                                $stmt = $con->prepare($count);
                                                $stmt->bind_param('s', $id_venta);
                                                $stmt->execute();
                                                $resu = Arreglo_Get_Result($stmt);
                                                $stmt->fetch();
                                                $stmt->close();

                                                foreach ($resu as $key => $value) {
                                                    $id_llanta = $value['id'];
                                                    $descripcion = $value['Descripcion'];
                                                    $marca = $value['Marca'];
                                                    $cantidad = $value['Cantidad'];
                                                    $precio_unitario =number_format($value['precio_Unitario'], 2, '.', ',');
                                                    $importe =number_format($value['Importe'], 2, '.', ',');

                                                    echo '<tr class="bg-white">
                                                            <td>'.$id_llanta.'</td>
                                                            <td>'.$descripcion.'</td>
                                                            <td>'.$marca.'</td>
                                                            <td>'.$cantidad.'</td>
                                                            <td>'.$precio_unitario.'</td>
                                                            <td>'.$importe.'</td>
                                                         </tr>';
                                                }
                                            }
                                        }
                                        ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>                    
                </div>
            <!-- End of Main Content -->
  <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; El Rayo Service Manager <?php print_r(date("Y")) ?></span><br><br>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">¿Listo para salir?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Seleccione "salir" para cerra su sesión actual.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                    <a class="btn btn-primary" href="./modelo/login/cerrar-sesion.php">Salir</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="src/vendor/jquery/jquery.min.js"></script>
    <script src="src/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="src/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="src/js/sb-admin-2.min.js?v=1239"></script>

    <!-- Page level plugins -->
    <script src="src/vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts 
    <script src="src/js/demo/chart-area-demo.js"></script>
    <script src="src/js/demo/chart-pie-demo.js"></script>-->


    <!-- Cargamos nuestras librerias-->

    
   <!-- Cargamos nuestras librerias-->
    
   <script src="src/vendor/datatables/jquery.dataTables.min.js"></script> 
    <script src="src/vendor/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="src/vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" integrity="sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="src/vendor/datatables/defaults.js"></script>
    <script src="src/vendor/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script src="src/js/creditos.js"></script>
    
   
</body>

</html>