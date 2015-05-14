<?php

namespace Helpers;

use Models\Vin;

class FormHelperVin {

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

    public function __construct(Vin $vin) {
        $this->vin = $vin;
    }

    /**
     * @return Vin
     */
    public function getVin()
    {
        return $this->vin;
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

    public function getLien() {
        return (! is_null($this->getVin()->getId())) ?
            "<ul class='pager'><li class='previous'><a href='?action=show&id={$this->getVin()->getId()}'>&larr; Fiche</a></li></ul>" :
            "";
    }
    
    public function inputNom() {
        $taille = Vin::$NOM_LONGUEUR_MAX;
        $nom = $this->vin->getNom();

        return <<<HTML
            <input type="text" class="form-control"
                name="nom" id="inputNom"
                placeholder="Nom" value="$nom"
                size="$taille" maxlength="$taille"
                required>
HTML;
    }

    public function selectPays() {
        $options = ($this->vin->getPays()) ? "" : "<option selected disabled>Sélectionner un pays</option>";
        foreach (Vin::$PAYS as $continent => $pays) {
            $options .= "<optgroup label='$continent'>";
            foreach ($pays as $p) {
                $selected = ($this->vin->getPays() == $p) ? "selected='selected'" : "";
                $options .= "<option value='$p' $selected>$p</option>";
            }
            $options .= "</optgroup>";
        }

        return $options;
    }

    public function inputRegion() {
        $region = $this->vin->getRegion();

        return <<<HTML
            <input type="text" class="form-control"
                name="region" id="inputRegion"
                value="$region" placeholder="Région"
                required>
HTML;
    }

    public function selectCouleur() {
        $options = "";

        foreach (Vin::$COULEURS as $couleur) {
            $nom = ucfirst($couleur);
            $checked = ($this->vin->getCouleur() == $couleur) ? "checked" : "";
            $options .= <<<HTML
            <label class="radio-inline">
                <input type="radio" name="couleur" value="$couleur" required $checked>
                $nom
            </label>
HTML;
        }

        return $options;
    }

    public function inputMillesime() {
        $millesime = $this->vin->getMillesime();

        return <<<HTML
            <input type="number" class="form-control"
                name="millesime" id="inputMillesime"
                value="$millesime" placeholder="Année"
                required>
HTML;
    }

    public function inputImage() {
        $required = (! $this->getVin()->inDB() && ! file_exists($this->getVin()->getImageAbsolute())) ? "required" : "";

        return <<<HTML
            <input type="file" name="image" id="inputImage" class="form-control" accept="image/jpeg" $required>
HTML;

    }

    public function hasError($champ)
    {
        if ($this->validated)
            return (isset($this->erreurs[$champ])) ? "has-error" : "has-success";
        return "";
    }

    /**
     * @return bool
     */
    public function valider()
    {
        // Les champs du formulaire. TRUE si le champ est requis, FALSE sinon
        $fields = ["nom" => true, "pays" => true, "region" => true, "couleur" => true, "millesime" => true/*, "appellation" => false, "cepage" => false*/];
        // Les types d'images acceptées
        $types = array("image/jpeg");

        foreach ($fields as $key => $mandatory) {

            // On s'assure que la clé existe dans la table $_POST
            $_POST[$key] = (array_key_exists($key, $_POST)) ? $_POST[$key] : null;

            try {

                $setter = "set" . ucfirst($key); // Le nom du setter de l'objet Vin
                
                // On vérifie que la méthode existe et on l'appelle
                if (is_callable([$this->vin, $setter])) {
                    $this->vin->$setter($_POST[$key]);
                } else {
                    throw new \InvalidArgumentException("Le champ \"$key\" n'est associé à aucune propriété de l'objet Vin");
                }

            } catch (\InvalidArgumentException $erreur) {
                $this->erreurs[$key] = $erreur->getMessage();
            }
        }

        if (array_key_exists("image", $_FILES) && $_FILES["image"]["error"] == 0) {
            $image = $_FILES["image"];
            if (! in_array($image["type"], $types))
                $this->erreurs["image"] = "Le fichier n'est pas du bon type.";

        } else if (! $this->getVin()->inDB() && ! file_exists($this->getVin()->getImageAbsolute())) {
            $this->erreurs["image"] = "Aucun fichier n'a été uploadé !";
        }

        $this->validated = true; // Le formulaire a été validé

        return empty($this->erreurs);
    }
}