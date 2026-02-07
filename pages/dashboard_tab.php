<?php
// Dashboard Tab - Data Processing

// Calculate TDEE and recommendations
$tdee = hitungTDEE($user['berat'], $user['tinggi'], $user['usia'], $user['gender'], $user['aktivitas'], $user['tujuan'], $user['berat_target']);
$rekom = rekomendasiNutrisi($tdee, $user['tujuan'], $user['berat']);

// Get today's intake
$tanggal_hari_ini = date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM asupan_detail WHERE user_id = ? AND DATE(waktu) = ? ORDER BY waktu DESC");
$stmt->execute([$user_id, $tanggal_hari_ini]);
$asupan_hari_ini = $stmt->fetchAll();

// Calculate totals
$total = ['karbohidrat' => 0, 'protein' => 0, 'lemak' => 0, 'gula' => 0, 'air' => 0, 'kalori' => 0];
foreach ($asupan_hari_ini as $item) {
    $total['karbohidrat'] += $item['karbohidrat'];
    $total['protein'] += $item['protein'];
    $total['lemak'] += $item['lemak'];
    $total['gula'] += $item['gula'];
    $total['air'] += $item['air'];
    $total['kalori'] += $item['kalori'];
}

// Calculate progress percentages
$progress = [];
$progress['kalori'] = $rekom['kalori'] > 0 ? round(($total['kalori'] / $rekom['kalori']) * 100) : 0;
$progress['protein'] = $rekom['protein'] > 0 ? round(($total['protein'] / $rekom['protein']) * 100) : 0;
$progress['karbohidrat'] = $rekom['karbohidrat'] > 0 ? round(($total['karbohidrat'] / $rekom['karbohidrat']) * 100) : 0;
$progress['lemak'] = $rekom['lemak'] > 0 ? round(($total['lemak'] / $rekom['lemak']) * 100) : 0;
$progress['air'] = $rekom['air'] > 0 ? round(($total['air'] / $rekom['air']) * 100) : 0;

// Get weekly stats (7 days)
$tujuh_hari_lalu = date('Y-m-d', strtotime('-6 days'));
$stmt = $pdo->prepare("
    SELECT DATE(waktu) as tanggal, 
           SUM(kalori) as total_kalori,
           SUM(protein) as total_protein,
           SUM(karbohidrat) as total_karbohidrat
    FROM asupan_detail 
    WHERE user_id = ? AND waktu >= ?
    GROUP BY DATE(waktu)
    ORDER BY tanggal ASC
");
$stmt->execute([$user_id, $tujuh_hari_lalu]);
$data_7_hari = $stmt->fetchAll();

$total_kalori_7_hari = array_sum(array_column($data_7_hari, 'total_kalori'));
$total_protein_7_hari = array_sum(array_column($data_7_hari, 'total_protein'));
$total_karbo_7_hari = array_sum(array_column($data_7_hari, 'total_karbohidrat'));

$target_kalori_7_hari = $rekom['kalori'] * 7;
$target_protein_7_hari = $rekom['protein'] * 7;
$target_karbo_7_hari = $rekom['karbohidrat'] * 7;

$progress_kalori = $target_kalori_7_hari > 0 ? round(($total_kalori_7_hari / $target_kalori_7_hari) * 100) : 0;
$progress_protein = $target_protein_7_hari > 0 ? round(($total_protein_7_hari / $target_protein_7_hari) * 100) : 0;
$progress_karbo = $target_karbo_7_hari > 0 ? round(($total_karbo_7_hari / $target_karbo_7_hari) * 100) : 0;

// Analyze eating habits
$analisis = [];
if (!empty($asupan_hari_ini)) {
    $waktu_count = ['Pagi' => 0, 'Siang' => 0, 'Malam' => 0, 'Cemilan' => 0];
    foreach ($asupan_hari_ini as $item) {
        foreach (array_keys($waktu_count) as $waktu) {
            if (strpos($item['nama_makanan'], $waktu) === 0) {
                $waktu_count[$waktu]++;
                break;
            }
        }
    }
    arsort($waktu_count);
    $most_frequent = key($waktu_count);
    if ($waktu_count[$most_frequent] > 0) {
        $analisis[] = "Kamu paling sering makan di waktu <strong>$most_frequent</strong>.";
    }

    if ($total['protein'] < $rekom['protein'] * 0.7) {
        $analisis[] = "Asupan proteinmu masih rendah. Coba tambah sumber protein seperti telur, ayam, atau tahu.";
    }
    if ($total['karbohidrat'] > $rekom['karbohidrat'] * 1.3) {
        $analisis[] = "Asupan karbohidratmu melebihi target. Kurangi nasi, gula, atau camilan manis.";
    }
    if ($total['kalori'] < $rekom['kalori'] * 0.8) {
        $analisis[] = "Kalorimu hari ini masih kurang dari target. Pastikan makan cukup nutrisi!";
    }
    if ($total['kalori'] > $rekom['kalori'] * 1.2) {
        $analisis[] = "Kalorimu hari ini melebihi target. Perhatikan porsi makan dan camilan.";
    }
}

// Prediction
$prediksi = "";
if ($user['tujuan'] !== 'kesehatan' && $user['berat_target']) {
    $selisih_berat = abs($user['berat'] - $user['berat_target']);
    if ($selisih_berat > 0) {
        $defisit_surplus = 500;
        $hari_diperlukan = ceil(($selisih_berat * 7700) / $defisit_surplus);
        $minggu_diperlukan = ceil($hari_diperlukan / 7);
        $prediksi = "Dengan defisit 500 kkal/hari, kamu akan mencapai target dalam <strong>$minggu_diperlukan minggu</strong>.";
    }
}

// Calculate BMI
$tinggi_meter = $user['tinggi'] / 100;
$bmi = round($user['berat'] / ($tinggi_meter * $tinggi_meter), 1);
?>
