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


    public function getPrivateHome($username, $data)
    {


    }

    public function getPublicHome()
    {

    }


}

$homeRest = new HomeRest();
URIDispatcher::getInstance()
    ->map("GET", "/home/$1", array($homeRest, "getPrivateHome")) //Home pública
    ->map("GET", "/home/", array($homeRest, "getPublicHome")); //Home privada