<?php

require_once(__DIR__ . "/BaseRest.php");

require_once(__DIR__ . "/../model/Video.php");
require_once(__DIR__ . "/../model/VideoMapper.php");
require_once(__DIR__ . "/../model/User.php");
require_once(__DIR__ . "/../model/UserMapper.php");
require_once(__DIR__ . "/../model/Like.php");
require_once(__DIR__ . "/../model/LikeMapper.php");
require_once(__DIR__ . "/../model/Follower.php");
require_once(__DIR__ . "/../model/FollowerMapper.php");

class VideoRest extends BaseRest
{
    private $videoMapper;

    public function __construct()
    {
        parent::__construct();

        $this->videoMapper = new VideoMapper();
        $this->userMapper = new UserMapper();
        $this->likeMapper = new LikeMapper();
        $this->followMapper = new FollowerMapper();
    }

    public function getVideo($id)
    {
        $video = $this->videoMapper->findById("$id");
        if ($video === null) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo(json_encode(array("error" => "The video doesn't exists")));
        } else {
            $array = array();
            if (($currentLogged = parent::isAuthenticateUser()) !== null) {

                $likes = $this->likeMapper->isLike($currentLogged->getUsername(), "$id");
                $following = $this->followMapper->isFollowing($currentLogged->getUsername(), $video->getAuthor());
                $array["like"] = $likes;
                $array["following"] = $following;
            }
            $array["video"] = $video;
            header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
            header('Content-Type: application/json');
            echo(json_encode($array));
        }
    }

    public function deleteVideo($id)
    {

    }
    public function getVideosByHashtag($hashtag)
    {

    }
    public function uploadVideo($id)
    {
        $video = $this->videoMapper->findById("$id");
        if ($video === null) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo(json_encode(array("error" => "The video doesn't exists")));
        } else {

            if (($currentLogged = parent::authenticateUser()) !== null && ($video->getAuthor() === $currentLogged->getUsername())) {
                $this->videoMapper->delete($video);
                header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
            }else{
                http_response_code(401);
                header('Content-Type: application/json');
                echo(json_encode(array("error" => "Unauthorized")));
            }

        }
    }

}

$videoRest = new VideoRest();
URIDispatcher::getInstance()
    ->map("GET", "/video/$1", array($videoRest, "getVideo")) //getVideo
    ->map("GET", "/video/hashtag/$1", array($videoRest, "getVideosByHashtag")) //getVideosByHashtag
    ->map("POST", "/video", array($videoRest, "uploadVideo"))//uploadVideo
    ->map("DELETE", "/video/$1", array($videoRest, "deleteVideo")); //deleteVideo