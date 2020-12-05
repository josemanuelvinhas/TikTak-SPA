<?php
//file: model/HashtagMapper.php

require_once(__DIR__."/../core/PDOConnection.php");
require_once(__DIR__."/../model/Hashtag.php");

class HashtagMapper
{

    private $db;

    public function __construct()
    {
        $this->db = PDOConnection::getInstance();
    }

    public function save($hashtag) {
        $stmt = $this->db->prepare("INSERT INTO hashtags(id,hashtag) values (?,?)");
        $stmt->execute(array($hashtag->getId(), $hashtag->getHashtag()));
    }

    public function delete($hashtag) {
        $stmt = $this->db->prepare("DELETE from hashtags WHERE id=? AND hashtag=?");
        $stmt->execute(array($hashtag->getId(), $hashtag->getHashtag()));
    }
    public function findTop5Hashtag()
    {
        $stmt = $this->db->query(" SELECT hashtag,COUNT(hashtag) AS num FROM `hashtags` GROUP by hashtag ORDER by num DESC LIMIT 0,5");
        $hashtag = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $hashtags = array();

        foreach ($hashtag as $hg) {
            array_push($hashtags, substr($hg["hashtag"],1));
        }

        return $hashtags;
    }


}