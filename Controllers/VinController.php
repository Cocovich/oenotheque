<?php

namespace Controllers;

use Helpers\FormHelperCommentaire;
use Helpers\FormHelperVin;
use Models\Commentaire;
use Models\Notation;
use Models\Vin;

class VinController
{

    /**
     * Liste tous les vins
     */
    public static function index()
    {
        $view = (array_key_exists("view", $_POST)) ? $_POST["view"] : "mosaique";
        $search = (array_key_exists("search", $_POST)) ? $_POST["search"] : "";
        $order = (array_key_exists("order", $_POST)) ? $_POST["order"] : "date_ajout";
        $limit = (array_key_exists("limit", $_POST)) ? intval($_POST["limit"]) : Vin::count();

        $header = array(
            'titre' => 'Liste des vins',
            'description' => 'Tous les vins',
        );

        $orders = "";
        $options = array(
            "Nom" => "nom",
            "Date d'ajout" => "date_ajout",
            "Meilleure note" => "moyenne"
        );
        foreach ($options as $label => $option) {
            $selected = ($option == $order) ? "selected='selected'" : "";
            $orders .= "<option value='$option' $selected>$label</option>";
        }

        $limits = "";
        $options = [5 => 5, 10 => 10, 20 => 20, 50 => 50, 100 => 100, "Tout" => Vin::count()];
        foreach ($options as $label => $option) {
            $selected = ($option == $limit) ? "selected='selected'" : "";
            $limits .= "<option value='$option' $selected>$label</option>";
        }

        $vins_mosaique = Vin::getAll($search, $order, $limit);

        $erreur = (count($vins_mosaique) == 0) ? "<div class='col-md-offset-4'>Aucun vin trouvé</div>" : "";

        $vins_liste = [];
        foreach ($vins_mosaique as $num => $vin)
            $vins_liste[$vin->getPays()][$num] = $vin;

        ksort($vins_liste); // Trie les clés d'un tableau alphabétiquement

        require('Views/vins/index/index.php');
    }

    /**
     * Affiche un vin sur base de son ID
     */
    public static function show()
    {
        if (!array_key_exists("id", $_GET))
            throw new \Exception("L'URL n'est pas complète !");

        $id = $_GET["id"];
        $vin = Vin::getById($id);

        $limit = (array_key_exists("limit", $_POST)) ? intval($_POST["limit"]) : 10;
        $limits = "";
        $options = [5 => 5, 10 => 10, 20 => 20, 50 => 50, 100 => 100, "Tout" => Commentaire::count()];
        foreach ($options as $label => $option) {
            $selected = ($option == $limit) ? "selected='selected'" : "";
            $limits .= "<option value='$option' $selected>$label</option>";
        }

        $comment = new Commentaire();
        $comment->setVin($vin);

        $form = new FormHelperCommentaire($comment);

        if (array_key_exists("submit", $_POST)) {
            if ($form->valider() && $form->getCommentaire()->save()) {
                $form->setCommentaire(new Commentaire());
            }
        }

        $comments = Commentaire::getAll($id, $limit);

        $header = array(
            'titre' => 'Oenothèque',
            'description' => 'Vin',
        );
        require('Views/vins/show/show.php');
    }

    /**
     * Ajoute un vin à partir d'un formulaire
     */
    public static function add()
    {
        $vin = new Vin();

        $header = array(
            "titre" => "Ajouter un vin",
            "description" => "Page d'ajout d'un nouveau vin",
        );

        self::change($vin, $header);
    }

    /**
     * Edite un vin à partir d'un formulaire
     */
    public static function edit()
    {
        if (!array_key_exists("id", $_GET))
            throw new \Exception("L'URL n'est pas complète !");

        $id = $_GET["id"];
        $vin = Vin::getById($id);

        $header = array(
            "titre" => "Editer un vin",
            "description" => "Page d'édition d'un vin",
        );

        self::change($vin, $header);
    }

    /**
     * Change un vin à partir d'un formulaire
     */
    public static function change($vin, $header)
    {
        $form = new FormHelperVin($vin);

        if (array_key_exists("submit", $_POST)) {

            $valide = $form->valider();
            $vin = $form->getVin();

            if ($valide) {

                if ($vin->save()) {
                    if (isset($_FILES["image"]))
                        move_uploaded_file($_FILES["image"]["tmp_name"], "public/images/{$vin->getId()}.jpg");
                    require('Views/vins/change/success.php');
                } else {
                    require('Views/vins/change/failure.php');
                }
            } else {
                require('Views/vins/change/change.php');
            }

        } else {
            require('Views/vins/change/change.php');
        }
    }

    /**
     * Supprime un vin
     */
    public static function remove()
    {
        if (!array_key_exists("id", $_GET))
            throw new \Exception("L'URL n'est pas complète !");

        $id = $_GET["id"];
        $vin = Vin::getById($id);

        $header = [
            "titre" => "Supprimer un vin",
            "description" => "Page de suppression d'un vin"
        ];

        if (array_key_exists("delete", $_POST)) {
            // TODO Afficher un message de suppression effectuée
            if ($vin->remove()) {
                header('Location: index.php?action=index');
                exit;
            } else
                require "Views/vins/remove/failure.php";
        } else {
            require "Views/vins/remove/form.php";
        }
    }

    public static function rate() {
        header("Content-type: text/javascript");

        if (! array_key_exists("id_vin", $_POST))
            echo json_encode(array("title" => "Erreur", "message" => "L'ID n'est pas spécifié !", "moyenne" => 0, "nb_votes" => 0));
        else {
            $id = $_POST["id_vin"];
            $vin = Vin::getById($id);

            try {
                $note = new Notation();
                $note->setVin($vin);
                $note->setNote($_POST["note"]);
                if ($note->save()) {
                    $vin = $note->getVin();
                    echo json_encode(array("title" => "Succès", "message" => "La note a bien été enregistrée.", "moyenne" => $vin->getMoyenne(), "nb_votes" => $vin->getNbVotes()));
                }
                else
                    throw new \Exception("La note n'a pas pû être enregistrée !");
            } catch (\Exception $e) {
                echo json_encode(array("title" => "Erreur", "message" => $e->getMessage(), "moyenne" => $vin->getMoyenne(), "nb_votes" => $vin->getNbVotes()));
            }
        }
    }

    public static function random()
    {
        $ids = Vin::allId();

        $_GET["id"] = $ids[array_rand($ids)];
        self::show();
    }
} 