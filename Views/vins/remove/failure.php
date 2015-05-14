<?php
$body = <<<HTML
<p>
Il y a eu une erreur lors de la suppression de votre vin en base de données.<br>
Veuillez réessayer...<br>
<a href="?action=remove&id={$vin->getId()}">Réessayer de supprimer le vin</a>
<a href="?action=index">Revenir au menu</a>
</p>
HTML;

require "Views/layout.php";