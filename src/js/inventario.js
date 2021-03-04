$(document).ready(function() {
  

 
   /* columnDefs = [{
      title: "Codigo",
      data:  "id"
    }, {
      title: "Descripción",
      data:  "Descripcion"
    }, {
      title: "Modelo",
      data:  "Modelo"
    },  {
      title: "Cantidad"
    }, {
      title: "Precio",
      data:  "precio_Inicial"
    }, {
      title: "Precio de venta",
      data:  "precio_Venta"
    }, {
      title: "Accion",
      data: null
    }];*/
    
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
        { title: "Precio Inicial", data: "precio_Inicial" },
        { title: "Precio Venta",   data: "precio_Venta"   },
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