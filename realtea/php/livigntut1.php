<?php  

include_once 'idents.php';

if(isset($_POST['login'], $_POST['mdp'])) # Si le formulaire est soumis
{
 if (!empty($_POST['login']) && !empty($_POST['mdp']))
 {

 $login = $_POST['login']; # Protection des Injection SQL ect...
 $mdp = sha1($_POST['mdp']);
 
 $login_q = $bdd->prepare("SELECT * FROM users WHERE login = :login AND pass = :mdp AND valid = '1'");
 $login_q->execute(array( 
	'login' => $login, 
	'mdp' => $mdp
 ));
 
 if($login_q->fetchColumn() > 0) # Si login pass et valid ok
 {
 $user = $login_q->fetch(PDO::FETCH_OBJ); 
 
 $token = sha1(uniqid().$user->id.sha1(uniqid()));
 setcookie('token', $token, time()+3600); # Création des cookies
 $user_id = $user->id;
 $setToken_q = $bdd->prepare("UPDATE users SET token = :token, token_date = NOW() WHERE id = :user_id");
 $setToken_q->execute(array( 
	'token' => $token, 
	'user_id' => $user_id
 ));
 $token = null;
 $_SESSION['login'] = $user->login; # Création des sessions
 
 header('Location: actu.php'); # Redirection
 
 exit();
 }
 else # Sinon
 {
 # Login ou Mot de passe incorrect
 }
 }
}
 
?>
<!DOCTYPE html>
<html lang="fr">
 <head>
 <meta charset="utf-8" />
 <title>Real Tea : Tutoriel pour Living Tuts - fr.livingtuts.com</title>
 
 <link rel="stylesheet" href="style.css" type="text/css" />
 
 <!--[if IE]>
 <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
 <!--[if lte IE 8]>
 <link rel="stylesheet" type="text/css" media="all" href="ie8.css"/><! [endif]-->
 <!--[if lte IE 7]>
 <link rel="stylesheet" type="text/css" media="all" href="ie7.css"/>
 <script src="js/IE8.js" type="text/javascript"></script><![endif]-->
 
 </head>
 
 <body>
 
 <!--[if IE]><div id="fond_ie"><! [endif]-->
 <nav id="menu_top">
 <ul>
 <li><a href="#">Actualités</a></li>
 <li><a href="#">Pages</a></li>
 <li><a id="deco" href="logout.php">Se déconnecter</a></li>
 </ul>
 </nav>
 <div></div>
 
 <div>
 
 <header id="header_top">
 <a id="logo" href="#">
 <h1><img src="images/logo.png" alt="Real Tea : société spécialisée dans le thé rare de qualité"/></h1>
 </a>
 
 </header>
 
 <section id="connexion">
 <form method="post" action="connexion.php">
 <label for="id">Identifiant</label>
 <input id="login" name="login" type="text" required /> <!-- Remplir ce champ est requis -->
 <br/>
 <label for="mdp">Mot de passe</label>
 <input id="mdp" name="mdp" type="password" required /> <!-- Remplir ce champ est requis -->
 <a href="#" id="pwd_oubli" >Mot de passe oublié ?</a>
 <input type="submit" value="Connexion">
 </form>
 </section>
 
 <div></div>
 
 <footer id="footer_site">
 <aside>
 <p>Retrouvez-nous aussi sur : </p><p style="float:left;"><a href="#"><img src="images/fb.png" alt="Joignez-nous sur Facebook"/></a> <a href="#"><img src="images/twitter.png" alt="Joignez-nous sur Twitter"/></a></p>
 </aside>
 
 <p id="copyright">© Gaétan Weltzer  Tous droits réservés - tutoriel pour Living Tuts : <a href="http://www.livingtuts.com">www.livingtuts.com</a></p>
 
 <div></div>
 </footer>
 
 </div>
 <!--[if lte IE 8]></div> <! [endif]-->
 </body>
</html>