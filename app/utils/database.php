<?php

function get_mysql_connection(){
  $server = "localhost";
  $user = "root";
  $pass = "";
  $db = "La_Lico";
  $mysqli = new mysqli($server, $user, $pass, $db);
  return $mysqli;
}
?>
