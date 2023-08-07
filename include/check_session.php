<?php
if (!isset($_SESSION))
{
  session_start();
}

if ( ! (isset($_SESSION["current_user"]) ) ){
  $_SESSION["message"]="Session not valid. Please login.";

  header('Location: ./index.php');
  exit;
}

?>

