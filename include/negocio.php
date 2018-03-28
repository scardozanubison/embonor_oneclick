<?php

    require_once('conexion.php');

    /**¨
     * 
     * Clase con funciones estaticas que permite las transacciones realizadas con webpay normal y webpay oneclick
     * 
     * 
     */

    class Negocio{

        /** METODO WEBPAY NORMAL - GUARDAR TRANSACCION */
        static function wpTransaction($glSessionId,$cdOrdenCompra,$glTpTransaccion,$cdRespTbk,$cdAutorizacionTbk,$nrMonto,$nrTarjeta,$fcContableTbk,$fcTransaccionTbk,$cdTipoPago,$nrCuotas){
            $result = false;
            $database = new DBConnection();

            try{
                $stmt = $database->db->prepare("call sp_WP_TRANSACCION(?,?,?,?,?,?,?,?,?,?,?)");
                $result = $stmt->execute(array(
                    $glSessionId,$cdOrdenCompra,
                    $glTpTransaccion,$cdRespTbk,
                    $cdAutorizacionTbk,$nrMonto,
                    $nrTarjeta,$fcContableTbk,
                    $fcTransaccionTbk,$cdTipoPago,
                    $nrCuotas
                ));
                

            }catch(Exception $e){
                print_r($e->getMessage());
                
            }

            return $result;
            
        }

        /** METODO WEBPAY NORMAL - OBTENER TRANSACCION POR ID UNICO*/
        static function getWpTransaction($idSession){
            $arr = array();
            $database = new DBConnection();

            $query = "   select glSessionId,
            cdOrdenCompra,
            glTpTransaccion,
            cdRespTbk,
            cdAutorizacionTbk,
            nrMonto,
            nrTarjeta,
            fcContableTbk,
            fcTransaccionTbk,
            cdTipoPago,
            nrCuotas 
                from WP_TRANSACCION 
                where glSessionId = ?;"; 

            try{
                $stmt = $database->db->prepare($query);
                $stmt->execute(array($idSession));

                if($stmt){
                    while($fila = $stmt->fetch()){
                        $arr = $fila;
                    }
                }

            }catch(Exception $e){
                print_r($e->getMessage());
            }
            return $arr;
        }




        /** METODO WEBPAY ONECLICK - GUARDAR TOKEN GENERADO POR EL USUARIO*/
        static function wpSaveCardToken($email, $token){
            $result = false;
            $database = new DBConnection();

            try{
                //$stmt = $database->db->prepare("call sp_wpSaveCardToken(?,?)");
                $stmt = $database->db->prepare("insert into wp_cards_tokens(guid,token) values((select guid from websecurityusers where userid= ? ), ?);");
                $result = $stmt->execute(array($email, $token));

            }catch(Exception $e){
                print_r($e->getMessage());
                
            }
            return $result;
        }

        /** METODO WEBPAY ONECLICK - GUARDAR DATOS DE LA TARJETA */
        static function wpSaveCard($token, $authCode, $creditCardType, $last4CardDigits, $tbkUser){
            $result = false;
            $database = new DBConnection();

            try{
                //$stmt = $database->db->prepare("call sp_wpSaveCard(?,?,?,?,?)");
                $stmt = $database->db->prepare("insert into wp_cards(id_card_token, authCode, creditCardType, last4CardDigits, tbkUser)
                        values((select id_card_token from wp_cards_tokens where token = ?), ?, ?, ?, ?);");
                $result = $stmt->execute(array($token,
                 $authCode, 
                 $creditCardType, 
                 $last4CardDigits, 
                 $tbkUser));

            }catch(Exception $e){
                print_r($e->getMessage());
                
            }
            return $result;
        }

        /** METODO WEBPAY ONECLICK - OBTENER DATOS DE LA TARJETA SEGUN EL TOKEN*/
        static function wpSelectCard($token){
            $arr = array();
            $database = new DBConnection();

            $query = "select 
                    card.authCode, 
                    card.creditCardType, 
                    card.last4CardDigits 
                    from wp_cards card
                    inner join wp_cards_tokens tok on tok.id_card_token = card.id_card_token 
                    where tok.token = ?;";


            try{
                $stmt = $database->db->prepare($query);
                $stmt->execute(array($token));

                if($stmt){
                    while($fila = $stmt->fetch()){
                        $arr = $fila;
                    }
                }

            }catch(Exception $e){
                print_r($e->getMessage());
            }
            return $arr;
        }

        /** METODO WEBPAY ONECLICK - ACTUALIZAR ESTADO PARA DESHABILITAR O HABILITAR LA TARJETA PARA TRANSACCIONES */
        static function wpUpdateCardState($token, $state){
            $result = false;
            $database = new DBConnection();

            $query = "update wp_cards set estado = ? where id_card >= 1 and tbkUser = ?;";
            try{
                $stmt = $database->db->prepare($query);
                $result = $stmt->execute(array($state, $token));

            }catch(Exception $e){
                print_r($e->getMessage());
                
            }
            return $result;
        }

        /** METODO WEBPAY ONECLICK - GUARDAR TRANSACCION REALIZADA AL SELECCIONAR LA TARJETA*/
        static function wpTransactionOneClick($authCode, $buyOrder, $reponseCode, $cardType, $amount, $last4Digits, $transactionCode){
            $result = false;
            $database = new DBConnection();
            $query = "insert into wp_trasaccion_oneclick(authCode,buyOrder,reponseCode,cardType,amount,last4Digits,transactionCode)values(?,?,?,?,?,?,?);";
            try{
                $stmt = $database->db->prepare($query);
                $result = $stmt->execute(array($authCode, $buyOrder, $reponseCode, $cardType, $amount, $last4Digits, $transactionCode));
            }catch(Exception $e){
                print_r($e->getMessage());
            }
            return $result;
        }


        /** METODO QUE DESPLIEGA LA PAGINA DE FRACASO AL OCURRIR ALGUN ERROR EN AMBOS PROCESOS(NORMAL O ONECLICK)*/
        static function throwException($folder_url, $ex){
            #En caso de alguna excepción , redirige a la página de fracaso
            $request = new Request();
            $request->forward($folder_url."/Fracaso-url.php");
        }

    }

?>