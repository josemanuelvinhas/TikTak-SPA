<?php
//file: model/UserMapper.php

require_once(__DIR__ . "/../core/PDOConnection.php");

class UserMapper
{
    private $db;

    public function __construct()
    {
        $this->db = PDOConnection::getInstance();
    }

    public function save($user)
    {
        $stmt = $this->db->prepare("INSERT INTO users (username, email, passwd) values (?,?,?)");
        $stmt->execute(array($user->getUsername(), $user->getEmail(), $user->getPasswd()));
    }

    public function findByUsername($username)
    {

        $stmt = $this->db->prepare("SELECT * FROM users WHERE username=?");
        $stmt->execute(array($username));
        $users = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($users != null) {
            return new User(
                $users["username"],
                $users["passwd"],
                $users["email"],
                $users["nfollowers"],
                $users["nfollowing"]);
        } else {
            return NULL;
        }

    }

    public function usernameExists($username)
    {
        $stmt = $this->db->prepare("SELECT count(username) FROM users where username=?");
        $stmt->execute(array($username));

        if ($stmt->fetchColumn() > 0) {
            return true;
        }
    }

    public function emailExists($email)
    {
        $stmt = $this->db->prepare("SELECT count(email) FROM users where email=?");
        $stmt->execute(array($email));

        if ($stmt->fetchColumn() > 0) {
            return true;
        }
    }

    public function isValidUser($username, $passwd)
    {
        $stmt = $this->db->prepare("SELECT count(username) FROM users where username=? and passwd=?");
        $stmt->execute(array($username, $passwd));

        if ($stmt->fetchColumn() > 0) {
            return true;
        }
    }

    public function findTop5ByFollowers(){
        $stmt = $this->db->query("SELECT username FROM users ORDER BY nfollowers DESC LIMIT 0,5");

        $top5 = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $top5;
    }
}