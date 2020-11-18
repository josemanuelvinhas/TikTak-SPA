DROP DATABASE IF EXISTS TIKTAK;

CREATE DATABASE TIKTAK CHARACTER SET utf8mb4;

USE TIKTAK;

CREATE OR REPLACE TABLE USERS(
    username VARCHAR(15),
    email VARCHAR(320) NOT NULL UNIQUE,
    passwd VARCHAR(20) NOT NULL,
    nfollowers INT unsigned NOT NULL DEFAULT 0,
	nfollowing INT unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (username)
) ENGINE = INNODB DEFAULT CHARACTER SET = utf8;

CREATE OR REPLACE TABLE VIDEOS(
    id INT unsigned auto_increment,
    videoname VARCHAR(30) NOT NULL,
    videodescription VARCHAR(320) NOT NULL,
    videodate DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    author VARCHAR(15) NOT NULL,
    nlikes INT unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    FOREIGN KEY (author) REFERENCES USERS(username) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARACTER SET = utf8;

CREATE OR REPLACE TABLE HASHTAGS(
    id INT unsigned,
    hashtag VARCHAR(320) NOT NULL,
    PRIMARY KEY (id, hashtag),
    FOREIGN KEY (id) REFERENCES VIDEOS(id) ON DELETE CASCADE
)ENGINE = INNODB DEFAULT CHARACTER SET = utf8;

CREATE OR REPLACE TABLE LIKES(
    username VARCHAR(15),
    id INT unsigned,
    PRIMARY KEY (username, id),
    FOREIGN KEY (username) REFERENCES USERS(username) ON DELETE CASCADE,
    FOREIGN KEY (id) REFERENCES VIDEOS(id) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARACTER SET = utf8;

CREATE OR REPLACE TABLE FOLLOWERS(
    username_follower VARCHAR(15),
    username_following VARCHAR(15),
    PRIMARY KEY (username_follower, username_following),
    FOREIGN KEY (username_follower) REFERENCES USERS(username) ON DELETE CASCADE,
    FOREIGN KEY (username_following) REFERENCES USERS(username) ON DELETE CASCADE
) ENGINE = INNODB DEFAULT CHARACTER SET = utf8;

DELIMITER //
CREATE OR REPLACE TRIGGER increase_likes
AFTER INSERT ON LIKES
FOR EACH ROW
BEGIN
    DECLARE numlikes INT;
    SELECT COUNT(*) INTO numlikes FROM LIKES WHERE LIKES.id = NEW.id;
    UPDATE VIDEOS SET nlikes = numlikes WHERE VIDEOS.id = NEW.id;
END//

DELIMITER //
CREATE OR REPLACE TRIGGER decrease_likes
AFTER DELETE ON LIKES
FOR EACH ROW
BEGIN
    DECLARE numlikes INT;
    SELECT COUNT(*) INTO numlikes FROM LIKES WHERE LIKES.id = OLD.id;
    UPDATE VIDEOS SET nlikes = numlikes WHERE VIDEOS.id = OLD.id;
END//

DELIMITER //
CREATE OR REPLACE TRIGGER increase_follower
AFTER INSERT ON FOLLOWERS
FOR EACH ROW
BEGIN
    DECLARE numfollowers INT;
	DECLARE numfollowings INT;

    SELECT COUNT(*) INTO numfollowers FROM FOLLOWERS WHERE FOLLOWERS.username_following = NEW.username_following;
    UPDATE USERS SET nfollowers = numfollowers WHERE USERS.username = NEW.username_following;

    SELECT COUNT(*) INTO numfollowings FROM FOLLOWERS WHERE FOLLOWERS.username_follower = NEW.username_follower;
    UPDATE USERS SET nfollowing = numfollowings WHERE USERS.username = NEW.username_follower;
END//

DELIMITER //
CREATE OR REPLACE TRIGGER decrease_follower
AFTER DELETE ON FOLLOWERS
FOR EACH ROW
BEGIN
    DECLARE numfollowers INT;
	DECLARE numfollowings INT;

    SELECT COUNT(*) INTO numfollowers FROM FOLLOWERS WHERE FOLLOWERS.username_following = OLD.username_following;
    UPDATE USERS SET nfollowers = numfollowers WHERE USERS.username = OLD.username_following;

    SELECT COUNT(*) INTO numfollowings FROM FOLLOWERS WHERE FOLLOWERS.username_follower = OLD.username_follower;
    UPDATE USERS SET nfollowing = numfollowings WHERE USERS.username = OLD.username_follower;
END//

grant all privileges on tiktak.* to mvcuser@localhost identified by "mvctiktakpass";