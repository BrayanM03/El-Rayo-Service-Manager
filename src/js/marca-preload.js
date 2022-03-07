
document.documentElement.style.setProperty('--animate-duration', '.3s');

$(".add-images").on("click", function () { 

    $("#imagen-marca").click();

 });

(function () {
    'use strict';
   
    var file = document.getElementById('imagen-marca');
    var formData = new FormData();
    var agregarAuto = document.getElementById('btn-agregar-marca');
    var preload = document.querySelector('.preload');
    


    file.addEventListener('change', function (e) {

        for (let i = 0; i < file.files.length; i++) {

            var thumbnail_id = Math.floor(Math.random() * 30000) + '_' + Date.now();
            createThumbnail(file, i, thumbnail_id);
            formData.append(thumbnail_id, file.files[i]);

        }

        e.target.value = '';

       /*  $('.add-images').empty(); */
       // document.getElementById('preview-images').insertAdjacentHTML('beforeend', '<i class="icono-add-image bx bxs-message-square-add bx-tada-hover bx-lg"></i>');

    });

    //Funcion que crea los thumbnail images temp y las pinta en el DOM
    var createThumbnail = function (archivo, iterador, thumbnail_id) {
        var thumbnail = document.createElement('div');
        thumbnail.classList.add('thumbnail', thumbnail_id);
        
        thumbnail.dataset.id = thumbnail_id;

        thumbnail.setAttribute('style', `background-image: url(${URL.createObjectURL(archivo.files[iterador])})`);


        document.getElementById('preview-images').appendChild(thumbnail);

        createCloseButton(thumbnail_id);
        thumbnail.classList.add('animate__animated', 'animate__fadeInRight');
        var contenedor = $(".add-images");
        contenedor.empty();

        console.log(formData);
        
    };

    //Funcion que crea los thumbnail-close images del DOM
    var createCloseButton = function (thumbnail_id) {

       var closeButton = document.createElement('div');
       closeButton.classList.add('close-button');
       closeButton.innerHTML = "<i class='fas fa-times-circle remover'></i>";
       document.getElementsByClassName(thumbnail_id)[0].appendChild(closeButton);

      }

     

      //Funcion que borrara el form data y las imagenes
      var clearFormDataAndThumbnails = function () { 
           for (var key of formData.keys()) {
               formData.delete(key);
           }

           document.querySelectorAll('.thumbnail').forEach(function (thumbnail) { 
               thumbnail.classList.remove('animate__fadeInRight');
               thumbnail.classList.add('animate__backOutUp');
               thumbnail.remove();
                
               
            });

           

       }

      //Evento que eliminara la img
      document.body.addEventListener('click', function (e) { 
   
          if(e.target.classList.contains('remover')){
              
                e.target.parentNode.parentNode.classList.remove('animate__fadeInRight');
                e.target.parentNode.parentNode.classList.add('animate__fadeOutDown');
               
                setInterval(borrarData, 350);

                function borrarData(){
                   e.target.parentNode.parentNode.remove(); 
                   formData.delete(e.target.parentNode.parentNode.dataset.id);
                }

                $(".add-images").append('<div class="add-images">'+
                '<img src="./src/img/add.png" style="width:40px"></img><br>'+
                '<span class="span-add-image text-center">Agregar logo</span>'+
                '</div>');
               
          }

         

       })

       //Evento que subira las imagenes a la database ------------------******
       agregarAuto.addEventListener('click', function (e) { 

           e.preventDefault();
           //Activamos el preload
           

           var marca = document.getElementById('nombre-marca').value;
           
        if(marca == "" || marca == null){

            Swal.fire({
                title: '¡Hey! te falto algo...',
                html: "<span>Escribe el nombre de la marca</span>",
                icon: "warning",
                cancelButtonColor: '#00e059',
                showConfirmButton: true,
                confirmButtonText: 'Aceptar', 
                cancelButtonColor:'#ff764d',
            })

        }else{
            preload.classList.add('activate-preload');
            formData.append('marca', marca);

            fetch('./modelo/marcas/subir_marca.php', {
                method: 'POST',
                body: formData
            }).then(function (response) { 
                return response.json();
             }).then(function (data) { 
                 preload.classList.remove('activate-preload');
                 clearFormDataAndThumbnails();
                 if(data.type == 1){
 
                     $(".add-images").append('<div class="add-images">'+
                 '<img src="./src/img/add.png" style="width:40px"></img><br>'+
                 '<span class="span-add-image text-center">Agregar logo</span>'+
                 '</div>');
 
                 }
                 
                 document.getElementById('nombre-marca').value = "";
 
                 Swal.fire({
                     title: 'Marca agregada',
                     html: "<span>La marca se agregó con exito</span>",
                     icon: "success",
                     cancelButtonColor: '#00e059',
                     showConfirmButton: true,
                     confirmButtonText: 'Aceptar', 
                     cancelButtonColor:'#ff764d',
                 })
 
                 console.log(data);
              }).catch(function (err) { 
                  console.log(err);
               })

               
        }
          
        });


        

    
})();