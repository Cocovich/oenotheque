<?php
$body .= <<<HTML
<div id="add-comm-form">

    <h3>Ajouter un commentaire</h3>
    <form action="" method="post" id="add-form">
        {$form->getErreurs()}
        <div class="row">
            <div class="form-group col-md-6 {$form->hasError("pseudo")}">
                <label for="inputPseudo" class="control-label">Pseudo *</label>
                {$form->inputPseudo()}
            </div>

            <div class="form-group col-md-12 {$form->hasError("contenu")}">
                <label for="textContenu" class="control-label">Commentaire *</label>
                {$form->textContenu()}
            </div>

            <div class="col-md-3">
                <input type="submit" name="submit" value="Envoyer" class="btn btn-primary">
            </div>
        </div>
    </form>
</div>

HTML;
