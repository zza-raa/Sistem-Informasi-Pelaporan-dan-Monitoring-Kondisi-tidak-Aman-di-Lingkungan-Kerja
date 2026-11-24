<?php
    session_start();

    if (!isset($_SESSION["login"]) || $_SESSION["peran"] != "karyawan") {
        header("Location: ../halamanAwal.php");
        exit();
    }

    include "../connection.php";

    $mode = $_GET["mode"] ?? "dashboard";
    if ($mode == "laporan") {
        header("Location: buatLaporan.php");
        exit();
    }

    $karyawan_id = $_SESSION["id"] ?? $_SESSION["user_id"] ?? 0;
    $nama_user = $_SESSION["namaLengkap"] ?? $_SESSION["nama"] ?? "Karyawan";
    $sql = "SELECT * FROM laporan WHERE karyawan_id = '$karyawan_id' ORDER BY tanggal_lapor DESC";
    $result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <style>
        .table-wrapper {
            border-radius: 10px;
            overflow-x: auto;
            margin-top: 12px;
        }
        body {background: #f8f9fa;}
        .tag {padding: 4px 10px; border-radius: 99px; font-size: 12px; font-weight: 600; background: #e3e6eb; display: inline-block;}
        .t-r {color: #dc2626}
        .t-a {color: #d97706}
        .t-g {color: #16a34a}
        .aksi {text-decoration: none; border: 1px solid #d1d5db; background: #fff; color: #1c2430; border-radius: 6px; padding: 4px 10px; font-size: 12px; font-weight: 600; display: inline-block;}
        .aksi.edit {border-color: #d97706; color: #d97706;}
        .aksi.hapus {border-color: #dc2626; color: #dc2626;}
        .aksi.nonaktif {opacity: 0.4; background: #e5e7eb; color: #6b7280; border-color: #d1d5db; pointer-events: none; cursor: not-allowed;}
    </style>
</head>
<body style="font-family: 'Outfit', sans-serif; background-color: #f4f4f5;">
    <nav class="navbar bg-dark d-flex align-items-center justify-content-between" style="color: white;">
        <div class="container">
            <div class="d-flex gap-2">
                <h3 style="font-weight: 600;">🦺 Safety</h3> 
                <h3 style="font-weight: 600; color: #ffc107;"> Reporting </h3> 
                <h3 style="font-weight: 600;"> System ⛑️</h3> 
            </div>
            <a href="../logout.php" class="btn btn-outline-dark btn-warning" style="font-weight: 600;">Logout </a>
        </div>
    </nav>
    <div class="container">
        <div class="mt-4 mb-3">
            <a href="?mode=dashboard" class="btn <?= $mode == "dashboard" ? "btn-dark" : "btn-outline-dark" ?>">Laporan Saya</a>
            <a href="?mode=laporan" class="btn <?= $mode == "laporan" ? "btn-dark" : "btn-outline-dark" ?>">Buat Laporan</a>
        </div>
        <header>
            <div class="container">
                <p>Selamat Datang, <?= htmlspecialchars($nama_user);?></p>
            </div>
        </header>
        
        <?php if($mode == "dashboard"):?>
            <div class="card shadow-sm">
        <div class="card-body mx-4">
            <h2>Daftar Laporan</h2>
            <div class="table-responsive">
                <div class="table-wrapper">
                    <table class="table table-boarder table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Tanggal</th>
                                <th>Judul</th>
                                <th>Deskripsi Laporan</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                                <th class="text-center" style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($result)):
                                $warna = "";
                                if ($row['status'] == "Menunggu Verifikasi" || $row['status'] == "Ditolak"){
                                    $warna = "t-r";
                                } else if ($row['status'] == "Sedang Ditindaklanjuti" || $row['status'] == "Dalam Perbaikan"){
                                    $warna = "t-a";
                                    } else if ($row['status'] == "Selesai"){
                                        $warna = "t-g";
                                        } 
                                        
                                        $bisa_ubah = ($row['status'] == "Menunggu Verifikasi");
                                        ?>
                        <tr>
                            <td><?= date("d M Y", strtotime($row['tanggal_lapor'])); ?></td>
                            <td><?= htmlspecialchars($row['judul']); ?></td>
                            <td><?= htmlspecialchars($row['deskripsi']); ?></td>
                            <td><?= htmlspecialchars ($row['kategori']); ?></td>
                            <td><?= htmlspecialchars ($row['lokasi']); ?></td>
                            <td><span class="tag <?php echo $warna; ?>"><?php echo $row['status']; ?></span></td>
                            <td class="text-center">
                                <?php if ($bisa_ubah): ?>
                                    <a href="editLaporan.php?id=<?php echo $row['id']; ?>" class="aksi edit">Edit</a>
                                    <a href="hapusLaporan.php?id=<?php echo $row['id']; ?>" class="aksi hapus" onclick="return confirm('Yakin ingin menghapus laporan ini?')">Hapus</a>
                                <?php else: ?>
                                    <span class="aksi nonaktif">Edit</span>
                                    <span class="aksi nonaktif">Hapus</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada laporan yang dibuat.</td> 
                            </tr>
                            <?php endif; ?>
                            <?php elseif($mode == "laporan"):
                                header ("Location: karyawan/buatLaporan.php");
                                exit();
                            endif; ?> 
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>