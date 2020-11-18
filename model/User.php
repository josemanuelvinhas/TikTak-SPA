<?php
//file: model/User.php

require_once(__DIR__ . "/../core/ValidationException.php");

class User
{

    private $username;
    private $passwd;
    private $email;
    private $nfollowers;
    private $nfollowings;

    public function __construct($username = NULL, $passwd = NULL, $email = NULL, $nfollowers = NULL, $nfollowings = NULL)
    {
        $this->username = $username;
        $this->passwd = $passwd;
        $this->email = $email;
        $this->nfollowers = $nfollowers;
        $this->nfollowings = $nfollowings;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getPasswd()
    {
        return $this->passwd;
    }

    public function setPasswd($passwd)
    {
        $this->passwd = $passwd;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getNfollowings()
    {
        return $this->nfollowings;
    }

    public function setNfollowings($nfollowings)
    {
        $this->nfollowings = $nfollowings;
    }

    public function getNfollowers()
    {
        return $this->nfollowers;
    }

    public function setNfollowers($nfollowers)
    {
        $this->nfollowers = $nfollowers;
    }

    public function checkIsValidForRegister()
    {
        $errors = array();

        $tempErr = $this->checkUsername();
        if (!empty($tempErr)) {
            $errors["username"] = $tempErr;
        }

        $tempErr = $this->checkPasswd();
        if (!empty($tempErr)) {
            $errors["passwd"] = $tempErr;
        }

        $tempErr = $this->checkEmail();
        if (!empty($tempErr)) {
            $errors["email"] = $tempErr;
        }

        if (sizeof($errors) > 0) {
            throw new ValidationException($errors, "user is not valid");
        }
    }

    public function checkIsValidForLogin()
    {
        $errors = array();

        $tempErr = $this->checkUsername();
        if (!empty($tempErr)) {
            $errors["username"] = $tempErr;
        }

        $tempErr = $this->checkPasswd();
        if (!empty($tempErr)) {
            $errors["passwd"] = $tempErr;
        }

        if (sizeof($errors) > 0) {
            throw new ValidationException($errors, "user is not valid");
        }
    }

    private function checkUsername()
    {
        $var = $this->getUsername();
        $message = "Insert between 3 and 15 numbers or letters";
        $pattern = "/^[a-zA-Z0-9]{3,15}$/";

        $errors = array();
        if (!preg_match($pattern, $var)) {
            array_push($errors, $message);
        }
        return $errors;
    }

    private function checkPasswd()
    {
        $var = $this->getPasswd();
        $message = "Insert between 3 and 20 numbers or letters";
        $pattern = "/^[a-zA-Z0-9]{3,20}$/";

        $errors = array();
        if (!preg_match($pattern, $var)) {
            array_push($errors, $message);
        }
        return $errors;
    }

    private function checkEmail()
    {
        $var = $this->getEmail();
        $message = "Insert a valid email";

        $errors = array();
        if(!filter_var($var, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, $message);
        }
        return $errors;
    }


}