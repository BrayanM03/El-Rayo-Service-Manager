toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": false,
    "progressBar": true,
    "positionClass": "toast-bottom-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
  }

  let rol_usuario = $("#emp-title").attr('sesion_rol')


 
  function MostrarHorarios() {  
    //$.fn.dataTable.ext.errMode = 'none';
   // ocultarSidebar();
  table = $('#horarios').DataTable({
      
    processing: true,
    serverSide: true,
    ajax: './modelo/configuraciones/configuracion_horarios/historial-horarios.php',
    rowCallback: function(row, data, index) {
        var info = this.api().page.info();
        var page = info.page;
        var length = info.length;
        var columnIndex = 0; // Índice de la primera columna a enumerar
        
        $('td:eq(' + columnIndex + ')', row).html(page * length + index + 1);
        $(row).css('background-color','white')
        
      },
     
    columns: [   
    { title: "#",              data: null     },
    { title: "Nombre",          data: 2        },
    { title: "Fecha registro",    data: 3,  render: function(data, type, row) {
        if (data == null) {
          return '-';
        } else {
          const fechaFormateada = formatearFecha(data)
          return fechaFormateada;
        }
      }},
     
      { title: "Estatus",    data: 4 , render: function(data, type, row){
        data = parseInt(data)
        switch(data){
          case 1:
          var estatus = 'Activo';
          var class_btn = 'success'
          break;
          case 0:
          var estatus = 'Inactivo';
          var class_btn = 'danger'
          break;  
          default:
            var estatus ='Sin información'
            var class_btn = 'secondary'
        }
        
          return `<div class="btn btn-${class_btn}" style="font-size: 12px !important;">${estatus}</div>`;
        
      }       },
   

    { title: "Accion",
      data: null,
      className: "celda-acciones",
      render: function (row, data) {
        /* rol = $("#emp-title").attr("sesion_rol");
        if(rol == "1" || rol == '2'){ */
        return `<div class="btn btn-primary" onclick="verRequerimiento(${row[1]})">
        <i class="fa fa-eye"></i></div>`
      /*  }else{
        return '';
       } */
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
  order: [[1, "desc"]],
  
  //order: [1, "desc"],
  
  
  });
  //table.columns( [6] ).visible( true );
  $("table.dataTable thead").addClass("table-dark")
  $("table.dataTable").css("background-color", 'white')
  
  }
  armarHorario()
  function armarHorario(){
    Swal.fire({
        width: '1000px',
        title: 'Agregar horario',
        showCloseButton: true,
        confirmButtonText: 'Registrar horario',
        html: `
        <div class="container" id="contenedor-formulario-horario" style="display:flex; flex-direction:column; jusitfy-content:center">
            <div class="row">
                <div class="col-12 col-md-12">
                    Escoge los dias a los que pertenecerá el horario y el horario por dia
                </div>
            </div>
            <div class="row mt-2 justify-content-center">
                <div class="col-12 col-md-9">
                    <label>Nombre del horario:</label>
                    <input id="nombre-horario" class="form-control" class="form-control" value="Jelou" placeholder="Nombre">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12 col-md-12">
                    <div class="contenedor-dias">
                        <ol>
                            <li id="dia-1" seleccionado="false" class="primer-dia" onclick="adjuntarHorario(1)">Lunes</li>
                            <li id="dia-2" seleccionado="false" onclick="adjuntarHorario(2)">Martes</li>
                            <li id="dia-3" seleccionado="false" onclick="adjuntarHorario(3)">Miercoles</li>
                            <li id="dia-4" seleccionado="false" onclick="adjuntarHorario(4)">Jueves</li>
                            <li id="dia-5" seleccionado="false" onclick="adjuntarHorario(5)")>Viernes</li>
                            <li id="dia-6" seleccionado="false" onclick="adjuntarHorario(6)">Sabado</li>
                            <li id="dia-7" seleccionado="false" onclick="adjuntarHorario(7)" class="ultimo-dia">Domingo</li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center mb-3">
                <div class="col-12 col-md-8">
                        <div class="contenedor-horas sin-elementos" id="contenedor-horas">
                            <span>Sin dias seleccionados</span>
                        </div>
                </div>
            </div>
        </div>
        
        `,
        preConfirm: function(){
         let nombre_horario = $("#nombre-horario").val()
         if(nombre_horario.trim()==''){
          Swal.showValidationMessage(`
           Ingresa el nombre del horario
          `);
         }

         let horarios = document.querySelectorAll('.contenedor-horas select')
         if(horarios.length==0){
          Swal.showValidationMessage(`
          Selecciona al menos 1 día 
         `);
         }
         

        }
    }).then((r)=>{
      if(r.isConfirmed){
 
        datos_enviar={};
        let horarios = document.querySelectorAll('#contenedor-formulario-horario input, select')
         horarios.forEach(element => {
          if(element.id!=''){
            datos_enviar[element.id]=element.value;
          }
         });
         console.log(datos_enviar);

         $.ajax({
          type: "POST",
          url: "./modelo/configuraciones/configuracion_horarios/registrar-horario.php",
          data: datos_enviar,
          dataType: "json",
          success: function (response) {
            Swal.fire({
              confirmButtonText:'Enterado',
              icon: response.tipo,
              title: response.mensaje
            })

            table.ajax.reload(false, null)
          }
         });
      }
    })
  }

  const arreglo_dias=[];
  /* adjuntarHorario(2,1) */
  function adjuntarHorario(dia, tipo){
    let letra;
    switch (dia) {
      case 1:
        letra='L';
        break;
        case 2:
        letra='M';
        break;
        case 3:
        letra='M';
        break;
        case 4:
        letra='J';
        break;
        case 5:
        letra='V';
        break;
        case 6:
        letra='S';
        break;
        case 7:
        letra='D';
        break;
    
      default:
        break;
    }
    
    let dia_seleccionado = $("#dia-"+dia).attr('seleccionado');
 
    if(dia_seleccionado=='true'){
      $("#fila-dia-"+dia).remove()
      $("#dia-"+dia).attr('seleccionado', false)
      $("#dia-"+dia).removeClass('dia_seleccionado')
      console.log($("#dia-"+dia).attr('seleccionado'));
      let pos = arreglo_dias.indexOf(dia);
      arreglo_dias.splice(pos, 1);
      if(arreglo_dias.length==0){
        console.log(arreglo_dias);
        let contenedor_horas = $("#contenedor-horas");
        contenedor_horas.addClass('sin-elementos')
        contenedor_horas.empty().append(` <span>Sin dias seleccionados</span>`)
      }
    }else{
      arreglo_dias.push(dia)

      let contenedor_horas = $("#contenedor-horas");
      contenedor_horas.removeClass('sin-elementos')
      if(arreglo_dias.length==1){
        contenedor_horas.removeClass('sin-elementos')
        contenedor_horas.empty()
      }
      contenedor_horas.append(`
                <div class="row mt-2" id="fila-dia-${dia}">
                    <div class="col-12 col-md-1 mt-2 text-right">
                      <span class="letra-dia"><b>${letra}</b></span>
                    </div>
                    <div class="col-12 col-md-5">
                          <select id="hora-inicio-${dia}" class="selectpicker form-control">
                            <option value="00:00">12:00 am</option>
                            <option value="00:30">12:30 am</option>
                            <option value="01:00">01:00 am</option>
                            <option value="01:30">01:30 am</option>
                            <option value="02:00">02:00 am</option>
                            <option value="02:30">02:30 am</option>
                            <option value="03:00">03:00 am</option>
                            <option value="03:30">03:30 am</option>
                            <option value="04:00">04:00 am</option>
                            <option value="04:30">04:30 am</option>
                            <option value="05:00">05:00 am</option>
                            <option value="05:30">05:30 am</option>
                            <option value="06:00">06:00 am</option>
                            <option value="06:30">06:30 am</option>
                            <option value="07:00">07:00 am</option>
                            <option value="07:30">07:30 am</option>
                            <option value="08:00">08:00 am</option>
                            <option value="08:30" selected>08:30 am</option>
                            <option value="09:00">09:00 am</option>
                            <option value="09:30">09:30 am</option>
                            <option value="10:00">10:00 am</option>
                            <option value="10:30">10:30 am</option>
                            <option value="11:00">11:00 am</option>
                            <option value="11:30">11:30 am</option>
                            <option value="12:00">12:00 pm</option>
                            <option value="12:30">12:30 pm</option>
                            <option value="13:00">01:00 pm</option>
                            <option value="13:30">01:30 pm</option>
                            <option value="14:00">02:00 pm</option>
                            <option value="14:30">02:30 pm</option>
                            <option value="15:00">03:00 pm</option>
                            <option value="15:30">03:30 pm</option>
                            <option value="16:00">04:00 pm</option>
                            <option value="16:30">04:30 pm</option>
                            <option value="17:00">05:00 pm</option>
                            <option value="17:30">05:30 pm</option>
                            <option value="18:00">06:00 pm</option>
                            <option value="18:30">06:30 pm</option>
                            <option value="19:00">07:00 pm</option>
                            <option value="19:30">07:30 pm</option>
                            <option value="20:00">08:00 pm</option>
                            <option value="20:30">08:30 pm</option>
                            <option value="21:00">09:00 pm</option>
                            <option value="21:30">09:30 pm</option>
                            <option value="22:00">10:00 pm</option>
                            <option value="22:30">10:30 pm</option>
                            <option value="23:00">11:00 pm</option>
                            <option value="23:30">11:30 pm</option>
                          </select>
                    </div>
                    <div class="col-12 col-md-5">
                          <select id="hora-fin-${dia}" class="selectpicker form-control">
                          <option value="00:00">12:00 am</option>
                          <option value="00:30">12:30 am</option>
                          <option value="01:00">01:00 am</option>
                          <option value="01:30">01:30 am</option>
                          <option value="02:00">02:00 am</option>
                          <option value="02:30">02:30 am</option>
                          <option value="03:00">03:00 am</option>
                          <option value="03:30">03:30 am</option>
                          <option value="04:00">04:00 am</option>
                          <option value="04:30">04:30 am</option>
                          <option value="05:00">05:00 am</option>
                          <option value="05:30">05:30 am</option>
                          <option value="06:00">06:00 am</option>
                          <option value="06:30">06:30 am</option>
                          <option value="07:00">07:00 am</option>
                          <option value="07:30">07:30 am</option>
                          <option value="08:00">08:00 am</option>
                          <option value="08:30">08:30 am</option>
                          <option value="09:00">09:00 am</option>
                          <option value="09:30">09:30 am</option>
                          <option value="10:00">10:00 am</option>
                          <option value="10:30">10:30 am</option>
                          <option value="11:00">11:00 am</option>
                          <option value="11:30">11:30 am</option>
                          <option value="12:00">12:00 pm</option>
                          <option value="12:30">12:30 pm</option>
                          <option value="13:00">01:00 pm</option>
                          <option value="13:30">01:30 pm</option>
                          <option value="14:00">02:00 pm</option>
                          <option value="14:30">02:30 pm</option>
                          <option value="15:00">03:00 pm</option>
                          <option value="15:30">03:30 pm</option>
                          <option value="16:00">04:00 pm</option>
                          <option value="16:30">04:30 pm</option>
                          <option value="17:00">05:00 pm</option>
                          <option value="17:30">05:30 pm</option>
                          <option value="18:00">06:00 pm</option>
                          <option value="18:30" selected>06:30 pm</option>
                          <option value="19:00">07:00 pm</option>
                          <option value="19:30">07:30 pm</option>
                          <option value="20:00">08:00 pm</option>
                          <option value="20:30">08:30 pm</option>
                          <option value="21:00">09:00 pm</option>
                          <option value="21:30">09:30 pm</option>
                          <option value="22:00">10:00 pm</option>
                          <option value="22:30">10:30 pm</option>
                          <option value="23:00">11:00 pm</option>
                          <option value="23:30">11:30 pm</option>
                        </select>      
                    </div>
                    <div class="col-12 col-md-1 text-right">
                      <span onclick="adjuntarHorario(${dia})" class="btn" style="background-color:#ef6d31; color:white;"><i class="fas fa-trash"></i></span>
                    </div>
                </div>
      `)
  
      $("#hora-inicio-"+dia).selectpicker('refresh');
      $("#hora-fin-"+dia).selectpicker('refresh');
      $("#dia-"+dia).attr('seleccionado', true)
      $("#dia-"+dia).addClass('dia_seleccionado')
      console.log($("#dia-"+dia).attr('seleccionado'));
      
    }
    console.log(arreglo_dias);
   
  }

  function formatearFecha(fecha) {
    const meses = [
        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", 
        "Octubre", "Noviembre", "Diciembre"
    ];

    const fechaObj = new Date(fecha);
    const dia = fechaObj.getDate();
    const mes = meses[fechaObj.getMonth()];
    const anio = fechaObj.getFullYear().toString().slice(-2); // Tomar los últimos 2 dígitos del año

    return `${dia} de ${mes} ${anio}`;
}
