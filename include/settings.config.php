
<?php

    /**
     * 
     * Clase que contiene los parametros de conexion a la base de datos
     * actualmente se cuenta con la conexion al servidor mysql local y de nubisonlab
     * 
     * 
     * Se requiere que el usuario de la base de datos cuente con contraseña, debido a que
     * PDO necesita de ese parametro para establecer la conexion
     * 
     */

    class Settings{

        public function __construct() {
            
        }

        public function getLocalSettings(){
            $localhost = array(
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'port' =>'3306',
                'dbname' => 'db_facturas_embonor',
                'username' => 'webpay',
                'password' => 'nbsn'
            );

            return $localhost;
        }

        public function getNubisonLabSettings(){
            $nubison_lab = array(
                'driver' => 'mysql',
                'host' => 'http://nubisonlab.cl/',
                'port' =>'1581',
                'dbname' => 'db_facturas_embonor',
                'username' => 'root',
                'password' => ''
            );

            return $nubison_lab;
        }

    }

    

    

?>