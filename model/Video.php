<?php
//file: model/Video.php

require_once(__DIR__ . "/../core/ValidationException.php");

class Video
{

    private $id;
    private $videoname;
    private $videodescription;
    private $videodate;
    private $author;
    private $nlikes;

    public function __construct($id=NULL, $videoname=NULL,$videodescription=NULL,$videodate=NULL,$author=NULL,$nlikes=NULL) {
        $this->id = $id;
        $this->videoname = $videoname;
        $this->videodescription = $videodescription;
        $this->videodate = $videodate;
        $this->author = $author;
        $this->nlikes = $nlikes;

    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getVideoname()
    {
        return $this->videoname;
    }

    public function setVideoname($videoname)
    {
        $this->videoname = $videoname;
    }

    public function getVideodescription()
    {
        return $this->videodescription;
    }

    public function setVideodescription($videodescription)
    {
        $this->videodescription = $videodescription;
    }

    public function getVideodate()
    {
        return $this->videodate;
    }

    public function setVideodate($videodate)
    {
        $this->videodate = $videodate;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor($author)
    {
        $this->author = $author;
    }

    public function getNlikes()
    {
        return $this->nlikes;
    }

    public function setNlikes($nlikes)
    {
        $this->nlikes = $nlikes;
    }

    public function checkIsValidForUpload()
    {
        $errors = array();

        $tempErr = $this->checkDescription();
        if (!empty($tempErr)) {
            $errors["videodescription"] = $tempErr;
        }

        if (sizeof($errors) > 0) {
            throw new ValidationException($errors, "video is not valid");
        }
    }

    private function checkDescription()
    {
        $var = $this->getVideodescription();
        $message = "Insert between 0 and 320 numbers, letters or #";
        $pattern = "/^[a-zA-Z0-9À-ÿñÑ #]{0,320}$/";

        $errors = array();
        if (!preg_match($pattern, $var)) {
            array_push($errors, $message);
        }
        return $errors;
    }


}