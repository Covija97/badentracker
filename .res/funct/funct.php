<?php

function linkDB ():mysqli  {
    $servername = "localhost";
    $database = "badentracker";
    $username = "bt";
    $password = "BadenTracker2025*";
    
    return new mysqli($servername, $username, $password, $database);
}

function getTable(): string {
    return "badentracker";
}

?>