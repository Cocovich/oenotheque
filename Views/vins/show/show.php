<?php
if (isset($_SESSION) && array_key_exists("user_id", $_SESSION)) {
    $buttons = <<<HTML
    <span id="vin-liens">
        <a href="?action=edit&id={$vin->getId()}" class="vin-edit-icon"><span class="glyphicon glyphicon-pencil"></span></a>
        <a href="?action=remove&id={$vin->getId()}" class="vin-remove-icon"><span class="glyphicon glyphicon-trash"></span></a>
    </span>
HTML;

} else {
    $buttons = "";
}

$body = <<<HTML

<div id="fiche-vin">
    <div class="row">
        <div class="col-md-3" id="vin-image">
            <img src="{$vin->getImageRelative()}" alt="{$vin->getNom()}" class="vin-image">
        </div>
        <div class="col-md-9" id="vin-contenu">
            <h2>
                {$vin->getNom()}
                $buttons
            </h2>
            <div class="row">
                <div class="col-md-6">
                    <span class="vin-label">Pays :</span>
                    {$vin->getPays()}
                </div>
                <div class="col-md-6">
                    <span class="vin-label">Région :</span>
                    {$vin->getRegion()}
                </div>
                <div class="col-md-6">
                    <span class="vin-label">Couleur :</span>
                    {$vin->getCouleur()}
                </div>
                <div class="col-md-6">
                    <span class="vin-label">Millésime :</span>
                    {$vin->getMillesime()}
                </div>
                <div class="col-md-6">
                    <p>
                        <span class="vin-label">Moyenne :</span>
                        <span id="vin-moyenne">{$vin->getMoyenne()}</span> / 10 sur <span id="vin-nb-votes">{$vin->getNbVotes()}</span> vote(s)
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div id="vin-notation" class="col-md-3 form-inline">
            <label for="inputNote" class="control-label">Note</label>
            <input type="number" name="note" id="inputNote" class="form-control" value="0" min="0" max="10">
            <br>
            <button type="reset" id="reset-note" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-repeat"></span></button>
            <a href="#" id="submit-note" class="btn btn-sm btn-default"><span class="glyphicon glyphicon-ok"></span></a>
        </div>
        <div class="col-md-9" id="vin-date">
            <p>
                <span class="vin-label">Dernière mise à jour :</span>
                {$vin->getDateAjout()->format("d/m/Y H:i")}
            </p>
        </div>
    </div>
</div>

HTML;

require_once "Views/vins/show/modal-response.php";

$body .= "<script>";
$body .= <<<JS
$("#submit-note").click(function() {
    var note = $("#inputNote");
    {
        $.ajax({
            type: "POST",
            url: "index.php?action=add_rate",
            data: {
                id_vin : {$vin->getId()},
                note: note.val()
            },
            dataType: "json",
            success : function(response) {
                $("#rated-wine .modal-title").html(response.title);
                $("#rated-wine .modal-body").html(response.message);
                $("#rated-wine").modal('show');
                if (response.title == "Succès") {
                    $("#vin-moyenne").text(response.moyenne);
                    $("#vin-nb-votes").text(response.nb_votes);
                }
            }
        });
    }
});
JS;
$body .= "</script>";

require("Views/commentaires/add.php");

require("Views/commentaires/index.php");

require_once "Views/layout.php";