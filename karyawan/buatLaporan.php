<?php
session_start();

if(!isset($_SESSION["login"])){
    header("Location: ../halamanAwal.php");
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Laporan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
</head>
<body style="font-family: 'Outfit', sans-serif; background-color: #f4f4f5;">
    <div class="container my-4">
        <div class="card">
            <div class="card-header bg-dark text-white py-3 px-4;">
                <h3 class="fw-bold px-4">📋 Buat Laporan Baru</h3>
            </div>
            <div class="card-body">
               <form method="POST" enctype="multipart/form-data">
                <?php if($error) : ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
    
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="judul" style="font-weight: 650; font-size: larger;">Judul</label>
                        <input type="text" id="judul" name="judul" class="form-control" maxlength="150" placeholder="Masukkan judul laporan" required value="<?= htmlspecialchars($_POST["judul"] ?? "") ?>" >
                    </div>
    
                    <div class="col-12 col-md-4">
                        <label class="form-label" for="kategori" style="font-weight: 650; font-size: larger;">Kategori</label>
                        <select name="kategori" id="kategori" class="form-select" required>
                            <?php foreach($daftarKategori as $k) : ?>
                                <option value="<?= htmlspecialchars($k) ?>" <?= (($_POST["kategori"] ?? "") === $k) ? "selected" : "" ?>>
                                    <?= htmlspecialchars ($k) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
    
                    <div class="col-12 col-md-4">
                        <label for="lokasi" class="form-label" style="font-weight: 650; font-size: larger;">Lokasi</label>
                        <input type="text" id="lokasi" name="lokasi" class="form-control" placeholder="Contoh: Gedung F, Ruang 203" maxlength="150" required value="<?= htmlspecialchars($_POST["lokasi"] ?? "") ?>">
                    </div>
                </div>
    
                <div class="mb-3">
                    <label for="file_lampiran" class="form-label" style="font-weight: 650; font-size: larger;">Upload file (opsional)</label>
                    <input type="file" id="file_lampiran" name="file_lampiran" class="form-control" accept=".jpg,.jpeg,.png">
                </div>
    
                <div class="mb-4">
                    <label for="deskripsi" class="form-label" style="font-weight: 650; font-size: larger;">Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4" placeholder="Apa yang terjadi atau ditemukan?" required><?= htmlspecialchars($_POST["deskripsi"] ?? "") ?></textarea>
                </div>
    
                <a href="./dashboardKaryawan.php" class="btn btn-secondary">Batal</a>
                <button class="btn btn-warning" type="submit" name="simpan">Simpan</button>
               </form>
    
            </div>
        </div>
    </div>
</body>
</html>