<?php

namespace Models;


class Notation
{
    private static $NOTE_MIN = 0;
    private static $NOTE_MAX = 10;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var Vin
     */
    private $vin;

    /**
     * @var integer
     */
    private $note;

    /*
     *----------------------------------------------------------------------------------------------------------------------
     *                                        MODEL FUNCTIONS
     *----------------------------------------------------------------------------------------------------------------------
     */

    function __construct()
    {
    }

    /**
     * Obtient la notation sous forme de tableau
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            "Id vin" => $this->getVin()->getId(),
            "Note" => $this->getNote(),
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

        $query = $db->prepare("INSERT INTO notations (id_vin, note) VALUES (?, ?)");
        $success = $query->execute([$this->getVin()->getId(), $this->getNote()]);

        if ($success) {
            $this->setId($db->lastInsertId());

            $id_vin = $this->getVin()->getId();
            $nvMoyenne = self::getAvgByVinId($id_vin);
            $this->vin->setMoyenne($nvMoyenne);
            $nbVotes = self::countByWineId($id_vin);
            $this->vin->setNbVotes($nbVotes);
            $this->vin->save();
        }

        return $success;
    }


    /*
    *----------------------------------------------------------------------------------------------------------------------
    *                                      DB QUERIES
    *----------------------------------------------------------------------------------------------------------------------
    */

    /**
     * Calcule le nombre de votes pour un vin
     * @param int $id_vin
     * @return int
     */
    public static function countByWineId($id_vin)
    {
        $db = DBConnection::getInstance();
        $query = $db->prepare("SELECT COUNT(id_vin) FROM notations WHERE id_vin = ?");

        $query->execute([$id_vin]);

        return $query->fetch(\PDO::FETCH_COLUMN);
    }

    /**
     * Donne la note moyenne d'un vin donné
     *
     * @param integer $id_vin
     * @return integer
     */
    public static function getAvgByVinId($id_vin)
    {
        $db = DBConnection::getInstance();
        $query = $db->prepare("SELECT AVG(note) FROM notations WHERE id_vin = ?");
        $query->execute([$id_vin]);
        return $query->fetch(\PDO::FETCH_COLUMN);
    }

    /*
     * ---------------------------------------------------------------------------------------------------------------------
     *                                      GETTERS & SETTERS
     * ---------------------------------------------------------------------------------------------------------------------
     */
    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @param int $note
     * @throws \InvalidArgumentException
     */
    public function setNote($note)
    {
        if (! is_numeric($note))
            throw new \InvalidArgumentException("La note ne peut pas être vide !");

        $note = intval($note);
        if ($note < self::$NOTE_MIN || $note > self::$NOTE_MAX)
            throw new \InvalidArgumentException("La note attribuée est invalide !");

        $this->note = $note;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Vin
     */
    public function getVin()
    {
        return $this->vin;
    }

    /**
     * @return int
     */
    public function getNote()
    {
        return $this->note;
    }

}