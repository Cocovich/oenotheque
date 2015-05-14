<?php

$body .= '<div class="row">';
foreach ($vins as $num => $vin) {
    $num++;
    $body .= <<<HTML
    <div class="col-sm-3 col-md-2">
        <div class="thumbnail">
            <a href="?action=show&id={$vin->getId()}" class="thumbnail">
                <img src="{$vin->getImageRelative()}" alt="{$vin->getNom()}" data-src="holder.js/190x230">
            </a>

            <div class="caption">
                <h5>$num. {$vin->getNom()}</h5>
                <p>
                    <a href="?action=show&id={$vin->getId()}" class="btn btn-primary btn-xs">Voir</a>
                    <a href="?action=edit&id={$vin->getId()}" class="btn btn-warning btn-xs">Editer</a>
                    <a href="?action=remove&id={$vin->getId()}" class="btn btn-danger btn-xs">Supprimer</a>
                </p>
            </div>
        </div>
    </div>
HTML;
}
$body .= '</div>';
