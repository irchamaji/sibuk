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

Repositori ini bersifat publik untuk mendukung audit kode sumber. Berikut pemetaan **75 kontrol keamanan** (Pasal 26 huruf a–m) ke file dan baris kode yang relevan. Kontrol yang tidak diterapkan di lapisan aplikasi disertai keterangan lokasi alternatif yang perlu diperiksa.

> **Legenda:** ⚠️ = celah/keterbatasan yang disengaja (intentional) atau perlu perhatian khusus · ❌ = tidak diimplementasikan di kode aplikasi

---

### a. Autentikasi

| # | Kontrol | File | Baris | Catatan |
|---|---------|------|-------|---------|
| a.1 | Manajemen kata sandi untuk autentikasi | `app/Http/Controllers/Auth/LoginController.php` | 26–53 | `Auth::attempt()` di baris 53 |
| a.2 | Verifikasi kata sandi pada sisi server | `app/Http/Controllers/Auth/LoginController.php` | 37–53 | Lookup user lalu `Auth::attempt()` |
| a.3 | Jumlah karakter, kombinasi, masa berlaku | `app/Http/Controllers/Auth/RegisterController.php` | 33–41 | min 8, mixedCase, angka; ⚠️ tanpa simbol (intentional); ⚠️ tanpa masa berlaku |
| a.4 | Jumlah maksimum kesalahan pemasukan | `app/Http/Controllers/Auth/LoginController.php` | 56–64 | Kunci akun setelah 5× gagal selama 1 menit |
| a.5 | Mekanisme pemulihan kata sandi | `app/Http/Controllers/Auth/ForgotPasswordController.php` | 27–44, 62–93 | Link reset via email; Laravel `Password::sendResetLink()` |
| a.6 | Kerahasiaan kata sandi via kriptografi | `app/Models/User.php` · `app/Http/Controllers/Auth/RegisterController.php` | 32 · 56 | Cast `hashed` (bcrypt); `BCRYPT_ROUNDS=12` di `.env:13` |
| a.7 | Jalur komunikasi diamankan | `bootstrap/app.php` · `docker/nginx/default.conf` | 17–25 · 1–32 | ❌ Tidak ada TLS di nginx; TLS di-terminate Cloudflare Tunnel; proxy di-trust di `bootstrap/app.php` |

---

### b. Manajemen Sesi

| # | Kontrol | File | Baris | Catatan |
|---|---------|------|-------|---------|
| b.1 | Pengendali sesi untuk manajemen sesi | `app/Http/Middleware/CheckSessionTimeout.php` | 14–46 | Middleware idle-timeout kustom |
| b.2 | Pengendali sesi dari kerangka kerja | `.env` · `database/migrations/0001_01_01_000000_create_users_table.php` | 29–30 · 41–48 | `SESSION_DRIVER=database`; tabel `sessions` bawaan Laravel |
| b.3 | Pembuatan dan keacakan token sesi | `app/Http/Controllers/Auth/LoginController.php` | 74 | `session()->regenerate()` — token dikelola Laravel internal (`vendor/laravel/framework`) |
| b.4 | Kondisi dan jangka waktu habis sesi | `app/Http/Middleware/CheckSessionTimeout.php` · `.env` | 17, 32–38 · 30 | `SESSION_TIMEOUT = 1800` detik; `SESSION_LIFETIME=30` |
| b.5 | Validasi dan pencantuman session id | `database/migrations/0001_01_01_000000_create_users_table.php` | 42 | Session id di kolom `id` tabel `sessions`; validasi dikelola Laravel; ⚠️ tidak ada kustom validasi session id |
| b.6 | Pelindungan token sesi terautentikasi | `app/Http/Controllers/Auth/LoginController.php` | 74, 89–90 | Regenerate saat login; invalidate + regenerateToken saat logout |
| b.7 | Pelindungan duplikasi dan persetujuan | `app/Http/Controllers/Auth/LoginController.php` | 74 | `session()->regenerate()` cegah session fixation; ⚠️ tidak ada pembatasan satu sesi aktif per pengguna |

---

### c. Persyaratan Kontrol Akses

