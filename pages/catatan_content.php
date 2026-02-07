<!-- Catatan Tab Content -->

<!-- Weekly Progress -->
<div class="weekly-stats">
    <h3>📊 Progress Mingguan</h3>
    <div class="weekly-stat-item">
        <strong>Kalori: <?php echo $progress_kalori; ?>% tercapai</strong>
        <div class="stat-bar">
            <div class="stat-fill" style="width: <?php echo min(100, $progress_kalori); ?>%"></div>
        </div>
    </div>
    <div class="weekly-stat-item">
        <strong>Protein: <?php echo $progress_protein; ?>% tercapai</strong>
        <div class="stat-bar">
            <div class="stat-fill" style="width: <?php echo min(100, $progress_protein); ?>%"></div>
        </div>
    </div>
    <div class="weekly-stat-item">
        <strong>Karbohidrat: <?php echo $progress_karbo; ?>% tercapai</strong>
        <div class="stat-bar">
            <div class="stat-fill" style="width: <?php echo min(100, $progress_karbo); ?>%"></div>
        </div>
    </div>
</div>

<!-- Analysis -->
<?php if (!empty($analisis)): ?>
<div class="card">
    <h3>🧠 Analisis Kebiasaan Makan</h3>
    <?php foreach ($analisis as $pesan): ?>
        <div class="analysis-item" style="padding:8px 0;"><?php echo $pesan; ?></div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Target & Stats Today -->
<div class="card">
    <h3>📊 Target & Statistik Hari Ini</h3>
    <p><strong>Kalori:</strong> <?php echo round($total['kalori']); ?> / <?php echo $rekom['kalori']; ?> kkal</p>
    <div style="height:20px; background:#ecf0f1; border-radius:10px; margin:10px 0;">
        <?php $kalori_pct = round(($total['kalori'] / max(1, $rekom['kalori'])) * 100); ?>
        <div style="height:100%; width:<?php echo min(100, $kalori_pct); ?>%; background:var(--success); border-radius:10px;"></div>
    </div>
    <p>Protein: <?php echo round($total['protein']); ?> / <?php echo $rekom['protein']; ?> g</p>
    <p>Karbo: <?php echo round($total['karbohidrat']); ?> / <?php echo $rekom['karbohidrat']; ?> g</p>
    <p>Air: <?php echo $total['air']; ?> / <?php echo $rekom['air']; ?> L</p>
</div>

<!-- Search Database -->
<div class="card">
    <h3>🔍 Cari dari Database Makanan</h3>
    <p style="font-size:12px; color:var(--text-secondary); margin-bottom:15px;">
        Database berisi 1500+ makanan dengan info gizi lengkap
    </p>
    
    <div class="search-container">
        <input type="text" 
               id="searchMakanan" 
               placeholder="🔎 Ketik nama makanan... (contoh: ayam, nasi, telur)" 
               autocomplete="off">
        
        <div id="searchResults"></div>
    </div>
    
    <div id="selectedMakanan">
        <div class="selected-header">
            <img id="makananImage" src="" alt="" style="display:none;">
            <div class="selected-title">
                <h4>✅ Makanan Terpilih</h4>
                <p id="makananName"></p>
            </div>
        </div>
        
        <div id="makananInfo"></div>
        
        <div class="portion-controls">
            <div>
                <label>Waktu Makan</label>
                <select id="waktuMakan">
                    <option value="Pagi">🌅 Pagi</option>
                    <option value="Siang" selected>☀️ Siang</option>
                    <option value="Malam">🌙 Malam</option>
                    <option value="Cemilan">🍪 Cemilan</option>
                </select>
            </div>
            
            <div>
                <label>Jumlah Porsi</label>
                <input type="number" id="jumlahPorsi" value="1" min="0.1" step="0.1" onchange="updateNutritionInfo()">
            </div>
        </div>
        
        <div id="totalNutrisi">
            <strong>📊 Total Nutrisi:</strong>
            <div id="totalInfo"></div>
        </div>
        
        <div class="action-buttons">
            <button onclick="tambahDariDataset()" class="btn-add">➕ Tambahkan</button>
            <button onclick="batalPilih()" class="btn-cancel">✕ Batal</button>
        </div>
    </div>
</div>

<!-- Manual Input -->
<div class="card">
    <h3>➕ Tambah Asupan Hari Ini (Manual)</h3>
    <?php if (!empty($success_asupan)): ?>
        <p class="success"><?php echo htmlspecialchars($success_asupan); ?></p>
    <?php endif; ?>
    <?php if (!empty($error_asupan)): ?>
        <p class="error"><?php echo htmlspecialchars($error_asupan); ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="tambah_asupan" value="1">
        
        <div class="input-row">
            <label>Waktu Makan</label>
            <select name="waktu_makan" required>
                <option value="Pagi">Pagi</option>
                <option value="Siang" selected>Siang</option>
                <option value="Malam">Malam</option>
                <option value="Cemilan">Cemilan</option>
            </select>
        </div>
        
        <div class="input-row">
            <label>Nama Makanan</label>
            <input type="text" name="nama_makanan" placeholder="Contoh: Nasi goreng" required>
        </div>
        
        <div class="input-row">
            <label>Karbohidrat <span class="unit">(g)</span></label>
            <input type="number" step="0.1" name="karbohidrat" placeholder="0">
        </div>
        
        <div class="input-row">
            <label>Protein <span class="unit">(g)</span></label>
            <input type="number" step="0.1" name="protein" placeholder="0">
        </div>
        
        <div class="input-row">
            <label>Lemak <span class="unit">(g)</span></label>
            <input type="number" step="0.1" name="lemak" placeholder="0">
        </div>
        
        <div class="input-row">
            <label>Gula <span class="unit">(g)</span></label>
            <input type="number" step="0.1" name="gula" placeholder="0">
        </div>
        
        <div class="input-row">
            <label>Air <span class="unit">(L)</span></label>
            <input type="number" step="0.1" name="air" placeholder="0">
        </div>
        
        <button type="submit" class="btn" style="background:var(--success); color:white; width:100%; margin-top:10px;">
            ➕ Tambahkan
        </button>
    </form>
