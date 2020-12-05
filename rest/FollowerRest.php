<?php

require_once(__DIR__ . "/BaseRest.php");

require_once(__DIR__ . "/../model/Follower.php");
require_once(__DIR__ . "/../model/FollowerMapper.php");

class FollowerRest extends BaseRest
{
    private $followerMapper;

    public function __construct()
    {
        parent::__construct();

        $this->followerMapper = new FollowerMapper();
    }

    public function follow($username)
    {
        $currentLogged = parent::authenticateUser();

        $follower = new Follower($currentLogged->getUsername(), $username);

        if ($this->followerMapper->isFollowing($follower->getUsernameFollower(), $follower->getUsernameFollowing())) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo(json_encode(array("Error" => "Already follow this user")));
        } elseif ($follower->getUsernameFollower() == $follower->getUsernameFollowing()) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo(json_encode(array("Error" => "A user cannot follow himself")));
        } else {
            $this->followerMapper->save($follower);
            header($_SERVER['SERVER_PROTOCOL'] . ' 201 Created');
        }


    }

    public function unfollow($username)
    {
        $currentLogged = parent::authenticateUser();

        $follower = new Follower($currentLogged->getUsername(), $username);

        if ($this->followerMapper->isFollowing($follower->getUsernameFollower(), $follower->getUsernameFollowing())) {
            $this->followerMapper->delete($follower);
            header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
        } else {
            http_response_code(400);
            header('Content-Type: application/json');
            echo(json_encode(array("Error" => "You can't unfollow someone you don't follow")));
        }

    }

}

$followerRest = new FollowerRest();
URIDispatcher::getInstance()
    ->map("POST", "/follower/$1", array($followerRest, "follow"))
    ->map("DELETE", "/follower/$1", array($followerRest, "unfollow"));