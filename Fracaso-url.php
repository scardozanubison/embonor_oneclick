<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pagina de fracaso - OneClick</title>
</head>
    <link rel="stylesheet" href="css/framework7.min.css">
<body>

    <div class="display-flex justify-content-center align-content-center">
        <div class="block" style="width:95%;background-color: #1976d2;border-radius: 2px;">
            <div class="row" align="center">
                <div class="col-100">
                    <h2 class="no-margin-bottom" style="color:white;"> 
                        Transacci&oacute;n Fracasada
                    </h2>
                </div>
            </div>
            <div class="row" align="left">
                <div class="col-100" style="color:white;">
                    <h3 class="no-margin-bottom"> Las posibles causas de este rechazo son: </h3>
                </div>
            </div>
            <div class="row" style="background-color:white;margin-bottom:15px;margin-top: 15px">
                <div class="col-100" aling="center">
                    <div class="data-table">
                        <table>
                            <tbody>
                                <tr>
                                    <td class="label-cell">
                                        Error en el ingreso de los datos de su tarjeta de cr&eacute;dito o debito
                                        (fecha y/o c&oacute;digo de seguridad)           
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label-cell">
                                        Su tarjeta de cr&eacute;dito o debito no cuenta con el cupo necesario para cancelar la compra.
                                    </td>                               
                                </tr>
                                <tr>
                                    <td class="label-cell">
                                        Tarjeta a&uacute;n no habilitada en el sistema financiero.
                                    </td>                               
                                </tr>                         
                            </tbody>
                        </table>
                    </div>  
                </div>
            </div>
        </div>  
    </div>

</body>
</html>
<script src="js/framework7.min.js"></script>
<script>
    var app = new Framework7({});
</script>