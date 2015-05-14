<?php

// Si on est connecté
if (isset($_SESSION["user_id"])) {
    $ajout = "<a href='?action=add' class='btn btn-default'>Ajouter un vin</a>";
    $admin = "";
    $logout = "<a href='?action=logout' class='btn btn-default'>Déconnexion</a>";
} else {
    $ajout = "";
    $admin = "<a href='?action=login' class='btn btn-default'>Zone admin</a>";
    $logout = "";
}

echo <<<HTML
<html>

<head>
    <meta charset="UTF-8">
    <title>{$header['titre']}</title>
    <meta name="author" content="Patrycjusz Dolega, Corentin Dandoy">
    <meta name="description" content="{$header['description']}">
    <link href="public/css/bootstrap/bootstrap.css" type="text/css" rel="stylesheet">
    <link href="public/css/style.css" type="text/css" rel="stylesheet">
    <link href="public/css/wines.css" type="text/css" rel="stylesheet">
    <link href="public/css/comments.css" type="text/css" rel="stylesheet">
</head>

<body>
<script src="public/js/jquery-1.11.1.min.js"></script>
<script src="public/js/bootstrap.min.js"></script>

<header>
	<hgroup>
		<h1>Oenotheque</h1>
		<h2>La plus grande collection de vins de 1BIN !</h2>
	</hgroup>
	<nav>
		<div class="btn-group">
            <a href="?action=index" class="btn btn-default">Accueil</a>
            <a href="?action=lastcomms" class="btn btn-default">Derniers commentaires</a>
            <a href="?action=abracadabra" class="btn btn-default">Abracadabra</a>
            {$ajout}
            {$admin}
            {$logout}
		</div>
	</nav>
</header>

<section>
	$body
</section>

<footer>
	<p>&copy; Corentin Dandoy && Patrycjusz Dolega</p>
	<p>1BIN 2013-2014 - Projet PHP</p>
</footer>

</body>
</html>
HTML;
