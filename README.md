# SIBUK — Sistem Informasi Buka Usaha Kecil

Aplikasi simulasi objek audit keamanan berbasis web yang meniru layanan pemerintah daerah untuk pengajuan izin usaha UMKM. Dibuat sebagai media pelatihan audit keamanan aplikasi web.

**URL:** https://sibuk.ircham.dev

---

## Tentang Aplikasi

SIBUK adalah aplikasi fiktif yang mensimulasikan sistem perizinan usaha kecil menengah milik pemerintah daerah. Aplikasi ini **sengaja dirancang dengan celah keamanan** untuk keperluan latihan dan edukasi audit keamanan.

### Fitur Utama

- Registrasi dan login akun masyarakat
- Pengajuan izin usaha UMKM (upload dokumen, data usaha, omzet)
- Pelacakan status perizinan (Pengajuan → Diizinkan / Ditolak)
- Panel admin untuk menyetujui atau menolak perizinan
- Manajemen akun pengguna oleh superadmin
- Manajemen sesi (timeout 30 menit, blokir setelah 5 kali login gagal)

### Stack Teknologi

| Komponen | Teknologi |
|----------|-----------|
| Backend | Laravel 11 |
| Frontend | Vue.js 3 + Inertia.js |
| CSS | Tailwind CSS 3 |
| Database | PostgreSQL 16 |
| Web Server | Nginx |
| Runtime | Docker (php-fpm 8.4) |

---

## ⚠️ Ketentuan Penggunaan

Aplikasi ini berjalan secara publik dan dapat diakses oleh siapa saja untuk keperluan simulasi audit keamanan. Agar aplikasi tetap berfungsi sebagaimana mestinya dan dapat digunakan oleh semua peserta simulasi, harap patuhi ketentuan berikut:

### Yang Diperbolehkan

- Melakukan pengujian dan identifikasi celah keamanan pada aplikasi ini
- Mengeksploitasi kelemahan yang ada dalam batas wajar untuk keperluan pembelajaran
- Mendokumentasikan temuan sebagai bagian dari laporan audit keamanan

### Yang Dilarang

- **Dilarang** melakukan serangan yang menyebabkan aplikasi tidak dapat diakses (*denial of service*, crash, atau kerusakan data permanen)
- **Dilarang** menghapus, merusak, atau memanipulasi data pengguna lain secara destruktif
- **Dilarang** mengubah konfigurasi server atau melakukan eskalasi hak akses di luar lingkup aplikasi
- **Dilarang** menggunakan aplikasi ini sebagai batu loncatan untuk menyerang sistem atau infrastruktur lain
- **Dilarang** melakukan tindakan yang mengganggu ketersediaan aplikasi bagi peserta simulasi lainnya

> Seluruh aktivitas pengujian harus dilakukan dengan tetap menjaga aplikasi ini agar dapat terus berjalan dan digunakan sebagaimana tujuannya. **Penyalahgunaan dapat melanggar hukum yang berlaku, termasuk UU ITE.**

---

## Where to Look — Panduan Audit Kode Sumber

Repositori ini bersifat publik untuk mendukung audit kode sumber. Berikut pemetaan area pengujian ke file dan baris kode yang relevan.

---

### a. Autentikasi

| Aspek | File | Baris |
|-------|------|-------|
| Proses login (validasi kredensial) | `app/Http/Controllers/Auth/LoginController.php` | 27–35 |
| Pengecekan akun terkunci | `app/Http/Controllers/Auth/LoginController.php` | 44–51 |
| Mekanisme `Auth::attempt` | `app/Http/Controllers/Auth/LoginController.php` | 53 |
| Blokir 5x gagal login (1 menit) | `app/Http/Controllers/Auth/LoginController.php` | 55–64 |
| Proses registrasi akun baru | `app/Http/Controllers/Auth/RegisterController.php` | 29–57 |
| Reset password via email token | `app/Http/Controllers/Auth/ForgotPasswordController.php` | 33–45, 68–86 |
| Definisi field autentikasi di model | `app/Models/User.php` | 13–34 |
| Pengecekan status kunci akun | `app/Models/User.php` | 37–40 |

---

### b. Manajemen Sesi

