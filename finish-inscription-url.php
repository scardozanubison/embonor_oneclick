<?php

require_once('libwebpay/webpay.php');
require_once('certificates/cert-oneclick.php');
require_once('include/request.php');
require_once('include/negocio.php');

$ClassNegocio = new Negocio();

/** Configuracion de la ruta de los archivos */
$proyect_baseurl = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER["HTTP_HOST"];
$sample_baseurl = $proyect_baseurl . $_SERVER['PHP_SELF'];
$folder_url = "/embonor_oneclick";

/** Configuracion parametros de la clase Webpay */
$configuration = new Configuration();
$configuration->setEnvironment($certificate['environment']);
$configuration->setCommerceCode($certificate['commerce_code']);
$configuration->setPrivateKey($certificate['private_key']);
$configuration->setPublicCert($certificate['public_cert']);
$configuration->setWebpayCert($certificate['webpay_cert']);

/** Creacion Objeto Webpay */
$webpay = new Webpay($configuration);

/** PASO A INSCRIPCION */
$tx_step = "Get Finish Inscription";

#Setea Respuesta del comercio esperado
$com_respuesta = 0;

if (!isset($_REQUEST["TBK_TOKEN"]) || empty($_REQUEST["TBK_TOKEN"])){
    $message = "Ha ocurrido un error";
    echo $message;
    die();
}

$token = $_REQUEST["TBK_TOKEN"];
try{
    /** Rescatamos resultado y datos de la transaccion */
    $result = $webpay->getOneClickTransaction()->finishInscription($token);
    $authCode = $result->authCode;
    $creditCardType = $result->creditCardType;
    $last4CardDigits = $result->last4CardDigits;
    $responseCode = $result->responseCode;
    $tbkUser = $result->tbkUser;

}catch(Exception $e){
    $message = "Ha ocurrido un error". $e->getMessage();
    echo $message;
    die();
}


if ($responseCode != $com_respuesta) {
    $message = "Transacci&oacute;n rechazada por webpay";
    echo $message;
    die();
}

/** Iniciamos Transaccion - clase negocio*/
$resp = $ClassNegocio::wpSaveCard($token, $authCode, $creditCardType, $last4CardDigits, $tbkUser);
if(!$resp){
    echo "Error proceso de transaccion";
    die();

}


/** POST TO FINAL-URL*/
$next_page = $proyect_baseurl.$folder_url."/final-url.php"; // URL de transicion 
$request = new Request();
$params = array('TBK_TOKEN' => $token);
$request->setParams($params);
$request->forward($next_page, $wait = true);


?>

