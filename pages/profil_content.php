<!-- Profil Tab Content -->

<!-- Profile Header -->
<div class="profile-header">
    <div class="profile-avatar-large">👤</div>
    <h3><?php echo htmlspecialchars($user['nama_lengkap']); ?></h3>
    <p><?php echo $user['usia']; ?> tahun | <?php echo $user['gender'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></p>
</div>

<!-- Info Grid -->
<div class="info-grid">
    <div class="info-item">
        <div class="info-label">Tinggi</div>
        <div class="info-value"><?php echo $user['tinggi']; ?><span class="info-unit">cm</span></div>
    </div>
    <div class="info-item">
        <div class="info-label">Berat</div>
        <div class="info-value"><?php echo $user['berat']; ?><span class="info-unit">kg</span></div>
    </div>
    <div class="info-item">
        <div class="info-label">BMI</div>
        <div class="info-value"><?php echo $bmi; ?></div>
    </div>
    <div class="info-item">
        <div class="info-label">TDEE</div>
        <div class="info-value"><?php echo $tdee; ?><span class="info-unit">kkal</span></div>
    </div>
</div>

<!-- BMI Card -->
<div class="bmi-card">
    <h4>📊 Body Mass Index (BMI)</h4>
    <div class="bmi-value"><?php echo $bmi; ?></div>
    <span class="bmi-category <?php echo $bmi_class; ?>"><?php echo $bmi_category; ?></span>
    <p style="margin-top: 15px; font-size: 14px; color: var(--text-secondary);">
        BMI dihitung dari: berat / (tinggi × tinggi)
    </p>
</div>

<!-- Harris-Benedict Calculator -->
<div class="card">
    <h3>🔥 Kalori Harian (Harris-Benedict)</h3>
    <?php
    // Hitung BMR dengan Harris-Benedict
    if ($user['gender'] == 'L') {
        // Pria: BMR = 66 + (13.7 × BB) + (5 × TB) - (6.8 × U)
        $bmr = 66 + (13.7 * $user['berat']) + (5 * $user['tinggi']) - (6.8 * $user['usia']);
    } else {
        // Wanita: BMR = 655 + (9.6 × BB) + (1.8 × TB) - (4.7 × U)
        $bmr = 655 + (9.6 * $user['berat']) + (1.8 * $user['tinggi']) - (4.7 * $user['usia']);
    }
    
    // Faktor aktivitas
    switch($user['aktivitas']) {
        case 'rendah':
            $aktivitas_multiplier = 1.2;
            break;
        case 'sedang':
            $aktivitas_multiplier = 1.55;
            break;
        case 'tinggi':
            $aktivitas_multiplier = 1.9;
            break;
        default:
            $aktivitas_multiplier = 1.2;
    }
    
    $tdee_hb = round($bmr * $aktivitas_multiplier);
    ?>
    
    <div class="harris-benedict-grid">
        <div class="hb-item">
            <div class="hb-label">BMR (Basal Metabolic Rate)</div>
            <div class="hb-value"><?php echo round($bmr); ?> <span>kkal/hari</span></div>
            <div class="hb-desc">Kalori yang dibakar saat istirahat total</div>
        </div>
        
        <div class="hb-item highlight">
            <div class="hb-label">TDEE (Total Daily Energy Expenditure)</div>
            <div class="hb-value"><?php echo $tdee_hb; ?> <span>kkal/hari</span></div>
            <div class="hb-desc">BMR × faktor aktivitas (<?php echo $aktivitas_multiplier; ?>x)</div>
        </div>
    </div>
    
    <div style="margin-top: 15px; padding: 12px; background: var(--border-light); border-radius: 8px; font-size: 13px; color: var(--text-secondary);">
        <strong>💡 Penjelasan:</strong><br>
        • BMR = Kalori yang tubuh butuhkan untuk fungsi dasar (bernapas, sirkulasi darah, dll)<br>
        • TDEE = BMR dikali faktor aktivitas harian Anda<br>
        • Untuk diet: kurangi 300-500 kkal dari TDEE<br>
        • Untuk bulk/massa otot: tambah 300-500 kkal ke TDEE
    </div>
</div>

<!-- Activity & Goal Badges -->
<div class="card">
    <h3>🎯 Info Aktivitas & Tujuan</h3>
    <div class="activity-badges">
        <span class="badge primary">
            Level Aktivitas: <?php 
                if ($user['aktivitas'] == 'rendah') {
                    echo 'Rendah';
                } elseif ($user['aktivitas'] == 'sedang') {
                    echo 'Sedang';
                } elseif ($user['aktivitas'] == 'tinggi') {
                    echo 'Tinggi';
                } else {
                    echo 'Sedang';
                }
            ?>
        </span>
        <span class="badge warning">
            Tujuan: <?php 
                if ($user['tujuan'] == 'kesehatan') {
                    echo 'Jaga Kesehatan';
                } elseif ($user['tujuan'] == 'diet') {
                    echo 'Diet';
                } elseif ($user['tujuan'] == 'otot') {
                    echo 'Pembentukan Otot';
                } else {
                    echo 'Jaga Kesehatan';
                }
            ?>
        </span>
    </div>
    
    <?php if ($user['tujuan'] !== 'kesehatan' && $user['berat_target']): ?>
        <div style="margin-top: 20px;">
            <strong>Target Berat:</strong> <?php echo $user['berat_target']; ?> kg<br>
            <strong>Selisih:</strong> <?php echo abs($user['berat'] - $user['berat_target']); ?> kg
        </div>
    <?php endif; ?>
</div>

<!-- Target Card -->
<?php if ($user['tujuan'] !== 'kesehatan' && $user['berat_target']): ?>
<div class="target-card">
    <h4>🎯 Progress Menuju Target</h4>
    <div class="target-stats">
        <span>Berat Sekarang: <?php echo $user['berat']; ?> kg</span>
        <span>Target: <?php echo $user['berat_target']; ?> kg</span>
    </div>
    <div class="target-progress">
        <div class="target-fill" style="width: <?php echo $target_progress; ?>%"></div>
    </div>
    <p style="margin-top: 10px; font-size: 13px;">
        <?php echo $user['tujuan'] == 'diet' ? 'Kurangi' : 'Tambah'; ?> 
        <?php echo abs($user['berat'] - $user['berat_target']); ?> kg lagi untuk mencapai target!
    </p>
</div>
<?php endif; ?>

<!-- Daily Nutrition Target -->
<div class="card">
    <h3>📘 Target Nutrisi Harian</h3>
    <ul style="list-style: none; padding: 0;">
        <li style="padding: 8px 0; border-bottom: 1px solid var(--border-color);">
            <strong>🔥 Kalori:</strong> <?php echo $rekom['kalori']; ?> kkal
        </li>
        <li style="padding: 8px 0; border-bottom: 1px solid var(--border-color);">
            <strong>🥩 Protein:</strong> <?php echo $rekom['protein']; ?> g
        </li>
        <li style="padding: 8px 0; border-bottom: 1px solid var(--border-color);">
            <strong>🍚 Karbohidrat:</strong> <?php echo $rekom['karbohidrat']; ?> g
        </li>
        <li style="padding: 8px 0; border-bottom: 1px solid var(--border-color);">
            <strong>🥑 Lemak:</strong> <?php echo $rekom['lemak']; ?> g
        </li>
        <li style="padding: 8px 0;">
            <strong>💧 Air:</strong> <?php echo $rekom['air']; ?> liter
        </li>
    </ul>
</div>

<!-- Edit Form -->
<div class="edit-form">
    <h3>✏️ Update Profil</h3>
    
    <?php if ($success): ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <input type="hidden" name="simpan_profil" value="1">
        
        <div class="form-section">
            <h4>⚖️ Berat Badan</h4>
            <div class="form-group">
                <label>Berat Sekarang (kg)</label>
                <input type="number" name="berat_sekarang" value="<?php echo $user['berat']; ?>" 
                       min="30" max="250" required>
                <small>Masukkan berat badan terkini untuk perhitungan yang akurat</small>
            </div>
        </div>
        
        <div class="form-section">
            <h4>🎯 Tujuan Kesehatan</h4>
            <div class="form-group">
                <label>Pilih Tujuan</label>
                <select name="tujuan" id="tujuanSelect" required>
                    <option value="kesehatan" <?php echo $user['tujuan'] == 'kesehatan' ? 'selected' : ''; ?>>
                        Jaga Kesehatan
                    </option>
                    <option value="custom" <?php echo $user['tujuan'] != 'kesehatan' ? 'selected' : ''; ?>>
                        Custom (Diet/Pembentukan Otot)
                    </option>
                </select>
            </div>
            
            <div class="form-group" id="targetBeratDiv" style="<?php echo $user['tujuan'] == 'kesehatan' ? 'display:none;' : ''; ?>">
                <label>Berat Target (kg)</label>
                <input type="number" name="berat_target" value="<?php echo $user['berat_target'] ?? ''; ?>" 
                       min="30" max="250">
                <small>Jika berat target < berat sekarang = Diet, jika > = Pembentukan Otot</small>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn-save">💾 Simpan Perubahan</button>
            <button type="reset" class="btn-reset">🔄 Reset</button>
        </div>
    </form>
</div>

<!-- Logout Section -->
<div class="logout-section">
    <a href="logout.php" class="btn-logout" onclick="return confirm('Yakin ingin logout?')">
        🚪 Logout
    </a>
</div>

<script>
document.getElementById('tujuanSelect').addEventListener('change', function() {
    const targetDiv = document.getElementById('targetBeratDiv');
    targetDiv.style.display = this.value === 'custom' ? 'block' : 'none';
});
</script>
