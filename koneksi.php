<?php
    $conn = mysqli_connect("localhost", "root", "", "");

    if (!$conn) {
        die("Koneksi gagal:" . mysqli_connect_error());
    }
?>