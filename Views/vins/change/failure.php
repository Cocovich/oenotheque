<?php
$body = <<<HTML
<p>
Il y a eu une erreur lors de l'ajout/édition de votre vin en base de données.<br>
Veuillez réessayer...<br>
<a href="?action=add">Réessayer d'ajouter un vin</a>
<a href="?action=edit&id={$vin->getId()}">Réessayer d'éditer le vin</a>
</p>
HTML;

require "Views/layout.php";