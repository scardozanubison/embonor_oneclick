<?php
    require_once('settings.config.php');

    /**
     * 
     * Clase Conexion a la base de datos utilizando PDO
     * 
     */


class DBConnection{

    protected $_config;
    public $db;
    protected $settings;

    public function __construct() {
        $settings = new Settings();
        $config = $settings->getLocalSettings();
        
        $this->_config = $config;
        $this->getPDOConnection();
    }

    public function __destruct(){
        $this->db = NULL;
    }

    public function getPDOConnection(){
        if($this->db == NULL){
            $dsn = "".
                $this->_config["driver"].
                ":host=". $this->_config["host"].
                ";port=". $this->_config["port"].
                ";dbname=". $this->_config["dbname"];
            try{
                $this->db = new PDO($dsn, $this->_config["username"], $this->_config["password"]);
            }catch(PDOException $e){
                echo __LINE__.$e->getMessage();
            }
        }
    }

    public function runQuery($sql){
        try{
            $rowAffect = $this->db->exec($sql) or print_r($this->db->errorInfo());
        }catch(PDOException $e){
            echo __LINE__.$e->getMessage();
        }
        return $rowAffect;
    }

    public function getQuery($sql){
        try{
            $stmt = $this->db->query($sql);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
        }catch(PDOException $e){
            echo __LINE__.$e->getMessage();
        }
        return $stmt;
    }

    public function isConnected(){
        try{
            getPDOConnection();
            echo "Connected";
        }catch(Exception $e){
            echo "Unable to connect: " . $e->getMessage() ."<p>";
        }
    }


}

?>