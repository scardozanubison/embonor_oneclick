<?php
require_once('libwebpay/webpay.php');
require_once('certificates/cert-oneclick.php');
require_once('include/request.php');
require_once('include/negocio.php');

$ClassNegocio = new Negocio();


/** Configuracion parametros de la clase Webpay */
$configuration = new Configuration();
$configuration->setEnvironment($certificate['environment']);
$configuration->setCommerceCode($certificate['commerce_code']);
$configuration->setPrivateKey($certificate['private_key']);
$configuration->setPublicCert($certificate['public_cert']);
$configuration->setWebpayCert($certificate['webpay_cert']);

//Creacion Objeto Webpay 
$webpay = new Webpay($configuration);

//PASO A INSCRIPCION 
$tx_step = "Get Authorize";

//Setea Respuesta del comercio esperado
$com_respuesta = 0;

if (!isset($_REQUEST["TBK_TOKEN"]) || empty($_REQUEST["TBK_TOKEN"])){
    $message = "Ha ocurrido un error";
    echo $message;
    die();
}

$token = $_REQUEST["TBK_TOKEN"];
$username = "username";//$_REQUEST["username"]; - UNIQUE
$tbkUser = $token;

// Monto del pago en pesos 
$amount = intval($_REQUEST["amount"]);
        
// Identificador único de la compra generado por el comercio 
$buyOrder = rand();

$data = array($buyOrder, $tbkUser, $username, $amount); 
try{
    // Rescatamos resultado y datos de la transaccion 
    $result = $webpay->getOneClickTransaction()->authorize($buyOrder, $tbkUser, $username, $amount);
    $responseCode = $result->responseCode;
    $authorizationCode = $result->authorizationCode;
    $creditCardType = $result->creditCardType;
    $last4CardDigits = $result->last4CardDigits;
    $transactionId = $result->transactionId;

    /** Iniciamos Transaccion - clase negocio*/
    $resp = $ClassNegocio::wpTransactionOneClick($authorizationCode, $buyOrder, $responseCode, $creditCardType, $amount, $last4CardDigits , $transactionId);
    

}catch(Exception $e){
    $message = "Ha ocurrido un error". $e->getMessage();
    echo $message;
    die();
}

if ($responseCode != $com_respuesta) {
    $message = "Transaccion rechazada por webpay";
    echo $message;
    die();
}



/**
 * 
 * Comprobante de pago realizado y autorizado utilizando oneclick
 * 
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Prototipo - Autorizacion de pago OneClick</title>
</head>
    <!--<link rel="stylesheet" href="css/framework7.min.css">-->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/framework7/2.1.3/css/framework7.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/framework7/2.1.3/js/framework7.min.js"></script>
<body>
    <input type="hidden" name="hddtoken" id="hddtoken" value="<?php echo $token;?>">
    <input type="hidden" name="hddTransactionCode" id="hddTransactionCode" value="<?php echo $transactionId;?>">

    <div class="display-flex justify-content-center align-content-center">
        <div class="block" style="width:95%;background-color: #1777d8;border-radius: 3px;">
            <div class="row" align="center">
                <div class="col-100">
                    <h2 class="no-margin-bottom" style="color:white;"> El pago automatico con Oneclick se ha realizado correctamente</h2>
                </div>
            </div>
            <div class="row" align="center">
                <div class="col-100" style="color:white;">
                    <h3 class="no-margin-bottom"> Comprobante Oneclick - Webpay </h3>
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
        const hddTransactionCode = document.getElementById("hddTransactionCode").value;
        window.parent.postMessage("transactionId:" + hddTransactionCode, "*");
    }

</script>