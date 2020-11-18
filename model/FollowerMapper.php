<?php
//file: model/FollowerMapper.php

require_once(__DIR__ . "/../core/PDOConnection.php");
require_once(__DIR__ . "/../model/Follower.php");

class FollowerMapper
{

    private $db;

    public function __construct()
    {
        $this->db = PDOConnection::getInstance();
    }

    public function findFollowersByUsername($username_following)
    {
        $stmt = $this->db->prepare("SELECT username_follower FROM followers WHERE username_following=?");
        $stmt->execute(array($username_following));
        $followers_db = $stmt->fetch(PDO::FETCH_ASSOC);

        return $followers_db;
    }

    public function findFollowingByUsername($username_follower)
    {
        $stmt = $this->db->prepare("SELECT username_following FROM followers WHERE username_follower=?");
        $stmt->execute(array($username_follower));
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function isFollowing($following, $follower)
    {
        $stmt = $this->db->prepare("SELECT username_following FROM followers WHERE username_follower=? and username_following=?");
        $stmt->execute(array($following,$follower));

        if ($stmt->rowCount() > 0)
            return true;
        else
            return false;
    }


    public function save($followers)
    {
        $stmt = $this->db->prepare("INSERT INTO followers(username_follower,username_following) values (?,?)");
        $stmt->execute(array($followers->getUsernameFollower(), $followers->getUsernameFollowing()));
    }

    public function delete($followers)
    {
        $stmt = $this->db->prepare("DELETE from followers WHERE username_follower=? AND username_following=?");
        $stmt->execute(array($followers->getUsernameFollower(), $followers->getUsernameFollowing()));
    }

}