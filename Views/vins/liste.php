<?php

foreach ($vins as $pays => $table) {
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

    foreach ($table as $num => $vin) {
        $num++;
        $body .= <<<HTML
            <li class="list-group-item">
                $num. {$vin->getNom()}
                <a href="?action=show&id={$vin->getId()}" class="vin-show-icon" title="Voir">
                    <span class="glyphicon glyphicon-eye-open"></span>
                </a>
                <a href="?action=edit&id={$vin->getId()}" class="vin-edit-icon" title="Editer">
                    <span class="glyphicon glyphicon-pencil"></span>
                </a>
                <a href="?action=remove&id={$vin->getId()}" class="vin-remove-icon" title="Supprimer">
                    <span class="glyphicon glyphicon-trash"></span>
                </a>
            </li>
HTML;
    }

    $body .= <<<HTML
            </ul >
        </div>
    </div>
HTML;

}
