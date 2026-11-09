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
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
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