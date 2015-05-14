<?php

namespace Models;

class Admin {

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $email;
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
            "Nom d'utilisateur" => $this->getUsername(),
            "Mot de passe" => $this->getPassword(),
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

        $query = $db->prepare("INSERT INTO admins (username, password, mail) VALUES (?, ?, ?)");
        $success = $query->execute([$this->getUsername(), $this->getPassword(), $this->getEmail()]);

        if ($success) {
            $this->setId($db->lastInsertId());
        }

        return $success;
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function logIn($username, $password){
        $user = self::getByUsername($username);

        if((sha1($password) == $user->getPassword()) === false)
            throw new \InvalidArgumentException("Erreur mot de passe !");

        return true;
    }

    /*
    *----------------------------------------------------------------------------------------------------------------------
    *                                      DB QUERIES
    *----------------------------------------------------------------------------------------------------------------------
    */

    public static function getByUsername($username){
        $db = DBConnection::getInstance();

        $query = $db->prepare("SELECT * FROM admins WHERE username LIKE ?");
        $success = $query->execute(["$username"]);
        if($success === false)
            throw new \InvalidArgumentException("Le nom d'utilisateur spécifié n'existe pas !");

       return $query->fetchObject("\\Models\\Admin");
    }


    /*
     * ------------------------------------------------------------------------------------------------------------------
     *                  GETTERS & SETTERS
     *------------------------------------------------------------------------------------------------------------------
     */

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $password
     * @throws \InvalidArgumentException
     */
    public function setPassword($password)
    {
        if(empty($password))
            throw new \InvalidArgumentException("Le mot de passe ne peut pas être vide !");
        if(!preg_match("#^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\\d]){1,})(?=(.*[\\W]){1,})(?!.*\\s).{6,}$#",$password))
            throw new \InvalidArgumentException(
                "Le mot de passe doit être composé de : " . "<br>
                    - au moins 6 caractères <br>
                    - au moins une lettre minuscule(a-z) <br>
                    - au moins une lettre majuscule(A-Z) <br>
                    - au moins un chiffre(0-9) <br>
                    - au moins un symbole spécial (@,!,?,...)");

        $password = sha1($password);
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $username
     * @throws \InvalidArgumentException
     */
    public function setUsername($username)
    {
        if(preg_match('![^a-z0-9]!i', $username))
            throw new \InvalidArgumentException("Le mot d'utilisateur ne peut contenir que des lettres miniscules et des chiffres !");

        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $email
     * @throws \InvalidArgumentException
     */
    public function setEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new \InvalidArgumentException("L'adresse mail n'est pas correcte !");
        
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }



} 