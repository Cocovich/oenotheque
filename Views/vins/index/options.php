<?php
$body = <<<HTML
<div class="row" id="options">
    <div id="view_select" class="col-md-3">
        <div class="nav nav-pills">
            <a href="#mosaique" class="btn btn-primary btn-sm" data-toggle="pill" data-value="mosaique">
                <span class="glyphicon glyphicon-th"></span>&nbsp;Vue Mosaïque
            </a>
            <a href="#liste" class="btn btn-primary btn-sm" data-toggle="pill" data-value="liste">
                <span class="glyphicon glyphicon-list"></span>&nbsp;Vue Liste
            </a>
        </div>
    </div>
    
    <form action="" method="post" id="form-filters" name="form-filters" class="form-inline">
        <div class="form-group col-md-push-1 col-md-4">
            <label for="inputSearch" class="control-label">Rechercher&nbsp;</label>
            <input type="text" name="search" id="inputSearch" class="form-control" value="$search" placeholder="Nom" onsubmit="this.form.submit()">
        </div>
        &nbsp;
        <div class="form-group col-md-push-1 col-md-2">
            <label for="selectOrder" class="control-label">Tri&nbsp;</label>
            <select name="order" id="selectOrder" class="form-control" onchange="this.form.submit()">
                $orders
            </select>
        </div>
        &nbsp;
        <div class="form-group col-md-push-1 col-md-2">
            <label for="selectLimit" class="control-label">Affichage&nbsp;</label>
            <select name="limit" id="selectLimit" class="form-control" onchange="this.form.submit()">
                $limits
            </select>
        </div>
        <input type="hidden" name="view" value="$view">
    </form>
</div>
HTML;
