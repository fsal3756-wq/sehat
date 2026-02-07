<?php
require 'config.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? '';
$valid_token = false;

if ($token) {
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        $valid_token = true;
    } else {
        $error = "Link reset password tidak valid atau sudah kadaluarsa.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $password_baru = $_POST['password'];
    $konfirmasi = $_POST['password_confirm'];
    
    if (strlen($password_baru) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif ($password_baru !== $konfirmasi) {
        $error = "Password tidak cocok.";
    } else {
        $hashed = password_hash($password_baru, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE reset_token = ?");
        $stmt->execute([$hashed, $token]);
        
        $success = "Password berhasil diubah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - Hidup Sehat</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 400px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .input-row {
            margin: 15px 0;
        }
        .input-row label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        .input-row input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #2ecc71;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background: #27ae60;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 6px;
            margin: 10px 0;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 6px;
            margin: 10px 0;
            text-align: center;
        }
        .success a {
            color: #155724;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔑 Reset Password</h2>
        
        <?php if ($error): ?>
            <p class="error"><?= $error ?></p>
            <p style="text-align:center; margin-top:15px;">
                <a href="lupa_password.php" style="color:#3498db;">Minta link baru</a>
            </p>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <p class="success">
                <?= $success ?><br><br>
                <a href="login.php">Login Sekarang →</a>
            </p>
        <?php elseif ($valid_token): ?>
            <p style="text-align:center; color:#666; margin-bottom:20px;">
                Masukkan password baru Anda
            </p>
            <form method="POST">
                <div class="input-row">
                    <label>Password Baru</label>
                    <input type="password" name="password" placeholder="Minimal 6 karakter" required minlength="6">
                </div>
                <div class="input-row">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="password_confirm" placeholder="Ulangi password" required>
                </div>
                <button type="submit">Ubah Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>