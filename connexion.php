<?php

    try{
        $conn = new PDO("mysql:host=localhost;dbname=smi6", "root", "");
    }
    catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }

?>