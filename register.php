<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

require 'config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $nama = trim($_POST['nama']);
    $usia = (int)$_POST['usia'];
    $gender = $_POST['gender'];
    $tinggi = (int)$_POST['tinggi'];
    $berat = (int)$_POST['berat'];
    $aktivitas = $_POST['aktivitas'];
    $tujuan = $_POST['tujuan'];
    $berat_target = ($tujuan === 'diet' || $tujuan === 'otot') ? (int)$_POST['berat_target'] : null;
    
    // Security Questions
    $security_question_1 = $_POST['security_question_1'];
    $security_answer_1 = strtolower(trim($_POST['security_answer_1']));
    $security_question_2 = $_POST['security_question_2'];
    $security_answer_2 = strtolower(trim($_POST['security_answer_2']));

    if (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif (empty($security_answer_1) || empty($security_answer_2)) {
        $error = "Jawaban keamanan harus diisi.";
    } elseif ($security_question_1 === $security_question_2) {
        $error = "Pilih pertanyaan keamanan yang berbeda.";
    } else {
        try {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, nama_lengkap, usia, gender, tinggi, berat, aktivitas, tujuan, berat_target, security_question_1, security_answer_1, security_question_2, security_answer_2) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashed, $nama, $usia, $gender, $tinggi, $berat, $aktivitas, $tujuan, $berat_target, $security_question_1, $security_answer_1, $security_question_2, $security_answer_2]);
            header("Location: login.php");
            exit;
        } catch (PDOException $e) {
            $error = "Username atau email sudah digunakan.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Daftar – Hidup Sehat</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 500px;
            overflow-y: auto;
            max-height: 95vh;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
            font-size: 28px;
            font-weight: 600;
        }

        h3 {
            color: #555;
            margin-top: 20px;
            margin-bottom: 12px;
            font-size: 17px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 6px;
            font-weight: 600;
        }

        .error {
            background: #ffebee;
            color: #c62828;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
            border-left: 4px solid #c62828;
            font-weight: 500;
        }

        input, select {
            width: 100%;
            padding: 11px 14px;
            margin: 8px 0;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #fafafa;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        input::placeholder {
            color: #999;
        }

        label {
            display: block;
            margin-top: 10px;
            margin-bottom: 4px;
            color: #555;
            font-weight: 600;
            font-size: 13px;
        }

        .info-text {
            font-size: 12px;
            color: #777;
            margin-top: -4px;
            margin-bottom: 12px;
            font-style: italic;
        }

        #targetDiv {
            display: none;
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        button {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            margin-top: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        button:active {
            transform: translateY(0);
        }

        p {
            text-align: center;
            margin-top: 15px;
            color: #666;
            font-size: 14px;
        }

        a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin: 12px 0;
            border: 1px solid #e9ecef;
        }

        /* Responsive Design untuk Tablet */
        @media (min-width: 768px) {
            body {
                padding: 20px;
            }

            .container {
                padding: 35px;
                max-width: 650px;
            }

            h2 {
                font-size: 30px;
                margin-bottom: 22px;
            }

            h3 {
                font-size: 18px;
                margin-top: 22px;
                margin-bottom: 14px;
            }

            input, select {
                padding: 12px 16px;
                font-size: 15px;
                margin: 9px 0;
            }

            button {
                padding: 15px;
                font-size: 17px;
            }

            .section {
                padding: 18px;
                margin: 14px 0;
            }

            label {
                font-size: 14px;
            }
        }

        /* Responsive Design untuk Desktop/Laptop - FULL WIDTH */
        @media (min-width: 1024px) {
            body {
                padding: 30px;
                align-items: flex-start;
                padding-top: 50px;
            }

            .container {
                padding: 40px 50px;
                max-width: 900px;
                max-height: none;
                overflow-y: visible;
            }

            h2 {
                font-size: 34px;
                margin-bottom: 25px;
            }

            h3 {
                font-size: 19px;
                margin-top: 25px;
                margin-bottom: 15px;
            }

            /* Grid Layout untuk Form di Desktop */
            .form-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }

            .form-grid .full-width {
                grid-column: 1 / -1;
            }

            input, select {
                padding: 13px 18px;
                font-size: 15px;
                margin: 5px 0;
            }

            button {
                padding: 16px;
                font-size: 18px;
                margin-top: 20px;
            }

            .section {
                padding: 20px;
                margin: 15px 0;
            }

            p {
                font-size: 15px;
                margin-top: 20px;
            }

            label {
                font-size: 14px;
                margin-top: 8px;
            }

            .info-text {
                font-size: 13px;
                margin-bottom: 15px;
            }
        }

        /* Responsive Design untuk Desktop Besar */
        @media (min-width: 1440px) {
            .container {
                max-width: 1100px;
                padding: 50px 60px;
            }

            h2 {
                font-size: 38px;
                margin-bottom: 30px;
            }

            h3 {
                font-size: 21px;
                margin-top: 30px;
            }

            .form-grid {
                gap: 25px;
            }

            input, select {
                padding: 14px 20px;
                font-size: 16px;
            }

            button {
                padding: 18px;
                font-size: 19px;
            }

            .section {
                padding: 25px;
            }
        }

        /* Responsive Design untuk Mobile Kecil */
        @media (max-width: 480px) {
            body {
                padding: 10px;
            }

            .container {
                padding: 20px;
                border-radius: 12px;
            }

            h2 {
                font-size: 24px;
                margin-bottom: 18px;
            }

            h3 {
                font-size: 16px;
                margin-top: 18px;
                margin-bottom: 10px;
            }

            input, select {
                padding: 10px 12px;
                font-size: 14px;
                margin: 7px 0;
            }

            button {
                padding: 12px;
                font-size: 15px;
            }

            .section {
                padding: 14px;
                margin: 10px 0;
            }

            label {
                font-size: 13px;
            }

            .info-text {
                font-size: 12px;
            }

            p {
                font-size: 13px;
            }
        }

        /* Scrollbar Styling */
        .container::-webkit-scrollbar {
            width: 8px;
        }

        .container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .container::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 10px;
        }

        .container::-webkit-scrollbar-thumb:hover {
            background: #764ba2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📝 Daftar Akun</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST" id="form">
            <h3>👤 Data Akun</h3>
            <div class="form-grid">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <div class="full-width">
                    <input type="password" name="password" placeholder="Password (min 6 karakter)" required>
                </div>
            </div>
            
            <h3>📋 Data Pribadi</h3>
            <div class="form-grid">
                <div class="full-width">
                    <input type="text" name="nama" placeholder="Nama Lengkap" required>
                </div>
                <input type="number" name="usia" placeholder="Usia" min="10" max="100" required>
                <select name="gender" required>
                    <option value="">Jenis Kelamin</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
                <input type="number" name="tinggi" placeholder="Tinggi (cm)" required>
                <input type="number" name="berat" placeholder="Berat (kg)" required>
            </div>
            
            <h3>🎯 Target Kesehatan</h3>
            <div class="form-grid">
                <select name="aktivitas" required>
                    <option value="">Level Aktivitas</option>
                    <option value="rendah">Rendah (Jarang Olahraga)</option>
                    <option value="sedang">Sedang (Olahraga 3-5x/minggu)</option>
                    <option value="tinggi">Tinggi (Olahraga Intensif)</option>
                </select>
                <select name="tujuan" id="tujuan" required>
                    <option value="">Pilih Tujuan</option>
                    <option value="kesehatan">Jaga Kesehatan</option>
                    <option value="diet">Diet (Turunkan Berat)</option>
                    <option value="otot">Pembentukan Otot</option>
                </select>
            </div>
            <div id="targetDiv">
                <input type="number" name="berat_target" placeholder="Berat Target (kg)">
            </div>
            
            <h3>🔒 Pertanyaan Keamanan</h3>
            <p class="info-text">Untuk reset password jika lupa</p>
            
            <div class="form-grid">
                <div class="section">
                    <label>Pertanyaan 1:</label>
                    <select name="security_question_1" required>
                        <option value="">Pilih pertanyaan</option>
                        <option value="Nama hewan peliharaan pertama?">Nama hewan peliharaan pertama?</option>
                        <option value="Nama ibu kandung?">Nama ibu kandung?</option>
                        <option value="Kota tempat lahir?">Kota tempat lahir?</option>
                        <option value="Nama sekolah SD?">Nama sekolah SD?</option>
                        <option value="Makanan favorit?">Makanan favorit?</option>
                    </select>
                    <input type="text" name="security_answer_1" placeholder="Jawaban (huruf kecil)" required>
                </div>
                
                <div class="section">
                    <label>Pertanyaan 2:</label>
                    <select name="security_question_2" required>
                        <option value="">Pilih pertanyaan</option>
                        <option value="Warna favorit?">Warna favorit?</option>
                        <option value="Nama guru favorit?">Nama guru favorit?</option>
                        <option value="Hobi favorit?">Hobi favorit?</option>
                        <option value="Film favorit?">Film favorit?</option>
                        <option value="Nama sahabat masa kecil?">Nama sahabat masa kecil?</option>
                    </select>
                    <input type="text" name="security_answer_2" placeholder="Jawaban (huruf kecil)" required>
                </div>
            </div>
            
            <button type="submit">Daftar</button>
        </form>
        <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>

    <script>
        document.getElementById('tujuan').addEventListener('change', function() {
            document.getElementById('targetDiv').style.display = 
                (this.value === 'diet' || this.value === 'otot') ? 'block' : 'none';
        });
    </script>
</body>
</html>