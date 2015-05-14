<?php
$body = <<<HTML
<p>
Votre vin a bien été ajouté/édité !<br>
<a href="?action=show&id={$vin->getId()}">Aller sur sa fiche</a><br>
<a href="?action=add">Ajouter un autre vin</a>
</p>
HTML;

require "Views/layout.php";