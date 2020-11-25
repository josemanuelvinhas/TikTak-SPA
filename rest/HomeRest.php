<?php

require_once(__DIR__ . "/BaseRest.php");

require_once(__DIR__ . "/../model/Video.php");
require_once(__DIR__ . "/../model/VideoMapper.php");


class HomeRest extends BaseRest
{
    private $videoMapper;


    public function __construct()
    {
        $this->videoMapper = new VideoMapper();
    }


    public function getPrivateHome($username, $page)
    {


        header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
        header('Content-Type: application/json');
        echo(json_encode(array("metodo"=>"private")));

    }

    public function getPublicHome($page)
    {
        header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
        header('Content-Type: application/json');
        echo(json_encode(array("metodo"=>"public")));
    }


}

$homeRest = new HomeRest();
URIDispatcher::getInstance()
    ->map("GET", "/home/$/$2", array($homeRest, "getPrivateHome")) //Home pública
    ->map("GET", "/home/$1", array($homeRest, "getPublicHome")); //Home privada