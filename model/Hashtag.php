<?php
//file: model/Hashtag.php

class Hashtag
{
    public static $regexpContentHashtag = "/[a-zA-Z0-9]+/";
    public static $regexpHashtag = "/#([a-zA-Z0-9]+)/";

    private $id;
    private $hashtag;

    public function __construct($id=NULL, $hashtag=NULL) {
        $this->id = $id;
        $this->hashtag = $hashtag;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getHashtag()
    {
        return $this->hashtag;
    }

    public function setHashtag($hashtag)
    {
        $this->hashtag = $hashtag;
    }



    public static function isValidContentHashtag($hashtag) {
        return preg_match(Hashtag::$regexpContentHashtag, $hashtag);
    }

    public static function isValidHashtag($hashtag) {
        return preg_match(Hashtag::$regexpHashtag, $hashtag);
    }


}
