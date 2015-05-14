<?php

namespace Helpers;

use Models\Commentaire;
use Models\Vin;

class FormHelperCommentaire {

    /**
     * @var Commentaire
     */
    private $commentaire;
    /**
     * @var Vin
     */
    private $vin;
    /**
     * @var array
     */
    private $erreurs = array();

    /**
     * @var bool
     */
    private $validated = false;


    public function __construct(Commentaire $commentaire) {
        $this->commentaire = $commentaire;
        $this->vin = $commentaire->getVin();
    }

    /**
     * @return Commentaire
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * @param \Models\Commentaire $commentaire
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;
    }

    /**
     * @return string
     */
    public function getErreurs() {

        if (empty($this->erreurs))
            return "";

        $erreurs = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
        $erreurs .= implode('</div><div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>', $this->erreurs);
        $erreurs .= '</div>';

        return $erreurs;
    }

    public function setVin(Vin $vin){
        $this->vin = $vin;
    }

    public  function  getVin(){
        return $this->vin;
    }

    public function inputPseudo() {
        $pseudo = $this->commentaire->getPseudo();

        return <<<HTML
            <input type="text" class="form-control"
                name="pseudo" id="inputPseudo"
                placeholder="Pseudo" value="$pseudo"
                required>
HTML;
    }

    public function textContenu() {
        $contenu = $this->commentaire->getContenu();

        return <<<HTML
            <textarea class="form-control"
                name="contenu" id="textContenu"
                placeholder="Votre commentaire ..."
                required>$contenu</textarea>
HTML;
    }

    public function hasError($champ)
    {
        if ($this->validated && ! empty($this->erreurs))
            return (isset($this->erreurs[$champ])) ? "has-error" : "has-success";
        return "";
    }

    /**
     * @return bool
     */
    public function valider()
    {
        // Les champs du formulaire. TRUE si le champ est requis, FALSE sinon
        $fields = ["vin" => true, "pseudo" => true, "contenu" => true];

        foreach ($fields as $key => $mandatory) {

            $value = null;
            $getter = 'get'.ucfirst($key);
            if (array_key_exists($key, $_POST))
                $value = $_POST[$key];
            else if (is_callable([$this, $getter]))
                $value = $this->$getter();

            try {

                $setter = "set" . ucfirst($key); // Le nom du setter de l'objet Commentaire

                // On vérifie que la méthode existe et on l'appelle
                if (is_callable([$this->commentaire, $setter])) {
                    $this->commentaire->$setter($value);
                } else {
                    throw new \InvalidArgumentException("Le champ \"$key\" n'est associé à aucune propriété de l'objet Commentaire");
                }

            } catch (\InvalidArgumentException $erreur) {
                $this->erreurs[$key] = $erreur->getMessage();
            }
        }

        $this->validated = true; // Le formulaire a été validé

        return empty($this->erreurs);
    }
}