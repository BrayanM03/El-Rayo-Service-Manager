function cargarImagen(tipo){
    switch (tipo) {
        case 1:
            $("#file_img_principal").click()
            break;
        case 2:
            $("#file_img_frontal").click()
            break; 
        case 3:
            $("#file_img_perfil").click()
            break; 
        case 4:
            $("#file_img_piso").click()
            break;          
    
        default:
            break;
    }
}

function setearImagen(id_imagen, event){
     // Si es una imagen (cualquier tipo de imagen)
     var reader = new FileReader();
     const file = event.target.files[0];

     if(file){
      let img_preview = document.getElementById(id_imagen)
      img_preview.src = './src/img/preload.gif';

      setTimeout(function(){
        reader.onloadend = function () {
          img_preview.src = reader.result;
          img_preview.onclick = function() {};
        }
      
        if (file) {
          reader.readAsDataURL(file);
          let colElement = $(`#${id_imagen}_col`);
            img_preview.setAttribute("eliminar", false);
            let boton_borrar = '<div class="btn btn-danger" id="' + id_imagen + '_delete_btn" style="position:absolute !important; right:50px !important; top:34px;" onclick="eliminarPreview(`'+id_imagen+'`)"><i class="fas fa-trash-alt"></i></div>';
            colElement.append(boton_borrar);
        } else {
          img_previewpreview.src = "";
        }
  
        Swal.resetValidationMessage()
        toastr.success('Documento adjunto agregado con exito' ); 
        $("#input-comprobante-edicion").attr('eliminar', false);
      }, 650)

     }else{
      toastr.error('No se cargó un documento' ); 
     }
     
}

$(document).ready(function(){


  let id_llanta = getParameterByName('id_llanta')

  $.ajax({
    type: "post",
    url: "./modelo/catalogo/editar-llanta-inv-total.php",
    data: {codigo: id_llanta},
    dataType: "json",
    success: function (response) {
        if(response.estatus){
          setearAplicacion()
          
          $("#modelo").val(response.datos.modelo)
          $("#ancho").val(response.datos.ancho)
          $("#alto").val(response.datos.alto)
          $("#rin").val(response.datos.rin)
          $("#construccion").val(response.datos.construccion)
          $("#descripcion").val(response.datos.descripcion)
          $("#costo").val(response.datos.costo)
          $("#precio").val(response.datos.precio)
          $("#precio_mayoreo").val(response.datos.mayoreo)
          $("#modelo").val(response.datos.modelo)
          $("#aplicacion").val(response.datos.aplicacion)
          $("#tipo_carga").val(response.datos.tipo_carga)
          $("#tipo_vehiculo").val(response.datos.tipo_vehiculo)
          $("#activar_promocion").val(response.datos.promocion)
          $("#precio_promocion").val(response.datos.precio_promocion)
          $("#rango_carga_1").val(response.datos.indice_carga_1)
          $("#rango_carga_2").val(response.datos.indice_carga_2)
          $("#indice_velocidad").val(response.datos.indice_velocidad)
          $("#posicion").val(response.datos.posicion)
          $("#psi").val(response.datos.psi)
          select2Marca(response.datos.marca)
          desactivarPrecioPromocion()
          /* if(response.datos.urls!=null){
              let url_principal = response.datos.urls['url_principal'] != null ? response.datos.urls['url_principal'] : 'NA.JPG'
              let url_perfil = response.datos.urls['url_perfil'] != null ? response.datos.urls['url_perfil'] : 'NA.JPG'
              let url_frontal = response.datos.urls['url_frontal'] != null ? response.datos.urls['url_frontal'] : 'NA.JPG'
              let url_piso = response.datos.urls['url_piso'] != null ? response.datos.urls['url_piso'] : 'NA.JPG'

              let boton_borrar = '<div class="btn btn-danger" id="'+url_frontal+'" style="position:absolute !important; right:50px !important; top:34px;"><i class="fas fa-trash-alt"></i></div>'
              setTimeout(function(){
                $("#img_principal").attr('src', './src/img/neumaticos/'+ url_principal).attr('style', 'border-radius:9px; width:90%; margin:auto;').addClass('border')
                $("#img_principal_col").append(boton_borrar)
                $("#img_perfil").attr('src', './src/img/neumaticos/'+ url_perfil).attr('style', 'border-radius:9px; width:90%; margin:auto;').addClass('border')
                $("#img_frontal").attr('src', './src/img/neumaticos/'+ url_frontal).attr('style', 'border-radius:9px; width:90%; margin:auto;').addClass('border')
                $("#img_piso").attr('src', './src/img/neumaticos/'+ url_piso).attr('style', 'border-radius:9px; width:90%; margin:auto;').addClass('border')
              },650)
          }else{
            setTimeout(function(){
              $("#img_principal").attr('src', './src/img/neumaticos/NA.JPG').attr('style', 'border-radius:9px; width:90%; margin:auto;').addClass('border')
              $("#img_perfil").attr('src', './src/img/neumaticos/NA.JPG').attr('style', 'border-radius:9px; width:90%; margin:auto;').addClass('border')
              $("#img_frontal").attr('src', './src/img/neumaticos/NA.JPG').attr('style', 'border-radius:9px; width:90%; margin:auto;').addClass('border')
              $("#img_piso").attr('src', './src/img/neumaticos/NA.JPG').attr('style', 'border-radius:9px; width:90%; margin:auto;').addClass('border')
            },650)
          } */
          if (response.datos.urls != null) {
            let urls = {
                'url_principal': 'NA.JPG',
                'url_perfil': 'NA.JPG',
                'url_frontal': 'NA.JPG',
                'url_piso': 'NA.JPG'
            };
        
            for (let key in urls) {
                if (response.datos.urls[key] != null && response.datos.urls[key] != '') {
                    urls[key] = response.datos.urls[key];
                }else{
                   urls[key] = 'NA.JPG';
                }
            }
        
            let keys = Object.keys(urls);
            setTimeout(function () {

                keys.forEach(key => {
                  const cacheBuster = Math.floor(1000 + Math.random() * 9000);

                    let imgId = key.replace('url_', 'img_');
                    let colId = imgId + '_col';
                    let version = Date.now(); // fallback
                   
                    let imgSrc = './src/img/neumaticos/' + urls[key] + '?v=' + cacheBuster ;
                    let imgElement = $('#' + imgId);

                    let colElement = $('#' + colId);
                    imgElement.onclick = function() {};
                    imgElement.attr('src', imgSrc)
                              .attr('eliminar', false)
                              .attr('style', 'border-radius:9px; width:90%; margin:auto;')
                              .addClass('border');
                             
                    
                    if (urls[key] != 'NA.JPG') {
                        let boton_borrar = '<div class="btn btn-danger" url_imagen="' + urls[key] + '" id="'+imgId+'_delete_btn" style="position:absolute !important; right:50px !important; top:34px;" onclick="eliminarPreview(`'+imgId+'`)"><i class="fas fa-trash-alt"></i></div>';
                        colElement.append(boton_borrar);
                        imgElement .attr('onclick', '')
                    }
                });
            }, 650);
        }else{
          setTimeout(function(){
            $("#img_principal").attr('src', './src/img/neumaticos/NA.JPG').attr('style', 'border-radius:9px; width:90%; margin:auto;').addClass('border') .attr('eliminar', false)
            $("#img_perfil").attr('src', './src/img/neumaticos/NA.JPG').attr('style', 'border-radius:9px; width:90%; margin:auto;').addClass('border') .attr('eliminar', false)
            $("#img_frontal").attr('src', './src/img/neumaticos/NA.JPG').attr('style', 'border-radius:9px; width:90%; margin:auto;').addClass('border') .attr('eliminar', false)
            $("#img_piso").attr('src', './src/img/neumaticos/NA.JPG').attr('style', 'border-radius:9px; width:90%; margin:auto;').addClass('border') .attr('eliminar', false)
          },650)
        }
        
        }
    }
  });

 
});

