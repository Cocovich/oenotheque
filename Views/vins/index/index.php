<?php

require "Views/vins/index/options.php";

$body .= "<div class='tab-content'>";

$body .= $erreur;

$active = (! array_key_exists("view", $_POST) || $_POST["view"] == "mosaique") ? "in active" : "";
$body .= "<div class='tab-pane fade $active' id='mosaique'>";
require_once "Views/vins/index/mosaique.php";
$body .= "</div>";

$active = (array_key_exists("view", $_POST) && $_POST["view"] == "liste") ? "in active" : "";
$body .= "<div class='tab-pane fade $active' id='liste'>";
require_once "Views/vins/index/liste.php";
$body .= "</div>";

$body .= "</div>";

$body .= "<script>";
$body .= <<<JS
$('a[data-toggle="pill"]').on('show.bs.tab', function (e) {
    document.forms["form-filters"].elements["view"].setAttribute("value",e.target.getAttribute("data-value"));
});
JS;
$body .= "</script>";

require "Views/layout.php";