<?php

namespace Models;

use DateTime;

class Vin
{
    public static $NOM_LONGUEUR_MAX = 50;

    public static $PAYS = array(
        "Europe" => ["Belgique", "France", "Italie", "Espagne"],
        "Amérique" => ["USA", "Brésil", "Argentine", "Chili"],
    );

    public static $COULEURS = array(
        "rouge", "rosé", "blanc"
    );

    public static $MAPPING = array(
        "Nom" => "nom",
        "Pays" => "pays",
        "Région" => "region",
        "Couleur" => "couleur",
        "Millésime" => "millesime",
        "Appellation" => "appellation",
        "Moyenne" => "moyenne",
        "Nombre de votes" => "nb_votes",
        "Date d'ajout" => "date_ajout",
    );

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $nom;
    /**
     * @var string
     */
    private $pays;
    /**
     * @var string
     */
    private $region;
    /**
     * @var string
     */
    private $couleur;
    /**
     * @var integer
     */
    private $millesime;

    /**
     * @var double
     */
    private $moyenne = 0;
    /**
     * @var integer
     */
    private $nb_votes = 0;
    /**
     * @var DateTime
     */
    private $date_ajout;


    /*
     * ----------------------------------------------------------------------------------------------------------------
     *                                          MODEL FUNCTIONS
     * ----------------------------------------------------------------------------------------------------------------
     */


    public function __construct()
    {
        $this->date_ajout = (is_null($this->date_ajout)) ? new DateTime() : new DateTime($this->date_ajout);
    }

     /**
     * Obtient le vin sous forme de tableau
     *
     * @return array
     */
    public function toArray()
    {
        // Le filtre par défaut est de garder tous les éléments qui ne sont pas nul
        return array_filter(
            array(
                "Nom" => $this->getNom(),
                "Pays" => $this->getPays(),
                "Région" => $this->getRegion(),
                "Couleur" => $this->getCouleur(),
                "Millésime" => $this->getMillesime(),
                "Moyenne" => $this->getMoyenne(),
                "Nombre de votes" => $this->getNbVotes(),
                "Date d'ajout" => $this->getDateAjout()->format("Y/m/d H:i:s"),
            )
        );
    }

    /**
     * Sauve l'objet en base de données
     *
     * @return bool
     */
    public function save()
    {
        $db = DBConnection::getInstance();

        // Les valeurs à insérer en BDD
        $params = $this->toArray();
        // Les champs à insérer en BDD
        $mapping = array_intersect_key(self::$MAPPING, $params);
        // On combine les noms de champs avec leur valeur en un seul et même tableau
        $params = array_combine(range(0, count($params) - 1), $params);


        if ($this->inDB()) {
            $params[] = $this->getId();
            // Les champs sur une ligne
            $values = implode(", ", array_map(function($champ) {
                        return $champ . " = ?";
                    }, $mapping));

            $query = "UPDATE vins SET {$values} WHERE id = ?";
            $query = $db->prepare($query);
            $success = $query->execute($params);

        } else {
            // Les noms de champ sur une ligne
            $names = implode(", ", $mapping);
            // Les ? sur une ligne
            $values = implode(", ", array_fill(0, count($mapping), "?"));


            $query = "INSERT INTO vins ({$names}) VALUES ({$values})";
            $query = $db->prepare($query);
            $success = $query->execute($params);

            if ($success)
                $this->setId($db->lastInsertId());

        }
        return $success;
    }

    /**
     * Supprime l'objet de la base de données
     */
    public function remove()
    {
        $db = DBConnection::getInstance();

        $success = $db
            ->prepare("DELETE FROM vins WHERE id = ?")
            ->execute([$this->getId()]);

        if ($success) {
            $this->inDB = true;
            return unlink($this->getImageAbsolute());
        }
        return $success;
    }


    /*
     * ----------------------------------------------------------------------------------------------------------------
     *                                                 DB QUERIES
     * ----------------------------------------------------------------------------------------------------------------
     */

