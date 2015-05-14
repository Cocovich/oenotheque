<?php

$body .= '<div class="row">';
foreach ($vins_mosaique as $num => $vin) {
    $num++;
    $stars = "";
    for ($i = 1; $i <= $vin->getMoyenne()/2; $i++)
        $stars .= "<span class='glyphicon glyphicon-star' style='color: gold;'></span>";
    if (isset($_SESSION["user_id"])) {
        $buttons = <<<HTML
        <div class="captionbuttons">
            <p>
                <br>
                <a href="?action=show&id={$vin->getId()}" class="btn btn-primary btn-xs">Voir</a>
                <a href="?action=edit&id={$vin->getId()}" class="btn btn-warning btn-xs">Editer</a>
                <a href="?action=remove&id={$vin->getId()}" class="btn btn-danger btn-xs">Supprimer</a>
            </p>
        </div>
HTML;
    }else{
        $buttons = "";
    }
    $body .= <<<HTML
    <div class="col-sm-3 col-md-2">
        <div class="thumbnail">
            <div class="caption">
                <p><span class="vin-num">$num</span> {$vin->getNom()} <span class="vin-moyenne">{$stars}</span></p>
            </div>

            <a href="?action=show&id={$vin->getId()}">
                <img src="{$vin->getImageRelative()}" alt="{$vin->getNom()}" class="vin-image" data-src="holder.js/190x210">
            </a>
            $buttons
        </div>
    </div>
HTML;
}
$body .= '</div>';
