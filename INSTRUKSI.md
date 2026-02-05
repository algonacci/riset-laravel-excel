# INSTRUKSI SETUP LARAVEL STATELESS (LOAD BALANCER READY)

Panduan ini berisi checklist dan konfigurasi wajib agar aplikasi Omniflow-Admin / Laravel Starter ini siap diduplikasi (horizontal scaling) di belakang Load Balancer (seperti Nginx, AWS ALB, atau Cloudflare) tanpa error session hilang atau data tidak konsisten.

---

## 🚩 Prinsip Utama: "Shared Nothing"
Agar aplikasi bisa berjalan di banyak server (Node A, Node B, dst), **JANGAN PERNAH** menyimpan state (kondisi) di dalam server itu sendiri. Simpanlah state di layanan eksternal yang bisa diakses semua server.

## 1. Konfigurasi Session (Login Anti-Logout)

**Masalah:** Default Laravel menyimpan session login di file lokal (`storage/framework/sessions`). Jika user login di Server A, lalu request berikutnya masuk ke Server B, user akan diminta login lagi.
**Solusi:** Gunakan Database atau Redis yang terpusat.

### A. Opsi Database (Disarankan jika belum punya Redis Cluster)
Pastikan nama tabel session tidak bentrok dengan tabel legacy di database utama.
1. Set `.env`:
   ```env
   SESSION_DRIVER=database
   SESSION_LIFETIME=120
   SESSION_TABLE=laravel_cms_sessions  # Ganti nama tabel biar gak bentrok sama 'sessions' bawaan app lain
   ```
2. Pastikan file migrasi untuk tabel `laravel_cms_sessions` sudah dibuat dan dijalankan.

### B. Opsi Redis (Performa Tertinggi)
1. Set `.env`:
   ```env
   SESSION_DRIVER=redis
   REDIS_HOST=10.x.x.x  # IP Server Redis Pusat (Bukan 127.0.0.1 lokal)
   ```

---

## 2. Konfigurasi Cache (Data Konsisten)

**Masalah:** Jika Admin A update data Config di Server A, Server B masih menyimpan cache Config lama.
**Solusi:** Gunakan Redis Terpusat.

1. Set `.env`:
   ```env
   CACHE_DRIVER=redis
   CACHE_PREFIX=omniflow_admin_ # Prefix biar gak kecampur key-nya sama app lain
   ```
2. Saat deploy update codingan, jalankan perintah ini di **salah satu server saja** atau di pipeline deployment:
   ```bash
   php artisan cache:clear
   php artisan view:clear
   php artisan config:clear
   ```

---

## 3. Upload File & Gambar (Storage)

**Masalah:** User upload avatar admin di Server A (`storage/app/public`). Request user lain masuk ke Server B, gambar "404 Not Found".
**Solusi:** Wajib pakai Object Storage (S3 / MinIO / R2 / GCS).

1. Install Driver S3:
   ```bash
   composer require league/flysystem-aws-s3-v3
   ```
2. Set `.env`:
   ```env
   FILESYSTEM_DISK=s3
   AWS_ACCESS_KEY_ID=xxx
   AWS_SECRET_ACCESS_KEY=xxx
   AWS_DEFAULT_REGION=ap-southeast-1
   AWS_BUCKET=omniflow-assets
   AWS_URL=https://cdn.omniflow.id
   ```
3. Di kode program, jangan pernah pakai path lokal. Selalu gunakan Storage facade:
   ```php
   // ❌ SAHLA: move('public/uploads')
   // ✅ BENAR: Storage::disk('s3')->put('uploads', $file)
   ```

---

## 4. Database & Migrations (Legacy Friendly)

Karena aplikasi ini menumpang di Database Apps Utama (Shared DB), kita harus sopan.

1. **Namespace Tabel:**
   Ubah nama tabel default Laravel agar tidak menimpa tabel aplikasi utama.
   Set `.env`:
   ```env
   DB_MIGRATIONS_TABLE=laravel_migrations
   ```
2. **Read/Write Splitting (Opsional):**
   Jika trafik tinggi, pisahkan koneksi baca dan tulis.
   Di `config/database.php`:
   ```php
   'mysql' => [
       'read' => [
           'host' => ['192.168.1.1'],
       ],
       'write' => [
           'host' => ['196.168.1.2'],
       ],
       'sticky'    => true, // Penting! Biar habis write langsung bisa read data yg sama
   ],
   ```

---

## 5. Background Jobs & Queue (Fire and Forget)

Sesuai arsitektur Omniflow, Laravel Admin tugasnya hanya trigger (pemicu), bukan pekerja berat.

**Pola:**
1. Admin klik tombol "Generate Laporan Tahunan".
2. Laravel kirim message ke **RabbitMQ**.
3. Laravel selesai, return response "Laporan sedang diproses".
4. **Worker GO/Python** mengambil message dari RabbitMQ dan memprosesnya.

**Konfigurasi RabbitMQ di Laravel:**
1. Install package (pilih salah satu library php-amqplib).
2. Set `.env` (Jika pakai queue driver Laravel):
   ```env
   QUEUE_CONNECTION=rabbitmq
   ```
   *Atau jika murni Fire-and-Forget manual, gunakan library AMQP langsung di Controller.*

**PENTING:** Jangan jalankan `php artisan queue:work` di dalam container Web Server. Jalankan di container terpisah (Worker Container) jika memang butuh worker PHP.

---

## 6. Logs (Observability)

Jangan simpan log di file lokal (`storage/logs/laravel.log`) karena akan hilang saat container mati/restart.

1. Set `.env`:
   ```env
   LOG_CHANNEL=stderr
   ```
2. Log akan keluar di output `docker logs`. Tools monitoring (Datadog/CloudWatch_Logs/Promtail) tinggal baca dari situ.

---

## 7. App Key & Encryption

Pastikan **SEMUA SERVER** yang ada di belakang Load Balancer menggunakan `APP_KEY` yang **SAMA PERSIS**.
Jika beda 1 karakter saja, session user akan invalid (Logout tiba-tiba) karena enkripsi cookie session gagal didekripsi server tetangga.

---

## 8. Health Check Endpoint (Untuk Load Balancer)

Load Balancer butuh URL untuk ngecek apakah server ini sehat atau mati.
Buat route simpel di `routes/web.php` yang tidak butuh akses database berat:

```php
Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'server' => gethostname()], 200);
});
```
Arahkan Health Check LB ke `/health`.

---

## Ringkasan .env WAJIB UBAH (Template Production)

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:HARUS_SAMA_DI_SEMUA_SERVER

# Database Utama (Shared)
DB_CONNECTION=mysql
DB_HOST=master-db-ip
DB_DATABASE=hris_onni
DB_MIGRATIONS_TABLE=laravel_migrations

# Session & Cache (Stateless)
SESSION_DRIVER=redis # atau database (tabel: laravel_cms_sessions)
CACHE_DRIVER=redis
QUEUE_CONNECTION=rabbitmq # atau redis

# Storage (Stateless)
FILESYSTEM_DISK=s3

# Logs
LOG_CHANNEL=stderr
```