function select2Marca(marca_actual = "Selecciona una marca") {

  $('#marca').select2({
    placeholder: marca_actual,
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
            return "Busca la marca...";
          },
          
        noResults: function() {
    
          return "Sin resultados";        
        },
        searching: function() {
    
          return "Buscando..";
        }
      },
      escapeMarkup: function (markup) { return markup; },
      templateResult: formatRepo,
      templateSelection: formatRepoSelectionSX
  });
  $("#select2-marca-container").attr('marca', marca_actual)
  function formatRepo (repo) {
    
    if (repo.loading) {
      return repo.text;
    }
    
      var $container = $(
          "<div class='select2-result-repository clearfix'>" +
          "<div class='select2-contenedor-principal'>" +
          "<div class='select2-result-repository__avatar'><img style='width: 50px; border-radius: 6px' src='./src/img/logos/" + repo.imagen + ".jpg' /></div>" +
            "<div class='select2-contenedor'>" +
            "<div class='select2_marca' marca='"+ repo.imagen +"'></div>" +
            "</div>" +
            "</div>" +
            "</div>" 
      );
    
      $container.find(".select2_marca").text(repo.nombre);
    
      
    
      return $container;
    }  
    
    function formatRepoSelectionSX (repo) {
      $("#select2-marca-container").attr('marca', repo.imagen)
      console.log(repo);
      return repo.nombre || repo.text;
    }
}