| # | Kontrol | File | Baris | Catatan |
|---|---------|------|-------|---------|
| c.1 | Otorisasi pengguna (RBAC) | `app/Http/Middleware/CheckRole.php` · `routes/web.php` · `app/Models/User.php` | 15–27 · 41, 58, 66, 76 · 51–57 | Role enum: `pengguna`, `admin-pemda`, `superadmin`; definisi di migration baris 23 |
| c.2 | Peringatan serangan otomatis | `app/Http/Controllers/Auth/LoginController.php` | 56–64 | Blokir 5× gagal login; ⚠️ tidak ada rate limiter/alert untuk endpoint lain; ❌ tidak ada notifikasi ke admin |
| c.3 | Antarmuka sisi administrator | `routes/web.php` · `resources/js/Pages/Admin/` | 66–83 | Route `/admin/*` diproteksi `role:admin-pemda,superadmin` dan `role:superadmin` |
| c.4 | Verifikasi token saat akses data dikecualikan | `app/Http/Controllers/PerizinanController.php` · `routes/web.php` | 88–94 · 41 | Middleware `auth` di semua route terproteksi; ⚠️ INTENTIONAL: raw SQL concat di baris 88–94 (SQL injection + potensi IDOR) |

---

### d. Validasi Input

| # | Kontrol | File | Baris | Catatan |
|---|---------|------|-------|---------|
| d.1 | Validasi input pada sisi server | `app/Http/Controllers/Auth/LoginController.php` · `app/Http/Controllers/Auth/RegisterController.php` · `app/Http/Controllers/PerizinanController.php` · `app/Http/Controllers/Admin/PerizinanController.php` · `app/Http/Controllers/AkunController.php` · `app/Http/Controllers/Admin/AkunController.php` | 26–34 · 29–48 · 42–57 · 52–58 · 32–40, 57–69 · 33–49, 68–77 | Seluruh input divalidasi Laravel `$request->validate()` di server |
| d.2 | Penolakan input saat validasi gagal | Seluruh controller di atas | — | `$request->validate()` otomatis return 422 + error ke form; `back()->withErrors()` di controller |
| d.3 | Runtime environment tidak rentan | `Dockerfile` | 1, 18–28 | PHP 8.4-fpm-alpine; hanya extension minimal; ⚠️ tidak ada `composer audit` / `npm audit` di CI/CD (tidak ada pipeline) |
| d.4 | Validasi positif seluruh input | `app/Http/Controllers/Auth/RegisterController.php` · `app/Http/Controllers/Admin/PerizinanController.php` · `app/Http/Controllers/Admin/AkunController.php` | 29–41 · 53 · 36 | Whitelist karakter/nilai: `required\|string\|email`, `in:Pengajuan,Diizinkan,Ditolak`, `in:pengguna,admin-pemda,superadmin` |
| d.5 | Filter data tidak dipercaya | `app/Models/User.php` · `app/Models/Perizinan.php` | 14–21 · 9–18 | `$fillable` mencegah mass assignment; Eloquent ORM parameterize semua query (kecuali poin d.8) |
| d.6 | Penggunaan fitur kode dinamis | `resources/js/` | — | ❌ Tidak ada `eval()` atau dynamic code execution di aplikasi; Vue.js reactive binding aman |
| d.7 | Pelindungan konten skrip (XSS) | `resources/js/Pages/` | — | Vue.js escape `{{ }}` secara default; tidak ada `v-html`; ⚠️ tidak ada Content Security Policy (CSP) header |
| d.8 | Pelindungan injeksi basis data | `app/Http/Controllers/PerizinanController.php` | 88–94 | ✅ Seluruh query lain pakai Eloquent ORM (parameterized); ⚠️ INTENTIONAL: raw SQL string concat di `show()` baris 88–94 |

---

### e. Kriptografi pada Verifikasi Statis

| # | Kontrol | File | Baris | Catatan |
|---|---------|------|-------|---------|
| e.1 | Algoritma kriptografi sesuai ketentuan | `app/Models/User.php` · `.env` | 32 · 13 | Cast `hashed` = bcrypt; `BCRYPT_ROUNDS=12`; ⚠️ data perizinan disimpan plaintext (tidak dienkripsi) |
| e.2 | Autentikasi data yang dienkripsi | `app/Http/Controllers/AkunController.php` · `app/Http/Controllers/Auth/LoginController.php` | 72 · 53 | `Hash::check()` verifikasi password lama; `Auth::attempt()` memanggil `Hash::check()` internal |
| e.3 | Manajemen kunci kriptografi | `.env` | 3 | `APP_KEY` tersimpan di `.env` di server; ⚠️ tidak ada rotation policy; ⚠️ tidak ada enkripsi data sensitif di database |
| e.4 | Generator angka acak kriptografi | `app/Http/Controllers/Auth/ForgotPasswordController.php` | 77–85 | Token reset via Laravel `Password::reset()` menggunakan CSPRNG (`random_bytes()`); session token juga dari Laravel CSPRNG |

