$(document).ready(function() {
  
    table = $('#inventario').DataTable({
      
      
        ajax: {
            method: "POST",
            url: "./modelo/traerInventario.php"
        },  
  
      columns: [   
        { title: "Codigo",         data: "id"             },
        { title: "Descripcion",    data: "Descripcion"    },
        { title: "Marca",          data: "Marca"          },
        { title: "Modelo",         data: "Modelo"         },
        { title: "Costo", data: "precio_Inicial" },
        { title: "Precio",   data: "precio_Venta"   },
        { title: "Precio Mayoreo", data: "precio_Mayoreo" },
        { title: "Sucursal",       data: "Sucursal" },
        { title: "Stock",          data: "Stock"          },
        { title: "Imagen",         data: "Marca", render: function(data,type,row) {
          return '<img src="./src/img/logos/'+ data +'.jpg" style="width: 60px; border-radius: 8px">';
          }},
        {
          data: null,
          className: "celda-acciones",
          render: function () {
            return '<div style="display: flex"><button type="button" class="buttonEditar btn btn-warning" style="margin-right: 8px"><span class="fa fa-edit"></span><span class="hidden-xs"></span></button><br><button type="button" class="buttonBorrar btn btn-danger"><span class="fa fa-trash"></span><span class="hidden-xs"></span></button></div>';
          },
        },
      ],
      paging: true,
      searching: true,
      scrollY: "600px",
      info: true,
      responsive: true,
    
      
    });

    

});

function agregarLLanta() {

  Swal.fire({
    title: "Agregar llanta nueva",
    html: '<form class="mt-4" id="formulario-editar-registro">'+

    '<div class="row">'+
    '<div class="col-4">'+
    '<div class="form-group">'+
    '<label><b>Unidad:</b></label></br>'+
    '<input class="form-control " value="" name="id-input-modal" readonly>'+
       '</div>'+
       '</div>'+
       '<div class="col-8">'+
       '<div class="form-group">'+
       '<label><b>Clave SAT:</b></label></br>'+
       '<input class="form-control " value="" name="cate-input-modal" readonly>'+
          '</div>'+
          '</div>'+
       '</div>'+

    '<div class="row">'+
        '<div class="col-4">'+
        '<div class="form-group">'+
        '<label for="exampleInputEmail1"><b>Ancho:</b></label></br>'+
        '<input type="number" class="form-control" id="cr-input-modal" value="" name="cr-input-nuevaOrden" aria-describedby="emailHelp" placeholder="Ancho" autocomplete="off">'+


   ' </div>'+
    '</div>'+
    
    
   '<div class="col-4">'+
    '<div class="form-group">'+
    '<label><b>Alto:</b></label></br>'+
    '<input type="number" value="" name="date-nuevaOrden" class="form-control" placeholder="Proporcion">'+
    '</div>'+
    '</div>'+

    
        '<div class="col-4">'+
        '<div class="form-group">'+
        '<label><b>Rin</b></label>'+
        '<input type="text" class="tienda-span-modal-mto form-control" value=""  id="tienda-cliente" name="tienda-span-modal-mto" placeholder="Diametro">'+
    '</div>'+
        '</div>'+

       
        '<div class="col-6">'+
        '<div class="form-group">'+
        '<label><b>Marca</b></label>'+
        '<select class="form-control" id="select-status" value="" name="status-new-orden">'+
        '<option value="Abierto">Abierto</option>'+
        '<option value="Cerrado">Cerrado</option>'+
        '</select>'+
    '</div>'+
        '</div>'+

        '<div class="col-6">'+
        '<div class="form-group">'+
        '<label><b>Modelo</b></label>'+
        '<input type="text" class="form-control" value=""  id="usuario-editar" name="usuario-editar" placeholder="Modelo">'+
        '</div>'+
        '</div>'+
       


    '</div>'+

    '<div class="row">'+
        '<div class="col-4">'+
            '<div class="form-group">'+
                '<label><b>Costo</b></label>'+
                '<input type="number" class="form-control" value=""name="folio-nueva-orden" placeholder="0.00">'+
            '</div>'+
        '</div>'+
        '<div class="col-4">'+
        '<div class="form-group">'+
        '<label><b>Precio</b></label>'+
        '<input type="number" class="form-control" value=""name="folio-nueva-orden" placeholder="0.00">'+
    '</div>'+
'</div>'+
'<div class="col-4">'+
        '<div class="form-group">'+
        '<label><b>Mayorista</b></label>'+
        '<input type="number" class="form-control" value=""name="folio-nueva-orden" placeholder="0.00">'+
    '</div>'+
'</div>'+
        '</div>'+
    '</div>'+
    '<div class="row">'+
    '<div class="col-6">'+
        '<div class="form-group">'+
            '<label><b>Fecha</b></label>'+
            '<input type="date" class="form-control" value=""name="subcat-editar-orden" >'+
        '</div>'+
    '</div>'+
    
    '<div class="col-6">'+
        '<div class="form-group">'+
            '<label><b>Cantidad</b></label>'+
            '<input type="text" class="form-control" value=""name="mes-editar-orden" placeholder="0">'+
        '</div>'+
    '</div>'+
    '</div>'+

    '<div class="row  mt-1">'+
    '<div class="col-12">'+
    '<div class="form-group" id="area-solucion">'+
    '<label><b>Descripción</b></label>'+
    '<textarea class="form-control" name="swal-solucion" style="height:100px" id="textarea-swal-solucion" form="formulario-editar-registro" placeholder="Escriba la descripcion del producto"></textarea>'+
    '</div>'+
    '</div>'+
    '</div>'+
            '</div>'+
'</form>',
    showCancelButton: true,
    cancelButtonText: 'Cerrar',
    cancelButtonColor: '#00e059',
    showConfirmButton: true,
    confirmButtonText: 'Actualizar', 
    cancelButtonColor:'#ff764d',
  });

}