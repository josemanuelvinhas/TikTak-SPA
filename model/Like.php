<?php
//file: model/Like.php

class Like
{

    private $id;
    private $username;

    public function __construct($id=NULL, $username=NULL) {
        $this->id = $id;
        $this->username = $username;
     }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

}
