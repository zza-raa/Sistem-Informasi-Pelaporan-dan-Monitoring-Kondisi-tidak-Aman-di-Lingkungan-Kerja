<?php
session_start();

if(!isset($_SESSION['login'])){
    header("Location: ../login.php");
    exit;
}

require '../connection.php';

$id = (int)($_GET["id"] ?? 0);
$karyawan_id = (int)$_SESSION["id"];

$cek = mysqli_query($conn, "SELECT status FROM laporan WHERE id = $id AND karyawan_id = $karyawan_id");
$data = mysqli_fetch_assoc($cek);

if(!data){
    echo "Laporan tidak ditemukan.";
    exit;
}

if($data["status"] !== "Menunggu Verifikasi"){
    echo "Laporan sudah diproses admin, tidak bisa dihapus.";
    exit;
}

$hapus = mysqli_query($conn, "DELETE FROM laporan where id = $id AND karyawan_id = $karyawan_id");

if($hapus){
    if(!empty($data["file_lampiran"])){
        $path = ".../uploads/" . basename($data["file_lampiran"]);
        if(file_exists($path)){
            unlink($path);
        }
    }
    header("Location: dashboardKaryawan.php");
    exit;
}else{
    echo "Gagal menghapus.";
}

?>