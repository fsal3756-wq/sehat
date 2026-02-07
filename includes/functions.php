<?php
// Metode Mifflin-St Jeor (lebih modern dan akurat)
function hitungTDEE($berat, $tinggi, $usia, $gender, $aktivitas, $tujuan) {
    $bmr = ($gender === 'L') 
        ? (10 * $berat + 6.25 * $tinggi - 5 * $usia + 5)
        : (10 * $berat + 6.25 * $tinggi - 5 * $usia - 161);

    // Faktor aktivitas
    switch($aktivitas) {
        case 'rendah':
            $faktor = 1.2;
            break;
        case 'sedang':
            $faktor = 1.55;
            break;
        case 'tinggi':
            $faktor = 1.725;
            break;
        default:
            $faktor = 1.2;
    }

    $tdee = $bmr * $faktor;

    if ($tujuan === 'diet') $tdee -= 500;
    elseif ($tujuan === 'otot') $tdee += 300;

    return max(1200, round($tdee));
}

// Metode Harris-Benedict (klasik, lebih banyak digunakan secara medis)
function hitungHarrisBenedict($berat, $tinggi, $usia, $gender, $aktivitas) {
    // Hitung BMR dengan Harris-Benedict
    if ($gender === 'L') {
        // Pria: BMR = 66 + (13.7 × berat kg) + (5 × tinggi cm) - (6.8 × usia)
        $bmr = 66 + (13.7 * $berat) + (5 * $tinggi) - (6.8 * $usia);
    } else {
        // Wanita: BMR = 655 + (9.6 × berat kg) + (1.8 × tinggi cm) - (4.7 × usia)
        $bmr = 655 + (9.6 * $berat) + (1.8 * $tinggi) - (4.7 * $usia);
    }
    
    // Faktor aktivitas
    $faktor_aktivitas = [
        'sedentary' => 1.2,      // Sangat sedikit atau tidak ada olahraga
        'light' => 1.375,        // Olahraga ringan 1-3 hari/minggu
        'moderate' => 1.55,      // Olahraga sedang 3-5 hari/minggu
        'active' => 1.725,       // Olahraga berat 6-7 hari/minggu
        'very_active' => 1.9,    // Olahraga sangat berat atau pekerjaan fisik
        'rendah' => 1.2,
        'sedang' => 1.55,
        'tinggi' => 1.725
    ];
    
    $tdee = $bmr * ($faktor_aktivitas[$aktivitas] ?? 1.2);
    
    return [
        'bmr' => round($bmr, 0),
        'tdee' => round($tdee, 0)
    ];
}

function rekomendasiNutrisi($tdee, $tujuan, $berat) {
    // Gunakan if-else untuk kompatibilitas PHP 7.x
    if ($tujuan === 'diet') {
        return [
            'kalori' => $tdee,
            'protein' => round(2.2 * $berat),
            'karbohidrat' => round(($tdee * 0.4) / 4),
            'lemak' => round(($tdee * 0.3) / 9),
            'air' => 2.5
        ];
    } elseif ($tujuan === 'otot') {
        return [
            'kalori' => $tdee,
            'protein' => round(2.2 * $berat),
            'karbohidrat' => round(($tdee * 0.5) / 4),
            'lemak' => round(($tdee * 0.25) / 9),
            'air' => 3.5
        ];
    } else {
        return [
            'kalori' => $tdee,
            'protein' => round(1.5 * $berat),
            'karbohidrat' => round(($tdee * 0.5) / 4),
            'lemak' => round(($tdee * 0.3) / 9),
            'air' => 2.0
        ];
    }
}
?>
