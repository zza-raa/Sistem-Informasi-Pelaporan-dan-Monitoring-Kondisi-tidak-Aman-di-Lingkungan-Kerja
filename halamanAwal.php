<?php
    session_start();
    require 'connection.php';

    $error = "";
    $sukses = "";

    if(($_GET["daftar"] ?? "") == "sukses"){
        $sukses = "Registrasi berhasil. Silakan login.";
    }

    // verifikasi mode
    $mode = $_GET["mode"] ?? "karyawan";
    if ($mode != "karyawan" && $mode != "admin" && $mode != "register"){
        $mode = "karyawan";
    }

    if($mode == "register"){
        // registrasi karyawan
        if(isset($_POST["register"])) {
            $name = mysqli_real_escape_string($conn, trim($_POST["name"]));
            $username = mysqli_real_escape_string($conn, trim($_POST["username"]));
            $cek_password = $_POST["password"];
    
            if (strlen($_POST["password"]) < 8){
                $error = "Password minimal 8 karakter.";
            } else {
                $cek_usn = mysqli_query($conn, "SELECT id FROM users WHERE username ='$username'");
    
                if(mysqli_num_rows($cek_usn) > 0) {
                    $error = "Username sudah digunakan.";
                } else {
                    $password = password_hash($cek_password, PASSWORD_DEFAULT);
    
                    $insert = mysqli_query($conn, "INSERT INTO users (namaLengkap, username, password, peran) VALUES ('$name', '$username', '$password', 'karyawan')");
    
                    if($insert) {
                        header("Location: halamanAwal.php?mode=karyawan&daftar=sukses");
                        exit();           
                    } else {
                        $error = "Registrasi gagal. Coba lagi.";
                    }
                }
            }
        }
    } else {
        if(isset($_POST["login"])) {
            $username = mysqli_real_escape_string($conn, trim($_POST["username"]));
            $password = $_POST["password"];
            
            $data = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' and peran = '$mode'");
            
            if(mysqli_num_rows($data) == 1){
                $cek = mysqli_fetch_assoc($data);
    
                if(password_verify($password, $cek["password"])){
                    session_regenerate_id(true);
                    $_SESSION["login"] = true;
                    $_SESSION["username"] = $cek["username"];
                    $_SESSION["peran"] = $cek["peran"];
    
                    if (isset($_POST["remember"])){
                        setcookie("remember_usn", $cek["username"], time() + (24 * 60 * 60), "/");
                    } else {
                        setcookie("remember_usn", "", time() - 3600, "/");
                    }
    
                    // pengarahan peran
                    if($cek["peran"] == "admin"){
                        header("Location: admin/dashboardAdmin.php");
                    } else if($cek["peran"] == 'karyawan'){
                        header("Location: karyawan/dashboardKaryawan.php");
                    }
                    exit();
                } else {
                    $error = "Username atau password salah.";
                }
            } else {
                $error = "Username atau password salah.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container justify-content">
        <div class="d-flex gap-2 mt-3 mb-3">
            <a href="?mode=register" class="btn <?= in_array($mode, ["karyawan", "register"]) ? "btn-dark" : "btn-outline-dark" ?>">Karyawan</a>
            <a href="?mode=admin" class="btn <?= $mode == "admin" ? "btn-dark" : "btn-outline-dark" ?>">Admin</a>
        </div>
    
        <?php if($mode == "register"): ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
        
                    <h2 style="text-align: center;">Daftar Karyawan</h2>
                    
                    <form action="?mode=register" method="post">
                        <div class="mt-3 mb-3">
                            <label class="form-label">Nama lengkap</label>
                            <input type="text" name="name" class="form-control" placeholder="Masukkan nama Anda" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Masukkan username Anda" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password Anda" required>
                        </div>
                        
                        <button type="submit" name="register" class="btn btn-warning w-100">Daftar</button>
                        
                        <p class="mt-3" style="text-align: center;">Sudah punya akun? <a href="?mode=karyawan"> Login</a></p>
                    </form>
                </div>
            </div>
            
            
        <?php else: ?>
            <div class="card">
                <div class="card-body">
                    <h2 style="text-align: center;">Login <?= $mode == "admin" ? "Admin" : "Karyawan" ?></h2>
                    
                    <?php if($sukses): ?>
                        <div class="alert alert-success"><?= $sukses ?></div>
                    <?php endif; ?>
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form action="?mode=<?= $mode ?>" method="post">
                        <div class=" mt-3 mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Masukkan username Anda" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password Anda" required>
                        </div>
            
                        <button type="submit" name="login" class="btn btn-warning w-100">Login</button>

                        <?php if($mode == "karyawan"): ?>
                            <p class="mt-3" style="text-align: center;">Belum punya akun? <a href="?mode=register">Daftar</a></p>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
                
        <?php endif; ?>
    </div>
</body>
</html>