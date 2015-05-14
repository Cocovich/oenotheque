<?php
namespace Controllers;

use Models\Commentaire;

class CommentaireController
{
    /**
     * Affiche tout les commentaires
     */
    public static function index()
    {
        $limit = (array_key_exists("limit", $_POST)) ? intval($_POST["limit"]) : 10;
        $comments = Commentaire::getAll(null, $limit);
        
        $header = array(
            "titre"=>"Liste des commentaires",
            "description"=>"Tous les commentaires",
        );
        
        $limits = "";
        $options = [5 => 5, 10 => 10, 20 => 20, 50 => 50, 100 => 100, "Tout" => Commentaire::count()];
        foreach ($options as $label => $option) {
            $selected = ($option == $limit) ? "selected='selected'" : "";
            $limits .= "<option value='$option' $selected>$label</option>";
        }
        
        require('Views/commentaires/index.php');
    }

    /**
     * Supprime dynamiquement un commentaire.
     * Cette fonctione ne retourne rien, elle est appelée par la requête ajax de la vue "Views/commentaires/index.php".
     * Cette requête récupère l'objet javascript affiché par la function et l'affiche dans un modal Bootstrap.
     */
    public static function remove() {
        header("Content-type: text/javascript");

        if (! array_key_exists("id", $_POST))
            echo json_encode(array("title" => "Erreur", "message" => "L'ID n'est pas spécifié !"));
        else {
            $id = $_POST["id"];

            try {
                $commentaire = Commentaire::getById($id);
                if ($commentaire->remove())
                    echo json_encode(array("title" => "Succès", "message" => "Le commentaire a bien été supprimé."));
                else
                    throw new \Exception("Le commentaire n'a pas pu être supprimé !");
            } catch (\Exception $e) {
                echo json_encode(array("title" => "Erreur", "message" => $e));
            }
        }
    }
}
