<?php
    session_start();
    include "connection.php";

    if (!isset($_SESSION["status_login"]) || $_SESSION["status_login"] !== true || $_SESSION !== "admin"){
        header("location: ../halamanAwal.php");
        exit();
    }

    $kode = mysqli_real_escape_string($conection, $_POST["kode"]);
    $statusBaru = mysqli_real_escape_string($conection, $_POST["status"]);
?>