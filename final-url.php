<?php
require_once('include/negocio.php');


/**
 * 
 * PAGINA DE TRANSACCION EXITOSA
 * REGISTRO Y VINCULACION DE TARJETA UTILIZANDO ONE-CLICK
 * 
 */


$TBK_TOKEN = $_REQUEST["TBK_TOKEN"];
$ClassNegocio = new Negocio();


try{
    $result = $ClassNegocio::wpSelectCard($TBK_TOKEN);
}catch(Exception $e){
    echo __LINE__.$e->getMessage();
    die();
}


$tbk_codigo_autorizacion = $result["authCode"];
$tbk_credit_type = $result["creditCardType"];
$tbk_card_digits = $result["last4CardDigits"];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Vinculacion de tarjeta - OneClick</title>
</head>
    <link rel="stylesheet" href="css/framework7.min.css">
<body>
    <input type="hidden" name="hddtoken" id="hddtoken" value="<?php echo $TBK_TOKEN;?>">

    <div class="display-flex justify-content-center align-content-center">
        <div class="block" style="width:95%;background-color: #1777d8;border-radius: 3px;">
            <div class="row" align="center">
                <div class="col-100">
                    <h2 class="no-margin-bottom" style="color:white;"> Gracias por preferirnos<b style="color:red">!</b> </h2>
                </div>
            </div>
            <div class="row" align="center">
                <div class="col-100" style="color:white;">
                    <h3 class="no-margin-bottom"> Se ha vinculado correctamente tu tarjeta de credito con OneClick </h3>
                </div>
            </div>
            <div class="row" style="background-color:white;margin-bottom:15px;margin-top: 15px">
                <div class="col-100" aling="center">
                    <div class="data-table">
                        <table>
                            <tbody>
                                <tr>
                                    <td class="label-cell">C&oacute;digo de Autorizaci&oacute;n: &nbsp;<?php echo $tbk_codigo_autorizacion; ?></td>
                                </tr>
                                <tr>
                                    <td class="label-cell">Tipo de Tarjeta: &nbsp;<?php echo  $tbk_credit_type?></td>                               
                                </tr>
                                <tr>
                                    <td class="label-cell">Tarjeta: &nbsp;<?php echo "XXXXXXXXXXXXXX".$tbk_card_digits; ?></td>                               
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
<script src="js/framework7.min.js"></script>
<script>
    var app = new Framework7({});
    function finishProcess(){
        //Funcion que notifica al elementro padre del token generado 
        //tbkUser
        const token = document.getElementById("hddtoken").value;
        window.parent.postMessage("token:" + token, "*");
    }
    

</script>