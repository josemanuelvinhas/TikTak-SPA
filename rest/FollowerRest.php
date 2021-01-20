<?php

require_once(__DIR__ . "/BaseRest.php");

require_once(__DIR__ . "/../model/Follower.php");
require_once(__DIR__ . "/../model/FollowerMapper.php");

require_once(__DIR__ . "/../model/UserMapper.php");

require_once(__DIR__ . "/../core/Mail.php");

class FollowerRest extends BaseRest
{
    private $followerMapper;
    private $userMapper;

    public function __construct()
    {
        parent::__construct();

        $this->followerMapper = new FollowerMapper();
        $this->userMapper = new UserMapper();
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


            $address = $this->userMapper->findByUsername($username)->getEmail();
            $subject = 'Tienes un nuevo seguidor en TikTak!';
            $body = 'Tu exito reciente en TikTak está dando sus frutos y has conseguido que ' . $follower->getUsernameFollower() . ' te siga.';

            $mail = new Mail($address, $username, $subject, $body);
            $mail->sendMail();

            header($_SERVER['SERVER_PROTOCOL'] . ' 201 Created');

        }


    }

    public function unfollow($username)
    {
        $currentLogged = parent::authenticateUser();

        $follower = new Follower($currentLogged->getUsername(), $username);

        if ($this->followerMapper->isFollowing($follower->getUsernameFollower(), $follower->getUsernameFollowing())) {
            $this->followerMapper->delete($follower);

            $address = $this->userMapper->findByUsername($username)->getEmail();
            $subject = 'Alguien te ha dejado de seguir en TikTak!';
            $body = 'Algo no estás haciendo bien en TikTak porque has hecho que ' . $follower->getUsernameFollower() . ' te deje de seguir.';

            $mail = new Mail($address, $username, $subject, $body);
            $mail->sendMail();

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