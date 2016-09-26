<?php

include_once "vendor/autoload.php";
use Pipa\Error\ErrorHandler;
use Pipa\Error\StdoutErrorDisplay;
use Pipa\ORM\Entity;
use Pipa\Data\ConnectionManager;
use Pipa\Data\Source\MySQL\MySQLDataSource;

ErrorHandler::addDisplay(new StdoutErrorDisplay);
ErrorHandler::register();

ConnectionManager::set("default", function(){
    return new MySQLDataSource("kakarchive");
});

/** @Collection("kak_users") */
class User extends Entity {

    /** @Id @AliasOf("userid") */
    public $id;

    /** @Id @AliasOf("pname") */
    public $name;

	/** @Many(class = "Post", fk = "ownerid") */
	public $posts;

}

/** @Collection("kak_topics") */
class Topic extends Entity {

	/** @Id @AliasOf("topicid") */
	public $id;

	public $title;

	/** @One(class = "User", fk = "ownerid") @NotNull */
	public $owner;
}

/** @Collection("kak_posts") */
class Post extends Entity {

    /** @Id @AliasOf("postid") */
    public $id;

    /** @NotNull */
    public $subject;

    /** @NotNull */
    public $content;

    /** @NotNull @AliasOf("docr") */
    public $date;

    /** @One(class = "Topic", fk = "topicid") @NotNull */
    public $topic;

    /** @One(class = "User", fk = "ownerid") @NotNull */
    public $owner;

}

/*
$users = User::getCriteria()
	->like("posts.subject", "%chiste%")
	->distinct()
	->queryAll();
*/

$posts = Post::getCriteria()
	->eq("topic.owner", "admin")
	->orderBy("topic.title")
	->queryAll();
