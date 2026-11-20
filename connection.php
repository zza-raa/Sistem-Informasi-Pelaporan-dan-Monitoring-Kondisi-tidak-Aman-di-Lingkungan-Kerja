<?php
    $conn = mysqli_connect("localhost", "root", "", "safety_reporting");

    if (!$conn) {
        die("Koneksi gagal:" . mysqli_connect_error());
    }
?>