<?php

require_once(__DIR__ . "/BaseRest.php");

require_once(__DIR__ . "/../model/Video.php");
require_once(__DIR__ . "/../model/VideoMapper.php");

require_once(__DIR__ . "/../model/Like.php");
require_once(__DIR__ . "/../model/LikeMapper.php");


class VideoRest extends BaseRest
{
    private $videoMapper;
    private $likeMapper;

    public function __construct()
    {
        parent::__construct();

        $this->videoMapper = new VideoMapper();
        $this->likeMapper = new LikeMapper();
    }


    public function like($id)
    {
        $currentLogged = parent::authenticateUser();

        $video = $this->videoMapper->findById($id);

        if ($video == null) { //Si no existe el video
            http_response_code(400);
            header('Content-Type: application/json');
            echo(json_encode(array("error" => "The video doesn't exists")));
        } else if ($this->likeMapper->isLike($currentLogged->getUsername(), $id)) { //Si ya existe el like
            http_response_code(400);
            header('Content-Type: application/json');
            echo(json_encode(array("error" => "You've already liked the video")));
        } else { //Ningun problema -> Se da like
            $like = new Like($id, $currentLogged->getUsername());
            $this->likeMapper->save($like);
            header($_SERVER['SERVER_PROTOCOL'] . ' 201 Created');
        }

    }

    public function dislike($id)
    {
        $currentLogged = parent::authenticateUser();

        $video = $this->videoMapper->findById($id);

        if ($video == null) { //Si no existe el video
            http_response_code(400);
            header('Content-Type: application/json');
            echo(json_encode(array("error" => "The video doesn't exists")));
        } else if (!$this->likeMapper->isLike($currentLogged->getUsername(), $id)) { //Si ya existe el like
            http_response_code(400);
            header('Content-Type: application/json');
            echo(json_encode(array("error" => "There is no like to delete")));
        } else { //Ningun problema -> Se da like
            $like = new Like($id, $currentLogged->getUsername());
            $this->likeMapper->delete($like);
            header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
        }

    }


}

$videoRest = new VideoRest();
URIDispatcher::getInstance()
    ->map("DELETE", "/video/like/$1", array($videoRest, "dislike")) //Dislike
    ->map("POST", "/video/like/$1", array($videoRest, "like")); //Like
