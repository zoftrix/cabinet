<?php 

include_once 'idents.php';

date_default_timezone_set('Europe/Paris'); # D�finition du Time Zone
 
$token = '';
if(isset($_COOKIE['token']) && !empty($_COOKIE['token']))
	$token = $_COOKIE['token']; # R�cup�ration des Cookies & Ou Sessions

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
 
if(isset($_GET['delete']) && !empty($_GET['delete'])) # Si le param�tre delete est sp�cifi� dans l'URL
{
 $id = $_GET['delete']; # Protection des Injection SQL ect...
 $delete_q = $bdd->prepare("UPDATE news SET valid = '0' WHERE id = :id");
 $delete_q->execute(array( 
	'id' => $id
 ));
}
 
if(isset($_POST['date1'], $_POST['titre'], $_POST['desc'])) # Cr�ation d'une news
{
	if(!empty($_POST['date1']) && !empty($_POST['titre']) && !empty($_POST['desc']))
	{
	 $date = date('Y-m-d H:i:s', $_POST['date1']); # Protection des Injection SQL ect...
	 $title = $_POST['titre'];
	 $text =$_POST['desc'];
	 
	 $insert_q = $bdd->prepare("INSERT INTO news VALUES ('', :title, :text, :date, '1')"); # Insertion dans la table d'une nouvelle news
	 $insert_q->execute(array( 
		'text' => $text,
		'title' => $title,
		'date' => $date
	 ));
	}
}
 
$news = array();
 
$selectAll_q = $bdd->prepare("SELECT id, title, text, DATE_FORMAT(date, '%b') AS date_b, DATE_FORMAT(date, '%d') AS date_d, valid FROM news WHERE valid = '1'"); # Selection de toutes les news valides
$selectAll_q->execute();

while($result = $selectAll_q->fetch(PDO::FETCH_ASSOC))
{
 $news[] = $result; # Replissage du tableau avec ces valeurs
}

?>
<!DOCTYPE html>
<html lang="fr">
 <head>
 <meta charset="utf-8" />
 <title>Real Tea : Tutoriel pour Living Tuts - fr.livingtuts.com</title>
 
 <link rel="stylesheet" href="style.css" type="text/css" />
 <link rel="stylesheet" type="text/css" href="inc/css/calendar-eightysix-v1.1-vista.css" media="screen" /> <!-- Template du calendar JS -->
 
 <!--[if IE]>
 <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
 <!--[if lte IE 8]>
 <link rel="stylesheet" type="text/css" media="all" href="ie8.css"/><! [endif]-->
 <!--[if lte IE 7]>
 <link rel="stylesheet" type="text/css" media="all" href="ie7.css"/>
 <script src="js/IE8.js" type="text/javascript"></script><![endif]-->
 
 <script type="text/javascript" src="inc/js/mootools-1.2.4-core.js"></script>  <!-- Frameworks JS Mootools Core -->
 <script type="text/javascript" src="inc/js/mootools-1.2.4.4-more.js"></script>  <!-- Frameworks JS Mootools More -->
 <script type="text/javascript" src="inc/js/calendar-eightysix-v1.1.js"></script>  <!-- Plugin Mootools Calendar Eightysix -->
 
 <script type="text/javascript">
 
 // Tr�s bonne doc du calendar : http://dev.base86.com/scripts/mootools_javascript_datepicker_calendar_eightysix.html
 
 window.addEvent('domready', function()
 {
 MooTools.lang.set('fr-FR', 'Date', {
 months: ['Janvier', 'F�vri�', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Ao�t', 'Septembre', 'Octobre', 'Novembre', 'Decembre'],
 days: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
 dateOrder: ['date', 'month', 'year', '/']
 });
 
 MooTools.lang.setLanguage('fr-FR');
 
 new CalendarEightysix('date1',
 {
 'theme': 'vista',
 'defaultView': 'day',
 'startMonday': true,
 'format': '%d/%m/%Y',
 'createHiddenInput': true,
 'hiddenInputName': 'date2',
 'disallowUserInput': true
 });
 });
 
 </script>
 
 </head>
 
 <body>
 
 <!--[if IE]><div id="fond_ie"><! [endif]-->
 <nav id="menu_top">
 <ul>
 <li><a href="actu.php">Actualit�s</a></li>
 <li><a href="page.php">Pages</a></li>
 <li><a id="deco" href="#">Se d�connecter</a></li>
 </ul>
 </nav>
 <div></div>
 
 <div>
 
 <header id="header_top">
 <a id="logo" href="#">
 <h1><img src="images/logo.png" alt="Real Tea : soci�t� sp�cialis�e dans le th� rare de qualit�"/></h1>
 </a>
 
 </header>
 
 <section id="actu">
 <header>
 <h2>Ajouter / Supprimer une actualit�</h2>
 </header>
 
 <p>Ici vous pouvez ajouter ou supprimer une actualit�. Celle-ci appara�tra automatiquement sur la page d'accueil et pourra ensuite �tre lue en entier lorsque le visiteur clique dessus.</p>
 
 <form method="post" action="actu.php">
 <label for="date1">Date</label>
 <input id="date1" name="date1" type="date" required /> <!-- Remplir ce champ est requis -->
 <br/>
 <label for="titre">Titre de l'actualit�</label>
 <input id="titre" name="titre" type="text" required /> <!-- Remplir ce champ est requis -->
 <br/>
 <label for="desc">Description <em>(limit� � 1000 caract�res - il est conseill� de rester synth�tique dans la description)</em></label>
 <textarea id="desc" name="desc"></textarea> <!-- Remplir ce champ est requis -->
 <br/>
 <input type="submit" value="Cr�er l'actualit�">
 </form>
 
 <header id="header_liste_actu">
 <h2>Actualit�s d�ja publi�es</h2>
 </header>
 
 <!-- boucle affichant les derni�res actus -->
 <ul id="news">
 <?php 
 foreach($news as $nw) # Boucle qui affiche les news
 {
 echo '
 <li>
 <article>
 <header>
 <h3><a href="#">'.$nw['title'].'</a></h3>
 </header>
 
 <footer>
 <time datetime="2011-01-01" pubdate>'.$nw['date_d'].' <span><br/>'.$nw['date_b'].'</span></time> <!-- l\'atribut bool�en pubdate signifie que le "time" sp�cifi� est bien celui de l\'article au cas o� il y aurait plusieurs <date> dedans-->
 </footer>
 
 <div>
 <p>'.$nw['text'].'</p>
 <div>
 <p><a href="#"> Voir l\'actualit�</a></p>
 <p><a href="actu.php?delete='.$nw['id'].'">Supprimer</a></p>
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
 
 <p id="copyright">� Ga�tan Weltzer  Tous droits r�serv�s - tutoriel pour Living Tuts : <a href="http://www.livingtuts.com">www.livingtuts.com</a></p>
 
 <div></div>
 </footer>
 
 </div>
 <!--[if lte IE 8]></div> <! [endif]-->
 </body>
</html>