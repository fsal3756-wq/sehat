<?php
require 'includes/auth_check.php';
require 'config.php';
require 'includes/functions.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$tdee = hitungTDEE($user['berat'], $user['tinggi'], $user['usia'], $user['gender'], $user['aktivitas'], $user['tujuan'], $user['berat_target']);
$rekom = rekomendasiNutrisi($tdee, $user['tujuan'], $user['berat']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Rekomendasi Nutrisi - Hidup Sehat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>📘 Rekomendasi Nutrisi Harian</h2>
        <div class="card">
            <p><strong>Nama:</strong> <?= htmlspecialchars($user['nama_lengkap']) ?></p>
            <p><strong>Tinggi:</strong> <?= $user['tinggi'] ?> cm</p>
            <p><strong>Berat:</strong> <?= $user['berat'] ?> kg</p>
            <p><strong>Tujuan:</strong> 
                <?php
                echo match($user['tujuan']) {
                    'kesehatan' => 'Jaga Kesehatan',
                    'diet' => 'Diet menuju ' . $user['berat_target'] . ' kg',
                    'otot' => 'Pembentukan Otot',
                    default => 'Tidak diketahui'
                };
                ?>
            </p>
            <hr>
            <h3>🎯 Target Harian Anda:</h3>
            <ul>
                <li>🔥 Kalori: <strong><?= $rekom['kalori'] ?> kkal</strong></li>
                <li>🥩 Protein: <strong><?= $rekom['protein'] ?> g</strong> → Penting untuk perbaikan sel & otot</li>
                <li>🍚 Karbohidrat: <strong><?= $rekom['karbohidrat'] ?> g</strong> → Sumber energi utama</li>
                <li>🥑 Lemak: <strong><?= $rekom['lemak'] ?> g</strong> → Penting untuk hormon & penyerapan vitamin</li>
                <li>💧 Air: <strong><?= $rekom['air'] ?> liter</strong> → Jaga hidrasi tubuh</li>
            </ul>
        </div>
        <p><a href="dashboard.php">← Kembali ke Dashboard</a></p>
    </div>
</body>
</html>