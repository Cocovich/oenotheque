<?php

namespace Models;

use DateTime;

class Commentaire
{
    private static $PSEUDO_LONGUEUR_MIN = 3;
    private static $PSEUDO_LONGUEUR_MAX = 40;

    public static $MAPPING = array(
        "Id vin" => "id_vin",
        "Pseudo" => "pseudo",
        "Contenu" => "contenu",
        "Ajout" => "date_ajout",
    );

    /**
     * @var integer
     */
    private $id;

    /**
     * @var Vin
     */
    private $vin;

    /**
     * @var string
     */
    private $pseudo;

    /**
     * @var string
     */
    private $contenu;

    /**
     * @var DateTime
     */
    private $date_ajout;


    /*
     * -----------------------------------------------------------------------------------------------------------------
     *                                     MODEL FUNCTIONS
     * -----------------------------------------------------------------------------------------------------------------
    */
    public function  __construct()
    {
        if (! is_null($this->date_ajout)) {
            $this->date_ajout = new DateTime($this->date_ajout);
        }
    }

    /**
     * Obtient le commentaire sous forme de tableau
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            "Id vin" => $this->getVin()->getId(),
            "Pseudo" => $this->getPseudo(),
            "Contenu" => $this->getContenu(),
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

        // Les noms de champ sur une ligne
        $names = implode(", ", $mapping);
        // Les ? sur une ligne => les ? sont des marqueurs de valeurs pour PDO::query->prepare()
        $values = implode(", ", array_fill(0, count($mapping), "?"));


        $query = "INSERT INTO commentaires ({$names}) VALUES ({$values})";
        $query = $db->prepare($query);
        $success = $query->execute($params);

        if ($success)
            $this->setId($db->lastInsertId());

        return $success;
    }

    public function remove()
    {
        $db = DBConnection::getInstance();

        $success = $db
            ->prepare("DELETE FROM commentaires WHERE id = ?")
            ->execute([$this->getId()]);

        return $success;
    }

    /*
    *-------------------------------------------------------------------------------------------------------------------
    *                                      DB QUERIES
    *-------------------------------------------------------------------------------------------------------------------
    */

    public static function count(){
        $db=DBConnection::getInstance();

        return $db->query("SELECT COUNT(*) FROM commentaires")->fetchColumn();
    }

    public static function  getById($id){
        if (! is_numeric($id))
            throw new \Exception("L'ID du commentaire doit être un nombre !");
        $id = intval($id);

        $db = DBConnection::getInstance();

        $query = $db->prepare('SELECT * FROM commentaires WHERE id = ?');
        $query->execute([$id]);

        $commentaire = $query->fetchObject("\\Models\\Commentaire");

        if ($commentaire === FALSE)
            throw new \Exception("Le commentaire d'ID $id n'existe pas !");

        return $commentaire;

    }

    /**
     * @param null $id_vin
     * @param int $limit
     * @return array
     */
    public static function getAll($id_vin = null, $limit = 10)
    {
        
        $db = DBConnection::getInstance();

        if (! is_null($id_vin)) {
            $where = "WHERE v.id = ?";
            $array = [$id_vin];
        } else {
            $where = "";
            $array = null;
        }

        $query = $db->prepare("SELECT *, c.id AS id, c.date_ajout AS date_ajout FROM commentaires c INNER JOIN vins v ON v.id = c.id_vin {$where} ORDER BY c.date_ajout DESC LIMIT {$limit}");
        $query->execute($array);

        $results = $query->fetchAll(\PDO::FETCH_CLASS, "\\Models\\Commentaire");
        $results = array_map(function(Commentaire $result) {
                $vin = new Vin();
                $vin->setId($result->id_vin);
                $vin->setNom($result->nom);
                $vin->setPays($result->pays);
                $vin->setRegion($result->region);
                $vin->setCouleur($result->couleur);
                $vin->setMillesime($result->millesime);

                $result->setVin($vin);

                return $result;
        }, $results);

        return $results;
    }
    
    /*
    * -----------------------------------------------------------------------------------------------------------------
    *                                      GETTERS & SETTERS
    * -----------------------------------------------------------------------------------------------------------------
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
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * @param string $pseudo
     * @throws \InvalidArgumentException
     */
    public function setPseudo($pseudo)
    {
        if(empty($pseudo))
            throw new \InvalidArgumentException("Le pseudo doit être spécifié !");

        if(strlen($pseudo) < self::$PSEUDO_LONGUEUR_MIN || strlen($pseudo) > self::$PSEUDO_LONGUEUR_MAX)
            throw new \InvalidArgumentException("Le pseudo doit comporter entre ".self::$PSEUDO_LONGUEUR_MIN." et ".self::$PSEUDO_LONGUEUR_MAX." caractères.");

        $this->pseudo = $pseudo;
    }

    /**
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }


    /**
     * @param string $contenu
     * @throws \InvalidArgumentException
     */
    public function setContenu($contenu)
    {
        if (empty($contenu))
            throw new \InvalidArgumentException("Le commentaire ne peut pas être vide !");
        if (strlen($contenu) < 5)
            throw new \InvalidArgumentException("Le commentaire doit comporter plus de 5 caractères !");


        $this->contenu = $contenu;
    }

    /**
     * @return Vin
     */
    public function getVin()
    {
        return $this->vin;
    }

    /**
     * @param Vin $vin
     * @throws \Exception
     */
    public function setVin(Vin $vin)
    {
        if (is_null($vin->getId()))
            throw new \Exception("Le vin passé en paramètres n'existe pas !");

        $this->vin = $vin;
    }

    /**
     * @return \DateTime
     */
    public function getDateAjout()
    {
        return $this->date_ajout;
    }
}