<?php
//file: model/LikeMapper.php

require_once(__DIR__ . "/../core/PDOConnection.php");
require_once(__DIR__ . "/../model/Like.php");

class LikeMapper
{

    private $db;

    public function __construct()
    {
        $this->db = PDOConnection::getInstance();
    }

    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT id FROM likes WHERE username=?");
        $stmt->execute(array($username));
        $toret = array();
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $value) {
            array_push($toret, $value);
        }
        return $toret;
    }

    public function isLike($username, $id)
    {
        $stmt = $this->db->prepare("SELECT id FROM likes WHERE username=? and id=?");
        $stmt->execute(array($username, $id));

        return $stmt->fetchColumn() > 0;
    }

    public function save($like)
    {
        $stmt = $this->db->prepare("INSERT INTO likes(id,username) values (?,?)");
        $stmt->execute(array($like->getId(), $like->getUsername()));
        return $this->db->lastInsertId();
    }

    public function delete($like)
    {
        $stmt = $this->db->prepare("DELETE from likes WHERE id=? AND username=?");
        $stmt->execute(array($like->getId(), $like->getUsername()));
    }

}