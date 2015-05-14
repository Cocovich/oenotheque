<?php
$body = <<<HTML
<div id="form">

    {$form->getLien()}

    <form action="" method="post" id="form-vin" enctype="multipart/form-data">

        {$form->getErreurs()}

        <div class="row">

            <div class="form-group col-md-9 {$form->hasError("nom")}">
                <label for="inputNom" class="control-label">Nom *</label>
                {$form->inputNom()}
            </div>

            <div class="form-group col-md-3 {$form->hasError("millesime")}">
                <label form="inputMillesime" class="control-label">Millésime *</label>
                {$form->inputMillesime()}
            </div>

        </div>

        <div class="row">

            <div class="form-group col-md-6 {$form->hasError("pays")}">
                <label for="selectPays" class="control-label">Pays *</label>
                <select name="pays" id="selectPays" class="form-control" required>
                    {$form->selectPays()}
                </select>
            </div>

            <div class="form-group col-md-6 {$form->hasError("region")}">
                <label for="inputRegion" class="control-label">Région *</label>
                {$form->inputRegion()}
            </div>

        </div>

        <div class="row">
            <div class="form-group col-md-6 {$form->hasError("couleur")}">
                <label class="control-label">Couleur *</label><br/>
                {$form->selectCouleur()}
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6 {$form->hasError("image")}">
                <label for="inputImage" class="control-label">Image *</label>
                {$form->inputImage()}
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <input type="reset" value="Par défaut" class="btn btn-default">
                <input type="submit" name="submit" value="Valider" class="btn btn-primary">
            </div>
        </div>

    </form>

</div>


HTML;


require "Views/layout.php";