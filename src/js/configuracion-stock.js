traerSucursales();
function traerSucursales(){
  $.ajax({
    type: "post",
    url: "modelo/busqueda/traer-sucursales.php",
    data: {"data":'data'},
    dataType: "json",
    success: function (response) {
        sucursales= response;
      $("#sucursal").empty();
      response.forEach(element => { 
      $("#sucursal").append(
        `<option value="${element.id}">${element.nombre}</option>`
      )
      });  
    }
  });
}

function obtenerNombreSucursal(idSucursal, sucursales) {
    const sucursalEncontrada = sucursales.find(sucursal => sucursal.id == idSucursal);
    return sucursalEncontrada ? sucursalEncontrada.nombre : "";
  }

tablaMedidas()
function tablaMedidas(){
     table = $('#medidas').DataTable({
     processing: true,
     serverSide: true,
     ajax: './modelo/configuraciones/configuracion_stock/historial-medidas-stock.php',
     rowCallback: function(row, data, index) {
         var info = this.api().page.info();
         var page = info.page;
         var length = info.length;
         var columnIndex = 0; // Índice de la primera columna a enumerar
   
         $('td:eq(' + columnIndex + ')', row).html(page * length + index + 1);
       },
      
     columns: [   
     { title: "#",              data: null   },
     { title: "Medida",              data: 1   },
     { title: "Stock minimo",          data: 7      },
     { title: "Stock maximo",      data: 8      },
     { title: "Sucursal",    data: 6, render(data){
        return obtenerNombreSucursal(data, sucursales);
     }}, 
     { title: "Estatus",          data: 9, render(data){
        if(data==1){
            return 'Activo';
        }else if(data==2){
        return 'Inactivo';
        }else{
            return '';
        }
     }},
     { title: "Creación",     data: 10    },
     { title: "Acción",
       data: null,
       className: "celda-acciones",
       render: function (row, data) {
         rol = $("#emp-title").attr("sesion_rol");
           if(rol == '1'){
             return `
             <button type="button" onclick="editarMedida(${row[0]});" title="Editar medida" class="btn btn-warning" style="margin-right: 10px">
             <span class="fa fa-edit"></span><span class="hidden-xs"></span></button>
             <button type="button" onclick="borrarrMedida(${row[0]});" title="Borrar medida" class="btn btn-danger">
             <span class="fa fa-trash"></span><span class="hidden-xs"></span></button>
             `;
         }
          },
     },
   ],
   paging: true,
   searching: true,
   scrollY: "50vh",
   info: false,
   responsive: false,
   ordering: "enable",
   multiColumnSort: true,
   order: [0, "desc"],
   });
   //table.columns( [6] ).visible( true );
   $("table.dataTable thead").addClass("table-info")
   
}

