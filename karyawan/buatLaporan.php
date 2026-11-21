<?php
session_start();

if(!isset($_SESSION["login"])){
    header("Location: ../login.php");
    exit;
}

require '../connection.php';

$daftarKategori = ["Kecelakaan Kerja", "Nyaris Celaka", "Kondisi Tidak Aman", "Tindakan Tidak Aman"];
$error = "";

if(isset($_POST["simpan"])){
    $karyawan_id = (int)$_SESSION["id"];
    $judul = mysqli_real_escape_string($conn, trim($_POST["judul"]));
    $kategori = $_POST["kategori"];
    $lokasi = mysqli_real_escape_string($conn, trim($_POST["lokasi"]));
    $deskripsi = mysqli_real_escape_string($conn, trim($_POST["deskripsi"]));

    if($judul == "" || $lokasi == "" || $deskripsi == ""){
        $error = "Judul, lokasi, dan deskripsi tidak boleh kosong";
    }else if(strlen($judul) > 150 || strlen($lokasi) > 150){
        $error = "Judul dan lokasi maksimal 150 karakter.";
    }else if(!in_array($kategori, $daftarKategori)){
        $error = "Kategori tidak valid.";
    }else{
        $file_lampiran = "";
        if($_FILES["file_lampiran"]["error"] !== UPLOAD_ERR_NO_FILE){
            $namaFile = $_FILES["file_lampiran"]["name"];
            $ukuran = $_FILES["file_lampiran"]["size"];
            $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

            if(!in_array($ekstensi, ["jpg", "jpeg", "png"])){
                $error = "Format file tidak valid.";
            }else if($ukuran > 2*1024*1024){
                $error = "Ukuran tidak valid.";
            }

            if($error == ""){
                $namaBaru = uniqid() . "." . $ekstensi;
                if(move_uploaded_file($_FILES["file_lampiran"]["tmp_name"], "../uploads/" . $namaBaru)){
                    $file_lampiran = $namaBaru;
                }else{
                    $error = "Gagal menyimpan file.";
                }
            }
        }

        if($error == ""){
            $insert = mysqli_query($conn, "INSERT INTO laporan (karyawan_id, judul, deskripsi, kategori, lokasi, file_lampiran) VALUES ('$karyawan_id', '$judul', '$deskripsi', '$kategori', '$lokasi', '$file_lampiran')");

            if($insert){
                header("Location: dashboardKaryawan.php");
                exit;
            }else{
                $error = "Gagal menyimpan data.";
            }
        }
    }
}
?>