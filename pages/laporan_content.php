<!-- Laporan Tab Content -->

<!-- Filter Section -->
<div class="filter-section">
    <h3>📅 Pilih Periode</h3>
    <form method="GET" class="filter-row">
        <input type="hidden" name="tab" value="laporan">
        <div class="filter-group">
            <label>Dari Tanggal</label>
            <input type="date" name="tgl_awal" value="<?php echo $tgl_awal; ?>" required>
        </div>
        <div class="filter-group">
            <label>Sampai Tanggal</label>
            <input type="date" name="tgl_akhir" value="<?php echo $tgl_akhir; ?>" required>
        </div>
        <button type="submit" class="btn-filter">🔍 Tampilkan</button>
    </form>
</div>

<!-- Summary Stats -->
<div class="summary-grid">
    <div class="summary-item success">
        <div class="summary-label">Total Hari dengan Data</div>
        <div class="summary-value"><?php echo $total_hari; ?></div>
    </div>
    <div class="summary-item <?php echo $pct_kalori >= 80 && $pct_kalori <= 120 ? 'success' : 'warning'; ?>">
        <div class="summary-label">Rata-rata Kalori/Hari</div>
        <div class="summary-value"><?php echo $avg_kalori; ?></div>
        <div class="summary-label"><?php echo $pct_kalori; ?>% dari target</div>
    </div>
    <div class="summary-item <?php echo $pct_protein >= 80 && $pct_protein <= 120 ? 'success' : 'warning'; ?>">
        <div class="summary-label">Rata-rata Protein/Hari</div>
        <div class="summary-value"><?php echo $avg_protein; ?>g</div>
        <div class="summary-label"><?php echo $pct_protein; ?>% dari target</div>
    </div>
    <div class="summary-item <?php echo $pct_karbo >= 80 && $pct_karbo <= 120 ? 'success' : 'warning'; ?>">
        <div class="summary-label">Rata-rata Karbo/Hari</div>
        <div class="summary-value"><?php echo $avg_karbo; ?>g</div>
        <div class="summary-label"><?php echo $pct_karbo; ?>% dari target</div>
    </div>
</div>

<!-- Charts -->
<?php if (!empty($data_grafik)): ?>
    <!-- Kalori Chart -->
    <div class="chart-container">
        <h3>📈 Grafik Kalori Harian</h3>
        <div class="chart-wrapper">
            <canvas id="chartKalori"></canvas>
        </div>
        <div class="chart-legend">
            <div class="legend-item">
                <div class="legend-color" style="background: rgb(52, 152, 219);"></div>
                <span>Aktual</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: rgb(231, 76, 60); border: 2px dashed;"></div>
                <span>Target</span>
            </div>
        </div>
    </div>

    <!-- Protein Chart -->
    <div class="chart-container">
        <h3>🥩 Grafik Protein Harian</h3>
        <div class="chart-wrapper">
            <canvas id="chartProtein"></canvas>
        </div>
    </div>

    <!-- Karbohidrat Chart -->
    <div class="chart-container">
        <h3>🍚 Grafik Karbohidrat Harian</h3>
        <div class="chart-wrapper">
            <canvas id="chartKarbo"></canvas>
        </div>
    </div>

    <script>
    // Chart.js configuration
    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    };

    // Kalori Chart
    new Chart(document.getElementById('chartKalori'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Kalori Aktual',
                data: <?php echo json_encode($data_kalori); ?>,
                borderColor: 'rgb(52, 152, 219)',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                tension: 0.3
            }, {
                label: 'Target',
                data: <?php echo json_encode($data_target_kalori); ?>,
                borderColor: 'rgb(231, 76, 60)',
                borderDash: [5, 5],
                fill: false
            }]
        },
        options: chartOptions
    });

    // Protein Chart
    new Chart(document.getElementById('chartProtein'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Protein Aktual',
                data: <?php echo json_encode($data_protein); ?>,
                borderColor: 'rgb(46, 204, 113)',
                backgroundColor: 'rgba(46, 204, 113, 0.1)',
                tension: 0.3
            }, {
                label: 'Target',
                data: <?php echo json_encode($data_target_protein); ?>,
                borderColor: 'rgb(231, 76, 60)',
                borderDash: [5, 5],
                fill: false
            }]
        },
        options: chartOptions
    });

    // Karbohidrat Chart
    new Chart(document.getElementById('chartKarbo'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Karbohidrat Aktual',
                data: <?php echo json_encode($data_karbo); ?>,
                borderColor: 'rgb(243, 156, 18)',
                backgroundColor: 'rgba(243, 156, 18, 0.1)',
                tension: 0.3
            }, {
                label: 'Target',
                data: <?php echo json_encode($data_target_karbo); ?>,
                borderColor: 'rgb(231, 76, 60)',
                borderDash: [5, 5],
                fill: false
            }]
        },
        options: chartOptions
    });
    </script>
<?php else: ?>
    <div class="empty-state">
        <div class="empty-state-icon">📊</div>
        <p>Tidak ada data pada periode ini</p>
        <p style="font-size: 14px; color: #999;">Silakan pilih periode lain atau mulai catat asupan makanan</p>
    </div>
<?php endif; ?>

<!-- Export Button -->
<div class="export-section">
    <a href="ekspor_csv.php?tgl_awal=<?php echo $tgl_awal; ?>&tgl_akhir=<?php echo $tgl_akhir; ?>" 
       class="btn-export">
        <span>📥</span> Ekspor ke CSV
    </a>
</div>
