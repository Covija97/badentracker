<?php

function linkDB ():mysqli  {
    $servername = "localhost";
    $database = "badentracker";
    $username = "root";
    $password = "";
    
    return new mysqli($servername, $username, $password, $database);
}

function getTable(): string {
    return "badentracker";
}

?>