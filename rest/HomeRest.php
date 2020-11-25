<?php

require_once(__DIR__ . "/BaseRest.php");

require_once(__DIR__ . "/../model/Video.php");
require_once(__DIR__ . "/../model/VideoMapper.php");
require_once(__DIR__ . "/../model/FollowerMapper.php");
require_once(__DIR__ . "/../model/HashtagMapper.php");
require_once(__DIR__ . "/../model/UserMapper.php");
require_once(__DIR__ . "/../model/LikeMapper.php");


class HomeRest extends BaseRest
{
    private $videoMapper;
    private $followerMapper;
    private $hashtagMapper;
    private $userMapper;
    private $likeMapper;


    public function __construct()
    {
        $this->videoMapper = new VideoMapper();
        $this->followerMapper = new FollowerMapper();
        $this->hashtagMapper = new HashtagMapper();
        $this->userMapper = new UserMapper();
        $this->likeMapper = new LikeMapper();
    }


    public function getPrivateHome($username, $nPage)
    {
        $currentLogged = parent::authenticateUser();

        if ($currentLogged->getUsername() != $username) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo(json_encode(array("error" => "Unauthorized")));
        } else {
            $user = $this->userMapper->findByUsername($currentLogged->getUsername());
            if ((int)$user->getNfollowings() > 0) {
                $nVideos = $this->videoMapper->countVideosByFollower($currentLogged->getUsername());
            } else {
                $nVideos = $this->videoMapper->countVideos();
            }

            if ($nVideos == 0) {
                $nPags = 1;
            } else {
                $nPags = ceil($nVideos / 6);
            }

            if (preg_match('/^[0-9]+$/', $nPage) && ($temp = (int)$nPage) < $nPags) {
                $page = $temp;
                $toret = array();

                if ((int)$user->getNfollowings() > 0) {
                    $toret["videos"] = $this->videoMapper->findAllByFollower($currentLogged->getUsername(), $page);
                } else {
                    $toret["videos"] = $this->videoMapper->findAll($page);
                }

                $toret["likes"] = $this->likeMapper->findByUsername($currentLogged->getUsername());
                $toret["followings"] = $this->followerMapper->findFollowingByUsername($currentLogged->getUsername());


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

        }
    }

    public function getPublicHome($nPage)
    {
        $nVideos = $this->videoMapper->countVideos();
        if ($nVideos == 0) {
            $nPags = 1;
        } else {
            $nPags = ceil($nVideos / 6);
        }

        if (preg_match('/^[0-9]+$/', $nPage) && ($temp = (int)$nPage) < $nPags) {
            $page = $temp;
            $toret = array();

            $toret["videos"] = $this->videoMapper->findAll($page);

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
    }


}

$homeRest = new HomeRest();
URIDispatcher::getInstance()
    ->map("GET", "/home/$1/$2", array($homeRest, "getPrivateHome")) //Home pública
    ->map("GET", "/home/$1", array($homeRest, "getPublicHome")); //Home privada