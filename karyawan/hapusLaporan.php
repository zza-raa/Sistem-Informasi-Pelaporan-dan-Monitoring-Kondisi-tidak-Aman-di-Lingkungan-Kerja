<?php
session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../login.php");
    exit;
}

require '../connection.php';

$id = (int)($_GET["id"] ?? 0);
$hapus = mysqli_query()

?>