<style>
    #contenedor-loader{
        background-color: rgb(1,1,1,0.75);
        position: absolute;
        width: 100%;
        height: 100%;
        z-index: 999999;
        overflow: hidden;
    }

        .loader-principal{
            width: 30rem;
            position:absolute;
            right: 50%;
            left: 44%;
            top: 30%;
            bottom: 50%;
            z-index: 999999;
        }

        .span-text-loader{
            color: white;
            font-size: larger;
            font-weight: bolder;
            z-index: 9999;
            position: absolute;
            right: 50%;
            left: 46%;
            top: 52%;
            bottom: 50%;
        }

        .dots{
            position: relative;
            right: 50%;
            left: 49.8%;
            top: 53%;
            bottom: 50%;
        }

        .dot {
            display: block;
            top: 4px;
            position: relative;
            transition: all 0.35s;
            color: white;
            font-size: 7px;
            float: left;
            animation: wave 1.3s linear infinite;
            &:nth-child(2) {
                    animation-delay: -1.1s;
                }

                &:nth-child(3) {
                    animation-delay: -0.9s;
                }
            }

            @keyframes wave {
                0%, 60%, 100% {
                    transform: initial;
                }

                30% {
                    transform: translateY(-15px);
                }
            }

        @media ( (max-width: 1900px) ){
            .loader-principal{
            width: auto;

            position:absolute;
            right: 50%;
            left: 45%;
            top: 30%;
            bottom: 50%;
            z-index: 999999;
        }

        .dots{
            position: relative;
            right: 50%;
            left: 54%;
            top: 57%;
            bottom: 50%;
        }

        .span-text-loader{
            color: white;
            font-size: larger;
            font-weight: bolder;
            z-index: 9999;
            position: absolute;
            right: 50%;
            left: 49%;
            top: 56%;
            bottom: 50%;
        }

        }
    </style>

<div id="contenedor-loader" class="d-none">
                <div class="option-card text-center loader-principal">
                    <lottie-player src="https://lottie.host/9ff4fc94-43ef-467b-aaf1-dafe7abaf53b/GFRYdl5GrJ.json" background="transparent" speed="1" style="width: 250px; height: 250px" loop autoplay></lottie-player>
                </div>  
                <span class="span-text-loader">Cargando
                </span>
                <div class="dots">
                        <div class="dot">&#9679;</div>
                        <div class="dot">&#9679;</div>
                        <div class="dot">&#9679;</div>
                    </div>
</div>