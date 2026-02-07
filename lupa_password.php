<?php
session_start();
require 'config.php';

$error = '';
$success = '';
$step = 1;

// Determine current step from session
if (isset($_SESSION['reset_verified']) && $_SESSION['reset_verified'] === true) {
    $step = 3;
} elseif (isset($_SESSION['reset_user_id']) && isset($_SESSION['questions'])) {
    $step = 2;
}

// Step 1: Cek username
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_username'])) {
    $username = trim($_POST['username']);
    
    $stmt = $pdo->prepare("SELECT id, security_question_1, security_question_2 FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['reset_user_id'] = $user['id'];
        $_SESSION['reset_username'] = $username;
        $_SESSION['questions'] = [
            $user['security_question_1'],
            $user['security_question_2']
        ];
        header("Location: lupa_password.php");
        exit;
    } else {
        $error = "Username tidak ditemukan.";
        $step = 1;
    }
}

// Step 2: Verifikasi jawaban
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_answers'])) {
    if (!isset($_SESSION['reset_user_id'])) {
        header("Location: lupa_password.php");
        exit;
    }
    
    $answer1 = strtolower(trim($_POST['answer_1']));
    $answer2 = strtolower(trim($_POST['answer_2']));
    
    $stmt = $pdo->prepare("SELECT security_answer_1, security_answer_2 FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['reset_user_id']]);
    $user = $stmt->fetch();
    
    if (strtolower($user['security_answer_1']) === $answer1 && 
        strtolower($user['security_answer_2']) === $answer2) {
        $_SESSION['reset_verified'] = true;
        header("Location: lupa_password.php");
        exit;
    } else {
        $error = "Jawaban salah. Silakan coba lagi.";
        $step = 2;
    }
}

// Step 3: Reset password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    if (!isset($_SESSION['reset_user_id']) || !isset($_SESSION['reset_verified'])) {
        header("Location: lupa_password.php");
        exit;
    }
    
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $error = "Password tidak cocok.";
        $step = 3;
    } elseif (strlen($new_password) < 6) {
        $error = "Password minimal 6 karakter.";
        $step = 3;
    } else {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed, $_SESSION['reset_user_id']]);
        
        $success_username = $_SESSION['reset_username'];
        unset($_SESSION['reset_user_id']);
        unset($_SESSION['reset_username']);
        unset($_SESSION['questions']);
        unset($_SESSION['reset_verified']);
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password – Hidup Sehat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #333;
        }
        .subtitle {
            text-align: center;
            color: #777;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 25px;
            gap: 10px;
        }
        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #999;
        }
        .step.active {
            background: #1976d2;
            color: white;
        }
        .step.completed {
            background: #4caf50;
            color: white;
        }
        .error {
            background: #ffebee;
            color: #c62828;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
        .success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
        }
        .success strong {
            display: block;
            font-size: 18px;
            margin-bottom: 10px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        .question-box {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
            border-left: 4px solid #1976d2;
        }
        .question-box p {
            margin: 0 0 10px 0;
            color: #333;
            font-weight: bold;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #1976d2;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            font-weight: bold;
        }
        button:hover {
            background: #1565c0;
        }
        .btn-success {
            background: #4caf50;
        }
        .btn-success:hover {
            background: #45a049;
        }
        p.link {
            text-align: center;
            margin-top: 20px;
        }
        a {
            color: #1976d2;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .info {
            background: #e3f2fd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #1565c0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔒 Lupa Password</h2>
        <p class="subtitle">Reset password dengan pertanyaan keamanan</p>
        
        <!-- Step Indicator -->
        <div class="step-indicator">
            <div class="step <?= $step >= 1 ? 'active' : '' ?> <?= $step > 1 ? 'completed' : '' ?>">1</div>
            <div class="step <?= $step >= 2 ? 'active' : '' ?> <?= $step > 2 ? 'completed' : '' ?>">2</div>
            <div class="step <?= $step >= 3 ? 'active' : '' ?>">3</div>
        </div>
        
        <?php if ($error): ?>
            <div class="error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success">
                <strong>✅ Password Berhasil Diubah!</strong>
                <p>Silakan login dengan password baru Anda</p>
            </div>
            <a href="login.php"><button class="btn-success">Login Sekarang</button></a>
            
        <?php elseif ($step === 1): ?>
            <!-- Step 1: Input Username -->
            <div class="info">📝 Langkah 1: Masukkan username Anda</div>
            <form method="POST">
                <input type="text" name="username" placeholder="Masukkan Username" required autofocus>
                <button type="submit" name="check_username">Lanjutkan</button>
            </form>
            
        <?php elseif ($step === 2): ?>
            <!-- Step 2: Jawab Pertanyaan Keamanan -->
            <div class="info">🔐 Langkah 2: Jawab pertanyaan keamanan</div>
            <form method="POST">
                <div class="question-box">
                    <p>Pertanyaan 1:</p>
                    <strong><?= htmlspecialchars($_SESSION['questions'][0]) ?></strong>
                    <input type="text" name="answer_1" placeholder="Jawaban (huruf kecil)" required autofocus>
                </div>
                
                <div class="question-box">
                    <p>Pertanyaan 2:</p>
                    <strong><?= htmlspecialchars($_SESSION['questions'][1]) ?></strong>
                    <input type="text" name="answer_2" placeholder="Jawaban (huruf kecil)" required>
                </div>
                
                <button type="submit" name="verify_answers">Verifikasi Jawaban</button>
            </form>
            
        <?php elseif ($step === 3): ?>
            <!-- Step 3: Reset Password -->
            <div class="info">🔑 Langkah 3: Buat password baru</div>
            <form method="POST">
                <label>Password Baru:</label>
                <input type="password" name="new_password" placeholder="Minimal 6 karakter" required minlength="6" autofocus>
                
                <label>Konfirmasi Password:</label>
                <input type="password" name="confirm_password" placeholder="Ulangi password baru" required>
                
                <button type="submit" name="reset_password" class="btn-success">Reset Password</button>
            </form>
        <?php endif; ?>
        
        <p class="link">
            <a href="login.php">← Kembali ke Login</a>
        </p>
    </div>
</body>
</html>