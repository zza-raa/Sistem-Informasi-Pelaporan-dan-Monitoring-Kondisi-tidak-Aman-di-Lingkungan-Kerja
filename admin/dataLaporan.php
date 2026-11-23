<?php
    session_start();
    include "../connection.php";
    
    // if(!isset($_SESSION ['user_id']) || $_SESSION['peran'] != 'admin'){
    //     header("location: ../halamanAwal.php");
    //     exit();
    // }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Admin</title>
</head>
<body>
    <nav>
        <a href="../dashboardAdmin.php">Dashboard</a>
        <a href="../dataLaporan.php">Laporan</a>
        <a href="../riwayatLaporan.php">Riwayat Laporan</a>
    </nav>
    <main>
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
    </main>
</body>
</html>