---

### f. Penanganan Error dan Pencatatan Log

| # | Kontrol | File | Baris | Catatan |
|---|---------|------|-------|---------|
| f.1 | Konten pesan error | `app/Http/Controllers/PerizinanController.php` · `.env` | 84–97 · 4 | ⚠️ INTENTIONAL: raw SQL error bocor ke user (tidak ada `try/catch`); ⚠️ `APP_DEBUG=true` — stack trace tampil di response |
| f.2 | Metode penanganan error | `bootstrap/app.php` | 40–42 | `withExceptions()` kosong — tidak ada handler kustom; Laravel default handler aktif; ⚠️ INTENTIONAL: raw SQL di `PerizinanController::show()` tanpa penanganan exception |
| f.3 | Log tidak memuat informasi dikecualikan | `.env` | 15–18 | `LOG_LEVEL=debug` ⚠️ — berpotensi merekam data sensitif di log; perlu audit isi log |
| f.4 | Cakupan log untuk penyelidikan insiden | `.env` · `docker-compose.prod.yml` | 15–18 · — | Log dikirim via stderr ke Docker log driver; ⚠️ tidak ada audit trail aksi pengguna (tidak ada event logging) |
| f.5 | Pelindungan log dari akses tidak sah | `docker-compose.prod.yml` | — | Log hanya dapat diakses via `docker logs` (OS level); ❌ tidak ada enkripsi atau akses kontrol khusus pada log |
| f.6 | Enkripsi data log untuk cegah injeksi log | — | — | ❌ Log ditulis plaintext ke stderr; tidak ada sanitasi input sebelum ditulis ke log |
| f.7 | Sinkronisasi waktu | — | — | ❌ Tidak dikonfigurasi di aplikasi; ditangani OS/Docker host (TZ env var di `docker-compose.prod.yml` jika diset) |

---

### g. Proteksi Data

| # | Kontrol | File | Baris | Catatan |
|---|---------|------|-------|---------|
| g.1 | Identifikasi & penyimpanan salinan informasi dikecualikan | `app/Models/User.php` · `app/Http/Middleware/HandleInertiaRequests.php` | 23–26 · 22–28 | `$hidden = ['password', 'remember_token']`; frontend hanya terima `id, name, email, role` |
| g.2 | Pelindungan informasi dikecualikan sementara | `.env` | 29, 31 | `SESSION_DRIVER=database` (tidak di file); ⚠️ `SESSION_ENCRYPT=false` — data sesi tidak dienkripsi |
| g.3 | Pertukaran, penghapusan, dan audit informasi | `app/Http/Controllers/Admin/AkunController.php` | 116–126 | Penghapusan akun oleh superadmin; ⚠️ tidak ada fitur ekspor data pengguna; ❌ tidak ada audit trail |
| g.4 | Penentuan jumlah parameter | `app/Models/User.php` · `app/Models/Perizinan.php` | 14–21 · 9–18 | `$fillable` membatasi parameter yang diproses Laravel |
| g.5 | Data disimpan dengan aman | `docker-compose.prod.yml` · `database/migrations/2026_05_04_015547_create_perizinans_table.php` | 43–44 · 17 | Data di PostgreSQL named volume; ⚠️ tidak ada enkripsi database at-rest; ⚠️ INTENTIONAL: primary key sequential (IDOR vector) |
| g.6 | Metode hapus dan ekspor data | `app/Http/Controllers/Admin/AkunController.php` | 116–126 | Hard delete akun; ❌ tidak ada fitur ekspor data; ❌ tidak ada soft delete |
| g.7 | Pembersihan memori | — | — | ❌ Tidak dikontrol di lapisan aplikasi; ditangani garbage collector PHP dan OS |

---

### h. Keamanan Komunikasi

