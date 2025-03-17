
    <nav>
      <div id="sidebar-cart" class="sidebar-cart d-none">
        <div class="sidebar-content-cart p-3">
            <div class="row">
                <div class="col-10">
                    <h4><b>Productos preventa</b></h4>
                </div>
                <div class="col-12 col-md-2">
                        <h2 class="boton-close-preview-neumatico" onclick="mostrarSidebarCart()"><b><i class="fas fa-window-close"></i></b></h2>
                </div>
            </div>
          
          <div class="item-cart mt-2" id="productos-preventa">
                <div class="row mx-2">
                    <div class="row">
                        <div class="col-12 col-md-4">
                            <img src="./src/img/neumaticos/llanta_10_1.png" style="width:100px;" alt=""/>
                        </div>
                        <div class="col-12 col-md-8 p-2">
                            <span>LLANTA 33X12.50R15 DISCOVERER STT PRO 108Q Cooper</span>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12 col-md-5 text-center">
                            <input type="text" class="form-control d-none" value="$2,595">
                            <div class="row">
                                <div class="btn btn-warning mx-3" style="height: 95%;"><i class="fas fa-lock"></i></div>
                                 <h4 style="color: tomato; margin-top:.5rem"><b>$2,595</b></h4>
                            </div>
                        </div>
                        <div class="col-12 col-md-5">
                            <div style="display:flex">
                                <div class="btn btn-info" onclick="aumentarCantidad(0, 'cantidad_preventa', event)" style="border-radius:10px 0px 0px 10px !important;" ><b>-</b></div>
                                    <input type="number" id="cantidad_preventa" style="border-radius:0px !important;" class="form-control" placeholder="0" value="1">
                                <div class="btn btn-info"  onclick="aumentarCantidad(1, 'cantidad_preventa', event)" style="border-radius:0px 10px 10px 0px !important;"><b>+</b></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-2">
                            <h2 class="boton-close-preview-neumatico" alt="Borrar producto" onclick="borrarItem()"><b><i class="fas fa-trash"></i></b></h2>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
            <div id="area-importe-procesar">
            <div class="row pt-2">
                <div class="col-9 text-right">
                    <h4><b>Importe total:</b></h4>
                </div>
                <div class="col-3">
                <h4><b><span style="color:#faa300" id="importe-total"></span></b></h4>
                </div>
            </div>
            <div class="row mt-2 mb-2">
                <div class="col-12 text-right">
                    <div class="btn btn-warning mr-2" onclick="limpiarPreventa()">Limpiar</div>
                    <div class="btn btn-success" onclick="comprobacionConToken()">Procesar</div>
                </div>
            </div>
            </div>
            
        
        </div>
      </div>
    </nav>

    <style>
        #sidebar-cart{
            width: 30rem;
            height: 100vh;
            margin-top: -5rem;
            right:0px;
            background-color: white;
            border-left: 1px solid #e3e6f0;
            border-top: 1px solid #e3e6f0;
            position: fixed;
            z-index: 9999;
            /* animation: slideInRight; 
            animation-duration: .3s;  */
            /*animation: slideOutRight;*/
        }


        #productos-preventa{
            max-height: 80vh; 
            overflow-y: auto;
            margin-bottom: 1rem;
            padding-left: 1rem;
        }
        #productos-preventa::-webkit-scrollbar {
            display: none;
        }
        @media (max-width: 768px) {
        #sidebar-cart {
            width: 100%; /* Ocupa toda la pantalla */
            margin-top: 0; /* Ajuste de márgenes */
            border-left: none; /* Quita el borde izquierdo */
        }

        #productos-preventa {
            background-color: whitesmoke;
            max-height: 55vh; /* Reduce la altura para acomodar mejor */
            padding-left: 0.5rem; /* Reduce el padding */
        }
        #area-importe-procesar h4{
            font-size: 17px !important;
        }
    }
    </style>

    <script>
        let flag =0;
        function mostrarSidebarCart(){
            const r_sidebar = document.querySelector("#sidebar-cart")
            if(flag==0){
                r_sidebar.classList.remove('d-none')
                r_sidebar.classList.remove('animate__slideOutRight')
                r_sidebar.classList.add('animate__animated', 'animate__slideInRight')
                flag=1
                r_sidebar.style.animation = 'slideInRight'
                r_sidebar.style.animationDuration = '.3s'

                cargarPreventa()


            }else{
                r_sidebar.style.animation = 'slideOutRight'
                r_sidebar.style.animationDuration = '.3s'
                r_sidebar.classList.remove('animate__animated', 'animate__slideInRight')
                r_sidebar.classList.add('animate__animated', 'animate__slideOutRight')
                setTimeout(function(){
                    r_sidebar.classList.add('d-none')
                    r_sidebar.classList.remove('animate__animated', 'animate__slideOutRight')
                },280)
                flag=0
            }
            
        }


      
    </script>