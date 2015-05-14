<?php

namespace Helpers;

use \Models\Admin;

class FormHelperLogin
{
    /**
     * @var Admin
     */
    private $admin;

    /**
     * @var array
     */
    private $erreurs = array();


    public function __construct(Admin $admin) {
        $this->admin = $admin;
    }

    /**
     * @return admin
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @param \Models\admin $admin
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
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

    /**
     * @param string $label
     * @param string $message
     * @throws \InvalidArgumentException
     */
    public function addErreur($label, $message){
        if(!isset($label) || empty($label))
            throw new \InvalidArgumentException("Paramètre vide !");

        if(!isset($message) || empty($message))
            throw new \InvalidArgumentException("Paramètre vide !");

        $this->erreurs["$label"] = $message;
    }

    /**
     * @return bool
     */
    public function valider()
    {
        try {
            $this->admin->setUsername($_POST["username"]);
        } catch (\Exception $e) {
            $this->erreurs["username"] = $e->getMessage();
        }

        try {
            $this->admin->setPassword($_POST["password"]);
        } catch (\Exception $e) {
            $this->erreurs["password"] = $e->getMessage();
        }

        return empty($this->erreurs);
    }
}