| # | Kontrol | File | Baris | Catatan |
|---|---------|------|-------|---------|
| h.1 | Komunikasi terenkripsi | `bootstrap/app.php` · `docker/nginx/default.conf` | 17–25 · 25 | TLS di-terminate Cloudflare Tunnel; nginx HTTP only; `fastcgi_param HTTPS on` baris 25 |
| h.2 | Koneksi masuk dan keluar aman | `.env` | 41–48 | Traffic masuk via Cloudflare (HTTPS); `MAIL_MAILER=log` (email tidak dikirim live); ⚠️ koneksi ke PostgreSQL tidak dienkripsi (Docker internal network) |
| h.3 | Jenis algoritma dan alat pengujian | — | — | ❌ Tidak dikonfigurasi di aplikasi/nginx; cipher suite dikelola Cloudflare — lihat: **Cloudflare dashboard → SSL/TLS** |
| h.4 | Sertifikat elektronik | — | — | ❌ Tidak ada sertifikat di kode/nginx; dikelola otomatis Cloudflare — lihat: **Cloudflare dashboard → SSL/TLS → Edge Certificates** |

---

### i. Pengendalian Kode Berbahaya

| # | Kontrol | File | Baris | Catatan |
|---|---------|------|-------|---------|
| i.1 | Analisis kode (SAST/DAST) | — | — | ❌ Tidak ada SAST/DAST tool; tidak ada CI/CD pipeline (direktori `.github/workflows/` tidak ada) |
| i.2 | Kode sumber tidak mengandung kode berbahaya | `app/` · `resources/js/` · `composer.json` · `package.json` | — | Audit manual kode sumber; ⚠️ tidak ada verifikasi checksum dependency; tidak ada `composer audit` / `npm audit` terotomasi |
| i.3 | Izin fitur atau sensor terkait privasi | `resources/js/Pages/` | — | Aplikasi web tidak meminta akses sensor device; Vue.js tidak memanggil browser permission API |
| i.4 | Pelindungan integritas | `Dockerfile` · `docker-compose.prod.yml` | 48 · 12–17 | `composer dump-autoload --optimize`; ⚠️ bind-mount source code di prod (baris 12–17) — kode dapat dimodifikasi tanpa rebuild |
| i.5 | Mekanisme pembaruan | `Dockerfile` | 37, 42 | Dependencies di-install saat build image; ❌ tidak ada mekanisme auto-update; update manual via `git pull` + Docker rebuild |

---

### j. Logika Bisnis

| # | Kontrol | File | Baris | Catatan |
|---|---------|------|-------|---------|
| j.1 | Alur logika bisnis urutan realistis | `app/Http/Controllers/PerizinanController.php` · `app/Http/Controllers/Admin/PerizinanController.php` | 64–77 · 49–67 | Status default `Pengajuan` saat buat; admin update ke `Diizinkan`/`Ditolak` |
| j.2 | Logika bisnis memiliki batasan dan validasi | `app/Http/Controllers/Admin/PerizinanController.php` · `database/migrations/2026_05_04_015547_create_perizinans_table.php` · `routes/web.php` | 52–58 · 35 · 58–61 | Enum status + validasi `in:`; hanya `role:pengguna` yang bisa ajukan perizinan |
| j.3 | Monitor aktivitas tidak biasa | `app/Http/Controllers/Auth/LoginController.php` | 56–64 | Blokir login setelah 5× gagal; ⚠️ tidak ada monitoring aktivitas tidak biasa di luar login |
| j.4 | Kontrol antiotomatisasi | `app/Http/Controllers/Auth/LoginController.php` | 56–64 | Blokir brute force login; ⚠️ tidak ada CAPTCHA; ❌ tidak ada `ThrottleRequests` middleware di endpoint lain |
| j.5 | Peringatan serangan otomatis | `app/Http/Controllers/Auth/LoginController.php` | 58–63 | Pesan "akun dikunci" ke pengguna; ❌ tidak ada notifikasi ke admin saat serangan terdeteksi |

---

### k. File

