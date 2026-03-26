# MaintenanceHub API

MaintenanceHub API adalah sistem backend berbasis Laravel yang dirancang untuk mengelola data maintenance sensor dan lokasi. Proyek ini menyediakan autentikasi, kontrol akses berbasis peran, penugasan teknisi, manajemen sensor, pencatatan aktivitas, serta notifikasi melalui REST API.

## Gambaran Umum

API ini dibangun untuk mendukung alur kerja maintenance, khususnya pada pengelolaan sensor dan lokasi. Sistem memungkinkan pengguna untuk melakukan autentikasi secara aman, mengelola data lokasi, mengelola data sensor, menugaskan teknisi ke lokasi tertentu, serta memantau pembaruan penting melalui log aktivitas dan notifikasi.

Proyek ini cocok digunakan sebagai media pembelajaran backend Laravel maupun sebagai fondasi awal untuk membangun platform maintenance management yang lebih lengkap.


## Fitur Utama

### 1. Autentikasi
- Registrasi pengguna
- Login pengguna
- Melihat profil pengguna yang sedang login
- Logout
- Autentikasi berbasis token menggunakan Laravel Sanctum

### 2. Manajemen Role dan Permission
- Otorisasi berbasis role menggunakan Spatie Permission
- Role yang tersedia:
  - Admin
  - Technician
  - Viewer
- Permission yang lebih rinci untuk:
  - membaca data lokasi
  - mengelola data lokasi
  - membaca data sensor
  - mengelola data sensor
  - menghapus data sensor

### 3. Manajemen Lokasi
- Menambah, melihat, memperbarui, dan menghapus lokasi
- Menugaskan teknisi ke lokasi tertentu
- Melihat daftar lokasi yang berhubungan dengan pengguna yang sedang login

### 4. Manajemen Sensor
- Menambah, melihat, memperbarui, dan menghapus sensor
- Mendukung partial update menggunakan PATCH
- Mendukung pencarian, filter, sorting, dan pagination
- Menghitung jadwal kalibrasi berikutnya (`next_due_date`)
- Melihat daftar sensor yang terkait dengan pengguna yang sedang login

### 5. Notifikasi
- Melihat semua notifikasi
- Melihat notifikasi yang belum dibaca
- Menandai notifikasi sebagai sudah dibaca

### 6. Activity Logging
- Mencatat aktivitas perubahan sensor
- Menyimpan riwayat aktivitas untuk kebutuhan audit


## Teknologi yang Digunakan

- **Framework:** Laravel 10
- **Authentication:** Laravel Sanctum
- **Authorization:** Spatie Laravel Permission
- **Starter Kit:** Laravel Breeze
- **Database:** MySQL / MariaDB / database relasional lain yang kompatibel
- **Bahasa:** PHP 8.1 atau lebih baru

## Struktur Proyek

```bash
app/
├── Events/
├── Http/
│   ├── Controllers/Api/
│   ├── Requests/
│   └── Resources/
├── Listeners/
├── Models/
├── Policies/
├── Services/
database/
├── migrations/
├── seeders/
routes/
├── api.php
├── web.php
```

### Komponen Penting

- **Model**
  - `User`
  - `Location`
  - `Sensor`
  - `ActivityLog`

- **Controller**
  - `AuthController`
  - `LocationController`
  - `SensorController`
  - `NotificationController`

- **Policy**
  - `SensorPolicy`

- **Event & Listener**
  - `SensorPatched`
  - `LogSensorPatchedListener`

## Relasi Database

### User
- memiliki banyak lokasi melalui relasi many-to-many
- memiliki role dan permission
- menerima notifikasi

### Location
- memiliki banyak sensor
- memiliki banyak user yang bertindak sebagai teknisi

### Sensor
- dimiliki oleh satu lokasi

### ActivityLog
- menyimpan catatan aktivitas penting dalam sistem

## Autentikasi API

Proyek ini menggunakan **Laravel Sanctum** untuk autentikasi API.

Setelah login berhasil, sistem akan mengembalikan token. Token tersebut digunakan pada header berikut untuk mengakses route yang diproteksi:

```http
Authorization: Bearer YOUR_TOKEN
```


## Endpoint API

## Auth

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| POST | `/api/auth/register` | Mendaftarkan user baru |
| POST | `/api/auth/login` | Login pengguna |
| GET | `/api/auth/me` | Melihat data user yang sedang login |
| POST | `/api/auth/logout` | Logout pengguna |

## Lokasi

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/locations` | Melihat semua lokasi |
| POST | `/api/locations` | Menambahkan lokasi baru |
| GET | `/api/locations/{id}` | Melihat detail lokasi |
| PUT | `/api/locations/{id}` | Memperbarui lokasi |
| DELETE | `/api/locations/{id}` | Menghapus lokasi |
| POST | `/api/locations/{id}/assign-technicians` | Menugaskan teknisi ke lokasi |

## Sensor

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/sensors` | Melihat semua sensor |
| POST | `/api/sensors` | Menambahkan sensor baru |
| GET | `/api/sensors/{id}` | Melihat detail sensor |
| PUT | `/api/sensors/{id}` | Memperbarui sensor |
| PATCH | `/api/sensors/{id}` | Memperbarui sebagian data sensor |
| DELETE | `/api/sensors/{id}` | Menghapus sensor |

## Resource Milik User Saat Ini

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/me/locations` | Melihat lokasi yang dimiliki / ditugaskan ke user |
| GET | `/api/me/sensors` | Melihat sensor yang terkait dengan user |

## Notifikasi

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/notifications` | Melihat semua notifikasi |
| GET | `/api/notifications/unread` | Melihat notifikasi yang belum dibaca |
| POST | `/api/notifications/{id}/read` | Menandai notifikasi sebagai sudah dibaca |


## Contoh Request Login

```http
POST /api/auth/login
Content-Type: application/json
Accept: application/json
```

```json
{
  "email": "admin@test.com",
  "password": "password"
}
```

### Contoh Response

```json
{
  "message": "Login berhasil",
  "token": "1|example_token_here",
  "user": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@test.com"
  }
}
```


## Contoh Payload Sensor

```json
{
  "name": "Temperature Sensor A1",
  "serial_number": "SN-001",
  "status": "active",
  "location_id": 1,
  "last_calibrated_at": "2026-03-01",
  "calibration_interval_days": 30
}
```


## Aturan Bisnis

- **Admin** dapat mengelola seluruh lokasi dan sensor.
- **Technician** hanya dapat mengelola sensor yang berada pada lokasi yang ditugaskan kepadanya.
- **Viewer** hanya dapat melihat data dan tidak dapat melakukan perubahan.
- Perubahan pada sensor dapat memicu:
  - pencatatan activity log
  - pengiriman notifikasi kepada admin


## Fitur Search, Filter, dan Sort

Endpoint daftar sensor mendukung beberapa parameter tambahan, seperti:

- pencarian berdasarkan nama sensor atau nomor seri
- filter berdasarkan lokasi
- filter berdasarkan status
- sort berdasarkan kolom tertentu
- pagination untuk data dalam jumlah besar

Contoh:

```bash
GET /api/sensors?search=temp&status=active&sort=name&page=1
```
