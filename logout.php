<?php
    session_start();
    session_destroy();
    header("Location: halamanAwal.php");
    exit();
?>