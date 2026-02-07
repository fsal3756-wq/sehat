<?php
// Laporan Tab - Data Processing

// Get date range
$tgl_awal = $_GET['tgl_awal'] ?? date('Y-m-d', strtotime('-6 days'));
$tgl_akhir = $_GET['tgl_akhir'] ?? date('Y-m-d');

// Get data for period
$stmt = $pdo->prepare("
    SELECT DATE(waktu) as tanggal, 
           SUM(kalori) as total_kalori,
           SUM(protein) as total_protein,
           SUM(karbohidrat) as total_karbohidrat
    FROM asupan_detail 
    WHERE user_id = ? AND DATE(waktu) BETWEEN ? AND ?
    GROUP BY DATE(waktu)
    ORDER BY tanggal ASC
");
$stmt->execute([$user_id, $tgl_awal, $tgl_akhir]);
$data_grafik = $stmt->fetchAll();

// Prepare data for charts
$labels = [];
$data_kalori = [];
$data_protein = [];
$data_karbo = [];
$data_target_kalori = [];
$data_target_protein = [];
$data_target_karbo = [];

$startDate = new DateTime($tgl_awal);
$endDate = new DateTime($tgl_akhir);
$interval = new DateInterval('P1D');
$daterange = new DatePeriod($startDate, $interval, $endDate->modify('+1 day'));

// Create data mapping
$data_map = [];
foreach ($data_grafik as $row) {
    $data_map[$row['tanggal']] = [
        'kalori' => (float)$row['total_kalori'],
        'protein' => (float)$row['total_protein'],
        'karbohidrat' => (float)$row['total_karbohidrat']
    ];
}

// Get TDEE and recommendations
$tdee = hitungTDEE($user['berat'], $user['tinggi'], $user['usia'], $user['gender'], $user['aktivitas'], $user['tujuan'], $user['berat_target']);
$rekom = rekomendasiNutrisi($tdee, $user['tujuan'], $user['berat']);

foreach ($daterange as $date) {
    $tgl_str = $date->format("Y-m-d");
    $labels[] = $date->format("d M");
    
    if (isset($data_map[$tgl_str])) {
        $data_kalori[] = round($data_map[$tgl_str]['kalori']);
        $data_protein[] = round($data_map[$tgl_str]['protein']);
        $data_karbo[] = round($data_map[$tgl_str]['karbohidrat']);
    } else {
        $data_kalori[] = 0;
        $data_protein[] = 0;
        $data_karbo[] = 0;
    }
    
    $data_target_kalori[] = $rekom['kalori'];
    $data_target_protein[] = $rekom['protein'];
    $data_target_karbo[] = $rekom['karbohidrat'];
}

// Calculate summary statistics
$total_hari = count($data_grafik);
$avg_kalori = $total_hari > 0 ? round(array_sum($data_kalori) / $total_hari) : 0;
$avg_protein = $total_hari > 0 ? round(array_sum($data_protein) / $total_hari) : 0;
$avg_karbo = $total_hari > 0 ? round(array_sum($data_karbo) / $total_hari) : 0;

$pct_kalori = $rekom['kalori'] > 0 ? round(($avg_kalori / $rekom['kalori']) * 100) : 0;
$pct_protein = $rekom['protein'] > 0 ? round(($avg_protein / $rekom['protein']) * 100) : 0;
$pct_karbo = $rekom['karbohidrat'] > 0 ? round(($avg_karbo / $rekom['karbohidrat']) * 100) : 0;
?>