    public static function exists($id) {
        try {
            Vin::getById($id);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Récupère le nombre d'éléments présents dans la table vins
     *
     * @return int
     */
    public static function count() {
        $db = DBConnection::getInstance();

        return $db->query("SELECT COUNT(*) FROM vins")->fetchColumn();
    }

    /**
     * Génère un tableau de tous les ID des vins
     *
     * @return array
     */
    public static function allId() {
        $db = DBConnection::getInstance();

        return $db->query("SELECT id FROM vins")->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Donne l'ID max de la table vins
     *
     * @return int
     */
    public static function maxId() {
        $db = DBConnection::getInstance();

        return intval($db->query("SELECT MAX(id) FROM vins")->fetchColumn());
    }

    /**
     * Récupère le vin d'ID spécifié
     *
     * @param integer $id
     * @throws \Exception
     * @return Vin
     */
    public static function getById($id)
    {
        if (! is_numeric($id))
            throw new \Exception("L'ID du vin doit être un nombre !");
        $id = intval($id);

        $db = DBConnection::getInstance();

        $query = $db->prepare("SELECT * FROM vins WHERE id = ?");
        $query->execute([$id]);

        $vin = $query->fetchObject("\\Models\\Vin");

        if ($vin === FALSE)
            throw new \Exception("Le vin d'ID $id n'existe pas !");

        return $vin;
    }

    /**
     * Donne les $max vins les plus récents
     *
     * @param string $search
     * @param string $order
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function getAll($search = "", $order = "date_ajout", $limit = 10, $offset = 0)
    {
        $db = DBConnection::getInstance();

        $sens = ["nom" => "ASC", "date_ajout" => "DESC", "moyenne" => "DESC"];
        $order = $order." ".$sens[$order];

        $query = $db->prepare("SELECT * FROM vins WHERE nom LIKE ? ORDER BY {$order} LIMIT {$limit} OFFSET {$offset}");
        $query->execute(["%$search%"]);

        return $query->fetchAll(\PDO::FETCH_CLASS, "\\Models\\Vin");
    }

    /*
     * ----------------------------------------------------------------------------------------------------------------
     *                                             GETTERS & SETTERS
     * ----------------------------------------------------------------------------------------------------------------
     */


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     * @throws \InvalidArgumentException
     */
    public function setNom($nom)
    {
        if (empty($nom))
            throw new \InvalidArgumentException("Le nom doit être défini !");

        if (strlen($nom) > self::$NOM_LONGUEUR_MAX)
            throw new \InvalidArgumentException("Le nom ne peut pas dépasser " . self::$NOM_LONGUEUR_MAX . " caractères.");

        if(!preg_match("#^[a-zA-ZàâçéèêëîïôûùüæœÀÂÇÉÈÊËÎÏÔÛÙÜÆŒ\\s.'´`-]{4,35}$#i",$nom))
            throw new \InvalidArgumentException("Erreur nom !");

        $this->nom = $nom;
    }

    /**
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * @param string $pays
     * @throws \InvalidArgumentException
     */
    public function setPays($pays)
    {
        if (empty($pays))
            throw new \InvalidArgumentException("Le pays doit être défini !");

        $liste_pays = [];
        foreach (self::$PAYS as $key => $value)
            $liste_pays = array_merge($liste_pays, self::$PAYS[$key]);

        if (!in_array($pays, $liste_pays))
            throw new \InvalidArgumentException("Le pays n'est pas valide !");

        $this->pays = $pays;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param string $region
     * @throws \InvalidArgumentException
     */
    public function setRegion($region)
    {
        if (empty($region))
            throw new \InvalidArgumentException("La région doit être définie !");

        if(!preg_match("#^[a-zàâçéèêëîïôûùüæœ\\s.'´`-]{4,25}$#i",$region))
            throw new \InvalidArgumentException("La région contient des caractères non autorisés !");

        $this->region = $region;
    }

    /**
     * @return int
     */
    public function getMillesime()
    {
        return $this->millesime;
    }

    /**
     * @param int $millesime
     * @throws \InvalidArgumentException
     */
    public function setMillesime($millesime)
    {
        if (empty($millesime))
            throw new \InvalidArgumentException("Le millésime doit être défini !");

        if (! is_numeric($millesime))
            throw new \InvalidArgumentException("Le millésime doit être un nombre !");

        $millesime = intval($millesime);

        $this->millesime = $millesime;
    }

    /**
     * @return string
     */
    public function getCouleur()
    {
        return $this->couleur;
    }

    /**
     * @param string $couleur
     * @throws \InvalidArgumentException
     */
    public function setCouleur($couleur)
    {
        if (empty($couleur))
            throw new \InvalidArgumentException("La couleur doit être définie !");

        if (!in_array($couleur, self::$COULEURS))
            throw new \InvalidArgumentException("La couleur n'est pas valide !");

        $this->couleur = $couleur;
    }

    /**
     * @param int $precision
     * @return float
     */
    public function getMoyenne($precision = 1)
    {
        return round($this->moyenne, $precision);
    }

    /**
     * @param float $moyenne
     */
    public function setMoyenne($moyenne)
    {
        $this->moyenne = $moyenne;
    }

    /**
     * @return \DateTime
     */
    public function getDateAjout()
    {
        return $this->date_ajout;
    }

    /**
     * @return string
     */
    public function getImageAbsolute()
    {
        return realpath($this->getImageRelative());
    }

    /**
     * @return string
     */
    public function getImageRelative()
    {
        return "public/images/{$this->getId()}.jpg";
    }

    /**
     * @return int
     */
    public function getNbVotes()
    {
        return $this->nb_votes;
    }

    public function setNbVotes($nb_votes)
    {
        $this->nb_votes = $nb_votes;
    }

    /**
     * Incrémente le nombre de votes
     */
    public function incrementVotes()
    {
        $this->nb_votes++;
    }

    public function inDB() {
        return ! is_null($this->getId());
    }
}