<?php
// Profil Tab - Data Processing

$error = '';
$success = '';

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan_profil'])) {
    $tujuan_input = $_POST['tujuan'] ?? 'kesehatan';
    $berat_sekarang = (int)($_POST['berat_sekarang'] ?? $user['berat']);
    $berat_target = $user['berat_target'];

    if ($berat_sekarang < 30 || $berat_sekarang > 250) {
        $error = "Berat badan harus antara 30–250 kg.";
    } else {
        if ($tujuan_input === 'custom') {
            if (empty($_POST['berat_target'])) {
                $error = "Berat target wajib diisi.";
            } else {
                $berat_target = (int)$_POST['berat_target'];
                if ($berat_target == $berat_sekarang) {
                    $error = "Berat target harus berbeda dari berat sekarang.";
                }
            }
        }

        if (!$error) {
            if ($tujuan_input === 'kesehatan') {
                $tujuan_db = 'kesehatan';
                $berat_target = null;
            } else {
                $tujuan_db = ($berat_target < $berat_sekarang) ? 'diet' : 'otot';
            }

            $stmt = $pdo->prepare("UPDATE users SET tujuan = ?, berat = ?, berat_target = ? WHERE id = ?");
            $stmt->execute([$tujuan_db, $berat_sekarang, $berat_target, $user_id]);

            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();

            $success = "Profil berhasil diperbarui!";
        }
    }
}

// Calculate BMI
$tinggi_meter = $user['tinggi'] / 100;
$bmi = round($user['berat'] / ($tinggi_meter * $tinggi_meter), 1);

// BMI Category
if ($bmi < 18.5) {
    $bmi_category = 'Kekurangan Berat Badan';
    $bmi_class = 'underweight';
} elseif ($bmi < 25) {
    $bmi_category = 'Normal';
    $bmi_class = 'normal';
} elseif ($bmi < 30) {
    $bmi_category = 'Kelebihan Berat Badan';
    $bmi_class = 'overweight';
} else {
    $bmi_category = 'Obesitas';
    $bmi_class = 'obese';
}

// Target progress (if applicable)
$target_progress = 0;
if ($user['tujuan'] !== 'kesehatan' && $user['berat_target']) {
    $total_selisih = abs($user['berat_target'] - $user['berat']);
    if ($total_selisih > 0) {
        $target_progress = 0; // Initial progress, update based on weight history
    }
}

// Get TDEE
$tdee = hitungTDEE($user['berat'], $user['tinggi'], $user['usia'], $user['gender'], $user['aktivitas'], $user['tujuan'], $user['berat_target']);
$rekom = rekomendasiNutrisi($tdee, $user['tujuan'], $user['berat']);
?>
