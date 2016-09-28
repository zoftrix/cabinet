<?php

include_once 'idents.php';
  
$token = '';
if(isset($_COOKIE['token']) && !empty($_COOKIE['token']))
	$token = $_COOKIE['token']; # Récupération des Cookies & Ou Sessions

$insert_q = $bdd->prepare("UPDATE users SET token = '', token_date = '' WHERE token = :token AND valid = '1'"); # Insertion de l'adresse email dans la table
$insert_q->execute(array( 
	'token' => $token
)); 


session_destroy(); 
$_SESSION = array();

setcookie('token', '', time()-3600); # Destruction des cookie

header('Location: connexion.php'); # Redirection



?>