<?php
require 'includes/auth_check.php';
require 'config.php';

$user_id = $_SESSION['user_id'];
$tanggal = date('Y-m-d');
$error = $success = '';

// Cek apakah sudah input hari ini
$stmt = $pdo->prepare("SELECT * FROM asupan_harian WHERE user_id = ? AND tanggal = ?");
$stmt->execute([$user_id, $tanggal]);
$asupanHariIni = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$asupanHariIni) {
    $karbo = isset($_POST['karbohidrat']) ? floatval($_POST['karbohidrat']) : 0;
    $protein = isset($_POST['protein']) ? floatval($_POST['protein']) : 0;
    $lemak = isset($_POST['lemak']) ? floatval($_POST['lemak']) : 0;
    $gula = isset($_POST['gula']) ? floatval($_POST['gula']) : 0;
    $air = isset($_POST['air']) ? floatval($_POST['air']) : 0;

    // Hitung kalori: karbo & protein = 4 kkal/g, lemak = 9 kkal/g
    $kalori = round(($karbo * 4) + ($protein * 4) + ($lemak * 9));

    try {
        $stmt = $pdo->prepare("INSERT INTO asupan_harian (user_id, tanggal, karbohidrat, protein, lemak, gula, air, kalori) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $tanggal, $karbo, $protein, $lemak, $gula, $air, $kalori]);
        $success = "✅ Asupan berhasil disimpan!";
        // Muat ulang data agar tampil setelah submit
        $asupanHariIni = [
            'karbohidrat' => $karbo,
            'protein' => $protein,
            'lemak' => $lemak,
            'gula' => $gula,
            'air' => $air,
            'kalori' => $kalori
        ];
    } catch (Exception $e) {
        $error = "❌ Gagal menyimpan. Coba lagi.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Input Asupan – Hidup Sehat</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .input-group {
            display: flex;
            align-items: center;
            margin: 12px 0;
        }
        .input-group label {
            width: 120px;
            font-weight: bold;
            color: #2c3e50;
        }
        .input-group input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        .unit {
            margin-left: 6px;
            color: #7f8c8d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📝 Input Asupan Hari Ini</h2>
        <p style="text-align:center; margin-bottom:20px; color:#7f8c8d;">
            <?= date('d F Y') ?> — Masukkan jumlah asupan hari ini
        </p>

        <?php if ($asupanHariIni): ?>
            <div class="card" style="background:#e8f5e9; border-left-color:#27ae60;">
                <p>✔️ Asupan hari ini sudah tersimpan:</p>
                <div class="input-group"><label>Karbo:</label> <span><?= $asupanHariIni['karbohidrat'] ?> g</span></div>
                <div class="input-group"><label>Protein:</label> <span><?= $asupanHariIni['protein'] ?> g</span></div>
                <div class="input-group"><label>Lemak:</label> <span><?= $asupanHariIni['lemak'] ?> g</span></div>
                <div class="input-group"><label>Gula:</label> <span><?= $asupanHariIni['gula'] ?> g</span></div>
                <div class="input-group"><label>Air:</label> <span><?= $asupanHariIni['air'] ?> L</span></div>
                <div class="input-group"><label>Kalori:</label> <span><?= $asupanHariIni['kalori'] ?> kkal</span></div>
            </div>
            <p style="text-align:center; margin-top:20px;">
                <a href="dashboard.php" class="btn">← Kembali ke Dashboard</a>
            </p>
        <?php else: ?>
            <?php if ($success): ?>
                <p class="success"><?= $success ?></p>
                <meta http-equiv="refresh" content="1;url=input_asupan.php">
            <?php endif; ?>
            <?php if ($error): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>

            <form method="POST">
                <div class="input-group">
                    <label>Karbohidrat</label>
                    <input type="number" step="0.1" name="karbohidrat" placeholder="Contoh: 120" required autofocus>
                    <span class="unit">gram</span>
                </div>
                <div class="input-group">
                    <label>Protein</label>
                    <input type="number" step="0.1" name="protein" placeholder="Contoh: 80" required>
                    <span class="unit">gram</span>
                </div>
                <div class="input-group">
                    <label>Lemak</label>
                    <input type="number" step="0.1" name="lemak" placeholder="Contoh: 50" required>
                    <span class="unit">gram</span>
                </div>
                <div class="input-group">
                    <label>Gula</label>
                    <input type="number" step="0.1" name="gula" placeholder="Contoh: 25" required>
                    <span class="unit">gram</span>
                </div>
                <div class="input-group">
                    <label>Air</label>
                    <input type="number" step="0.1" name="air" placeholder="Contoh: 2.5" required>
                    <span class="unit">liter</span>
                </div>

                <button type="submit" style="width:100%; margin-top:20px;">💾 Simpan Asupan</button>
            </form>
            <p style="text-align:center; margin-top:15px;">
                <a href="dashboard.php">← Batal / Kembali</a>
            </p>
        <?php endif; ?>
    </div>
</body>
</html>