| # | Kontrol | File | Baris | Catatan |
|---|---------|------|-------|---------|
| k.1 | Kuota file per pengguna | `app/Http/Controllers/PerizinanController.php` | 47–49 | ⚠️ Hanya `nullable\|string\|max:255` (nama file); ❌ tidak ada batas jumlah file; file tidak benar-benar diupload ke server (simulasi) |
| k.2 | Validasi file sesuai tipe konten | `app/Http/Controllers/PerizinanController.php` · `resources/js/Pages/Perizinan/Create.vue` | 47–49 · 122–127 | ⚠️ INTENTIONAL: tidak ada validasi tipe MIME/ekstensi; frontend hanya menyimpan `file.name` (string), tidak transmit binary |
| k.3 | Pelindungan metadata input dan file | `app/Http/Controllers/PerizinanController.php` | 62–63 | ⚠️ Nama file dari user langsung disimpan tanpa sanitasi path traversal; `$request->input('surat_kepemilikan')` |
| k.4 | Pemindaian file dari sumber tidak dipercaya | — | — | ❌ Tidak ada antivirus/scanning; file tidak diupload ke server saat ini; jika implementasi diubah ke real upload, perlu ClamAV atau layanan serupa |
| k.5 | Konfigurasi server untuk unduh file | `docker/nginx/default.conf` | 29–31 | Akses dotfiles diblokir; ❌ tidak ada konfigurasi `Content-Disposition` karena tidak ada file yang disimpan |

---

### l. Keamanan API dan Web Service

| # | Kontrol | File | Baris | Catatan |
|---|---------|------|-------|---------|
| l.1 | Konfigurasi layanan web | `docker/nginx/default.conf` · `bootstrap/app.php` · `routes/web.php` | 1–32 · 16–39 · 1–84 | Nginx + Laravel middleware pipeline + definisi route |
| l.2 | URI API tidak expose informasi celah keamanan | `routes/web.php` · `database/migrations/2026_05_04_015547_create_perizinans_table.php` | 55 · 17 | ⚠️ INTENTIONAL: `/perizinan/{id}` dengan ID sequential mengekspos IDOR vector; `$table->id()` auto-increment |
| l.3 | Keputusan otorisasi | `app/Http/Middleware/CheckRole.php` · `routes/web.php` | 15–27 · 41, 58, 66, 76 | RBAC middleware; `auth` middleware wajib untuk semua route terproteksi |
| l.4 | RESTful HTTP methods untuk input valid | `routes/web.php` | 1–84 | `GET`/`POST`/`PUT`/`DELETE` sesuai resource; validasi input di controller sebelum proses |
| l.5 | Validasi skema sebelum terima input | Seluruh controller | — | `$request->validate()` di semua controller sebelum menyentuh database (lihat d.1) |
| l.6 | Pelindungan layanan berbasis web (CSRF) | `bootstrap/app.php` | 28–30 | CSRF via session middleware bawaan Laravel; `HandleInertiaRequests` di-append ke web middleware; ⚠️ tidak ada security response headers (CSP, HSTS, X-Frame-Options) |
| l.7 | Kontrol antiotomatisasi | `app/Http/Controllers/Auth/LoginController.php` | 56–64 | Blokir brute force login; ❌ tidak ada `ThrottleRequests` middleware global; tidak ada CAPTCHA |

---

### m. Keamanan Konfigurasi

| # | Kontrol | File | Baris | Catatan |
|---|---------|------|-------|---------|
| m.1 | Server dikonfigurasi sesuai rekomendasi | `Dockerfile` · `docker/nginx/default.conf` · `docker-compose.prod.yml` | 1–63 · 1–32 · 1–60 | PHP 8.4-fpm-alpine + nginx:alpine; ⚠️ `APP_DEBUG=true` di `.env:4` (seharusnya `false` di production) |
| m.2 | Dokumentasi, salin konfigurasi, dependensi | `composer.json` · `composer.lock` · `package.json` · `package-lock.json` · `docker-compose.prod.yml` · `Dockerfile` · `README.md` | — | Seluruh dependensi terkunci via lockfile; infrastruktur terdokumentasi |
| m.3 | Hapus fitur tidak diperlukan | `Dockerfile` · `.env` | 18–28, 37 · 4 | `composer install --no-dev`; extension PHP minimal; ⚠️ `APP_DEBUG=true` aktif di production |
| m.4 | Validasi integritas aset eksternal | `public/build/` | — | Vite output dengan hashed filename untuk cache busting; ❌ tidak ada Subresource Integrity (SRI); semua aset lokal (tidak ada CDN eksternal) |
| m.5 | Respons aplikasi dan konten aman | `app/Http/Controllers/PerizinanController.php` · `.env` | 84–97 · 4 | ⚠️ INTENTIONAL: raw SQL error message bocor di response; ⚠️ `APP_DEBUG=true` — stack trace tampil saat exception; ❌ tidak ada security headers di nginx |

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
