<!-- tampilan awal sebelum register dan login dan dashboard -->
<!-- navbar, informasi layanan -->
<?php
    session_start();

    if(!isset($_SESSION["login"])) {
        header("Location: login.php");
        exit;
    }

    require 'connection.php';
 ?> 

 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
 </head>
 <body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <span>Keselamatan kerja</span>
        </div>
    </nav>
 </body>
 </html>