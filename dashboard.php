<?php
require 'includes/auth_check.php';
require 'config.php';
require 'includes/functions.php';

$user_id = $_SESSION['user_id'];

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user || !is_array($user)) {
    session_destroy();
    header("Location: login.php");
    exit;
}

// Determine active tab
$active_tab = $_GET['tab'] ?? 'dashboard';

// Include tab-specific logic
switch($active_tab) {
    case 'dashboard':
        include 'pages/dashboard_tab.php';
        break;
    case 'catatan':
        include 'pages/catatan_tab.php';
        break;
    case 'laporan':
        include 'pages/laporan_tab.php';
        break;
    case 'profil':
        include 'pages/profil_tab.php';
        break;
    default:
        $active_tab = 'dashboard';
        include 'pages/dashboard_tab.php';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard – Hidup Sehat</title>
    
    <!-- Load theme IMMEDIATELY before any render -->
    <script>
        // CRITICAL: Load theme before ANY CSS to prevent flash
        (function() {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/main.css">
    
    <!-- Tab-specific CSS -->
    <?php if($active_tab == 'dashboard'): ?>
        <link rel="stylesheet" href="assets/css/dashboard.css">
    <?php elseif($active_tab == 'catatan'): ?>
        <link rel="stylesheet" href="assets/css/catatan.css">
    <?php elseif($active_tab == 'laporan'): ?>
        <link rel="stylesheet" href="assets/css/laporan.css">
    <?php elseif($active_tab == 'profil'): ?>
        <link rel="stylesheet" href="assets/css/profil.css">
    <?php endif; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🏥 Hidup Sehat Dashboard</h2>
            <p style="margin:5px 0 0 0; color:var(--text-secondary);">
                Halo, <?= htmlspecialchars($user['nama_lengkap']) ?>!
            </p>
        </div>

        <!-- Tab Content -->
        <?php if($active_tab == 'dashboard'): ?>
            <?php include 'pages/dashboard_content.php'; ?>
        <?php elseif($active_tab == 'catatan'): ?>
            <?php include 'pages/catatan_content.php'; ?>
        <?php elseif($active_tab == 'laporan'): ?>
            <?php include 'pages/laporan_content.php'; ?>
        <?php elseif($active_tab == 'profil'): ?>
            <?php include 'pages/profil_content.php'; ?>
        <?php endif; ?>
    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <button class="nav-btn <?= $active_tab == 'dashboard' ? 'active' : '' ?>" 
                onclick="smoothNavigate('dashboard.php?tab=dashboard')">
            <span class="icon">🏠</span>
            Dashboard
        </button>
        <button class="nav-btn <?= $active_tab == 'catatan' ? 'active' : '' ?>" 
                onclick="smoothNavigate('dashboard.php?tab=catatan')">
            <span class="icon">📝</span>
            Catatan
        </button>
        <button class="nav-btn <?= $active_tab == 'laporan' ? 'active' : '' ?>" 
                onclick="smoothNavigate('dashboard.php?tab=laporan')">
            <span class="icon">📊</span>
            Laporan
        </button>
        <button class="nav-btn <?= $active_tab == 'profil' ? 'active' : '' ?>" 
                onclick="smoothNavigate('dashboard.php?tab=profil')">
            <span class="icon">👤</span>
            Profil
        </button>
    </div>

    <!-- Theme Toggle -->
    <button id="theme-toggle" onclick="toggleTheme()">🌙</button>

    <script>
        // Theme Toggle Function
        function toggleTheme() {
            const html = document.documentElement;
            const body = document.body;
            
            // Toggle both html and body
            html.classList.toggle('dark-mode');
            body.classList.toggle('dark-mode');
            
            // Check current state
            const isDark = body.classList.contains('dark-mode');
            
            // Save to localStorage
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            
            // Update button icon
            const btn = document.getElementById('theme-toggle');
            if (btn) {
                btn.textContent = isDark ? '☀️' : '🌙';
            }
        }

        // Apply saved theme on page load
        function applySavedTheme() {
            const savedTheme = localStorage.getItem('theme');
            const html = document.documentElement;
            const body = document.body;
            const btn = document.getElementById('theme-toggle');
            
            if (savedTheme === 'dark') {
                html.classList.add('dark-mode');
                body.classList.add('dark-mode');
                if (btn) btn.textContent = '☀️';
            } else {
                html.classList.remove('dark-mode');
                body.classList.remove('dark-mode');
                if (btn) btn.textContent = '🌙';
            }
        }

        // Apply theme immediately when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', applySavedTheme);
        } else {
            applySavedTheme();
        }

        // Smooth Navigation
        function smoothNavigate(url) {
            if (window.location.href.includes(url)) {
                return;
            }
            window.location.href = url;
        }
    </script>
    
    <?php if($active_tab == 'catatan'): ?>
        <script src="assets/js/catatan.js"></script>
    <?php endif; ?>
</body>
</html>
