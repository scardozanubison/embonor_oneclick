<h1>Ejemplos Webpay - Transaccion OneClick</h1>
<?php

/**
 * @author     Allware Ltda. (http://www.allware.cl)
 * @copyright  2015 Transbank S.A. (http://www.tranbank.cl)
 * @date       Jan 2015
 * @license    GNU LGPL
 * @version    2.0.2
 */

require_once('libwebpay/webpay.php');
require_once('certificates/cert-oneclick.php');

/** Configuracion parametros de la clase Webpay */
$sample_baseurl = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];

$configuration = new Configuration();
$configuration->setEnvironment($certificate['environment']);
$configuration->setCommerceCode($certificate['commerce_code']);
$configuration->setPrivateKey($certificate['private_key']);
$configuration->setPublicCert($certificate['public_cert']);
$configuration->setWebpayCert($certificate['webpay_cert']);

/** Creacion Objeto Webpay */
$webpay = new Webpay($configuration);  // Crea objeto WebPay

$action = isset($_GET["action"]) ? $_GET["action"] : 'init';

/** Nombre de usuario o cliente en el sistema del comercio */
$username = "username";

/** Dirección de correo electrónico registrada por el comercio */
$email = "username@allware.cl";

$post_array = false;

switch ($action) {

    default:
        $tx_step = "Init";

        $urlReturn = $sample_baseurl . "?action=OneClickFinishInscription";

        $request = array(
            "username" => $username,
            "email" => $email,
            "urlReturn" => $urlReturn
        );

        /** Iniciamos Transaccion */
        $result = $webpay->getOneClickTransaction()->initInscription($username, $email, $urlReturn);
        $result = get_object_vars($result);

        /** Verificamos respuesta de inicio en webpay */
        if (!empty($result["token"])) {
            $message = "Sesion iniciada con exito en Webpay";
            $token = $result["token"];
            $next_page = $result["urlWebpay"];
        } else {
            $message = "webpay no disponible";
        }

        break;

    case "OneClickFinishInscription":

        $post_array = false;
        
        $tx_step = "Get FinishInscription";

        if (!isset($_POST["TBK_TOKEN"]))
            break;

        /** Identificador del proceso de inscripción, entregado por Webpay en el método initInscription */
        $token = filter_input(INPUT_POST, 'TBK_TOKEN');

        $request = array(
            "token" => filter_input(INPUT_POST, 'TBK_TOKEN'),
        );

        /** Rescatamos resultado y datos de la transaccion */
        $result = $webpay->getOneClickTransaction()->finishInscription($token);

        $responseCode = $result->responseCode;
        $tbkUser = $result->tbkUser;

        if ($responseCode != 0) {

            $message = "Transacci&oacute;n RECHAZADO por webpay";
            $next_page = "";
            
        } else {
            $message = "Transacci&oacute;n ACEPTADA por webpay";
            $next_page = $sample_baseurl . "?action=OneClickAuthorize";

            $token = $tbkUser;
        }

        break;

    case "OneClickAuthorize":

        $tx_step = "Get Authorize";

        if (!isset($_POST["TBK_TOKEN"]))
            break;

        /** Identificador único de la inscripción del cliente */
        $tbkUser = filter_input(INPUT_POST, 'TBK_TOKEN');

        /** Monto del pago en pesos */
        $amount = 9200;
        
        /** Identificador único de la compra generado por el comercio */
        $buyOrder = rand();

        $request = array(
            "buyOrder" => $buyOrder,
            "tbkUser" => $tbkUser,
            "username" => $username,
            "amount" => $amount,
        );

        /** Rescatamos resultado y datos de la transaccion */
        $result = $webpay->getOneClickTransaction()->authorize($buyOrder, $tbkUser, $username, $amount);

        $responseCode = $result->responseCode;

        if ($responseCode != 0) {
            $message = "Transacci&oacute;n RECHAZADO por webpay";
            $next_page = "";
        } else {
            $message = "Transacci&oacute;n ACEPTADA por webpay";
            $next_page = $sample_baseurl . "?action=OneClickReverse";
            $token = $buyOrder;

        }

        break;

    case "OneClickReverse":

        $tx_step = "Get reverse";

        if (!isset($_POST["TBK_TOKEN"]))
            break;
        
        /** Identificador único de la compra generado por el comercio */
        $buyOrder = filter_input(INPUT_POST, 'TBK_TOKEN');

        $request = array(
            "buyOrder" => $buyOrder,
        );

        /** Rescatamos resultado y datos de la transaccion */
        $result = $webpay->getOneClickTransaction()->reverseTransaction($buyOrder);

        $responseMessage = $result->reversed;

        if (!$responseMessage) {
            $message = "Transacci&oacute;n RECHAZADO por webpay";
            $next_page = "";
        } else {
            $message = "Transacci&oacute;n ACEPTADA por webpay";
            $next_page = $sample_baseurl . "?action=OneClickFinal";
            
            $tbkUser = filter_input(INPUT_POST, 'username');
        }

        break;

    case "OneClickFinal":

        $tx_step = "Get removeUser";

        /** Identificador único de la inscripción del cliente */
        $tbkUser = filter_input(INPUT_POST, 'username');

        $request = array(
            "tbkUser" => $tbkUser,
            "commerceUser" => $username,
        );

        /** Rescatamos resultado y datos de la transaccion */
        $result = $webpay->getOneClickTransaction()->removeUser($tbkUser, $username);

        if (!$result) {
            $message = "Transacci&oacute;n RECHAZADO por webpay";
            $next_page = "";
        } else {
            $message = "Transacci&oacute;n ACEPTADA por webpay";
            $next_page = "";
        }

        session_unset();

        break;
}

echo "<h2>Step: " . $tx_step . "</h2>";

if (!isset($request) || !isset($result) || !isset($message) || !isset($next_page)) {

    $result = "Ocurri&oacute; un error al procesar tu solicitud";
    echo "<div style = 'background-color:lightgrey;'><h3>result</h3>$result;</div><br/><br/>";
    echo "<a href='.'>&laquo; volver a index</a>";
    die;
}
?>

<div style="background-color:lightyellow;">
    <h3>request</h3>
    <?php var_dump($request); ?>
</div>
<div style="background-color:lightgrey;">
    <h3>result</h3>
    <?php var_dump($result); ?>
</div>
<p><samp><?php echo $message; ?></samp></p>
<?php if (strlen($next_page)) { ?>
    <form action="<?php echo $next_page; ?>" method="post">
        <input type="hidden" name="TBK_TOKEN" value="<?php echo $token; ?>">
        <input type="hidden" name="username" value="<?php echo $tbkUser; ?>">
        <input type="submit" value="Continuar &raquo;">
    </form>
<?php } ?>
<br>
<a href=".">&laquo; volver a index</a>