<?php
require 'includes/auth_check.php';
require 'config.php';

$user_id = $_SESSION['user_id'];
$tgl_awal = $_GET['tgl_awal'] ?? date('Y-m-d', strtotime('-30 days'));
$tgl_akhir = $_GET['tgl_akhir'] ?? date('Y-m-d');

// Ambil nama user dan data profil
$stmt = $pdo->prepare("SELECT nama_lengkap, gender, usia, berat, tinggi, aktivitas FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Ambil data asupan
$stmt = $pdo->prepare("SELECT DATE(waktu) as tanggal, TIME(waktu) as jam, nama_makanan, karbohidrat, protein, lemak, gula, kalori, air 
                       FROM asupan_detail 
                       WHERE user_id = ? AND DATE(waktu) BETWEEN ? AND ?
                       ORDER BY waktu ASC");
$stmt->execute([$user_id, $tgl_awal, $tgl_akhir]);
$data = $stmt->fetchAll();

// Cek apakah ada data
if (empty($data)) {
    echo "<!DOCTYPE html>
    <html>
    <head><meta charset='UTF-8'><title>Tidak Ada Data</title></head>
    <body style='font-family:Arial; padding:50px; text-align:center;'>
        <h2>❌ Tidak Ada Data</h2>
        <p>Tidak ada data untuk diekspor pada periode tersebut.</p>
        <a href='dashboard.php?tab=laporan' style='color:#3498db;'>← Kembali ke Dashboard</a>
    </body>
    </html>";
    exit;
}

// Hitung kebutuhan kalori dengan Harris-Benedict
$bmr = 0;
if ($user['gender'] == 'L') {
    // Pria: BMR = 66 + (13.7 × berat) + (5 × tinggi) - (6.8 × usia)
    $bmr = 66 + (13.7 * $user['berat']) + (5 * $user['tinggi']) - (6.8 * $user['usia']);
} else {
    // Wanita: BMR = 655 + (9.6 × berat) + (1.8 × tinggi) - (4.7 × usia)
    $bmr = 655 + (9.6 * $user['berat']) + (1.8 * $user['tinggi']) - (4.7 * $user['usia']);
}

// Faktor aktivitas
$aktivitas_multiplier = [
    'rendah' => 1.2,
    'sedang' => 1.55,
    'tinggi' => 1.725
];
$tdee = $bmr * ($aktivitas_multiplier[$user['aktivitas']] ?? 1.2);

// Kebutuhan makronutrien
$target_protein = $user['berat'] * 0.8; // 0.8g per kg
$target_lemak = ($tdee * 0.25) / 9; // 25% dari kalori, 1g lemak = 9 kcal
$target_karbo = ($tdee - ($target_protein * 4) - ($target_lemak * 9)) / 4; // Sisanya dari karbo

// Set nama file
$filename = 'Laporan_Gizi_' . date('Y-m-d') . '.csv';

// Header untuk download CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Buat file output
$output = fopen('php://output', 'w');

// BOM untuk UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Header Laporan
fputcsv($output, ['=== LAPORAN ASUPAN GIZI ===']);
fputcsv($output, []);

// Informasi Pengguna
fputcsv($output, ['INFORMASI PENGGUNA']);
fputcsv($output, ['Nama', $user['nama_lengkap']]);
fputcsv($output, ['Jenis Kelamin', $user['gender'] == 'L' ? 'Laki-laki' : 'Perempuan']);
fputcsv($output, ['Usia', $user['usia'] . ' tahun']);
fputcsv($output, ['Berat Badan', $user['berat'] . ' kg']);
fputcsv($output, ['Tinggi Badan', $user['tinggi'] . ' cm']);
fputcsv($output, []);

// Informasi Periode & Target
fputcsv($output, ['PERIODE & TARGET HARIAN (Harris-Benedict)']);
fputcsv($output, ['Periode Laporan', date('d M Y', strtotime($tgl_awal)) . ' - ' . date('d M Y', strtotime($tgl_akhir))]);
fputcsv($output, ['Tanggal Cetak', date('d M Y H:i')]);
fputcsv($output, ['BMR (Basal Metabolic Rate)', round($bmr, 0) . ' kkal']);
fputcsv($output, ['TDEE (Total Daily Energy Expenditure)', round($tdee, 0) . ' kkal']);
fputcsv($output, ['Target Kalori', round($tdee, 0) . ' kkal/hari']);
fputcsv($output, ['Target Protein', round($target_protein, 1) . ' g/hari']);
fputcsv($output, ['Target Karbohidrat', round($target_karbo, 1) . ' g/hari']);
fputcsv($output, ['Target Lemak', round($target_lemak, 1) . ' g/hari']);
fputcsv($output, []);
fputcsv($output, []);

// Data Asupan
fputcsv($output, ['=== DATA ASUPAN MAKANAN ===']);
fputcsv($output, []);

// Header tabel dengan alignment yang lebih baik
fputcsv($output, ['No', 'Tanggal', 'Jam', 'Nama Makanan', 'Kalori (kkal)', 'Karbo (g)', 'Protein (g)', 'Lemak (g)', 'Gula (g)', 'Air (L)']);

// Tulis data dengan formatting yang konsisten
$no = 1;
$total = ['kalori' => 0, 'karbo' => 0, 'protein' => 0, 'lemak' => 0, 'gula' => 0, 'air' => 0];

// Group by date untuk perhitungan harian
$data_by_date = [];
foreach ($data as $item) {
    $date = $item['tanggal'];
    if (!isset($data_by_date[$date])) {
        $data_by_date[$date] = [];
    }
    $data_by_date[$date][] = $item;
}

// Tulis data per tanggal
foreach ($data_by_date as $date => $items) {
    $daily_total = ['kalori' => 0, 'karbo' => 0, 'protein' => 0, 'lemak' => 0, 'gula' => 0, 'air' => 0];
    
    foreach ($items as $item) {
        fputcsv($output, [
            $no++,
            date('d M Y', strtotime($date)),
            $item['jam'],
            $item['nama_makanan'],
            number_format($item['kalori'], 1, '.', ''),
            number_format($item['karbohidrat'], 1, '.', ''),
            number_format($item['protein'], 1, '.', ''),
            number_format($item['lemak'], 1, '.', ''),
            number_format($item['gula'], 1, '.', ''),
            number_format($item['air'], 1, '.', '')
        ]);
        
        // Akumulasi total harian
        $daily_total['kalori'] += $item['kalori'];
        $daily_total['karbo'] += $item['karbohidrat'];
        $daily_total['protein'] += $item['protein'];
        $daily_total['lemak'] += $item['lemak'];
        $daily_total['gula'] += $item['gula'];
        $daily_total['air'] += $item['air'];
        
        // Akumulasi total keseluruhan
        $total['kalori'] += $item['kalori'];
        $total['karbo'] += $item['karbohidrat'];
        $total['protein'] += $item['protein'];
        $total['lemak'] += $item['lemak'];
        $total['gula'] += $item['gula'];
        $total['air'] += $item['air'];
    }
    
    // Subtotal harian
    fputcsv($output, [
        '', '', '', 'Subtotal ' . date('d M Y', strtotime($date)),
        number_format($daily_total['kalori'], 1, '.', ''),
        number_format($daily_total['karbo'], 1, '.', ''),
        number_format($daily_total['protein'], 1, '.', ''),
        number_format($daily_total['lemak'], 1, '.', ''),
        number_format($daily_total['gula'], 1, '.', ''),
        number_format($daily_total['air'], 1, '.', '')
    ]);
    fputcsv($output, []); // Baris kosong pemisah
}

// Total Keseluruhan
fputcsv($output, []);
fputcsv($output, ['', '', '', '=== TOTAL KESELURUHAN ===', 
    number_format($total['kalori'], 1, '.', ''),
    number_format($total['karbo'], 1, '.', ''),
    number_format($total['protein'], 1, '.', ''),
    number_format($total['lemak'], 1, '.', ''),
    number_format($total['gula'], 1, '.', ''),
    number_format($total['air'], 1, '.', '')
]);

// Rata-rata Harian
$jumlah_hari = count($data_by_date);
fputcsv($output, ['', '', '', '=== RATA-RATA HARIAN ===',
    number_format($total['kalori'] / $jumlah_hari, 1, '.', ''),
    number_format($total['karbo'] / $jumlah_hari, 1, '.', ''),
    number_format($total['protein'] / $jumlah_hari, 1, '.', ''),
    number_format($total['lemak'] / $jumlah_hari, 1, '.', ''),
    number_format($total['gula'] / $jumlah_hari, 1, '.', ''),
    number_format($total['air'] / $jumlah_hari, 1, '.', '')
]);

// Persentase terhadap Target
fputcsv($output, []);
fputcsv($output, ['=== PERSENTASE TERHADAP TARGET HARIAN ===']);
fputcsv($output, ['Kalori', round(($total['kalori'] / $jumlah_hari / $tdee) * 100, 1) . '%']);
fputcsv($output, ['Protein', round(($total['protein'] / $jumlah_hari / $target_protein) * 100, 1) . '%']);
fputcsv($output, ['Karbohidrat', round(($total['karbo'] / $jumlah_hari / $target_karbo) * 100, 1) . '%']);
fputcsv($output, ['Lemak', round(($total['lemak'] / $jumlah_hari / $target_lemak) * 100, 1) . '%']);

// Footer
fputcsv($output, []);
fputcsv($output, []);
fputcsv($output, ['Dicetak dari Aplikasi Hidup Sehat - ' . date('d M Y H:i:s')]);
fputcsv($output, ['Metode Perhitungan: Harris-Benedict Equation']);

// Tutup file
fclose($output);
exit;
