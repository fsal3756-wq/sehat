# 🏥 Hidup Sehat - Aplikasi Tracking Nutrisi & Kesehatan

Aplikasi web untuk melacak asupan nutrisi harian, menghitung kebutuhan kalori, dan membantu mencapai target kesehatan Anda.

## 📋 Deskripsi

Hidup Sehat adalah aplikasi berbasis web yang membantu pengguna untuk:
- Melacak asupan makanan dan nutrisi harian
- Menghitung kebutuhan kalori berdasarkan profil pribadi
- Mendapatkan rekomendasi nutrisi yang disesuaikan dengan tujuan (diet, maintenance, atau pembentukan otot)
- Melihat laporan dan progress kesehatan
- Mengelola profil dan preferensi kesehatan

## ✨ Fitur Utama

### 🔐 Autentikasi & Keamanan
- Registrasi dengan data profil lengkap
- Login dengan username/email
- Security questions untuk pemulihan password
- Reset password via email
- Session management yang aman

### 📊 Dashboard Interaktif
- Ringkasan asupan nutrisi hari ini
- Progress bar untuk kalori, protein, karbohidrat, dan lemak
- Rekomendasi nutrisi personal berdasarkan:
  - Metode Mifflin-St Jeor (TDEE)
  - Metode Harris-Benedict (BMR)
- Grafik visualisasi data nutrisi

### 🍽️ Tracking Asupan Makanan
- Input asupan makanan dengan detail waktu
- Pencarian makanan dari database nutrisi (5000+ item)
- Import data nutrisi dari file CSV
- Riwayat asupan harian
- Edit dan hapus catatan asupan

### 📈 Laporan & Analisis
- Laporan nutrisi harian, mingguan, dan bulanan
- Grafik trend asupan kalori
- Perbandingan asupan vs target
- Export data ke CSV
- Visualisasi progress dengan chart

### 👤 Manajemen Profil
- Update informasi pribadi (berat, tinggi, usia)
- Ubah target dan tingkat aktivitas
- Update berat target untuk diet/bulking
- Dark mode / Light mode toggle
- Riwayat perubahan berat badan

## 🛠️ Teknologi yang Digunakan

### Backend
- **PHP 7.4+** - Server-side scripting
- **MySQL 8.0+** - Database management
- **PDO** - Database abstraction layer

### Frontend
- **HTML5** - Markup
- **CSS3** - Styling dengan responsive design
- **JavaScript** - Interaktivitas dan validasi
- **Chart.js** - Visualisasi data

### Keamanan
- Password hashing dengan `password_hash()`
- Prepared statements untuk mencegah SQL injection
- Session management
- Input validation dan sanitization

## 📦 Instalasi

### Prasyarat
- PHP 7.4 atau lebih tinggi
- MySQL 8.0 atau lebih tinggi
- Web server (Apache/Nginx)
- PHPMyAdmin (opsional, untuk manajemen database)

### Langkah Instalasi

1. **Clone atau Download Project**
   ```bash
   git clone <repository-url>
   cd sehat_modular
   ```

2. **Setup Database**
   - Buat database baru bernama `hidup_sehat`
   - Import file SQL:
   ```bash
   mysql -u root -p hidup_sehat < hidup_sehat.sql
   ```
   Atau melalui PHPMyAdmin:
   - Buka PHPMyAdmin
   - Buat database `hidup_sehat`
   - Import file `hidup_sehat.sql`

3. **Konfigurasi Database**
   - Edit file `config.php`:
   ```php
   <?php
   $host = 'localhost';
   $db   = 'hidup_sehat';
   $user = 'root';        // Sesuaikan dengan user MySQL Anda
   $pass = '';            // Sesuaikan dengan password MySQL Anda
   ```

4. **Import Data Nutrisi (Opsional)**
   - Akses `import_nutrition.php` melalui browser
   - File `nutrition.csv` akan diimport ke database
   - Berisi 5000+ data makanan dengan informasi nutrisi lengkap

5. **Setup Permissions**
   ```bash
   chmod 755 sehat_modular/
   chmod 644 sehat_modular/*.php
   ```

6. **Akses Aplikasi**
   - Buka browser dan akses: `http://localhost/sehat_modular/`
   - Atau sesuai dengan konfigurasi web server Anda

## 📁 Struktur Folder

```
sehat_modular/
│
├── assets/
│   └── css/                    # Stylesheet files
│       ├── main.css           # Global styles
│       ├── dashboard.css      # Dashboard styles
│       ├── catatan.css        # Notes page styles
│       ├── laporan.css        # Reports page styles
│       └── profil.css         # Profile page styles
│
├── includes/
│   ├── auth_check.php         # Authentication middleware
│   └── functions.php          # Utility functions (TDEE, BMR, etc.)
│
├── pages/
│   ├── dashboard_tab.php      # Dashboard logic
│   ├── dashboard_content.php  # Dashboard view
│   ├── catatan_tab.php        # Notes logic
│   ├── catatan_content.php    # Notes view
│   ├── laporan_tab.php        # Reports logic
│   ├── laporan_content.php    # Reports view
│   ├── profil_tab.php         # Profile logic
│   └── profil_content.php     # Profile view
│
├── config.php                 # Database configuration
├── index.php                  # Landing page
├── login.php                  # Login page
├── register.php               # Registration page
├── dashboard.php              # Main dashboard
├── input_asupan.php           # Food intake input
├── cari_makanan.php           # Food search API
├── rekomendasi.php            # Nutrition recommendations API
├── ekspor_csv.php             # CSV export functionality
├── import_nutrition.php       # Nutrition data import
├── lupa_password.php          # Password recovery
├── reset_password.php         # Password reset
├── logout.php                 # Logout handler
├── nutrition.csv              # Nutrition database (5000+ items)
├── hidup_sehat.sql            # Database schema and data
└── style.css                  # Additional styles
```

