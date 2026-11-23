<?php
    session_start();

    if (!isset($_SESSION["login"]) || $_SESSION["peran"] != "admin") {
        header("Location: ../halamanAwal.php");
        exit();
    }

    require '../connection.php';

    $mode = $_GET["mode"] ?? "dashboard";

    $jumlah_status = [
        "Menunggu Verifikasi" => 0,
        "Sedang Ditindaklanjuti" => 0,
        "Dalam Perbaikan" => 0,
        "Selesai" => 0,
        "Ditolak" => 0
    ];

    $jumlah_kategori = [
        "Kecelakaan Kerja" => 0,
        "Nyaris Celaka" => 0,
        "Kondisi Tidak Aman" => 0,
        "Tindakan Tidak Aman" => 0
    ];

    $daftar_status = [
        "Menunggu Verifikasi",
        "Sedang Ditindaklanjuti",
        "Dalam Perbaikan",
        "Selesai",
        "Ditolak"
    ];

    if(isset($_POST["aksi"]) && $_POST["aksi"] == "ubah_status") {
        $id = (int)$_POST["id"];
        $status_baru = mysqli_real_escape_string($conn, $_POST["status"]);
        
        if($id > 0 && in_array($status_baru, $daftar_status)) {
            if ($status_baru == "Selesai" || $status_baru == "Ditolak") {
                $update = mysqli_query($conn, "UPDATE laporan SET status = '$status_baru', tanggal_tutup = NOW() WHERE id = $id");
            } else {
                $update = mysqli_query($conn, "UPDATE laporan SET status = '$status_baru', tanggal_tutup = NULL WHERE id = $id");
            }
            if (!$update) {
                die("Gagal update: " . mysqli_error($conn));
            }
        }

        header("Location: ?mode=laporan");
        exit();
    }

    $hasil_status = mysqli_query($conn, "SELECT status, COUNT(*) AS jumlah FROM laporan GROUP BY status");
    $hasil_kategori = mysqli_query($conn, "SELECT kategori, COUNT(*) AS total FROM laporan GROUP BY kategori");
    $hasil_riwayat = mysqli_query($conn, "SELECT l.*, u.namaLengkap AS pelapor FROM laporan l JOIN users u ON l.karyawan_id = u.id WHERE l.status IN ('Selesai', 'Ditolak') ORDER BY l.tanggal_tutup DESC");
    $hasil_laporan = mysqli_query($conn, "SELECT l.*, u.namaLengkap AS pelapor FROM laporan l JOIN users u ON l.karyawan_id = u.id WHERE l.status NOT IN ('Selesai', 'Ditolak') ORDER BY l.id DESC");

    while ($baris = mysqli_fetch_assoc($hasil_status)) {
        $jumlah_status[$baris["status"]] = $baris["jumlah"];
    }
    while ($baris = mysqli_fetch_assoc($hasil_kategori)) {
        $jumlah_kategori[$baris["kategori"]] = $baris["total"];
    }

    $total = array_sum($jumlah_status);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>
    <style>
        #sectionLaporan .table-wrapper {
            border-radius: 10px;
            overflow-x: auto;
            margin-top: 12px;
        }
        #sectionLaporan table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }
        #sectionLaporan th,
        #sectionLaporan td {
            border: 1px solid #d1d5db;
            padding: 10px 12px;
            text-align: left;
        }
        #sectionLaporan th {
            background-color: rgb(190, 255, 233);
            font-weight: bold;
        }
    </style>
