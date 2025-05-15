<?php

function linkDB ():mysqli  {
    $servername = "localhost";
    $database = "badentracker";
    $username = "root";
    $password = "";
    
    $conx new mysqli($servername, $username, $password, $database);

    if($conx->connect_errno) {
        printf("Connect failed: %s\n", $conx->connect_error);
        exit();
    } else {
        return $conx;
        $db = mysqli_select_db($conx, $db);
    }

}

function getTable(): string {
    return "badentracker";
}

?>