<?php
    require 'connection.php';

    $error = "";

    if(isset($_POST["register"])) {
        $username = mysqli_real_escape_string($conn, trim($_POST["username"]));
        $email = mysqli_real_escape_string($conn, trim($_POST["email"]));
        $phone = mysqli_real_escape_string($conn, trim($_POST["phone"]));
        $cek_password = $_POST["password"];

        if (strlen($_POST["password"]) < 8){
            $error = "Password minimal 8 karakter.";
        } else {
            $cek_usn = mysqli_query($conn, "SELECT id FROM users WHERE name ='$username' OR email = '$email'");

            if(mysqli_num_rows($cek_usn) > 0) {
                $error = "Username sudah digunakan.";
            } else {
                $password = password_hash($cek_password, PASSWORD_DEFAULT);

                $insert = mysqli_query($conn, "INSERT INTO user (email, name, password, phone) VALUES ('$email', '$username', '$password', '$phone')");

                if($insert) {
                    header("Location: login.php?status=success");
                    exit;
                } else {
                    $error = "Registrasi gagal. coba lagi.";
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
    <title>Registrasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="bg-light">
        <div class="container py-5" style="max-width: 420px;">
            <div class="card">
                <div class="card-body">
                    <h4>Registrasi Akun</h4>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Masukkan email Anda" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Masukkan username Anda" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role_id" class="form-select" required>
                                <option value="" selected disabled>-- Pilih Role --</option>
                                <option value="1">Admin</option>
                                <option value="2">Karyawan</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" name="username" class="form-control" placeholder="Masukkan department Anda" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text" name="username" class="form-control" placeholder="Masukkan NIK Anda" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password Anda" required>
                        </div>

                        <button type="submit" name="register" class="mb-3 btn btn-primary w-100">Register</button>

                        <p class="text-center mb-3">Sudah punya akun? <a href="login.php"> Login</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>