</div>

<!-- Food List -->
<div class="card">
    <h3>📋 Asupan Hari Ini (<?php echo count($asupan_hari_ini); ?>)</h3>
    <?php if (empty($asupan_hari_ini)): ?>
        <p style="color:var(--text-secondary); text-align:center;">Belum ada asupan hari ini.</p>
    <?php else: ?>
        <?php foreach ($asupan_hari_ini as $item): ?>
            <div class="food-item">
                <strong><?php echo htmlspecialchars($item['nama_makanan']); ?></strong><br>
                <small>
                    Karbo: <?php echo $item['karbohidrat']; ?>g | 
                    Protein: <?php echo $item['protein']; ?>g | 
                    <?php echo $item['kalori']; ?> kkal
                </small>
                <a href="?hapus_id=<?php echo $item['id']; ?>&tab=catatan" 
                   class="delete-btn" 
                   onclick="return confirm('Hapus asupan ini?')">✕</a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
let selectedFood = null;

// Search functionality
document.getElementById('searchMakanan').addEventListener('input', function() {
    const keyword = this.value.trim();
    
    if (keyword.length < 2) {
        document.getElementById('searchResults').style.display = 'none';
        return;
    }
    
    fetch('cari_makanan.php?keyword=' + encodeURIComponent(keyword))
        .then(response => response.json())
        .then(data => {
            const resultsDiv = document.getElementById('searchResults');
            
            if (data.length === 0) {
                resultsDiv.innerHTML = '<div style="padding:15px; text-align:center; color:#999;">Tidak ditemukan</div>';
                resultsDiv.style.display = 'block';
                return;
            }
            
            let html = '';
            data.forEach(item => {
                html += `
                    <div class="search-result-item" onclick='pilihMakanan(${JSON.stringify(item)})'>
                        ${item.image ? `<img src="${item.image}" class="result-image" onerror="this.style.display='none'">` : ''}
                        <div class="result-info">
                            <div class="result-name">${item.name}</div>
                            <div class="result-nutrition">
                                ${item.calories} kkal | 
                                Protein: ${item.proteins}g | 
                                Karbo: ${item.carbohydrate}g
                            </div>
                        </div>
                    </div>
                `;
            });
            
            resultsDiv.innerHTML = html;
            resultsDiv.style.display = 'block';
        });
});

function pilihMakanan(food) {
    selectedFood = food;
    
    document.getElementById('searchResults').style.display = 'none';
    document.getElementById('selectedMakanan').style.display = 'block';
    document.getElementById('makananName').textContent = food.name;
    
    if (food.image) {
        document.getElementById('makananImage').src = food.image;
        document.getElementById('makananImage').style.display = 'block';
    }
    
    updateNutritionInfo();
}

function updateNutritionInfo() {
    if (!selectedFood) return;
    
    const porsi = parseFloat(document.getElementById('jumlahPorsi').value) || 1;
    
    const kalori = (selectedFood.calories * porsi).toFixed(1);
    const protein = (selectedFood.proteins * porsi).toFixed(1);
    const lemak = (selectedFood.fat * porsi).toFixed(1);
    const karbo = (selectedFood.carbohydrate * porsi).toFixed(1);
    
    document.getElementById('makananInfo').innerHTML = `
        <strong>Per Porsi:</strong><br>
        Kalori: ${selectedFood.calories} kkal | 
        Protein: ${selectedFood.proteins}g | 
        Lemak: ${selectedFood.fat}g | 
        Karbo: ${selectedFood.carbohydrate}g
    `;
    
    document.getElementById('totalInfo').innerHTML = `
        Kalori: ${kalori} kkal | 
        Protein: ${protein}g | 
        Lemak: ${lemak}g | 
        Karbo: ${karbo}g
    `;
}

function tambahDariDataset() {
    if (!selectedFood) return;
    
    const porsi = parseFloat(document.getElementById('jumlahPorsi').value) || 1;
    const waktu = document.getElementById('waktuMakan').value;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="tambah_dari_dataset" value="1">
        <input type="hidden" name="nama_makanan" value="${selectedFood.name}">
        <input type="hidden" name="waktu_makan" value="${waktu}">
        <input type="hidden" name="karbohidrat" value="${selectedFood.carbohydrate * porsi}">
        <input type="hidden" name="protein" value="${selectedFood.proteins * porsi}">
        <input type="hidden" name="lemak" value="${selectedFood.fat * porsi}">
        <input type="hidden" name="gula" value="0">
        <input type="hidden" name="air" value="0">
    `;
    
    document.body.appendChild(form);
    form.submit();
}

function batalPilih() {
    selectedFood = null;
    document.getElementById('selectedMakanan').style.display = 'none';
    document.getElementById('searchMakanan').value = '';
    document.getElementById('jumlahPorsi').value = '1';
}
</script>