function actualizarDatosLlanta(){
  let id_llanta = getParameterByName('id_llanta')
  let marca =  $("#select2-marca-container").attr('marca')
  let modelo = $('#modelo').val();
  let ancho = $('#ancho').val();
  let alto = $('#alto').val();
  let construccion = $('#construccion').val();
  let diametro = $('#rin').val();
  let descripcion = $('#descripcion').val();
  let costo = $('#costo').val();
  let precio = $('#precio').val();
  let precio_mayoreo = $('#precio_mayoreo').val();
  let aplicacion = $('#aplicacion').val();
  let tipo_carga = $('#tipo_carga').val();
  let tipo_vehiculo = $('#tipo_vehiculo').val();
  let activar_promocion = $('#activar_promocion').val();
  let precio_promocion = $('#precio_promocion').val();
  let rango_carga_1 = $('#rango_carga_1').val();
  let rango_carga_2 = $('#rango_carga_2').val();
  let indice_velocidad = $('#indice_velocidad').val();
  let presion = $('#psi').val();


  //Obteniendo pemisos de eliminación
  let eliminar_img_principal = $("#img_principal").attr('eliminar')
  let eliminar_img_frontal = $("#img_frontal").attr('eliminar')
  let eliminar_img_perfil = $("#img_perfil").attr('eliminar')
  let eliminar_img_piso = $("#img_piso").attr('eliminar')

  let file_img_principal = document.getElementById('file_img_principal').files[0];;
  let file_img_frontal = document.getElementById('file_img_frontal').files[0];
  let file_img_perfil = document.getElementById('file_img_perfil').files[0];
  let file_img_piso = document.getElementById('file_img_piso').files[0];

  let arreglo_permisos = [
    {'indice': 'img_principal', 'eliminar': eliminar_img_principal, 'url': 'url_principal', 'id_img': 1},
    {'indice': 'img_frontal', 'eliminar': eliminar_img_frontal, 'url': 'url_frontal', 'id_img': 2},
    {'indice': 'img_perfil', 'eliminar': eliminar_img_perfil, 'url': 'url_perfil', 'id_img': 3},
    {'indice': 'img_piso', 'eliminar': eliminar_img_piso, 'url': 'url_piso', 'id_img': 4},
  ]
 
  formData = new FormData(); 
  formData.append('id_llanta', id_llanta);
  formData.append('marca', marca);
  formData.append('modelo', modelo);
  formData.append('ancho', ancho);
  formData.append('alto', alto);
  formData.append('construccion', construccion);
  formData.append('diametro', diametro);
  formData.append('descripcion', descripcion);
  formData.append('costo', costo);
  formData.append('precio', precio);
  formData.append('precio_mayoreo', precio_mayoreo);
  formData.append('promocion', activar_promocion);
  formData.append('precio_promocion', precio_promocion);
  formData.append('aplicacion', aplicacion);
  formData.append('tipo_carga', tipo_carga);
  formData.append('tipo_vehiculo', tipo_vehiculo);
  formData.append('indice_carga_1', rango_carga_1);
  formData.append('indice_carga_2', rango_carga_2);
  formData.append('indice_velocidad', indice_velocidad);
  formData.append('psi', presion);
  formData.append('arreglo_permisos', JSON.stringify(arreglo_permisos));
  
  formData.append('file_img_principal', file_img_principal);
  formData.append('file_img_frontal', file_img_frontal);
  formData.append('file_img_perfil', file_img_perfil);
  formData.append('file_img_piso', file_img_piso);

  $.ajax({
  type: "post",
  url: "./modelo/catalogo/actualizar-avanzado-llanta.php",
  data: formData,
  contentType: false,
  processData: false,
  dataType: "json",
  success: function (response) {
    if(response.estatus){
      icono_response = 'success';
    }else{
      icono_response = 'error';
    }

    Swal.fire({icon: icono_response, title: response.mensaje, confirmButtonText: 'Enterado'})
  }
});

}

function eliminarPreview(id_imagen){
  let img_preview = document.getElementById(id_imagen);
  let btn_delete = document.getElementById(id_imagen+'_delete_btn')
  let file_input = document.getElementById('file_'+id_imagen)
  btn_delete.remove();

  img_preview.src = './src/img/preload.gif';
  img_preview.setAttribute('eliminar', true);
  setTimeout(function(){
    img_preview.src = './src/img/neumaticos/NA.JPG';
    file_input.value = ''

    switch (id_imagen) {
      case 'img_principal':
          tipo_carga=1;
        break;
        case 'img_frontal':
            tipo_carga=2;
          break;
          case 'img_perfil':
              tipo_carga=3;
            break;
            case 'img_piso':
                tipo_carga=4;
              break;
    
      default:
        break;
    }
    img_preview.setAttribute('onclick', 'cargarImagen('+tipo_carga+')'); 
  }, 650)

}

function desactivarPrecioPromocion(){
  let btn_activar = $('#activar_promocion')
  let val = btn_activar.val()
  console.log(val);
  if(val == 0){
    $("#precio_promocion").removeClass().addClass('form-control disabled')
  }else{
    $("#precio_promocion").removeClass().addClass('form-control')
  }
}

function getParameterByName(name) {
  name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
  var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
  results = regex.exec(location.search);
  return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}


function setearAplicacion(){
let id_tipo_vehiculo = $("#tipo_vehiculo").val();

$.ajax({
  type: "post",
  url: "./modelo/catalogo/traer-aplicacion-tipo-vehiculo.php",
  data: {id_tipo_vehiculo},
  dataType: "json",
  success: function (response) {
    let select_aplicaciones = $("#aplicacion")
      if(response.estatus){
        console.log(response);
        select_aplicaciones.empty()
        response.data.forEach(element => {
          select_aplicaciones.append(`
          <option value="${element.id}">${element.nombre}</option>
          `)
        });
      }else{
        select_aplicaciones.append(`
        <option value="0">No aplica</option>
        `)
      }
  }
});
}







