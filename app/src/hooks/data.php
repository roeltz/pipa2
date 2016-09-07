<?php

use Pipa\Data\ConnectionManager;
use Pipa\Data\Source\MySQL\MySQLDataSource;

$pipeline->add("data<routing", function($next) use($context){
    ConnectionManager::set("default", function(){
        return new MySQLDataSource("kakarchive");
    });
	$next();
});
