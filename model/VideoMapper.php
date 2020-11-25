<?php
//file: model/VideoMapper.php

require_once(__DIR__ . "/../core/PDOConnection.php");
require_once(__DIR__ . "/../model/Video.php");

class VideoMapper
{

    private $db;

    public function __construct()
    {
        $this->db = PDOConnection::getInstance();
    }

    public function findAll($pagina = 0, $per_page = 6)
    {
        $stmt = $this->db->prepare("SELECT * FROM videos ORDER BY videos.videodate DESC LIMIT ?,?");
        $offset = $pagina * $per_page;
        $stmt->execute(array($offset, $per_page));
        $videos_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $videos = array();

        foreach ($videos_db as $vi) {
            $video = new Video($vi["id"], $vi["videoname"], $vi["videodescription"], $vi["videodate"], $vi["author"], $vi["nlikes"]);
            array_push($videos, $video->toArray());
        }

        return $videos;
    }

    public function countVideos()
    {
        $stmt = $this->db->query("SELECT COUNT(id) FROM videos");
        return $stmt->fetchColumn();
    }

    public function findById($videoid)
    {
        $stmt = $this->db->prepare("SELECT * FROM videos WHERE id=?");
        $stmt->execute(array($videoid));
        $videos = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($videos != null) {
            return new Video(
                $videos["id"],
                $videos["videoname"],
                $videos["videodescription"],
                $videos["videodate"],
                $videos["author"],
                $videos["nlikes"]);
        } else {
            return null;
        }
    }

    public function findAllByFollower($follower, $pagina = 0, $per_page = 6)
    {
        $stmt = $this->db->prepare("SELECT videos.id, videos.videoname, videos.videodescription, videos.videodate, videos.author, videos.nlikes FROM videos, followers WHERE followers.username_follower = ? AND followers.username_following=videos.author ORDER BY videos.videodate DESC LIMIT ?,?");
        $offset = $pagina * $per_page;
        $stmt->execute(array($follower, $offset, $per_page));
        $videos_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $videos = array();

        foreach ($videos_db as $vi) {
            $video = new Video($vi["id"], $vi["videoname"], $vi["videodescription"], $vi["videodate"], $vi["author"], $vi["nlikes"]);
            array_push($videos, $video->toArray());
        }

        return $videos;
    }

    public function findAllByHashtag($hashtag, $pagina = 0, $per_page = 6)
    {
        $stmt = $this->db->prepare("SELECT videos.id, videos.videoname, videos.videodescription, videos.videodate, videos.author, videos.nlikes FROM videos, hashtags WHERE hashtags.hashtag = ? AND hashtags.id=videos.id ORDER BY videos.videodate DESC LIMIT ?,?");
        $offset = $pagina * $per_page;
        $stmt->execute(array($hashtag, $offset, $per_page));
        $videos_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $videos = array();

        foreach ($videos_db as $vi) {
            $video = new Video($vi["id"], $vi["videoname"], $vi["videodescription"], $vi["videodate"], $vi["author"], $vi["nlikes"]);
            array_push($videos, $video->toArray());
        }

        return $videos;
    }

    public function countVideosByFollower($follower)
    {
        $stmt = $this->db->prepare("SELECT COUNT(videos.id) FROM videos, followers WHERE followers.username_follower = ? AND followers.username_following=videos.author");
        $stmt->execute(array($follower));
        return $stmt->fetchColumn();
    }

    public function countVideosByHashtag($hashtag)
    {
        $stmt = $this->db->prepare("SELECT COUNT(videos.id) FROM videos, hashtags WHERE hashtags.hashtag = ? AND hashtags.id=videos.id");
        $stmt->execute(array($hashtag));
        return $stmt->fetchColumn();
    }


    public function findAllByAuthor($author)
    {
        $stmt = $this->db->prepare("SELECT * FROM videos WHERE author=? ORDER BY videodate DESC ");
        $stmt->execute(array($author));
        $videos_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $videos = array();

        foreach ($videos_db as $vi) {
            $video = new Video($vi["id"], $vi["videoname"], $vi["videodescription"], $vi["videodate"], $vi["author"], $vi["nlikes"]);
            array_push($videos, $video->toArray());
        }

        return $videos;
    }

    public function save($video)
    {
        $stmt = $this->db->prepare("INSERT INTO videos(videoname, videodescription,author) values (?,?,?)");
        $stmt->execute(array($video->getVideoname(), $video->getVideodescription(), $video->getAuthor()));

        return $this->db->lastInsertId("id");
    }

    public function delete($video)
    {
        $stmt = $this->db->prepare("DELETE from videos WHERE id=?");
        $stmt->execute(array($video->getId()));
        unlink(__DIR__ ."/../upload_videos/".$video->getVideoname());
    }

    public function uploadVideo()
    {
        $toret = array();
        $errors = array();
        $errors_video = array();

        if (isset($_FILES['videoUpload']) && $_FILES['videoUpload']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['videoUpload']['tmp_name'];
            $fileName = $_FILES['videoUpload']['name'];

            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            if ($fileExtension != "mp4") {
                array_push($errors_video, "Only .mp4 videos");
            }

            if(empty($errors_video)){
                $newFileName = time() . '_' . $_SESSION["currentuser"] . '.' . $fileExtension;
                $dest_path = __DIR__ . "/../upload_videos/" . $newFileName;

                $toret["fileName"] = $newFileName;
                if (!move_uploaded_file($fileTmpPath, $dest_path)) {
                    array_push($errors_video, "Error uploading video");
                }
            }

        } else {
            array_push($errors_video, "Error uploading video");
        }

        if (empty($errors_video)) {
            return $toret;
        } else {
            $errors["video"] = $errors_video;
            throw new ValidationException($errors, "error uploading video");
        }
    }

}