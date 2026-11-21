<?php
    session_start();
    require 'connection.php';

    $error = "";
    $sukses = "";

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
                    $sukses = "Registrasi berhasil. Silahkan login.";               
                } else {
                    $error = "Registrasi gagal. Coba lagi.";
                }
            }
        }
    }

    if(isset($_POST["login"])) {
        $username = mysqli_real_escape_string($conn, trim($_POST["username"]));
        $peran = mysqli_real_escape_string($conn, trim($_POST["peran"]));
        $password = $_POST["password"];
        
        $data = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' and peran = '$peran'");
        
        if(mysqli_num_rows($data) === 1){
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
                if($cek["peran"] === 'admin'){
                    header("Location: admin/dashboardAdmin.php");
                } else if($cek["peran"] === 'karyawan'){
                    header("Location: karyawan/dashboardKaryawan.php");
                }
                exit();
            } else {
                $error = "Username, password, atau peran salah.";
            }
        } else {
            $error = "Username, password, atau peran salah.";
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
    <div>
        
    </div>
</body>
</html>