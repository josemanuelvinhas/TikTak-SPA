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

require_once(__DIR__ . "/../model/HashtagMapper.php");


class VideoRest extends BaseRest
{
    private $videoMapper;
    private $likeMapper;
    private $userMapper;
    private $followerMapper;
    private $hashtagMapper;

    public function __construct()
    {
        parent::__construct();

        $this->videoMapper = new VideoMapper();
        $this->userMapper = new UserMapper();
        $this->likeMapper = new LikeMapper();
        $this->followerMapper = new FollowerMapper();
        $this->hashtagMapper = new HashtagMapper();
    }

    public function getVideo($id)
    {
        $video = $this->videoMapper->findById($id);

        if ($video === null) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo(json_encode(array("error" => "The video doesn't exists")));
        } else {
            $array = array();
            $currentLogged = parent::isAuthenticateUser();
            if ($currentLogged != null) {
                $likes = $this->likeMapper->isLike($currentLogged->getUsername(), $id);
                $following = $this->followerMapper->isFollowing($currentLogged->getUsername(), $video->getAuthor());
                $array["like"] = $likes;
                $array["following"] = $following;
            }
            $array["video"] = $video->toArray();
            header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
            header('Content-Type: application/json');
            echo(json_encode($array));
        }
    }

    public function deleteVideo($id)
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
            } else {
                http_response_code(401);
                header('Content-Type: application/json');
                echo(json_encode(array("error" => "Unauthorized")));
            }
        }
    }

    public function getVideosByHashtag($hashtag, $nPage)
    {
        if (Hashtag::isValidContentHashtag($hashtag)) {

            $nVideos = $this->videoMapper->countVideosByHashtag("#" . $hashtag);
            if ($nVideos == 0) {
                $nPags = 1;
            } else {
                $nPags = ceil($nVideos / 6);
            }

            if (preg_match('/^[0-9]+$/', $nPage) && ($temp = (int)$nPage) < $nPags) {
                $page = $temp;
                $toret = array();

                $toret["videos"] = $this->videoMapper->findAllByHashtag("#" . $hashtag, $page);

                $currentLogged = parent::isAuthenticateUser();
                if ($currentLogged != null) {
                    $toret["likes"] = $this->likeMapper->findByUsername($currentLogged->getUsername());
                    $toret["followings"] = $this->followerMapper->findFollowingByUsername($currentLogged->getUsername());
                }

                if ($nPags > 1) {
                    if ($page == 0) {
                        $toret["next"] = true;
                        $toret["previous"] = false;
                    } elseif ($page == ($nPags - 1)) {
                        $toret["next"] = false;
                        $toret["previous"] = true;
                    } else {
                        $toret["next"] = true;
                        $toret["previous"] = true;
                    }
                }

                $toret["topUsers"] = $this->userMapper->findTop5ByFollowers();
                $toret["trends"] = $this->hashtagMapper->findTop5Hashtag();

                header($_SERVER['SERVER_PROTOCOL'] . ' 200 Ok');
                header('Content-Type: application/json');
                echo(json_encode($toret));

            } else {
                http_response_code(400);
                header('Content-Type: application/json');
                echo(json_encode(array("error" => "Page not valid")));
            }


        } else {
            http_response_code(400);
            header('Content-Type: application/json');
            echo(json_encode(array("error" => "The hashtag isn't valid")));
        }

    }

    public function uploadVideo()
    {
        $currentLogged = parent::authenticateUser();
        $video = new Video();

        try {
            $uploadVideo = $this->videoMapper->uploadVideo($currentLogged->getUsername());

            $video->setVideodescription($_POST["description"]);
            $video->setVideoname($uploadVideo["fileName"]);
            $video->setAuthor($currentLogged->getUsername());

            $video->checkIsValidForUpload();
        } catch (ValidationException $ex) {
            if (isset($video) && $video->getVideoname() != "") {
                unlink(__DIR__ . "/../upload_videos/" . $video->getVideoname());
            }
            $errors = $ex->getErrors();
        }

        if (empty($errors)) {
            $id = $this->videoMapper->save($video);

            preg_match_all(Hashtag::$regexpHashtag . "u", $video->getVideodescription(), $matches);

            $hashtags = array_unique($matches[0]);
            foreach ($hashtags as $hs) {
                $hashtag = new Hashtag((int)$id, $hs);
                $this->hashtagMapper->save($hashtag);
            }
            header($_SERVER['SERVER_PROTOCOL'] . ' 201 Created');
            header('Content-Type: application/json');
            echo(json_encode(array("id_video" => $id)));
        } else {
            http_response_code(400);
            header('Content-Type: application/json');
            echo(json_encode($errors));
        }

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
    ->map("GET", "/video/$1", array($videoRest, "getVideo")) //getVideo
    ->map("GET", "/video/hashtag/$1/$2", array($videoRest, "getVideosByHashtag")) //Obtener videos hashtag
    ->map("POST", "/video", array($videoRest, "uploadVideo"))//Subir un video
    ->map("DELETE", "/video/$1", array($videoRest, "deleteVideo")) //deleteVideo
    ->map("DELETE", "/video/like/$1", array($videoRest, "dislike")) //Dislike
    ->map("POST", "/video/like/$1", array($videoRest, "like")); //Like

