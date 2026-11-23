<?php
    include "../connection.php";

    $id = $_POST['id'];
    $status = $_POST['status'];

    if ($status == "Selesai" || $status == "Ditolak"){
        $sql = "UPDATE laporan SET status = '$status', tanggal_tutup = CURDATE() WHERE id = '$id'";
    } else {
        $sql = "UPDATE laporan SET status = '$status' WHERE id = '$id'";
    }

    $result = mysqli_query($connection, $sql) or die(mysqli_error($connection));
    if ($result){
        header ("location: admin/ dataLaporan.php");
    } else {
        echo "Gagal Mengubah Status Laporan";
    }
?>