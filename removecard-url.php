<?php


/**
 * 
 * DESVINCULACION DE TARJETA SEGUN EL TOKEN -tbkUser- CON TRANSBANK
 * adicionalmente se actualiza la columna estado de la tabla para deshabilitar la tarjeta dentro del sistema
 */

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

/** Creacion Objeto Webpay */
$webpay = new Webpay($configuration);

/** PASO A INSCRIPCION */
$tx_step = "Remove User Card";


if (!isset($_REQUEST["TBK_TOKEN"]) || empty($_REQUEST["TBK_TOKEN"])){
    $message = "Ha ocurrido un error";
    echo $message;
    die();
}

$token = $_REQUEST["TBK_TOKEN"];
$username = "username";//$_REQUEST["username"];//


try{
	/** Iniciamos Transaccion - webservice transbank*/
	$result = $webpay->getOneClickTransaction()->removeUser($token, $username);

	if($result){
		/** Iniciamos Transaccion - clase negocio*/
		$resp = $ClassNegocio::wpUpdateCardState($token, 0);
		echo $resp;
	}

}catch(Exception $e){
	$message = "Ha ocurrido un error". $e->getMessage();
    echo $message;
    die();
}

?>