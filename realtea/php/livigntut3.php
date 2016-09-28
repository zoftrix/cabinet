<?php 

include_once 'idents.php';

date_default_timezone_set('Europe/Paris'); # Définition du Time Zone
 
$token = '';
if(isset($_COOKIE['token']) && !empty($_COOKIE['token']))
	$token = $_COOKIE['token']; # Récupération des Cookies & Ou Sessions

 $token = $token; # Protection des Injection SQL ect...
 $login_q = $bdd->prepare("SELECT * FROM users WHERE token = :token AND valid = '1'");
 $login_q->execute(array( 
	'token' => $token
 ));
 
if($login_q->fetchColumn() == 0) # Si login pass et valid nok
{
 header('Location: connexion.php'); # Redirection
 
 exit();
}
 
if(isset($_GET['delete'])) # Si le paramètre delete est spécifié dans l'URL
{
 if (!empty($_GET['delete']))
 {
 $id = $_GET['delete']; # Protection des Injection SQL ect...
 $delete_q = $bdd->prepare("UPDATE pages SET valid = '0' WHERE id = :id");
 $delete_q->execute(array( 
	'id' => $id
 ));
 }
}
 
if(isset($_POST['titre'], $_POST['desc'])) # Création d'une page
{
 if (!empty($_POST['titre']) && !empty($_POST['desc']))
 {
 $title = $_POST['titre']; # Protection des Injection SQL ect...
 $text = $_POST['desc'];
 
 $insert_q = $bdd->prepare("INSERT INTO pages VALUES ('', :title, :text, '1')");
 $insert_q->execute(array( 
	'title' => $title,
	'text' => $text
 ));
 }
}
 
$pages = array();
 
$selectAll_q = $bdd->prepare("SELECT * FROM pages WHERE valid = '1'"); # Selection de toutes les pages valides
$selectAll_q->execute();

while($result = $selectAll_q->fetch(PDO::FETCH_ASSOC))
{
 $pages[] = $result; # Replissage du tableau avec ces valeurs
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
 <li><a href="actu.php">Actualités</a></li>
 <li><a href="page.php">Pages</a></li>
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
 
 <section id="actu">
 <header>
 <h2>Ajouter / Supprimer une page</h2>
 </header>
 
 <p>Ici vous pouvez ajouter ou supprimer une page. Celle-ci apparaîtra automatiquement sur la page d'accueil et pourra ensuite être lue en entier lorsque le visiteur clique dessus.</p>
 
 <form method="post" action="page.php">
 <label for="titre">Titre de la page</label>
 <input id="titre" name="titre" type="text" required /> <!-- Remplir ce champ est requis -->
 <br/>
 <label for="desc">Description <em>(limité à 1000 caractères - il est conseillé de rester synthétique dans la description)</em></label>
 <textarea id="desc" name="desc"></textarea> <!-- Remplir ce champ est requis -->
 <br/>
 <input type="submit" value="Créer la page">
 </form>
 
 <header id="header_liste_actu">
 <h2>Pages déja crées</h2>
 </header>
 
 <!-- boucle affichant les dernières actus -->
 <ul id="news">
 <?php
 foreach ($pages as $page) # Boucle qui affiche les pages
 {
 echo '
 <li>
 <article>
 <header>
 <h3><a href="#">'.$page['title'].'</a></h3>
 </header>
 
 <div>
 <p>'.$page['text'].'</p>
 <div>
 <p><a href="#"> Voir la page</a></p>
 <p><a href="actu.php?delete='.$page['id'].'">Supprimer</a></p>
 </div>
 </div><!-- /.entry-content -->
 
 </article>
 </li>';
 }
 
 ?>
 </ul>
 
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