| Aspek | File | Baris |
|-------|------|-------|
| Konstanta timeout sesi (1800 detik) | `app/Http/Middleware/CheckSessionTimeout.php` | 17 |
| Logika cek idle dan paksa logout | `app/Http/Middleware/CheckSessionTimeout.php` | 26–42 |
| Pembaruan waktu aktivitas terakhir | `app/Http/Middleware/CheckSessionTimeout.php` | 43 |
| Regenerasi sesi saat login | `app/Http/Controllers/Auth/LoginController.php` | 75–76 |
| Invalidasi sesi saat logout | `app/Http/Controllers/Auth/LoginController.php` | 87–91 |
| Konfigurasi lifetime sesi (30 menit) | `.env` | 30 |
| Driver sesi database | `.env` | 29 |
| Enkripsi sesi (nonaktif) | `.env` | 31 |
| Registrasi middleware sesi ke pipeline | `bootstrap/app.php` | 26 |

---

### c. Persyaratan Kontrol Akses

| Aspek | File | Baris |
|-------|------|-------|
| Implementasi RBAC middleware | `app/Http/Middleware/CheckRole.php` | 13–27 |
| Pengecekan role di model | `app/Models/User.php` | 43–47 |
| Definisi role di enum migration | `database/migrations/0001_01_01_000000_create_users_table.php` | 20 |
| Pembagian grup route berdasarkan role | `routes/web.php` | 41, 58, 66, 76 |
| Route khusus pengguna biasa | `routes/web.php` | 58–61 |
| Route khusus admin-pemda dan superadmin | `routes/web.php` | 66–73 |
| Route khusus superadmin | `routes/web.php` | 76–82 |
| Registrasi alias middleware role | `bootstrap/app.php` | 22–27 |

---

### d. Validasi Input

| Aspek | File | Baris |
|-------|------|-------|
| Validasi form login | `app/Http/Controllers/Auth/LoginController.php` | 27–35 |
| Validasi form registrasi | `app/Http/Controllers/Auth/RegisterController.php` | 30–48 |
| Validasi aturan password (tanpa simbol — intentional) | `app/Http/Controllers/Auth/RegisterController.php` | 33–41 |
| Validasi form pengajuan perizinan | `app/Http/Controllers/PerizinanController.php` | 42–57 |
| Validasi file upload (hanya ukuran, tanpa tipe — intentional) | `app/Http/Controllers/PerizinanController.php` | 47–49 |
| Validasi update status perizinan oleh admin | `app/Http/Controllers/Admin/PerizinanController.php` | 51–58 |
| Validasi update profil akun | `app/Http/Controllers/AkunController.php` | 22–33 |
| Validasi ganti password | `app/Http/Controllers/AkunController.php` | 55–68 |
| Validasi CRUD akun oleh superadmin | `app/Http/Controllers/Admin/AkunController.php` | 27–44, 65–78 |

---

### e. Kriptografi pada Verifikasi Statis

| Aspek | File | Baris |
|-------|------|-------|
| Hashing password saat registrasi (bcrypt) | `app/Http/Controllers/Auth/RegisterController.php` | 56 |
| Hashing password saat ganti password | `app/Http/Controllers/AkunController.php` | 77 |
| Hashing password saat reset | `app/Http/Controllers/Auth/ForgotPasswordController.php` | 81 |
| Verifikasi password lama (Hash::check) | `app/Http/Controllers/AkunController.php` | 72 |
| Cast password sebagai hashed di model | `app/Models/User.php` | 32 |
| Hashing password akun seed (bcrypt, rounds=12) | `database/seeders/DatabaseSeeder.php` | 24, 34, 44 |
| Konfigurasi bcrypt rounds | `.env` | 16 |

---

### f. Penanganan Error dan Pencatatan Log

| Aspek | File | Baris |
|-------|------|-------|
| Raw SQL query tanpa exception handling (intentional leak) | `app/Http/Controllers/PerizinanController.php` | 84–97 |
| String concatenation pada query SQL (intentional) | `app/Http/Controllers/PerizinanController.php` | 91–95 |
| Konfigurasi error handler aplikasi | `bootstrap/app.php` | 29–31 |
| Konfigurasi log channel dan level | `.env` | 17–19 |

---

### g. Proteksi Data

| Aspek | File | Baris |
|-------|------|-------|
| Field tersembunyi dari serialisasi (password, token) | `app/Models/User.php` | 23–25 |
| Data user yang di-share ke frontend (minimal) | `app/Http/Middleware/HandleInertiaRequests.php` | 20–28 |
| Field yang dapat diisi massal (fillable) | `app/Models/User.php` | 13–20 |
| Field yang dapat diisi massal (perizinan) | `app/Models/Perizinan.php` | 5–14 |
| Primary key sequential / non-UUID (intentional IDOR) | `database/migrations/2026_05_04_015547_create_perizinans_table.php` | 16 |

