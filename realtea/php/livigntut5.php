<?php 

include_once 'idents.php';

if(isset($_POST['email'])) # Si le formulaire de newsletter est soumis
{
 if (!empty($_POST['email']))
 {
 $email = $_POST['email']; # Protection des Injection SQL ect...
 $insert_q = $bdd->prepare("INSERT INTO newsletter VALUES('', :email, '1')"); # Insertion de l'adresse email dans la table
 $insert_q->execute(array( 
	'email' => $email
 ));
 }
}
?>