<?php
session_start();

if(!isset($_SESSION["login"])){
    header("Location: ../halamanAwal.php");
    exit;
}

require '../connection.php';

$error = "";
$daftarKategori = ["Kecelakaan Kerja", "Nyaris Celaka", "Kondisi Tidak Aman", "Tindakan Tidak Aman"];
$id = (int)($_GET["id"] ?? 0);
$karyawan_id = (int)$_SESSION["id"];

$target = mysqli_query($conn, "SELECT * FROM laporan WHERE id = $id AND karyawan_id = $karyawan_id AND status = 'Menunggu Verifikasi'");
$laporan = mysqli_fetch_assoc($target);

if(!$laporan){
    header("Location: dashboardKaryawan.php");
    exit;
}

if(isset($_POST["simpan"])){
    $judulBaru = trim($_POST["judul"]);
    $kategoriBaru = $_POST["kategori"];
    $lokasiBaru = trim($_POST["lokasi"]);
    $deskripsiBaru = trim($_POST["deskripsi"]);

    $laporan["judul"] = $judulBaru;
    $laporan["kategori"] = $kategoriBaru;
    $laporan["lokasi"] = $lokasiBaru;
    $laporan["deskripsi"] = $deskripsiBaru;

    $judul = mysqli_real_escape_string($conn, $judulBaru);
    $lokasi = mysqli_real_escape_string($conn, $lokasiBaru);
    $deskripsi = mysqli_real_escape_string($conn, $deskripsiBaru);

    if($judul == "" || $lokasi == ""){
        $error = "Judul dan lokasi tidak boleh kosong.";
    }else if($deskripsi == ""){
        $error = "Deskripsi tidak boleh kosong.";
    }else if(!in_array($kategoriBaru, $daftarKategori)){
        $error = "Kategori tidak valid.";
    }else{
        $lampiranLama = $laporan["file_lampiran"];
        $lampiran = $lampiranLama;

        if($_FILES["file_lampiran"]["error"] !== UPLOAD_ERR_NO_FILE){
            $namaFile = $_FILES["file_lampiran"]["name"];
            $ukuran = $_FILES["file_lampiran"]["size"];
            $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

            if(!in_array($ekstensi, ["jpg", "jpeg", "png"])){
                $error = "Format file tidak valid.";
            }else if($ukuran > 2*1024*1024){
                $error = "Ukuran file maksimal 2 MB.";
            }

            if($error == ""){
                $namaBaru = uniqid() . "." . $ekstensi;
                if(move_uploaded_file($_FILES["file_lampiran"]["tmp_name"], "../uploads/" . $namaBaru)){
                    $lampiran = $namaBaru;
                }else{
                    $error = "Gagal menyimpan file.";
                }
            }
        }

        if($error == ""){
            $update = mysqli_query($conn, "UPDATE laporan SET judul = '$judul', kategori = '$kategoriBaru', lokasi = '$lokasi', deskripsi = '$deskripsi', file_lampiran = '$lampiran' WHERE id = $id AND karyawan_id = $karyawan_id AND status = 'Menunggu Verifikasi'");

            if($update){
                if($lampiran !== $lampiranLama && !empty($lampiranLama)){
                    $path = "../uploads/" . basename($lampiranLama);
                    if(file_exists($path)){
                        unlink($path);
                    }
                }
                header("Location: dashboardKaryawan.php");
                exit;
            }else{
                if($lampiran !== $lampiranLama){
                    $pathBaru = "../uploads/" . basename($lampiran);
                    if(file_exists($pathBaru)){
                        unlink($pathBaru);
                    }
                }
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
    <title>Edit Laporan</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.9/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
</head>
<body style="font-family: 'Outfit', sans-serif; background-color: #f4f4f5;">
    <div class="container my-4">
        <div class="col-lg-5 mx-auto">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h3 class="fw-bold mb-2 mt-2">✏️ Edit Laporan</h3>     
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
    
                        <?php if($error) : ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
    
                        <div class="mb-3">
                            <label class="form-label" for="judul" style="font-weight: 650; font-size: larger;">Judul</label>
                            <input type="text" id="judul" name="judul" class="form-control" required value="<?= htmlspecialchars($laporan["judul"]) ?>" maxlength="150">
                        </div>
    
                        <div class="mb-3">
                            <label class="form-label" for="kategori" style="font-weight: 650; font-size: larger;">Kategori</label>
                            <select name="kategori" id="kategori" class="form-select" required>
                            <?php foreach($daftarKategori as $k) : ?>
                                <option value="<?= htmlspecialchars($k) ?>" <?= (($laporan["kategori"] ?? "") === $k) ? "selected" : "" ?>>
                                    <?= htmlspecialchars ($k) ?>
                                </option>
                            <?php endforeach; ?>
                            </select>
                        </div>
    
                        <div class="mb-3">
                            <label for="lokasi" class="form-label" style="font-weight: 650; font-size: larger;">Lokasi</label>
                            <input type="text" id="lokasi" name="lokasi" class="form-control" maxlength="150" required value="<?= htmlspecialchars($laporan["lokasi"]) ?>">
                        </div>
    
                        <div class="mb-3">
                            <label for="file_lampiran" class="form-label" style="font-weight: 650; font-size: larger;">Ganti file (opsional)</label>
                            <input type="file" id="file_lampiran" name="file_lampiran" class="form-control" accept=".jpg,.jpeg,.png">
                        </div>
    
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label" style="font-weight: 650; font-size: larger;">Deskripsi</label>
                            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4"  required><?= htmlspecialchars($laporan["deskripsi"]) ?></textarea>
                        </div>
    
                        <div class="d-flex gap-2">
                            <a href="dashboardKaryawan.php" class="btn btn-secondary">Batal</a>
                            <button type="submit" name="simpan" class="btn btn-warning">Simpan</button>
                        </div>
                    </form>           
                </div>
            </div>
        </div>
    </div>
</body>
</html>
