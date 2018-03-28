<?php
ini_set('display_errors', 'off');
require_once('conexion.php');

$con = new DBConnection();


try{


  $query = "call sp_WP_TRANSACCION('".uniqid()."',
  '654424567',
  'TR_NORMAL_WS',
  0,
  '1213',
  '309970',
  '6623',
  '0320',
  '2018-03-20T16:50:58.722-04:00',
  'VN',
  0);";

  $db = $con->db;
  echo $query;
  $stmt = $db->prepare($query);

  $result = $stmt->execute();
  echo $result;
}catch(Exception $e){
  echo $e->getMessage();
}

/*$host = "127.0.0.1";
$user = "root";
$pass = "nbsn";
$db = "nubison_ventas";

$cursor = "cr_123456";

try
{
  //$dbh = new PDO("mysql:host=$host;port=3306;dbname=$db;user=$user;password=$pass");
  $dbh = new PDO("mysql:host=$host;port=3306;dbname=$db;", $user, $pass);
  echo "Connected<p>";
}
catch (Exception $e)
{
  echo "Unable to connect: " . $e->getMessage() ."<p>";
}
*/

?>