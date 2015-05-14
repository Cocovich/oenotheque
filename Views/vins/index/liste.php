<?php

if (isset($_SESSION["user_id"])) {
    $buttons = <<<HTML
    <span class="liens">
        <a href="?action=show&id={$vin->getId()}" class="vin-show-icon" title="Voir">
            <span class="glyphicon glyphicon-eye-open"></span>
        </a>
        <a href="?action=edit&id={$vin->getId()}" class="vin-edit-icon" title="Editer">
            <span class="glyphicon glyphicon-pencil"></span>
        </a>
        <a href="?action=remove&id={$vin->getId()}" class="vin-remove-icon" title="Supprimer">
            <span class="glyphicon glyphicon-trash"></span>
        </a>
    </span>
HTML;
}else{
    $buttons = "";
}
foreach ($vins_liste as $pays => $vins) {
    $body .= <<<HTML
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapse$pays">$pays</a>
            </h4>
        </div>

        <div id="collapse$pays" class="panel-collapse collapse in">
            <ul class="list-group">
HTML;

    foreach ($vins as $num => $vin) {
        $num++;
        $stars = "";
        for ($i = 1; $i <= $vin->getMoyenne()/2; $i++)
            $stars .= "<span class='glyphicon glyphicon-star' style='color: gold;'></span>";
        $body .= <<<HTML
            <li class="list-group-item">
                <span class="vin-num">$num</span>
                <a href="?action=show&id={$vin->getId()}" class="vin-show-icon" title="Voir">
                    {$vin->getNom()}
                </a>
                <span class="vin-moyenne">{$stars}</span>
                $buttons
            </li>
HTML;
    }

    $body .= <<<HTML
            </ul >
        </div>
    </div>
HTML;

}
