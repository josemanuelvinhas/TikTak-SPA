<?php
//file: model/Follower.php


class Follower
{

    private $username_follower;
    private $username_following;

    public function __construct($username_follower=NULL, $username_following=NULL) {
        $this->username_follower = $username_follower;
        $this->username_following = $username_following;
    }

    public function getUsernameFollower()
    {
        return $this->username_follower;
    }

    public function setUsernameFollower($username_follower)
    {
        $this->username_follower = $username_follower;
    }

    public function getUsernameFollowing()
    {
        return $this->username_following;
    }

    public function setUsernameFollowing($username_following)
    {
        $this->username_following = $username_following;
    }

}
