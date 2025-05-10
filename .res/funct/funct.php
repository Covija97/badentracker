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

function delete(string $table, string $columnaid, int $id){
    $conn = linkDB();

    $sql = "
    DELETE FROM $table
    WHERE $columnaid = $id;";
    
    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
    $conn->close();
    header("Location: ../");
    exit();
}

?>