## 🔧 Konfigurasi

### Database Configuration
Edit `config.php` untuk menyesuaikan koneksi database:

```php
$host = 'localhost';      // Database host
$db   = 'hidup_sehat';   // Database name
$user = 'root';          // Database username
$pass = '';              // Database password
```

### Email Configuration (untuk reset password)
Untuk fitur reset password via email, Anda perlu mengkonfigurasi SMTP di `lupa_password.php`

## 💡 Penggunaan

### 1. Registrasi
- Kunjungi halaman registrasi
- Isi data pribadi lengkap:
  - Username dan email
  - Password (minimal 6 karakter)
  - Nama lengkap
  - Usia, gender, tinggi, berat
  - Tingkat aktivitas (rendah/sedang/tinggi)
  - Tujuan (diet/maintenance/otot)
  - Berat target (jika diet atau bulking)
  - Dua security questions

### 2. Login
- Login dengan username/email dan password
- Sistem akan redirect ke dashboard

### 3. Dashboard
- Lihat ringkasan asupan hari ini
- Cek rekomendasi kalori dan nutrisi
- Akses quick actions untuk input makanan

### 4. Input Asupan Makanan
- Pilih waktu makan (sarapan/makan siang/makan malam/snack)
- Cari makanan dari database
- Atau input manual nama makanan dan nutrisi
- Tentukan porsi
- Simpan

### 5. Catatan Asupan
- Lihat riwayat asupan per hari
- Edit atau hapus catatan
- Filter berdasarkan tanggal

### 6. Laporan
- Lihat grafik asupan harian/mingguan/bulanan
- Export data ke CSV
- Analisis trend nutrisi

### 7. Profil
- Update data pribadi
- Ubah target dan aktivitas
- Toggle dark/light mode
- Lihat riwayat berat badan

## 🔐 Keamanan

### Implementasi Keamanan
- ✅ Password hashing dengan `PASSWORD_DEFAULT`
- ✅ Prepared statements untuk mencegah SQL injection
- ✅ Session management yang aman
- ✅ Input validation dan sanitization
- ✅ XSS protection
- ✅ CSRF protection (dapat ditingkatkan)
- ✅ Security questions untuk password recovery

### Rekomendasi Keamanan Tambahan
- Implementasi HTTPS di production
- Rate limiting untuk login attempts
- CSRF tokens untuk form submissions
- Content Security Policy (CSP)
- Regular security audits

## 📊 Database Schema

### Tabel Utama

#### `users`
Menyimpan informasi pengguna dan profil kesehatan
- id, username, email, password
- nama_lengkap, usia, gender
- tinggi, berat, berat_target
- aktivitas, tujuan
- security_question_1, security_answer_1
- security_question_2, security_answer_2
- created_at

#### `asupan_harian`
Menyimpan catatan asupan makanan
- id, user_id, tanggal, waktu_makan
- nama_makanan, kalori, protein
- karbohidrat, lemak, porsi
- created_at

#### `foods`
Database makanan dan nutrisi (5000+ item)
- id, nama_makanan, kalori
- protein, karbohidrat, lemak
- satuan, kategori

#### `riwayat_berat`
Tracking perubahan berat badan
- id, user_id, tanggal
- berat, catatan
- created_at

#### `password_resets`
Token untuk reset password
- id, user_id, token
- created_at, expires_at

## 🎨 Fitur UI/UX

- ✨ **Responsive Design** - Mobile-friendly
- 🌓 **Dark Mode** - Toggle light/dark theme
- 📱 **Progressive Web App Ready**
- 🎯 **Intuitive Navigation** - Tab-based interface
- 📊 **Data Visualization** - Charts dan graphs
- ⚡ **Fast Loading** - Optimized performance
- 🔔 **User Feedback** - Toast notifications

## 🚀 Fitur yang Akan Datang

- [ ] Push notifications untuk reminder makan
- [ ] Barcode scanner untuk input makanan
- [ ] AI-powered meal recommendations
- [ ] Social features (berbagi progress)
- [ ] Mobile app (React Native/Flutter)
- [ ] Integration dengan fitness trackers
- [ ] Meal planning dan grocery list
- [ ] Recipe database
- [ ] Community challenges

## 🐛 Bug Report & Feature Request

Jika menemukan bug atau ingin request fitur:
1. Buat issue di repository
2. Jelaskan bug/fitur dengan detail
3. Sertakan screenshot jika perlu

## 📄 Lisensi

Project ini menggunakan lisensi [MIT License](LICENSE)

## 📞 Kontak

Untuk pertanyaan atau dukungan:
- Email: support@hidupsehat.com
- Website: www.hidupsehat.com

## 🙏 Acknowledgments

- Data nutrisi dari USDA Food Database
- Icons dari Font Awesome
- Charts menggunakan Chart.js
- Gradient backgrounds dari UI Gradients

---

**Catatan**: Aplikasi ini dibuat untuk tujuan edukasi dan kesehatan. Selalu konsultasikan dengan profesional kesehatan untuk program diet atau fitness yang serius.

**Made with ❤️ for healthy living**