</head>
<body style="font-family: 'Outfit', sans-serif; background-color: #FCCC;">
    <div style="background-color: rgb(230, 147, 147);">
        <nav class="navbar d-flex align-items-center justify-content-between">
            <div class="container">
                <h3 style="font-weight: 600;">🦺 Safety Reporting System ⛑️</h3> 
                <a href="../logout.php" class="btn btn-outline-dark">Logout </a>
            </div>
        </nav>
    </div>
    
    <div class="container">

        <div class="mt-4 mb-3">
            <a href="?mode=dashboard" class="btn <?= $mode == "dashboard" ? "btn-dark" : "btn-outline-dark" ?>">Dashboard</a>
            <a href="?mode=laporan" class="btn <?= $mode == "laporan" ? "btn-dark" : "btn-outline-dark" ?>">Laporan</a>
            <a href="?mode=riwayat" class="btn <?= $mode == "riwayat" ? "btn-dark" : "btn-outline-dark" ?>">Riwayat Laporan</a>
        </div>

        <?php if($mode == "dashboard"): ?>
            <div class="row row-cols-6 g-3 mb-3">
                <div class="col">
                    <div class="card shadow-sm" style="background-color: #ffffd5;">
                        <div class="card-body">
                            <p style="color: grey;">Total Laporan</p>
                            <h1 style="font-weight: 750;"><?= $total ?></h1>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm" style="background-color: #ffffd5;">
                        <div class="card-body">
                            <p style="color: grey;">Menunggu Verifikasi</p>
                            <h1 style="font-weight: 750; color: blue;"><?= $jumlah_status["Menunggu Verifikasi"] ?></h1>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm" style="background-color: #ffffd5;">
                        <div class="card-body">
                            <p style="color: grey;">Sedang Ditindaklanjuti</p>
                            <h1 style="font-weight: 750; color: #d97706;"><?= $jumlah_status["Sedang Ditindaklanjuti"] ?></h1>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm" style="background-color: #ffffd5;">
                        <div class="card-body">
                            <p style="color: grey;">Dalam Perbaikan</p>
                            <h1 style="font-weight: 750; color: #FFCC00;"><?= $jumlah_status["Dalam Perbaikan"] ?></h1>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm" style="background-color: #ffffd5;">
                        <div class="card-body">
                            <p style="color: grey;">Selesai</p>
                            <h1 style="font-weight: 750; color: green;"><?= $jumlah_status["Selesai"] ?></h1>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm" style="background-color: #ffffd5;">
                        <div class="card-body">
                            <p style="color: grey;">Ditolak</p>
                            <h1 style="font-weight: 750; color: red;"><?= $jumlah_status["Ditolak"] ?></h1>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <p style="font-weight: 700; font-size: medium;" class="mb-2">Per Kategori</p>
                
                    <?php foreach ($jumlah_kategori as $nama => $jumlah): ?>
                        <?php $persen = $total > 0 ? round($jumlah / $total * 100) : 0; ?>

                        <div class="d-flex justify-content-between">
                            <span><?= $nama ?></span>
                            <span><?= $jumlah ?> laporan</span>
                        </div>

                        <div class="progress" role="progressbar" aria-label="example with label" aria-valuenow="<?php $persen ?>" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-warning" style="width: <?= $persen ?>%;"></div>
                        </div>
                        
                        <p style="font-size: small;" class="mb-2"><?= $persen?>% dari total laporan</p>
                    <?php endforeach; ?>
                </div>
            </div>
    
        <?php elseif($mode == "laporan"): ?>
            <section id="sectionLaporan">
                <h3>Daftar Laporan</h3>
                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Tanggal</th>
                                    <th>Pelapor</th>
                                    <th>Judul</th>
                                    <th>Kategori</th>
                                    <th>Lokasi</th>
                                    <th>Foto</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody style="background-color: #ffffd5;">
                            <?php while($row = mysqli_fetch_assoc($hasil_laporan)): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row["id"]) ?></td>
                                    <td><?= htmlspecialchars($row["tanggal_lapor"]) ?></td>
                                    <td><?= htmlspecialchars($row["pelapor"]) ?></td>
                                    <td><?= htmlspecialchars($row["judul"]) ?></td>
                                    <td><?= htmlspecialchars($row["kategori"]) ?></td>
                                    <td><?= htmlspecialchars($row["lokasi"]) ?></td>
                                    <td>
                                        <center>
                                        <?php if($row["file_lampiran"]) : ?>
                                                <img src="../uploads/<?= htmlspecialchars($row["file_lampiran"]) ?>" width="125" alt="lampiran kerusakan">
                                        <?php else: ?>
                                            -
                                        <?php endif;?>
                                        </center>
                                    <td>
                                        <form method="POST" action="?mode=laporan" style="margin: 0;">
                                            <input type="hidden" name="aksi" value="ubah_status">
                                            <input type="hidden" name="id" value="<?= (int) $row["id"] ?>">
                                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="Menunggu Verifikasi" <?= $row["status"] == "Menunggu Verifikasi" ? "selected" : "" ?>>Menunggu Verifikasi</option>
                                                <option value="Sedang Ditindaklanjuti" <?= $row["status"] == "Sedang Ditindaklanjuti" ? "selected" : "" ?>>Sedang Ditindaklanjuti</option>
                                                <option value="Dalam Perbaikan" <?= $row["status"] == "Dalam Perbaikan" ? "selected" : "" ?>>Dalam Perbaikan</option>
                                                <option value="Selesai" <?= $row["status"] == "Selesai" ? "selected" : "" ?>>Selesai</option>
                                                <option value="Ditolak" <?= $row["status"] == "Ditolak" ? "selected" : "" ?>>Ditolak</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
            </section>

        <?php elseif($mode == "riwayat"): ?>
            <div class="card shadow-sm">
                <table class="table align-middle">
                    <thead>
                        <tr class="">
                            <th>Kode</th>
                            <th>Tgl Lapor</th>
                            <th>Pelapor</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th>File</th>
                            <th>Status</th>
                            <th>Tgl Ditutup</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($hasil_riwayat)): ?>
                            <tr class="<?= $row["status"] == "Selesai" ? "table-success" : "table-danger" ?>">
                                <td><?=  $row["id"] ?></td>
                                <td><?=  $row["tanggal_lapor"] ?></td>
                                <td><?=  htmlspecialchars($row["pelapor"]) ?></td>
                                <td><?=  htmlspecialchars($row["judul"]) ?></td>
                                <td><?=  htmlspecialchars($row["kategori"]) ?></td>
                                <td><?=  htmlspecialchars($row["lokasi"]) ?></td>
                                <td><?=  $row["file_lampiran"] ? htmlspecialchars($row["file_lampiran"]) : "-" ?></td>
                                <td><?=  $row["status"] ?></td>
                                <td><?=  $row["tanggal_tutup"] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </div>
</body>
</html>