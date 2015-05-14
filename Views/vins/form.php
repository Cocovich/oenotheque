<?php

$body = <<<HTML
<p>
    Êtes-vous sûr de vouloir supprimer le vin "{$vin->getNom()}" ?<br>
    La suppression est définitive !
</p>
<form action="" method="post" id="remove-form">
    <a href="?action=index" class="btn btn-default">Revenir</a>
    <input type="submit" name="delete" value="Supprimer" class="btn btn-danger">
</form>
HTML;

require "Views/layout.php";