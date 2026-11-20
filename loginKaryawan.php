<?php
    session_start();

    require 'connection.php';
    $username = $_COOKIE["remember_usn"] ?? "";

    if (isset($_POST["login"])) {
        $identifikasi = mysqli_real_escape_string($conn, trim($_POST["identifikasi"]));
        $password = $_POST["password"];

        $query = mysqli_query($conn, "SELECT * FROM users name ='$identifikasi' OR email = '$identifikasi'");
        $data = mysqli_fetch_assoc($query);

        if(!$data || !password_verify($password, $data["password"])){
            $error = "Username/Email/Password salah.";
        } else {
            session_regenerate_id(true);
            $_SESSION["login"] = true;
            $_SESSION["username"] = $data["username"];
            $_SESSION["username"] = $data["username"];

            if (isset($_POST["remember"])){
                setcookie("remember_usn", $data["username"], time() + (24 * 60 * 60), "/");
            } else {
                setcookie("remember_usn", "", time() - 3600, "/");
            }

                header("Location: dashboardKaryawan.php");
                exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5" style="max-width: 420px;">
        <div class="card">
            <div class="card-body">
                <h4 style="text-align: center;">Login</h4>

                <form method="POST">
                    <div class="mb-3 mt-4">
                        <label class="form-label">Email/Username</label>
                        <input type="text" name="identifikasi" class="form-control" placeholder="Masukkan email/username Anda" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password Anda" required>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                        <label class="form-check-label" for="remember">Ingat email saya</label>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                </form>

                <p class="text-center mt-3">Belum punya akun? <a href="register.php"> Register</a></p>

            </div>
        </div>
    </div>
</body>
</html>