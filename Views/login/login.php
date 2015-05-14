<?php

$body = <<<HTML

<div class="form">

    {$form->getErreurs()}

    <form action="" method="post" class="form-horizontal" id="form-login">
        <div class="form-group">
            <label for="inputUsername" class="control-label col-md-3">Pseudo *</label>
            <div class="col-md-6">
                <input type="text" name="username" id="inputUsername" class="form-control" value="$username" required>
            </div>
        </div>

        <div class="form-group">
            <label for="inputPassword" class="control-label col-md-3">Mot de passe *</label>
            <div class="col-md-6">
                <input type="password" name="password" id="inputPassword" class="form-control" required>
            </div>
        </div>

        <div class="form-group">
            <div class="col-md-push-3 col-md-3">
                <input type="submit" name="submit" id="submit-login" class="btn btn-default" value="Valider">
            </div>
        </div>
    </form>
</div>

HTML;

require_once "Views/layout.php";