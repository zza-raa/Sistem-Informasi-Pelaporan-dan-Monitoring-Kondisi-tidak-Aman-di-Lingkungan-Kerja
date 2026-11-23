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

    $hasil_status = mysqli_query($conn, "SELECT status, COUNT(*) AS jumlah FROM laporan GROUP BY status");
    $hasil_kategori = mysqli_query($conn, "SELECT kategori, COUNT(*) AS total FROM laporan GROUP BY kategori");
    $hasil_riwayat = mysqli_query($conn, "SELECT l.*, u.namaLengkap AS pelapor FROM laporan l JOIN users u ON l.karyawan_id = u.id WHERE l.status IN ('Selesai', 'Ditolak') ORDER BY l.tanggal_tutup DESC");

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
</head>
<body>
    
    <div class="container">
        <nav class="navbar d-flex align-items-center justify-content-between">
            <h3>Safety Reporting System</h3> 
            <a href="../logout.php" class="btn btn-outline-dark">Logout</a>
        </nav>

        <div class="mt-4 mb-3">
            <a href="?mode=dashboard" class="btn <?= $mode == "dashboard" ? "btn-dark" : "btn-outline-dark" ?>">Dashboard</a>
            <a href="?mode=laporan" class="btn <?= $mode == "laporan" ? "btn-dark" : "btn-outline-dark" ?>">Laporan</a>
            <a href="?mode=riwayat" class="btn <?= $mode == "riwayat" ? "btn-dark" : "btn-outline-dark" ?>">Riwayat Laporan</a>
        </div>

        <?php if($mode == "dashboard"): ?>
            <div class="row row-cols-6 g-3 mb-3">
                <div class="col">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <p style="color: grey;">Total Laporan</p>
                            <h2><?= $total ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <p style="color: grey;">Menunggu Verifikasi</p>
                            <h2><?= $jumlah_status["Menunggu Verifikasi"] ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <p style="color: grey;">Sedang Ditindaklanjuti</p>
                            <h2><?= $jumlah_status["Sedang Ditindaklanjuti"] ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <p style="color: grey;">Dalam Perbaikan</p>
                            <h2><?= $jumlah_status["Dalam Perbaikan"] ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <p style="color: grey;">Selesai</p>
                            <h2><?= $jumlah_status["Selesai"] ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <p style="color: grey;">Ditolak</p>
                            <h2><?= $jumlah_status["Ditolak"] ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <strong>Per Kategori</strong>
                
                    <?php foreach ($jumlah_kategori as $nama => $jumlah): ?>
                        <?php $persen = $total > 0 ? round($jumlah / $total * 100) : 0; ?>

                        <div class="d-flex justify-content-between">
                            <span><?= $nama ?></span>
                            <span><?= $jumlah ?> laporan</span>
                        </div>

                        <div class="progress" role="progressbar" aria-label="example with label" aria-valuenow="<?php $persen ?>" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar" style="width: <?= $persen ?>%;"></div>
                        </div>
                        
                        <small><?= $persen?> dari total laporan</small>
                    <?php endforeach; ?>
                </div>
            </div>
    
        <?php elseif($mode == "laporan"): ?>
            <div class="card">
            <h2>Laporan Aktif</h2>
            <div class ="wrap">
                <table>
                    <tr>
                        <th>Kode</th>
                        <th>Tanggal</th>
                        <th>Pelapor</th>
                        <th>Deskripsi</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        <th>Foto</th>
                        <th>Status</th>
                    </tr>
                    <?php
                    $sql = "SELECT laporan.*, users.namaLengkap FROM laporan INNER JOIN users ON laporan.karyawan_id = users.id WHERE laporan.status != 'Selesai' AND laporan.status != 'Ditolak' ORDER BY laporan.tanggal_lapor DESC, laporan.id DESC";
                    $result = mysqli_query ($conn, $sql);

                    if (mysqli_num_rows($result) == 0){
                        echo "<tr><td colspan='6'>Tidak ada laporan aktif.</td></tr>";
                    }

                    $daftar_status = array (
                        "Menunggu Verifikasi",
                        "Sedang Ditindaklanjuti",
                        "Dalam Perbaikan",
                        "Selesai",
                        "Ditolak"
                    );

                    while ($data = mysqli_fetch_array($result)){
                    ?>
                    <tr>
                        <td><?php echo $data['kode']; ?></td>
                        <td><?php echo $data['tanggal_lapor']; ?></td>
                        <td><?php echo $data['namaLengkap']; ?></td>
                        <td><?php echo $data['deskripsi']; ?></td>
                        <td><?php echo $data['kategori']; ?></td>
                        <td><?php echo $data['lokasi']; ?></td>
                        <td>
                            <?php if (!empty($data['file_lampiran'])){?>
                                <a href="../uploads/<?php echo $data['file_lampiran']; ?>" target="_blank"><?php echo $data['file_lampiran']; ?></a>
                            <?php } else { echo "-"; } ?>
                        </td>
                        <td>
                            <form method="POST" action="ubahStatus.php" style="margin: 0;" >
                                <input type="hidden" name="id" value="<?php echo $data['id']; ?>">
                                <select name="status" onchange="this.form.submit()">
                                    <?php
                                    foreach ($daftar_status as $st){
                                        $selected = ($data['status'] == $st) ? "selected" : "";
                                        echo "<option value='$st' $selected>$ts</option>";
                                    }
                                    ?>
                                </select>
                            </form>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>

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
                                <td><?=  $row["kode"] ?></td>
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