---

### h. Keamanan Komunikasi

| Aspek | File | Baris |
|-------|------|-------|
| Batas ukuran request (5MB) | `docker/nginx/default.conf` | 8 |
| Konfigurasi URL aplikasi | `.env` | 5 |
| Konfigurasi mailer (log — tanpa TLS aktif) | `.env` | 41–46 |
| Tidak ada konfigurasi HTTPS/TLS di nginx lokal | `docker/nginx/default.conf` | 1–24 |

---

### i. Pengendalian Kode Berbahaya

| Aspek | File | Baris |
|-------|------|-------|
| File upload tanpa validasi tipe/ekstensi (intentional) | `app/Http/Controllers/PerizinanController.php` | 47–49, 62–64 |
| Penyimpanan file langsung ke public storage | `app/Http/Controllers/PerizinanController.php` | 63–64 |
| Konfigurasi disk penyimpanan | `.env` | 36 |

---

### j. Logika Bisnis

| Aspek | File | Baris |
|-------|------|-------|
| Alur pengajuan perizinan (status default: Pengajuan) | `app/Http/Controllers/PerizinanController.php` | 67–78 |
| Validasi transisi status oleh admin | `app/Http/Controllers/Admin/PerizinanController.php` | 51–65 |
| Pembatasan pengajuan hanya untuk role pengguna | `routes/web.php` | 58–61 |
| Definisi enum status perizinan | `database/migrations/2026_05_04_015547_create_perizinans_table.php` | 30 |
| Relasi user → perizinan | `app/Models/User.php` | 50–53 |
| Relasi perizinan → user | `app/Models/Perizinan.php` | 17–20 |

---

### k. File

| Aspek | File | Baris |
|-------|------|-------|
| Proses penyimpanan file upload | `app/Http/Controllers/PerizinanController.php` | 59–64 |
| Tidak ada validasi tipe MIME / ekstensi (intentional) | `app/Http/Controllers/PerizinanController.php` | 47–49 |
| Batasan ukuran file di validasi Laravel (5MB) | `app/Http/Controllers/PerizinanController.php` | 49 |
| Batasan ukuran request di nginx (5MB) | `docker/nginx/default.conf` | 8 |
| Kolom path file di tabel perizinan | `database/migrations/2026_05_04_015547_create_perizinans_table.php` | 24 |

---

### l. Keamanan API dan Web Service

| Aspek | File | Baris |
|-------|------|-------|
| Seluruh definisi route dan proteksi middleware | `routes/web.php` | 1–84 |
| Pembagian route publik vs terautentikasi | `routes/web.php` | 22–40 (guest), 41–84 (auth) |
| Data yang di-expose ke frontend via Inertia | `app/Http/Middleware/HandleInertiaRequests.php` | 18–35 |
| Proteksi CSRF (via session middleware Laravel) | `bootstrap/app.php` | 15–28 |

---

### m. Keamanan Konfigurasi

| Aspek | File | Baris |
|-------|------|-------|
| Mode debug aplikasi (APP_DEBUG=true) | `.env` | 4 |
| Konfigurasi database (kredensial plaintext) | `.env` | 21–26 |
| Enkripsi sesi dinonaktifkan | `.env` | 31 |
| Registrasi middleware global aplikasi | `bootstrap/app.php` | 15–28 |
| Konfigurasi Docker container (environment vars) | `docker-compose.yml` | 14–16 |
| Konfigurasi PHP-FPM dan web server | `Dockerfile` | 1–45 |

---

## Menjalankan Aplikasi Secara Lokal

### Prasyarat

- Docker & Docker Compose
- Node.js (untuk build aset frontend)

### Langkah Instalasi

```bash
# 1. Clone repositori
git clone <repo-url>
cd sibuk

# 2. Install dependensi frontend dan build aset
npm install
npm run build

# 3. Jalankan Docker
docker compose up -d --build

# 4. Akses aplikasi
open http://localhost:8080
```

> Migrasi dan seeding database berjalan otomatis saat container pertama kali dijalankan.

---

## Kontak

Untuk pertanyaan terkait aplikasi ini, hubungi: [contact@ircham.dev](mailto:contact@ircham.dev)

---

## Lisensi

Dibuat untuk keperluan edukasi dan pelatihan audit keamanan.
