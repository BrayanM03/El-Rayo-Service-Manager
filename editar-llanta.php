<?php
    session_start();

    date_default_timezone_set("America/Matamoros");
    include 'modelo/conexion.php';
    $con= $conectando->conexion(); 

    if (!$con) {
        echo "maaaaal";
    }

    if (!isset($_SESSION['id_usuario'])) {
        header("Location:login.php");
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

    <title>Edición avanzada</title>

    <!-- Custom fonts for this template-->
 
    <link rel="stylesheet" href="src/css/nueva-venta.css">
    <link href="src/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        <link href='https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.css' rel='stylesheet' />

    <!-- Custom styles for this template-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <!-- <link href="src/vendor/node_modules/@splidejs/splide/dist/css/splide.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="src/vendor/datatables/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="src/vendor/datatables-responsive/css/responsive.bootstrap4.min.css">
   
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="src/vendor/bower_components/select2-bootstrap-theme/dist/select2-bootstrap.css">

    <!---Librerias de estilos-->
    <link href="src/css/sb-admin-2.min.css" rel="stylesheet">
    <link href="src/css/menu-vertical.css" rel="stylesheet">
    <link href="src/css/slide_notificaciones.css" rel="stylesheet">
    <link href="src/css/bootstrap-select.min.css" rel="stylesheet">
    <link href="src/css/form-field.css" rel="stylesheet">
     
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.css" integrity="sha512-oe8OpYjBaDWPt2VmSFR+qYOdnTjeV9QPLJUeqZyprDEQvQLJ9C5PCFclxwNuvb/GQgQngdCXzKSFltuHD3eCxA==" crossorigin="anonymous" />
<style>
   /*  .card,.grupo-2, #content, .sticky-footer{
        background-color: rgb(21, 20, 27);
    } */
    /* .selectpicker{
        background-color: white !important; 
      
    } */
    /* .bootstrap-select{
       // border: 1px solid #ccc !important;
        //max-width: 350px !important;
   
    } */
    .btn-light{
            height: 43px !important;
            border-radius: 6px !important;
            border: 1px solid #CDD9ED !important;
            background: #fff !important;
            width: 100% !important;
            color: #99A3BA !important;
            padding: 8px 16px !important;
            line-height: 25px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            font-family: inherit !important;
            transition: border 0.3s ease !important;
      
        }

        .btn-light:focus{
            outline: none !important;
        border-color: #4570f0 !important;
        }

        .img_llanta:hover{
          cursor: pointer;
          border: 1px solid gray;
          box-shadow: rgba(0,0,0,0.1) 0 0 30px 0;
        }

        .select2-selection{
          width: 100% !important;
          height: calc(1.5em + .75rem + 2px) !important;
        }
</style>
</head>

<body id="page-top"> 

<?php include 'views/loader.php'; ?>
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!----sidebar--->

        <?php 
            require_once 'sidebar.php'
        ?>


        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content" sucursal_session_id= "<?php echo $_SESSION['id_sucursal']; ?>" rol_session_id="<?php echo $_SESSION['rol']?>">

                <!-- Topbar -->
                <?php include 'views/navbar.php'; ?>
                <!-- End of Topbar -->


                <!-- Begin Page Content -->
                <div class="container-fluid">
                <div class="row">
        <div class="col-md-4 order-md-2 mb-4">
          <h4 class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted">Carga de imagenes:</span>
            <!-- <span class="badge badge-secondary badge-pill">3</span> -->
          </h4>
         
          <div class="card pt-3 pb-3">
            <div class="row p-2">
              <div class="col-md-6" id="img_principal_col">
                <span>Principal:</span>
                <img src="./src/img/preload.gif" onclick="cargarImagen(1)" id="img_principal" class="img_llanta" style="width:100%; margin:auto; background-size:cover;" alt="">
                <input type="file" id="file_img_principal" onchange="setearImagen('img_principal', event)" accept="image/png" hidden>
              </div>
              <div class="col-md-6" id="img_frontal_col">
              <span>Frontal:</span>
              <img src="./src/img/preload.gif" onclick="cargarImagen(2)" id="img_frontal" class="img_llanta" style="width:100%; margin:auto; background-size:cover;" alt="">
              <input type="file" id="file_img_frontal" accept="image/png" onchange="setearImagen('img_frontal', event)" hidden> 
            </div>
            </div>

            <div class="row p-2">
              <div class="col-md-6" id="img_perfil_col">
                <span>Perfil:</span>
                <img src="./src/img/preload.gif" onclick="cargarImagen(3)" id="img_perfil" class="img_llanta" style="width:100%; margin:auto; background-size:cover;" alt="">
                <input type="file" id="file_img_perfil" accept="image/png" onchange="setearImagen('img_perfil', event)" hidden>
              </div>
              <div class="col-md-6" id="img_piso_col">
                <span>Piso:</span>
                <img src="./src/img/preload.gif" onclick="cargarImagen(4)" id="img_piso" class="img_llanta" style="border-radius:9px; width:90%; margin:auto;" alt="">
                <input type="file" id="file_img_piso" accept="image/png" onchange="setearImagen('img_piso', event)" hidden>
              </div>
            </div>
          </div>

        </div>
        <div class="col-md-8 order-md-1">
          <h4 class="mb-3">Editar datos del neumatico</h4>
          <form class="needs-validation" novalidate>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="firstName">Marca</label>
                <select class="form-field" id="marca"required>
                </select>
                <div class="invalid-feedback">
                  Valid first name is required.
                </div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="lastName">Modelo</label>
                <input type="text" class="form-field" id="modelo" placeholder="" value="" required>
                <div class="invalid-feedback">
                  Valid last name is required.
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="row">
                <div class="col-3">
                  <label for="ancho">Ancho</label>
                  <input type="text" id="ancho" class="form-field" placeholder="Ejem. 255">
                </div>
                <div class="col-3">
                  <label for="alto">Alto</label>
                  <input type="text" id="alto" class="form-field" placeholder="Ejem. 65">
                </div>
                <div class="col-3">
                  <label for="construccion">Contrucción</label>
                  <select type="text" id="construccion" class="form-field">
                      <option value="">Selecciona una construcción</option>
                      <option value="R">R (Radial)</option>
                      <option value="D">D (Diagonal)</option>
                      <option value="A">Autoportante</option>
                  </select>
                </div>
                <div class="col-3">
                  <label for="rin">Rin</label>
                  <input type="text" id="rin" class="form-field" placeholder="Ejem. 16">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-8 mb-3">
                <label for="descripcion">Descripción</label>
                <textarea class="form-field" id="descripcion" placeholder="Descripción del neumatico"></textarea>
                <div class="invalid-feedback">
                  Please enter a valid email address for shipping updates.
                </div>
              </div>
              <div class="col-4">
              <label for="psi">Presión maxima (PSI)</label>
              <input type="number" class="form-field" id="psi" placeholder="0">
              </div>
            </div>
            <div class="row mb-3">
            <?php
            $sel = 'SELECT count(*) FROM indices_carga';
            $stmt = $con->prepare($sel);
            $stmt->execute();
            $stmt->bind_result($total_ind_carga);
            $stmt->fetch();
            $stmt->close();

            $sel = 'SELECT count(*) FROM indices_velocidad';
            $stmt = $con->prepare($sel);
            $stmt->execute();
            $stmt->bind_result($total_ind_velocidad);
            $stmt->fetch();
            $stmt->close();

            if($total_ind_carga>0){
              $query = 'SELECT * FROM indices_carga';
              $stmt = $con->prepare($query);
              $stmt->execute();
              $result = $stmt->get_result();
              $stmt->free_result();
              $stmt->close();

              while ($value = $result->fetch_assoc()){
                $arreglo_carga[] = $value;
              }
            }else{
                $arreglo_carga = array();
            }

            if($total_ind_velocidad>0){
              $query = 'SELECT * FROM indices_velocidad';
              $stmt = $con->prepare($query);
              $stmt->execute();
              $result = $stmt->get_result();
              $stmt->free_result();
              $stmt->close();

              while ($value = $result->fetch_assoc()){
                $arreglo_velocidad[] = $value;
              }
            }else{
                $arreglo_velocidad = array();
            }
            
            ?>
                <div class="col-3">
                    <label for="activar_promocion">Activar promoción</label>
                    <select name="" id="activar_promocion" onchange="desactivarPrecioPromocion()" class="form-field">
                      <option value="1">Si</option>
                      <option value="0">No</option>
                    </select>
                </div>
                <div class="col-3">
                    <label for="activar_promocion">Rango de carga 1</label>
                    <select name="" id="rango_carga_1" class="form-field">
                      <option value="">Seleccione un indice de carga</option>
                    <?php
                        if(count($arreglo_carga)>0){
                          foreach ($arreglo_carga as $key => $value) {
                            $id_carga = $value['id']; 
                            $indice_carga = $value['indice_carga']; 
                            $kg_max = $value['kg_max']; 
                            print_r("<option value='$id_carga'>$indice_carga($kg_max kg)</option>");
                          }
                        }
                      ?>
                    </select>
                </div>
                <div class="col-3">
                    <label for="activar_promocion">Rango de carga 2</label>
                    <select name="" id="rango_carga_2" class="form-field">
                    <option value="">Seleccione un indice de carga</option>
                    <?php
                        if(count($arreglo_carga)>0){
                          foreach ($arreglo_carga as $key => $value) {
                            $id_carga = $value['id']; 
                            $indice_carga = $value['indice_carga']; 
                            $kg_max = $value['kg_max']; 
                            print_r("<option value='$id_carga'>$indice_carga ($kg_max kg)</option>");
                          }
                        }
                      ?>
                    </select>
                </div>
                <div class="col-3">
                    <label for="activar_promocion">Indice de velocidad</label>
                    <select name="" id="indice_velocidad" class="form-field">
                    <option value="">Seleccione un indice de velocidad</option>
                    <?php
                        if(count($arreglo_velocidad)>0){
                          foreach ($arreglo_velocidad as $key => $value) {
                            $id_velocidad = $value['id']; 
                            $codigo_velocidad = $value['codigo']; 
                            $velocidad_max = $value['velocidad_max']; 
                            print_r("<option value='$id_velocidad'>$codigo_velocidad ($velocidad_max)</option>");
                          }
                        }
                      ?>
                    </select>
                </div>
            </div>
            <div class="mb-3">
              <div class="row">
                <div class="col-3">
                  <label for="costo">Costo</label>
                  <input type="number" id="costo" class="form-field" placeholder="0.00">
                </div>
                <div class="col-3">
                  <label for="precio">Precio</label>
                  <input type="number" id="precio" class="form-field" placeholder="0.00">
                </div>
                <div class="col-3">
                  <label for="precio_mayoreo">Precio mayoreo</label>
                  <input type="number" id="precio_mayoreo" class="form-field" placeholder="0.00">
                </div>
                <div class="col-3">
                  <label for="precio_mayoreo">Precio promoción</label>
                  <input type="number" id="precio_promocion" class="form-field" placeholder="0.00">
                </div>
              </div>
            </div>

            <?php
            $sel = 'SELECT count(*) FROM aplicacion';
            $stmt = $con->prepare($sel);
            $stmt->execute();
            $stmt->bind_result($total_aplicaciones);
            $stmt->fetch();
            $stmt->close();

            $sel = 'SELECT count(*) FROM tipo_vehiculos';
            $stmt = $con->prepare($sel);
            $stmt->execute();
            $stmt->bind_result($total_tipo);
            $stmt->fetch();
            $stmt->close();

            if($total_aplicaciones>0){
              $query = 'SELECT * FROM aplicacion';
              $stmt = $con->prepare($query);
              $stmt->execute();
              $result = $stmt->get_result();
              $stmt->free_result();
              $stmt->close();

              while ($value = $result->fetch_assoc()){
                $arreglo_aplicacion[] = $value;
              }
            }else{
                $arreglo_aplicacion = array();
            }

            if($total_tipo>0){
              $query = 'SELECT * FROM tipo_vehiculos';
              $stmt = $con->prepare($query);
              $stmt->execute();
              $result = $stmt->get_result();
              $stmt->free_result();
              $stmt->close();

              while ($value = $result->fetch_assoc()){
                $arreglo_tipo[] = $value;
              }
            }else{
                $arreglo_tipo = array();
            }
            
            ?>
            <div class="mb-3">
              <div class="row">
                <div class="col-4">
                   <label for="aplicacion">Aplicación</label>
                  <select name="" id="aplicacion" class="form-field">
                      <option value="">Selecciona una aplicación</option>
                      <?php
                        if(count($arreglo_aplicacion)>0){
                          foreach ($arreglo_aplicacion as $key => $value) {
                            $id_aplicacion = $value['id']; 
                            $nombre_aplicacion = $value['nombre']; 
                            print_r("<option value='$id_aplicacion'>$nombre_aplicacion</option>");
                          }
                        }
                      ?>
                  </select>
                </div>
                <div class="col-4">
                <label for="tipo_vehiculo">Tipo de vehiculo</label>
                  <select name="" id="tipo_vehiculo" class="form-field">
                  <option value="">Selecciona un tipo de vehiculo</option>
                        
                  <?php
                        if(count($arreglo_tipo)>0){
                          foreach ($arreglo_tipo as $key => $value) {
                            $id_tipo = $value['id']; 
                            $nombre_tipo = $value['nombre']; 
                            print_r("<option value='$id_tipo'>$nombre_tipo</option>");
                          }
                        }
                      ?>
                  </select>
                </div>
                <div class="col-4">
                <label for="tipo_vehiculo">Posición (llantas de camión)</label>
                  <select name="" id="posicion" class="form-field">
                    <option value="">Selecciona una posición</option>
                    <option value="1">Toda posición / Dirección</option>
                    <option value="2">Tracción</option>
                    <option value="3">Arrastre</option>
                  </select>
                </div>
              </div>
            </div>

            
            <hr class="mb-4">
            <div class="btn btn-primary btn-lg btn-block" onclick="actualizarDatosLlanta()">Guardar</div>
          </form>
        </div>
      </div>
                </div>
             </div>
            <!-- End of Main Content -->
  <!-- Footer -->
            <footer class="sticky-footer">
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
                    <a class="btn btn-primary" href="modelo/login/cerrar-sesion.php">Salir</a>
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
    
    <script src="src/vendor/datatables/jquery.dataTables.min.js"></script> 
    <script src="src/vendor/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="src/vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js" integrity="sha512-lbwH47l/tPXJYG9AcFNoJaTMhGvYWhVM9YI43CT+uteTRRaiLCui8snIgyAN8XWgNjNhCqlAUdzZptso6OCoFQ==" crossorigin="anonymous"></script>
    
    <script src="src/vendor/datatables/defaults.js"></script>
    <script src="src/vendor/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.2.0/mapbox-gl.js"></script>
    <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.min.js"></script>
<link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v4.7.0/mapbox-gl-geocoder.css" type="text/css">

  
    <script src="src/js/bootstrap-select.min.js"></script>
    <script src="src/js/editar-neumatico-avanzado.js?v=<?php echo(rand()); ?>"></script>
   
   
</body>

</html>