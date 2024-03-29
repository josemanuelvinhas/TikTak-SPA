<?php

require_once(__DIR__ . "/BaseRest.php");

require_once(__DIR__ . "/../model/User.php");
require_once(__DIR__ . "/../model/UserMapper.php");
require_once(__DIR__ . "/../model/VideoMapper.php");
require_once(__DIR__ . "/../model/FollowerMapper.php");


class UserRest extends BaseRest
{
    private $userMapper;
    private $videoMapper;
    private $followerMapper;

    public function __construct()
    {
        parent::__construct();

        $this->userMapper = new UserMapper();
        $this->videoMapper = new VideoMapper();
        $this->followerMapper = new FollowerMapper();
    }

    public function register($data)
    {
        $errorsData = array();

        if (!isset($data->username)) {
            $errorsData["username"] = "It is necessary to send the username";
        }

        if (!isset($data->password)) {
            $errorsData["password"] = "It is necessary to send the password";
        }

        if (!isset($data->email)) {
            $errorsData["email"] = "It is necessary to send the email";
        }

        if (!empty($errorsData)) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo(json_encode($errorsData));
        } else {
            $user = new User($data->username, $data->password, $data->email);

            try {
                $user->checkIsValidForRegister();

                $errorsExists = array();
                if ($this->userMapper->usernameExists($user->getUsername())) {
                    $errorsExists["username"] = ["Username already exists"];
                }
                if ($this->userMapper->emailExists($user->getEmail())) {
                    $errorsExists["email"] = ["Email already exists"];
                }

                if (empty($errorsExists)) {
                    $this->userMapper->save($user);

                    header($_SERVER['SERVER_PROTOCOL'] . ' 201 Created');
                    header("Location: " . $_SERVER['REQUEST_URI'] . "/" . $data->username);
                } else {
                    http_response_code(400);
                    header('Content-Type: application/json');
                    echo(json_encode($errorsExists));
                }
            } catch (ValidationException $e) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo(json_encode($e->getErrors()));
            }
        }
    }

    public function login($username)
    {
        $currentLogged = parent::authenticateUser();
        if ($currentLogged->getUsername() != $username) {
            header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
            echo("You are not authorized to login as anyone but you");
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
            echo("Hello " . $username);
        }
    }

    public function profile($username)
    {

        $usuario = $this->userMapper->findByUsername($username);

        if ($usuario != null) {
            $usuario = $usuario->toArray();
            $videos = $this->videoMapper->findAllByAuthor($username);

            header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
            header('Content-Type: application/json');

            $currentLogged = parent::isAuthenticateUser();
            if ($currentLogged != false && $currentLogged->getUsername() != $username) {
                $following = $this->followerMapper->isFollowing($currentLogged->getUsername(), $username);
                echo(json_encode(array("user" => $usuario, "videos" => $videos, "following" => $following)));

            } else {
                echo(json_encode(array("user" => $usuario, "videos" => $videos)));
            }

        } else {
            http_response_code(400);
            header('Content-Type: application/json');
            echo(json_encode(array("error" => "Username does not exist")));
        }
    }

}

$userRest = new UserRest();
URIDispatcher::getInstance()
    ->map("GET", "/user/$1", array($userRest, "login")) //Login
    ->map("POST", "/user", array($userRest, "register")) //Register
    ->map("GET", "/user/profile/$1", array($userRest, "profile")); //Profile
