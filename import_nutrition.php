<?php
require 'config.php';

set_time_limit(300);

$csvFile = 'nutrition.csv';

if (!file_exists($csvFile)) {
    die("❌ File nutrition.csv tidak ditemukan!");
}

echo "<!DOCTYPE html>
<html><head><meta charset='UTF-8'>
<style>
body { font-family: Arial; padding: 20px; background: #f5f5f5; }
.container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; }
.success { color: #4caf50; }
.error { color: #f44336; }
.progress { background: #e0e0e0; height: 30px; border-radius: 15px; overflow: hidden; margin: 20px 0; }
.progress-bar { background: #4caf50; height: 100%; transition: width 0.3s; }
</style>
</head><body><div class='container'>";

echo "<h2>🚀 Import Data Makanan dari CSV</h2>";
flush();

try {
    $file = fopen($csvFile, 'r');
    
    // SKIP HEADER (baris pertama)
    $header = fgetcsv($file);
    echo "<p>✅ Header terbaca: " . implode(', ', $header) . "</p>";
    
    $count = 0;
    $success = 0;
    $failed = 0;
    $errors = [];
    
    $stmt = $pdo->prepare("INSERT INTO makanan_dataset (id, calories, proteins, fat, carbohydrate, name, image) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)
                           ON DUPLICATE KEY UPDATE 
                           calories = VALUES(calories),
                           proteins = VALUES(proteins),
                           fat = VALUES(fat),
                           carbohydrate = VALUES(carbohydrate),
                           name = VALUES(name),
                           image = VALUES(image)");
    
    echo "<div class='progress'><div class='progress-bar' id='progressBar' style='width:0%'></div></div>";
    echo "<p id='status'>Memproses data...</p>";
    flush();
    
    while (($data = fgetcsv($file)) !== FALSE) {
        $count++;
        
        // Validasi jumlah kolom
        if (count($data) < 7) {
            $failed++;
            $errors[] = "Baris $count: Kolom tidak lengkap";
            continue;
        }
        
        try {
            // Urutan di CSV: id, calories, proteins, fat, carbohydrate, name, image
            $id = (int)trim($data[0]);
            $calories = is_numeric($data[1]) ? (float)$data[1] : 0;
            $proteins = is_numeric($data[2]) ? (float)$data[2] : 0;
            $fat = is_numeric($data[3]) ? (float)$data[3] : 0;
            $carbohydrate = is_numeric($data[4]) ? (float)$data[4] : 0;
            $name = trim($data[5]);
            $image = trim($data[6]);
            
            // Skip jika id invalid atau nama kosong
            if ($id <= 0 || empty($name)) {
                $failed++;
                $errors[] = "Baris $count: ID tidak valid atau nama kosong";
                continue;
            }
            
            $stmt->execute([$id, $calories, $proteins, $fat, $carbohydrate, $name, $image]);
            $success++;
            
            // Update progress setiap 50 data
            if ($count % 50 == 0) {
                $percentage = round(($count / 1346) * 100);
                echo "<script>
                    document.getElementById('progressBar').style.width = '{$percentage}%';
                    document.getElementById('status').innerHTML = '⏳ Progress: $count / 1346 data ($percentage%)';
                </script>";
                flush();
            }
            
        } catch (PDOException $e) {
            $failed++;
            $errors[] = "Baris $count ($name): " . $e->getMessage();
        }
    }
    
    fclose($file);
    
    echo "<script>
        document.getElementById('progressBar').style.width = '100%';
        document.getElementById('status').innerHTML = '✅ Selesai!';
    </script>";
    
    echo "<hr>";
    echo "<h3 class='success'>🎉 Import Berhasil!</h3>";
    echo "<div style='background:#e8f5e9; padding:20px; border-radius:8px; margin:20px 0;'>";
    echo "<table style='width:100%;'>";
    echo "<tr><td><strong>📊 Total Baris:</strong></td><td><strong>$count</strong></td></tr>";
    echo "<tr><td><strong class='success'>✅ Berhasil:</strong></td><td><strong class='success'>$success</strong></td></tr>";
    echo "<tr><td><strong class='error'>❌ Gagal:</strong></td><td><strong class='error'>$failed</strong></td></tr>";
    echo "</table>";
    echo "</div>";
    
    // Tampilkan error jika ada (maksimal 10)
    if ($failed > 0 && count($errors) > 0) {
        echo "<details style='margin:20px 0; padding:15px; background:#fff3cd; border-radius:8px;'>";
        echo "<summary style='cursor:pointer; font-weight:bold;'>⚠️ Lihat Error ($failed error)</summary>";
        echo "<ul style='margin-top:10px;'>";
        foreach (array_slice($errors, 0, 10) as $error) {
            echo "<li style='color:#856404;'>$error</li>";
        }
        if (count($errors) > 10) {
            echo "<li><em>... dan " . (count($errors) - 10) . " error lainnya</em></li>";
        }
        echo "</ul>";
        echo "</details>";
    }
    
    // Verifikasi
    $stmt = $pdo->query("SELECT COUNT(*) FROM makanan_dataset");
    $total = $stmt->fetchColumn();
    
    echo "<div style='background:#e3f2fd; padding:20px; border-radius:8px; margin:20px 0;'>";
    echo "<h4>🔍 Verifikasi Database</h4>";
    echo "<p>Total makanan di database: <strong>$total</strong></p>";
    echo "</div>";
    
    // Sample data
    $stmt = $pdo->query("SELECT name, calories, proteins FROM makanan_dataset LIMIT 5");
    $samples = $stmt->fetchAll();
    
    echo "<h4>📋 Sample Data:</h4>";
    echo "<table style='width:100%; border-collapse:collapse;'>";
    echo "<tr style='background:#f5f5f5;'><th style='padding:8px; text-align:left;'>Nama</th><th>Kalori</th><th>Protein</th></tr>";
    foreach ($samples as $sample) {
        echo "<tr style='border-bottom:1px solid #ddd;'>";
        echo "<td style='padding:8px;'>{$sample['name']}</td>";
        echo "<td style='text-align:center;'>{$sample['calories']}</td>";
        echo "<td style='text-align:center;'>{$sample['proteins']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<div style='margin-top:30px; text-align:center;'>";
    echo "<a href='dashboard.php?tab=catatan' style='display:inline-block; padding:15px 30px; background:#4caf50; color:white; text-decoration:none; border-radius:8px; font-weight:bold; font-size:16px;'>🏠 Ke Dashboard</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background:#ffebee; padding:20px; border-radius:8px; color:#c62828; margin:20px 0;'>";
    echo "<h3>❌ Error Fatal</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "</div></body></html>";
?>