<?php

$body = <<<HTML
<p>
    Êtes-vous sûr de vouloir supprimer le vin "{$vin->getNom()}" ?<br>
    La suppression est définitive !
</p>
<form action="" method="post" id="remove-form">
    <a href="?action=index" class="btn btn-default">Revenir à la liste</a>
    <a href="?action=show&id={$vin->getId()}" class="btn btn-primary">Voir le vin</a>
    <input type="submit" name="delete" value="Supprimer" class="btn btn-danger">
</form>
HTML;

require "Views/layout.php";