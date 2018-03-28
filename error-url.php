<?php

	
$origen = isset($_REQUEST["origen"])? $_REQUEST["origen"]: "template";
$responseCode = isset($_REQUEST["responseCode"])? $_REQUEST["responseCode"]: "0";

echo $origen;
echo $responseCode;
die();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pagina de error - Webpay</title>
</head>
    <!--<link rel="stylesheet" href="css/framework7.min.css">-->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/framework7/2.1.3/css/framework7.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/framework7/2.1.3/js/framework7.min.js"></script>
<body>
    <input type="hidden" name="hddtoken" id="hddtoken" value="<?php echo $token;?>">

    <div class="display-flex justify-content-center align-content-center">
        <div class="block" style="width:95%;background-color: #1777d8;border-radius: 3px;">
            <div class="row" align="center">
                <div class="col-100">
                    <h2 class="no-margin-bottom" style="color:white;"> Se ha presentado un inconveniente</h2>
                </div>
            </div>
            <div class="row" align="center">
                <div class="col-100" style="color:white;">
                    <h3 class="no-margin-bottom"></h3>
                </div>
            </div>
            <div class="row" align="center">
                <div class="col-100 ">
                    <h5 class="no-margin-top" style="color:white;"> 
                        Estimado cliente, se ha realizado de manera satisfactoria
                        el pago de la boleta n&uacute;mero <b><?php echo $buyOrder; ?></b>
                        por un valor de <b>$ <?php echo number_format($amount,0,',','.'); ?> pesos,
                        </b> el cual ha sido cargado a su tarjeta bancaria.
                    </h5>
                </div>
            </div>  
            <div class="row" style="background-color:white;margin-bottom:15px;">
                <div class="col-100" aling="center">
                    <div class="data-table">
                        <table>
                            <tbody>
                                <tr>
                                    <td class="label-cell">C&oacute;digo de Autorizaci&oacute;n: &nbsp;<?php echo $authorizationCode; ?></td>                               
                                </tr>
                                <tr>
                                    <td class="label-cell">Orden de Compra: &nbsp;<?php echo $buyOrder; ?></td>                               
                                </tr>
                                <tr>
                                    <td class="label-cell">C&oacute;digo de respuesta: &nbsp;<?php echo $responseCode; ?></td>                               
                                </tr>
                                <tr>
                                    <td class="label-cell">Tarjeta de credito: &nbsp;<?php echo $creditCardType; ?></td>                               
                                </tr>
                                <tr>
                                    <td class="label-cell">Monto Pagado: &nbsp;<?php echo number_format($amount,0,',','.'); ?></td>                               
                                </tr>
                                <tr>
                                    <td class="label-cell">Tarjeta Bancaria: &nbsp;**************<?php echo $last4CardDigits; ?></td>                               
                                </tr>
                                <tr>
                                    <td class="label-cell">C&oacute;digo de transacci&oacute;n: &nbsp;<?php echo $transactionId; ?></td>                               
                                </tr>                          
                            </tbody>
                        </table>
                    </div>  
                </div>
            </div>
            
            <div class="row" style="width:40%;float: right;margin-bottom:25px;">
                <div class="col-100" style="background-color:white;">
                    <button class="col button button-outline" onclick="finishProcess()">Terminar</button>
                </div>
            </div>
        </div>  
    </div>

</body>
</html>

<script>
    var app = new Framework7();
    function finishProcess(){
        const token = document.getElementById("hddtoken").value;
        //window.parent.postMessage("token:" + token, "autorizeTransaction", false);
        window.parent.postMessage("token:" + token, "autorizeTransaction", false);
    }

</script>