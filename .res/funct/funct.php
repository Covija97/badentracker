<?php

function linkDB ():mysqli  {
    $servername = "localhost";
    $database = "badentracker";
    $username = "bt";
    $password = "badentracker*";
    
    return new mysqli($servername, $username, $password, $database);
}

function getTable(): string {
    return "badentracker";
}

?>