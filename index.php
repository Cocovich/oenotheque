<?php
//TODO intégrer la notion de session et éventuellement de cookies
//TODO penser à la zone admin
require_once 'autoload.php';

session_start();

if (! isset($_GET['action']))
    $_GET["action"] = "index";

try {
    switch ($_GET['action']) {
        case "index":
            \Controllers\VinController::index();
            break;
        case "show":
            \Controllers\VinController::show();
            break;
        case "add":
            \Controllers\VinController::add();
            break;
        case "edit":
            \Controllers\VinController::edit();
            break;
        case "remove":
            \Controllers\VinController::remove();
            break;
        case "abracadabra":
            \Controllers\VinController::random();
            break;

        case "lastcomms":
            \Controllers\CommentaireController::index();
            break;
        case "remove_comment":
            \Controllers\CommentaireController::remove();
            break;

        case "add_rate":
            \Controllers\VinController::rate();
            break;
            
        case "login":
            \Controllers\MainController::login();
            break;

        case "logout":
            \Controllers\MainController::logout();
            break;

        default:
            throw new Exception("Cette page n'existe pas !");
    }
} catch (Exception $e) {
    header("HTTP/1.0 404 Not Found");
    $header = [
        "titre" => "Erreur",
        "description" => "Erreur",
    ];
    $message = $e->getMessage();
    require 'Views/error.php';
}
