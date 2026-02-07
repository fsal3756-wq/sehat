<?php
// Catatan Tab - Data Processing

$error_asupan = '';
$success_asupan = '';

// Handle Tambah Asupan Manual
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_asupan'])) {
    $nama = trim($_POST['nama_makanan'] ?? '');
    $waktu_makan = $_POST['waktu_makan'] ?? 'Siang';
    $karbo = floatval($_POST['karbohidrat'] ?? 0);
    $protein = floatval($_POST['protein'] ?? 0);
    $lemak = floatval($_POST['lemak'] ?? 0);
    $gula = floatval($_POST['gula'] ?? 0);
    $air = floatval($_POST['air'] ?? 0);

    $nama_lengkap = "$waktu_makan: $nama";

    if (empty($nama)) {
        $error_asupan = "Nama makanan wajib diisi.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO asupan_detail (user_id, nama_makanan, karbohidrat, protein, lemak, gula, air) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $nama_lengkap, $karbo, $protein, $lemak, $gula, $air]);
        $success_asupan = "Asupan berhasil ditambahkan!";
        
        // Refresh data
        header("Location: dashboard.php?tab=catatan");
        exit;
    }
}

// Handle Tambah dari Dataset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_dari_dataset'])) {
    $nama = trim($_POST['nama_makanan']);
    $waktu_makan = $_POST['waktu_makan'] ?? 'Siang';
    $karbo = floatval($_POST['karbohidrat']);
    $protein = floatval($_POST['protein']);
    $lemak = floatval($_POST['lemak']);
    $gula = floatval($_POST['gula'] ?? 0);
    $air = floatval($_POST['air'] ?? 0);

    $nama_lengkap = "$waktu_makan: $nama";

    $stmt = $pdo->prepare("INSERT INTO asupan_detail (user_id, nama_makanan, karbohidrat, protein, lemak, gula, air) 
                          VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $nama_lengkap, $karbo, $protein, $lemak, $gula, $air]);
    
    header("Location: dashboard.php?tab=catatan");
    exit;
}

// Handle Hapus Asupan
if (isset($_GET['hapus_id'])) {
    $stmt = $pdo->prepare("DELETE FROM asupan_detail WHERE id = ? AND user_id = ?");
    $stmt->execute([(int)$_GET['hapus_id'], $user_id]);
    header("Location: dashboard.php?tab=catatan");
    exit;
}

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

// Get recommendations
$tdee = hitungTDEE($user['berat'], $user['tinggi'], $user['usia'], $user['gender'], $user['aktivitas'], $user['tujuan'], $user['berat_target']);
$rekom = rekomendasiNutrisi($tdee, $user['tujuan'], $user['berat']);

// Weekly progress
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
}
?>
