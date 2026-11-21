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

                $insert = mysqli_query($conn, "INSERT INTO users (department_id, employee_id, email, name, password, phone) VALUES ('$email', '$username', '$password', '$phone')");

                if($insert) {
                    header("Location: loginKaryawan.php?status=success");
                    exit;
                } else {
                    $error = "Registrasi gagal. Coba lagi.";
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
                    <div class="container d-flex gap-5">
                        <h4><a href="halamanAwal.php" style="text-decoration: none; color: black;">&#8592;</a></h4>
                        <h4 style="text-align: center;">Registrasi Akun</h4>
                    </div>
                    <form method="POST">
                        <div class="mt-4 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Masukkan email Anda" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" placeholder="Masukkan username Anda" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" name="username" class="form-control" placeholder="Masukkan department Anda" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text" name="employee_id" class="form-control" placeholder="Masukkan NIK Anda" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password Anda" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="password" name="password" class="form-control" placeholder="contoh: 081234567890" required>
                        </div>

                        <button type="submit" name="register" class="mb-3 btn btn-primary w-100">Register</button>

                        <p class="text-center mb-3">Sudah punya akun? <a href="loginKaryawan.php"> Login</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>