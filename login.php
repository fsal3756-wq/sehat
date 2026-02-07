<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

require 'config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login – Hidup Sehat</title>
    <style>
        :root {
            --bg-body: #f8f9fa;
            --bg-card: #ffffff;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --border-color: #dee2e6;
            --btn-primary: #0d6efd;
            --btn-danger: #dc3545;
            --success: #198754;
            --dark-bg: #212529;
            --dark-text: #f8f9fa;
            --shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .dark-mode {
            --bg-body: #121212;
            --bg-card: #1e1e1e;
            --text-primary: #f8f9fa;
            --text-secondary: #adb5bd;
            --border-color: #343a40;
            --dark-bg: #0a0a0a;
            --shadow: 0 4px 6px rgba(0,0,0,0.3);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-primary);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 16px;
            transition: background-color 0.3s, color 0.3s;
        }

        .container {
            width: 100%;
            max-width: 450px;
            background: var(--bg-card);
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 32px;
        }

        h2 {
            text-align: center;
            margin-bottom: 24px;
            font-weight: 600;
            font-size: 1.75rem;
            color: var(--text-primary);
        }

        .error {
            background: #f8d7da;
            color: #842029;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
            border-left: 4px solid var(--btn-danger);
        }

        input[type="text"],
        input[type="password"],
        input[type="email"],
        select {
            width: 100%;
            padding: 12px 16px;
            margin-bottom: 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-card);
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.2s;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: var(--btn-primary);
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: var(--btn-primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: #0b5ed7;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        p {
            text-align: center;
            margin-top: 20px;
            color: var(--text-secondary);
        }

        a {
            color: var(--btn-primary);
            text-decoration: none;
            font-weight: 600;
        }

        a:hover {
            text-decoration: underline;
        }

        .forgot-password {
            text-align: center;
            margin-top: 12px;
            margin-bottom: 8px;
        }

        .forgot-password a {
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-password a:hover {
            color: var(--btn-primary);
        }

        .divider {
            text-align: center;
            margin: 20px 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
            position: relative;
        }

        .divider::before,
        .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background: var(--border-color);
        }

        .divider::before {
            left: 0;
        }

        .divider::after {
            right: 0;
        }

        @media (max-width: 480px) {
            .container {
                padding: 24px;
            }
            
            h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔑 Login</h2>
        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn">Login</button>
        </form>
        
        <div class="forgot-password">
            <a href="lupa_password.php">🔒 Lupa Password?</a>
        </div>
        
        <div class="divider">atau</div>
        
        <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>
</body>
</html>