function agregarMedida(){
    Swal.fire({
        title: 'Agregar medida',
        html: `
        <div class="container">
        <!--- <div class="row mb-3">
               <div class="col-md-12">
                    <input type="text" class="form-control" id="marca">
                </div>
            </div>--->
            <div class="row">
                <div class="col-md-4">
                    <label>Ancho</label>
                    <input type="number" class="form-control" id="ancho" placeholder="0">
                </div>
                <div class="col-md-4">
                    <label>Perfil</label>
                    <input type="number" class="form-control" id="perfil" placeholder="0">
                </div>
                <div class="col-md-4">
                    <label>Rin</label>
                    <input type="number" class="form-control" id="rin" placeholder="0">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="construccion">Construcción</label>
                    <select class="form-control" id="construccion">
                            <option value="R">Radial</option>
                            <option value="D">Diagonal</option>
                            <option value="S">Sólida</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="estatus">Estatus</label>
                    <select class="form-control" id="estatus">
                            <option value="1">Activo</option>
                            <option value="2">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
            <div class="col-md-12">
                <label for="sucursal">Sucursal</label>
                <select class="form-control" id="sucursal">
                </select>
            </div>
        </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <label class="descripcion">Descripción de la medida</label>
                    <textarea class="form-control" id="descripcion" placeholder="Escriba la descripción de la medida..."></textarea>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="stock_minimo">Stock minimo</label>
                    <input class="form-control" id="stock_minimo" type="number" placeholder="0">
                </div>
                <div class="col-md-6">
                    <label class="stock_minimo">Stock maximo (opcional)</label>
                    <input class="form-control" id="stock_maximo" type="number" placeholder="0">
                </div>
            </div>
            
        </div>
        `,
        confirmButtonText: 'Registrar',
        didOpen: function(){
            let ancho_input = document.querySelector('#ancho');
            let perfil_input = document.querySelector('#perfil');
            let rin_input = document.querySelector('#rin');

            ancho_input.addEventListener("keyup", function(event) {
                let ancho = $("#ancho").val();
                let perfil = $("#perfil").val();
                let rin = $("#rin").val();
                let construccion = $("#construccion").val();

                let descripcion_medida = ancho + '/' + perfil + construccion + rin;
                $("#descripcion").val(descripcion_medida);
              });
              perfil_input.addEventListener("keyup", function(event) {
                let ancho = $("#ancho").val();
                let perfil = $("#perfil").val();
                let rin = $("#rin").val();
                let construccion = $("#construccion").val();

                let descripcion_medida = ancho + '/' + perfil + construccion + rin;
                $("#descripcion").val(descripcion_medida);
              });
              rin_input.addEventListener("keyup", function(event) {
                let ancho = $("#ancho").val();
                let perfil = $("#perfil").val();
                let rin = $("#rin").val();
                let construccion = $("#construccion").val();

                let descripcion_medida = ancho + '/' + perfil + construccion + rin;
                $("#descripcion").val(descripcion_medida);
              });

             /*  $('#marca').select2({
                placeholder: "Selecciona una marca",
                theme: "bootstrap",
                minimumInputLength: 1,
                ajax: {
                    url: "./modelo/traer-marca.php",
                    type: "post",
                    dataType: 'json',
                    delay: 250,

                    data: function (params) {
                     return {
                       searchTerm: params.term // search term
                       
                     };
                    },
                    processResults: function (data) {
                        return {
                           results: data
                        };
                      },
                   
                    cache: true

                },
                language:  {

                    inputTooShort: function () {
                        return "Busca la llanta...";
                      },
                      
                    noResults: function() {
                
                      return "Sin resultados";        
                    },
                    searching: function() {
                
                      return "Buscando..";
                    }
                  },

                  templateResult: formatRepo,
                  templateSelection: formatRepoSelection
            });


            function formatRepo (repo) {
                
              if (repo.loading) {
                return repo.text;
              }
              
                var $container = $(
                    "<div class='select2-result-repository clearfix'>" +
                    "<div class='select2-contenedor-principal'>" +
                    "<div class='select2-result-repository__avatar'><img style='width: 50px; border-radius: 6px' id='img_brand_"+repo.id+"' src='./src/img/logos/" + repo.imagen + ".jpg' /></div>" +
                      "<div class='select2-contenedor'>" +
                      "<div class='select2_marca' marca='"+ repo.imagen +"'></div>" +
                      "</div>" +
                      "</div>" +
                      "</div>" 
                );
              
                $container.find(".select2_marca").text(repo.nombre);

                
              
                return $container;
              }

             // Manejar errores de carga de imágenes
            $('#marca').on('select2:open', function (e) {
                $('.select2-results').find('img').on('error', function() {
                $(this).attr('src', 'undefined.jpg'); // Ruta de la imagen por defecto
                });
            }); 

              function formatRepoSelection (repo) {
                return repo.imagen || repo.text;
              }*/
              traerSucursales()
        },
        preConfirm: function(){
            if($("#stock_minimo").val()<=0){
                return Swal.showValidationMessage('Porfavor ingresa un stock minimo');
            }
            if($("#descripcion").val()==''){
                return Swal.showValidationMessage('Porfavor ingresa una medida');
            }
            if($("#marca").val()==''){
                return Swal.showValidationMessage('Porfavor selecciona una marca');
            }
        }
    }).then((r)=>{

        if(r.isConfirmed){
            let ancho = $("#ancho").val();
            let perfil = $("#perfil").val();
            let rin = $("#rin").val();
            let medida = $("#descripcion").val();
            let construccion = $("#construccion").val();
            let estatus = $("#estatus").val();
            let stock_minimo = $("#stock_minimo").val();
            let stock_maximo = $("#stock_maximo").val();
            let id_sucursal = $("#sucursal").val();

            $.ajax({
                type: "post",
                url: "./modelo/configuraciones/configuracion_stock/agregar-medida.php",
                data: {ancho, perfil, rin, medida, construccion, estatus, id_sucursal, stock_minimo, stock_maximo},
                dataType: "json",
                success: function (response) {
                     if(response.estatus){
                      var icon = 'success';
                     }else{
                     var icon ='error';
                     }

                     Swal.fire({
                      icon: icon,
                      html: response.mensaje
                     })

                     table.ajax.reload(null, false);
                }
            });
        }

    })
}

