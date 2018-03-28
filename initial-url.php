<?php
/*
    DATOS DE PRUEBA - TARJETA DE CREDITO
    Tarjeta VISA Nro 4051885600446623  //  123
    RUT 11.111.111-1  //  123
*/

// MEJORAR VALIDACIONES Y CONTROL DE ERRORES 

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
$tx_step = "Init";

/** Nombre de usuario o cliente en el sistema del comercio - UNIQUE*/
$username = "username";//$_POST["username"];

/** Dirección de correo electrónico registrada por el comercio  - UNIQUE*/
$email = $_GET["email"];//"username@allware.cl";

$urlReturn = $proyect_baseurl.$folder_url."/finish-inscription-url.php"; // URL de retorno 

try{
    /** Iniciamos Transaccion - webservice transbank*/
    $result = $webpay->getOneClickTransaction()->initInscription($username, $email, $urlReturn);
    $result = get_object_vars($result);
}catch(Exception $e){
    echo __LINE__.$e->getMessage();
    die();
}
    
/** Verificamos respuesta de inicio en webpay */
if (empty($result["token"])) {
    $message = "webpay no disponible"; 
    echo $message;
    die();
}


$token = $result["token"];
$next_page = $result["urlWebpay"];

/** Iniciamos Transaccion - clase negocio*/
$resp = $ClassNegocio::wpSaveCardToken($email, $token);
if(!$resp){
    echo "Error proceso de transaccion";
    die();

}

/** POST TO FINISH-INSCRIPTION-URL*/
$request = new Request();
$params = array('TBK_TOKEN' => $token);
$request->setParams($params);
$request->forward($next_page);


?>