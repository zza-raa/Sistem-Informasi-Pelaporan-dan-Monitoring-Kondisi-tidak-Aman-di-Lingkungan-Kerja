<?php
    session_start();
    include "../connection.php";

    if(!isset($_SESSION["id"])) {
        header("Location: ../loginKaryawan.php");
        exit();
    }

    if($_SESSION["peran"] !== "karyawan"){
        header("Location: ../halamanAwal.php");
        exit();
    }

    $id = $_SESSION["id"];
    $query = mysqli_query($connection, "SELECT * FROM laporan WHERE user_id='$id' ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <head>
        <div class="container">
            <span class="navbar-brand">Dashboard Karyawan</span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </head>

    <div class="container py-4">
        <h4>Selamat datang, <?php echo htmlspecialchars($_SESSION["nama"]);?></h4>
        <a href= "../logout.php">Logout</a>
    </div>

    <nav>
        <a href="dashboardKaryawan.php" class="aktif">Laporan Saya</a>
        <a href="buatLaporan.php" class="aktif">Buat Laporan</a>
    </nav>


</body>
</html>