function editarMedida(id_medida){
  $.ajax({
    type: "post",
    url: "./modelo/configuraciones/configuracion_stock/traer-medida.php",
    data: {id_medida},
    dataType: "json",
    success: function (response) {
      if(response.estatus){
        let ancho_actual = parseFloat(response.data.ancho);
        Swal.fire({
          title: 'Editar medida',
          html: `
          <div class="container">
          <!--- <div class="row mb-3">
                 <div class="col-md-12">
                      <input type="text" class="form-control" id="marca">
                  </div>
              </div>--->
              <div class="row">
                  <div class="col-md-4">
                      <label>Ancho</label>
                      <input type="number" value="${ancho_actual}" class="form-control" id="ancho" placeholder="0">
                  </div>
                  <div class="col-md-4">
                      <label>Perfil</label>
                      <input type="number" class="form-control" value="${response.data.perfil}" id="perfil" placeholder="0">
                  </div>
                  <div class="col-md-4">
                      <label>Rin</label>
                      <input type="number" class="form-control" id="rin" value="${response.data.rin}" placeholder="0">
                  </div>
              </div>
              <div class="row mt-3">
                  <div class="col-md-6">
                      <label class="construccion">Construcción</label>
                      <select class="form-control" value="${response.data.construccion}" id="construccion">
                              <option value="R">Radial</option>
                              <option value="D">Diagonal</option>
                              <option value="S">Sólida</option>
                      </select>
                  </div>
                  <div class="col-md-6">
                      <label class="estatus" value="${response.data.estatus}">Estatus</label>
                      <select class="form-control" id="estatus">
                              <option value="1">Activo</option>
                              <option value="2">Inactivo</option>
                      </select>
                  </div>
              </div>
              <div class="row mt-3">
              <div class="col-md-12">
                  <label for="sucursal">Sucursal</label>
                  <select class="form-control" id="sucursal">
                  </select>
              </div>
          </div>
              <div class="row mt-3">
                  <div class="col-md-12">
                      <label for="descripcion">Descripción de la medida</label>
                      <textarea class="form-control" id="descripcion" placeholder="Escriba la descripción de la medida...">${response.data.descripcion}</textarea>
                  </div>
              </div>
              <div class="row mt-3">
                  <div class="col-md-6">
                      <label for="stock_minimo">Stock minimo</label>
                      <input class="form-control" value="${response.data.stock_minimo}" id="stock_minimo" type="number" placeholder="0">
                  </div>
                  <div class="col-md-6">
                      <label for="stock_minimo">Stock maximo (opcional)</label>
                      <input class="form-control" id="stock_maximo" value="${response.data.maximo}" type="number" placeholder="0">
                  </div>
              </div>
              
          </div>
          `,
          confirmButtonText: 'Registrar',
          didOpen: function(){
              let ancho_input = document.querySelector('#ancho');
              let perfil_input = document.querySelector('#perfil');
              let rin_input = document.querySelector('#rin');
      
              ancho_input.addEventListener("keyup", function(event) {
                  let ancho = $("#ancho").val();
                  let perfil = $("#perfil").val();
                  let rin = $("#rin").val();
                  let construccion = $("#construccion").val();
      
                  let descripcion_medida = ancho + '/' + perfil + construccion + rin;
                  $("#descripcion").val(descripcion_medida);
                });
                perfil_input.addEventListener("keyup", function(event) {
                  let ancho = $("#ancho").val();
                  let perfil = $("#perfil").val();
                  let rin = $("#rin").val();
                  let construccion = $("#construccion").val();
      
                  let descripcion_medida = ancho + '/' + perfil + construccion + rin;
                  $("#descripcion").val(descripcion_medida);
                });
                rin_input.addEventListener("keyup", function(event) {
                  let ancho = $("#ancho").val();
                  let perfil = $("#perfil").val();
                  let rin = $("#rin").val();
                  let construccion = $("#construccion").val();
      
                  let descripcion_medida = ancho + '/' + perfil + construccion + rin;
                  $("#descripcion").val(descripcion_medida);
                });
      
               traerSucursales()
               $("#sucursal").val(response.data.id_sucursal)
               $("#estatus").val(response.data.estatus)
          },
          preConfirm: function(){
              if($("#stock_minimo").val()<=0){
                  return Swal.showValidationMessage('Porfavor ingresa un stock minimo');
              }
              if($("#descripcion").val()==''){
                  return Swal.showValidationMessage('Porfavor ingresa una medida');
              }
              if($("#marca").val()==''){
                  return Swal.showValidationMessage('Porfavor selecciona una marca');
              }
          }
      }).then((r)=>{
      
          if(r.isConfirmed){
              let ancho = $("#ancho").val();
              let perfil = $("#perfil").val();
              let rin = $("#rin").val();
              let medida = $("#descripcion").val();
              let construccion = $("#construccion").val();
              let estatus = $("#estatus").val();
              let stock_minimo = $("#stock_minimo").val();
              let stock_maximo = $("#stock_maximo").val();
              let id_sucursal = $("#sucursal").val();
      
              $.ajax({
                  type: "post",
                  url: "./modelo/configuraciones/configuracion_stock/actualizar-medida.php",
                  data: {ancho, perfil, rin, medida, construccion, estatus, id_sucursal, stock_minimo, stock_maximo, id_medida},
                  dataType: "json",
                  success: function (response) {
                       if(response.estatus){
                        var icon = 'success';
                       }else{
                       var icon ='error';
                       }
      
                       Swal.fire({
                        icon: icon,
                        html: response.mensaje
                       })
      
                       table.ajax.reload(null, false);
                  }
              });
          }
      
      })
      }
    }
  });
  
}

function borrarrMedida(id_medida){
    Swal.fire({
        icon: 'question',
        title: '¿Desea eliminar esta medida?',
        confirmButtonText: 'Eliminar',
        showCancelButton: true,
        showCancelButtonText: 'Cancelar'
    }).then(function(re){
        if(re.isConfirmed){
            $.ajax({
                type: "post",
                url: "./modelo/configuraciones/configuracion_stock/borrar-medida.php",
                data: {id_medida},
                dataType: "json",
                success: function (response) {
                    if(response.estatus){
                        var icon = 'success';
                       }else{
                       var icon ='error';
                       }
      
                       Swal.fire({
                        icon: icon,
                        html: response.mensaje
                       })
      
                       table.ajax.reload(null, false);
                }
            });
        }
    })
}