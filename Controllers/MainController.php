<?php

namespace Controllers;

use Helpers\FormHelperLogin;
use Models\Admin;

class MainController {

    public static function login()
    {
        $username = (array_key_exists("username", $_POST)) ? $_POST["username"] : "";

        $header = array(
            "titre" => "Login",
            "description" => "Page de connexion"
        );

        $admin = new Admin();
        $form = new FormHelperLogin($admin);

        if (array_key_exists("submit", $_POST)) {
            if ($form->valider()) {

                $user = Admin::getByUsername($_POST["username"]);
                try{
                    $user-> logIn($user->getUsername(), $_POST["password"]);
                    $_SESSION["user_id"] = $user->getId();
                    $_SESSION["username"] = $user->getUsername();
                    header("Location: index.php?action=index");
                }catch (\Exception $e){
                    $form->addErreur("login",$e->getMessage());
                }
            }
        }

        require("Views/login/login.php");
    }

    public static function logout()
    {
        if (isset($_SESSION) && array_key_exists("user_id", $_SESSION)) {
            unset($_SESSION);
            session_unset();
            header('Location: index.php?action=index');
        }
    }
} 