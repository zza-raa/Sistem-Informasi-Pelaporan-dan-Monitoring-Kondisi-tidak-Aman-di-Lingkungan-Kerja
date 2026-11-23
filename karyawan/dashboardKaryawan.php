<?php
    session_start();
    include "../connection.php";

    // if(!isset($_SESSION["user_id"])) {
    //     header("Location: ../loginKaryawan.php");
    //     exit();
    // }

    if($_SESSION["peran"] !== "karyawan"){
        header("Location: ../halamanAwal.php");
        exit();
    }

    $karyawan_id = $_SESSION["user_id"];
    $nama_user = $_SESSION["namaLengkap"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Karyawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .tag {padding: 4px 10px; border-radius: 99px; font-size: 12px; font-weight: 600; background: #e3e6eb; display: inline-block;}
        .t-r {color: #dc2626}
        .t-a {color: #d97706}
        .t-g {color: #16a34a}
        .aksi {text-decoration: none; border: 1px solid #d1d5db; background: #fff; color: #1c2430; border-radius: 6px; padding: 4px 10px; font-size: 12px; font-weight: 600; display: inline-block;}
        .aksi.hapus {border-color: #dc2626; color: #dc2626;}
        .aksi.nonaktif {opacity: 0.4; background: #e5e7eb; color: #6b7280; border-color: #d1d5db; pointer-events: none; cursor: not-allowed;}
    </style>
</head>
<body class="bg-light">
    <header>
        <div class="container">
            <span class="navbar-brand">Dashboard Karyawan</span>
            <a href="../logout.php">Logout</a>
        </div>
    </header>

    <div class="container py-4">
        <h4>Selamat datang, <?php echo htmlspecialchars($_SESSION["namaLengkap"]);?></h4>
    </div>

    <nav>
        <a href="dashboardKaryawan.php" class="aktif">Laporan Saya</a>
        <a href="buatLaporan.php" class="aktif">Buat Laporan</a>
    </nav>
    <main>
        <div class="card">
            <h2>Daftar Laporan</h2>
            <div class="wrap">
                <table>
                    <tr>
                        <th>Deskripsi Laporan</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                    <?php
                        $sql = "SELECT * FROM laporan WHERE karyawan_id = '$karyawan_id' ORDER BY tanggal_lapor DESC";
                        $result = mysqli_query($connection, $sql);
                        
                        if(mysqli_num_rows($result) == 0){
                            echo "<tr><td colspan='6'>Belum ada laporan yang dibuat.</td></tr>";
                        }

                        while($data = mysqli_fetch_assoc($result)){
                            $warna = "";
                            if ($data['status'] == "Menunggu Verifikasi"){
                                $warna = "t-r";
                            } else if ($data['status'] == "Sedang Ditindaklanjuti" || $data['status'] == "Dalam Perbaikan"){
                                $warna = "t-a";
                            } else if ($data['status'] == "Selesai"){
                                $warna = "t-g";
                            } 
                        $bisa_ubah = ($data['status'] == "Menunggu Verifikasi");
                    ?>
                    <tr>
                        <td><?php echo $data['deskripsi']; ?></td>
                        <td><?php echo $data['kategori']; ?></td>
                        <td><?php echo $data['lokasi']; ?></td>
                        <td><?php echo date("d M Y", strtotime($data['tanggal_lapor'])); ?></td>
                        <td><span class="tag <?php echo $warna; ?>"><?php echo $data['status']; ?></span></td>
                        <td>
                            <?php if ($bisa_ubah) { ?>
                                <a href="karyawan/editLaporan.php?id=<?php echo $data['id']; ?>" class="aksi edit">Edit</a>
                                <a href="karyawan/hapusLaporan.php?id=<?php echo $data['id']; ?>" class="aksi hapus" onclick="return confirm('Yakin ingin menghapus laporan ini?')">Hapus</a>
                            <?php } else { ?>
                                <span class="aksi nonaktif">Edit</span>
                                <span class="aksi nonaktif">Hapus</span>
                            <?php } ?>
                            </td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </main>
</body>
</html>