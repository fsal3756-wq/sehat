<!-- Dashboard Tab Content -->

<!-- Profile Summary Card -->
<div class="profile-card">
    <div class="profile-avatar">👤</div>
    <div class="profile-info">
        <h3><?php echo htmlspecialchars($user['nama_lengkap']); ?></h3>
        <p>
            <?php echo $user['tinggi']; ?> cm | <?php echo $user['berat']; ?> kg | BMI: <?php echo $bmi; ?>
        </p>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">🔥</div>
        <div class="stat-value"><?php echo round($total['kalori']); ?></div>
        <div class="stat-label">Kalori Hari Ini</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🥩</div>
        <div class="stat-value"><?php echo round($total['protein']); ?>g</div>
        <div class="stat-label">Protein</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🍚</div>
        <div class="stat-value"><?php echo round($total['karbohidrat']); ?>g</div>
        <div class="stat-label">Karbohidrat</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">💧</div>
        <div class="stat-value"><?php echo $total['air']; ?>L</div>
        <div class="stat-label">Air</div>
    </div>
</div>

<!-- Today's Progress -->
<div class="card">
    <h3>📊 Progress Hari Ini</h3>
    <div class="progress-container">
        <!-- Kalori -->
        <div class="progress-item">
            <div class="progress-label">
                <span>🔥 Kalori</span>
                <span><?php echo round($total['kalori']); ?> / <?php echo $rekom['kalori']; ?> kkal</span>
            </div>
            <div class="progress-bar">
                <?php 
                $kalori_class = $progress['kalori'] > 120 ? 'danger' : ($progress['kalori'] < 80 ? 'warning' : '');
                $kalori_width = min(100, $progress['kalori']);
                ?>
                <div class="progress-fill <?php echo $kalori_class; ?>" style="width: <?php echo $kalori_width; ?>%">
                    <?php echo $progress['kalori']; ?>%
                </div>
            </div>
        </div>

        <!-- Protein -->
        <div class="progress-item">
            <div class="progress-label">
                <span>🥩 Protein</span>
                <span><?php echo round($total['protein']); ?> / <?php echo $rekom['protein']; ?> g</span>
            </div>
            <div class="progress-bar">
                <?php 
                $protein_class = $progress['protein'] > 120 ? 'danger' : ($progress['protein'] < 80 ? 'warning' : '');
                $protein_width = min(100, $progress['protein']);
                ?>
                <div class="progress-fill <?php echo $protein_class; ?>" style="width: <?php echo $protein_width; ?>%">
                    <?php echo $progress['protein']; ?>%
                </div>
            </div>
        </div>

        <!-- Karbohidrat -->
        <div class="progress-item">
            <div class="progress-label">
                <span>🍚 Karbohidrat</span>
                <span><?php echo round($total['karbohidrat']); ?> / <?php echo $rekom['karbohidrat']; ?> g</span>
            </div>
            <div class="progress-bar">
                <?php 
                $karbo_class = $progress['karbohidrat'] > 120 ? 'danger' : ($progress['karbohidrat'] < 80 ? 'warning' : '');
                $karbo_width = min(100, $progress['karbohidrat']);
                ?>
                <div class="progress-fill <?php echo $karbo_class; ?>" style="width: <?php echo $karbo_width; ?>%">
                    <?php echo $progress['karbohidrat']; ?>%
                </div>
            </div>
        </div>

        <!-- Air -->
        <div class="progress-item">
            <div class="progress-label">
                <span>💧 Air</span>
                <span><?php echo $total['air']; ?> / <?php echo $rekom['air']; ?> L</span>
            </div>
            <div class="progress-bar">
                <?php 
                $air_class = $progress['air'] > 120 ? 'danger' : ($progress['air'] < 80 ? 'warning' : '');
                $air_width = min(100, $progress['air']);
                ?>
                <div class="progress-fill <?php echo $air_class; ?>" style="width: <?php echo $air_width; ?>%">
                    <?php echo $progress['air']; ?>%
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Weekly Progress -->
<div class="card">
    <h3>📈 Progress Mingguan (7 Hari)</h3>
    <p style="margin-bottom: 15px; color: var(--text-secondary);">
        Total asupan 7 hari terakhir dibanding target mingguan
    </p>
    
    <div class="progress-item">
        <div class="progress-label">
            <span><strong>Kalori:</strong></span>
            <span><?php echo $progress_kalori; ?>% tercapai</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?php echo min(100, $progress_kalori); ?>%"></div>
        </div>
    </div>
    
    <div class="progress-item">
        <div class="progress-label">
            <span><strong>Protein:</strong></span>
            <span><?php echo $progress_protein; ?>% tercapai</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?php echo min(100, $progress_protein); ?>%"></div>
        </div>
    </div>
    
    <div class="progress-item">
        <div class="progress-label">
            <span><strong>Karbohidrat:</strong></span>
            <span><?php echo $progress_karbo; ?>% tercapai</span>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?php echo min(100, $progress_karbo); ?>%"></div>
        </div>
    </div>
</div>

<!-- Analysis -->
<?php if (!empty($analisis)): ?>
<div class="analysis-card">
    <h3 style="margin-bottom: 15px; color: #856404;">🧠 Analisis Kebiasaan Makan</h3>
    <?php foreach ($analisis as $pesan): ?>
        <div class="analysis-item"><?php echo $pesan; ?></div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Prediction -->
<?php if ($prediksi): ?>
<div class="prediction-card">
    <h4>🎯 Prediksi Pencapaian Target</h4>
    <p><?php echo $prediksi; ?></p>
    <p style="margin-top: 10px; font-size: 13px; opacity: 0.9;">
        Target berat: <?php echo $user['berat_target']; ?> kg | 
        Selisih: <?php echo abs($user['berat'] - $user['berat_target']); ?> kg
    </p>
</div>
<?php endif; ?>

<!-- Quick Actions -->
<div class="card">
    <h3>⚡ Aksi Cepat</h3>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 15px;">
        <button onclick="window.location.href='dashboard.php?tab=catatan'" class="btn">
            📝 Tambah Asupan
        </button>
        <button onclick="window.location.href='rekomendasi.php'" class="btn" style="background: var(--warning);">
            📘 Lihat Rekomendasi
        </button>
    </div>
</div>
