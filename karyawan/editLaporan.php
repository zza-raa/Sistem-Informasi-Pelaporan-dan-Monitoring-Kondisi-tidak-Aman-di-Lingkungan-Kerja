<?php
    session_start();

    if (!isset($_SESSION["login"])) {
        header("Location: halamanAwal.php");
        exit;
    }

    require 'connection.php';
    $error = "";

    
?>