# MASTER BLUEPRINT: PPNI SYSTEM v5.0

### Panduan Lengkap untuk AI Agent & Developer

> **Dokumen ini adalah kiblat tunggal pengembangan sistem.** Setiap keputusan teknis, arsitektur, logika bisnis, dan estetika UI mengacu pada dokumen ini. Tidak ada interpretasi bebas di luar yang tertulis di sini.

**Target Entitas:** Pondok Pesantren / Boarding School Management — Unit Banin (MTs & MA)
**Zona Waktu Sistem:** `Asia/Jakarta` (WIB / UTC+7)
**Versi Dokumen:** 5.0
**Status:** Production-Ready Specification

### Changelog v4.0 → v5.0

| #   | Perubahan                                                                                                          | Severity    |
| --- | ------------------------------------------------------------------------------------------------------------------ | ----------- |
| 1   | Ganti CSS Framework: Tailwind CSS → **Bootstrap 5 + Akademi Template** (DexignLab)                                 | 🔴 Breaking |
| 2   | Ganti Font: Inter/Source Serif 4 → **Poppins** (default template)                                                  | 🔴 Breaking |
| 3   | Ganti Color Palette: emerald/slate → **`--primary: #4D44B5` / `--secondary: #FB7D5B`**                             | 🔴 Breaking |
| 4   | Ganti Layout Structure: Tailwind classes → **Akademi layout classes** (`.nav-header`, `.dlabnav`, `.content-body`) | 🔴 Breaking |
| 5   | Update Section 5.1 Tech Stack: tambah Bootstrap 5, jQuery, DataTables, SweetAlert2, Toastr                         | 🔴 Breaking |
| 6   | Update Section 5.4 JS Rules: jQuery sekarang WAJIB ada, Alpine tetap dikelola Livewire                             | 🟡 Update   |
| 7   | Update Section 7.1 Struktur Direktori: aset CSS/JS di `public/` mengikuti struktur template                        | 🟡 Update   |
| 8   | Rewrite Section 13 Estetika Frontend: semua class reference sekarang Bootstrap + Akademi                           | 🔴 Breaking |
| 9   | Update Section 14 Livewire Catalog: tambah contoh HTML komponen Bootstrap per Livewire                             | 🟡 Update   |
| 10  | Tambah Section 21: Panduan Integrasi Template Akademi (asset path, vendor, konfigurasi)                            | 🟢 Baru     |

---

## Daftar Isi

1. [Latar Belakang & Tujuan Sistem](#1-latar-belakang--tujuan-sistem)
2. [Visi, Misi & Prinsip Desain](#2-visi-misi--prinsip-desain)
3. [Peta Aktor & Kebutuhan Pengguna](#3-peta-aktor--kebutuhan-pengguna)
4. [Peta Fitur & Module Sistem](#4-peta-fitur--module-sistem)
5. [Spesifikasi Teknis & Aturan Mutlak](#5-spesifikasi-teknis--aturan-mutlak)
6. [Arsitektur Database Lengkap](#6-arsitektur-database-lengkap)
7. [Struktur Direktori & Konvensi Kode](#7-struktur-direktori--konvensi-kode)
8. [Protokol Anti-Looping (Unit Scope)](#8-protokol-anti-looping-unit-scope)
9. [Logika Bisnis Detail per Modul](#9-logika-bisnis-detail-per-modul)
10. [Sistem Notifikasi Alpha WhatsApp](#10-sistem-notifikasi-alpha-whatsapp)
11. [Otomatisasi & Pekerja Latar Belakang](#11-otomatisasi--pekerja-latar-belakang)
12. [Hierarki Akses & Gates](#12-hierarki-akses--gates)
13. [Estetika Frontend (UI/UX)](#13-estetika-frontend-uiux)
14. [Komponen Livewire — Katalog & Kontrak](#14-komponen-livewire--katalog--kontrak)
15. [Alur Navigasi & Routing](#15-alur-navigasi--routing)
16. [Migrasi, Seeding & Factory](#16-migrasi-seeding--factory)
17. [Kebijakan Data & Retensi](#17-kebijakan-data--retensi)
18. [Panduan Testing & Quality Gate](#18-panduan-testing--quality-gate)
19. [Panduan Deployment & Environment](#19-panduan-deployment--environment)
20. [Glosarium Istilah Domain](#20-glosarium-istilah-domain)
21. [Panduan Integrasi Template Akademi](#21-panduan-integrasi-template-akademi)

---

## 1. Latar Belakang & Tujuan Sistem

### 1.1 Konteks Masalah

Pondok pesantren modern mengelola ratusan hingga ribuan santri dengan struktur akademik yang kompleks: dua unit pendidikan (MTs dan MA), puluhan kelas, jadwal pelajaran harian yang berulang, dan kebutuhan pelaporan yang ketat kepada Kementerian Agama.

Permasalahan yang ada saat ini sebelum sistem dibangun:

- **Absensi manual berbasis kertas** — rentan hilang, sulit direkap, tidak bisa dipantau real-time.
- **Tidak ada sistem peringatan dini** — bagian keamanan baru mengetahui masalah kehadiran santri setelah terlambat.
- **Rekap laporan memakan waktu lama** — walikelas harus merekap manual setiap akhir bulan/semester.
- **Tidak ada sentralisasi data izin UKS** — guru tidak tahu santri mana yang sedang sakit hari ini.
- **Penggantian guru (badal) tidak terdokumentasi** — tidak ada jejak siapa yang mengajar saat guru asli tidak hadir.
- **Data santri pindah/lulus hilang** — riwayat akademik tidak terjaga dengan baik.

### 1.2 Tujuan Sistem

| ID   | Tujuan                          | Indikator Keberhasilan                                                                       |
| ---- | ------------------------------- | -------------------------------------------------------------------------------------------- |
| T-01 | Digitalisasi absensi KBM harian | 100% absensi tercatat secara digital, nol kertas                                             |
| T-02 | Peringatan dini alpha santri    | Notifikasi WA ke bagian Keamanan dalam < 1 jam setelah threshold terlampaui                  |
| T-03 | Sentralisasi data izin UKS      | Guru dapat melihat status izin santri real-time sebelum input absensi                        |
| T-04 | Dokumentasi guru badal          | Setiap jadwal penggantian tercatat dengan nama guru pengganti dan tanggal                    |
| T-05 | Rekap laporan otomatis          | Walikelas dapat mengunduh rekap absensi kelas kapan saja tanpa menghitung manual             |
| T-06 | Manajemen kalender akademik     | Admin dapat menandai hari libur sehingga sistem tidak salah menjadwalkan atau mencatat alpha |
| T-07 | Keamanan data multi-unit        | Guru MTs tidak dapat melihat data MA, dan sebaliknya                                         |
| T-08 | Audit trail lengkap             | Setiap perubahan status absensi tercatat (siapa, kapan, dari apa, menjadi apa)               |

### 1.3 Batasan Sistem (Out of Scope)

- Sistem keuangan / pembayaran SPP.
- Portal orang tua / wali santri.
- Nilai akademik / raport.
- Absensi asrama (hanya absensi KBM).
- Pengiriman WA otomatis via gateway — notifikasi WA bersifat manual (template siap salin).

---

## 2. Visi, Misi & Prinsip Desain

### 2.1 Visi

> **"Menjadi sistem manajemen absensi yang invisible bagi guru — semudah mengabsen di kertas, tapi sekuat sistem enterprise."**

### 2.2 Misi

- Mengurangi beban administratif guru hingga 80% dibanding sistem manual.
- Menyediakan data real-time kepada pimpinan pondok untuk pengambilan keputusan.
- Memastikan tidak ada data absensi yang hilang — sistem otomatis mengisi jika guru lupa.
- Memberikan peringatan dini tepat waktu kepada bagian keamanan pondok.

### 2.3 Prinsip Desain

| Prinsip                    | Penerapan                                                                                                        |
| -------------------------- | ---------------------------------------------------------------------------------------------------------------- |
| **Fault-tolerant**         | Jika guru lupa absen, cron otomatis mengisi "Hadir" pukul 14:01. Sistem tidak bergantung pada disiplin pengguna. |
| **Data immutability**      | Data absensi historis tidak pernah dihapus — hanya dikoreksi. Rekam jejak selalu ada via `attendance_logs`.      |
| **Scope isolation**        | Data setiap unit (MTs/MA) terisolasi secara otomatis. Pengguna tidak perlu memilih filter unit.                  |
| **Progressive disclosure** | Guru hanya melihat fitur yang relevan dengan perannya. Admin melihat lebih banyak. Superadmin melihat semuanya.  |
| **Mobile-first**           | Sebagian besar guru mengakses via smartphone. Setiap fitur harus nyaman digunakan di layar 5–6 inci.             |
| **Zero configuration**     | Setelah instalasi dan seeding, sistem langsung bisa digunakan tanpa konfigurasi tambahan oleh pengguna.          |

---

## 3. Peta Aktor & Kebutuhan Pengguna

### 3.1 Daftar Aktor

| Aktor          | Role di Sistem | Deskripsi                                                                               |
| -------------- | -------------- | --------------------------------------------------------------------------------------- |
| **Superadmin** | `superadmin`   | Pengelola teknis sistem. Akses penuh lintas unit. Mengatur konfigurasi global.          |
| **Admin**      | `admin`        | Staf TU/akademik. Mengelola data master (siswa, kelas, jadwal). Terikat pada satu unit. |
| **Walikelas**  | `walikelas`    | Guru yang merangkap wali kelas. Akses rekap kelas walinya + absensi.                    |
| **Guru**       | `guru`         | Guru pengampu mata pelajaran. Akses absensi jadwal yang dia ampu atau dibadali.         |
| **Sistem**     | —              | Aktor otomatis: cron job, observer, job queue.                                          |

### 3.2 User Story per Aktor

#### Guru

```
Sebagai Guru, saya ingin:
- Melihat daftar jadwal mengajar saya hari ini di dashboard
- Membuka form absensi untuk setiap jadwal dengan satu klik
- Melihat santri mana yang sedang izin/sakit (agar tidak perlu ditanya lagi)
- Melihat semua mata pelajaran (read-only) untuk referensi
- Melihat riwayat absensi per jadwal yang sudah saya isi
- Mengganti password saya sendiri
```

#### Walikelas

```
Sebagai Walikelas, saya ingin:
- Melihat rekap absensi lengkap kelas yang saya wali
- Melihat santri dengan alpha terbanyak di kelas saya
- Mengunduh rekap absensi dalam format yang bisa dilaporkan
- Semua yang bisa dilakukan Guru (jika saya juga mengampu pelajaran)
```

#### Admin

```
Sebagai Admin, saya ingin:
- Mendaftarkan santri baru dan memasukkannya ke kelas
- Membuat/mengedit kelas dan jadwal pelajaran
- Mengelola data guru dan mata pelajaran
- Mencatat santri yang izin/sakit hari ini (Pusat Izin UKS)
- Melihat daftar notifikasi alpha yang perlu ditindaklanjuti
- Mengelola kalender libur pondok
- Semua yang bisa dilakukan Guru dan Walikelas
```

#### Superadmin

```
Sebagai Superadmin, saya ingin:
- Semua yang bisa dilakukan Admin
- Mengatur threshold alpha dan template pesan WA
- Melihat dan memulihkan data yang di-soft-delete
- Memantau status backup Google Drive
- Mengelola data lintas unit (MTs dan MA)
- Melihat log perubahan settings sistem
```

#### Sistem (Aktor Otomatis)

```
Sebagai Sistem, saya harus:
- Pukul 14:01: mengisi absensi "Hadir" untuk santri yang belum diabsen
- Pukul 16:00: menandai alpha pada guru yang tidak mengisi absensi
- Pukul 02:00: menjalankan backup database ke Google Drive
- Real-time: mendeteksi alpha santri dan membuat notifikasi jika threshold terlampaui
```

---

## 4. Peta Fitur & Module Sistem

### 4.1 Struktur Module

```
PPNI System
│
├── [M-01] Autentikasi & Profil
│   ├── Login (NIP/Username + Password)
│   ├── Logout
│   └── Ganti Password Mandiri
│
├── [M-02] Dashboard
│   ├── Widget: Jadwal Mengajar Hari Ini (Guru/Walikelas)
│   ├── Widget: Statistik Absensi Unit (Admin/Superadmin)
│   ├── Widget: Notifikasi Alpha Pending (Admin/Superadmin)
│   └── Widget: Santri Izin Hari Ini (Admin/Superadmin)
│
├── [M-03] Data Master
│   ├── [M-03-A] Manajemen Santri (CRUD + import)
│   ├── [M-03-B] Manajemen Kelas & Penempatan Santri
│   ├── [M-03-C] Manajemen Guru & User
│   └── [M-03-D] Manajemen Mata Pelajaran
│
├── [M-04] Akademik
│   ├── [M-04-A] Manajemen Jadwal Pelajaran
│   ├── [M-04-B] Input Absensi KBM (StudentTake)
│   ├── [M-04-C] Rekap Absensi Kelas
│   ├── [M-04-D] Guru Badal (Penggantian Jadwal)
│   └── [M-04-E] Daftar Semua Mata Pelajaran (Read-Only untuk semua role)
│
├── [M-05] UKS & Izin
│   └── [M-05-A] Pusat Izin Santri (Sakit/Izin)
│
├── [M-06] Notifikasi Alpha
│   ├── [M-06-A] Daftar Notifikasi Pending
│   └── [M-06-B] Riwayat Notifikasi Terkirim
│
└── [M-07] Sistem (Superadmin Only)
    ├── [M-07-A] Kalender Libur & Acara
    ├── [M-07-B] Pengaturan Threshold Alpha
    ├── [M-07-C] Template Pesan WA
    └── [M-07-D] Status Backup Google Drive
```

### 4.2 Prioritas Pengembangan

| Prioritas         | Module                             | Alasan                                               |
| ----------------- | ---------------------------------- | ---------------------------------------------------- |
| P0 — Must Have    | M-01, M-02, M-04-A, M-04-B         | Tanpa ini sistem tidak bisa digunakan sama sekali    |
| P1 — Should Have  | M-03, M-04-C, M-04-E, M-05-A, M-06 | Fungsionalitas inti yang dibutuhkan harian           |
| P2 — Nice to Have | M-04-D, M-07-A, M-07-B, M-07-C     | Penting tapi bisa menyusul setelah P0 dan P1 selesai |
| P3 — Later        | M-07-D                             | Fitur pendukung operasional                          |

---

## 5. Spesifikasi Teknis & Aturan Mutlak

> ⛔ **Agen AI WAJIB membaca dan mematuhi seluruh bagian ini sebelum menulis satu baris kode pun. Tidak ada pengecualian.**

### 5.1 Tech Stack

| Komponen       | Teknologi               | Versi        | Keterangan                                                                         |
| -------------- | ----------------------- | ------------ | ---------------------------------------------------------------------------------- |
| Framework      | Laravel                 | 11           | Backbone utama                                                                     |
| Reaktivitas UI | Livewire                | 3.x          | Full-stack reactive, tanpa inisialisasi Alpine.js manual                           |
| Bahasa         | PHP                     | 8.5          | Pipe operator, clone with, array_first/last, #[NoDiscard]                          |
| CSS Framework  | **Bootstrap**           | **5.x**      | ⚠️ BUKAN Tailwind. Menggunakan Bootstrap dari template Akademi                     |
| Template Admin | **Akademi (DexignLab)** | —            | Custom CSS di `public/css/style.css`, vendor di `public/vendor/`                   |
| Database       | MySQL / MariaDB         | 8.0+ / 10.6+ | InnoDB, charset `utf8mb4_unicode_ci`                                               |
| Queue Driver   | Redis                   | 7.x          | Production; fallback `database` untuk dev                                          |
| Cache Driver   | Redis                   | 7.x          | Shared instance dengan queue                                                       |
| Backup         | spatie/laravel-backup   | latest       | Target: Google Drive                                                               |
| Server         | Nginx + PHP-FPM         | —            | Atau Laravel Octane (opsional)                                                     |
| Node.js        | Node.js                 | 20 LTS       | Untuk build assets (Vite) — **hanya untuk Livewire assets**, bukan CSS/JS template |

> ⚠️ **PENTING — CSS/JS Template:** Seluruh aset template Akademi (Bootstrap, MetisMenu, DataTables, dll.) sudah tersedia sebagai **file statis di `public/`**. Jangan install ulang via npm. Gunakan langsung dengan `asset('vendor/...')` atau `asset('css/style.css')`.

> **Catatan PHP 8.5:** OPcache kini selalu dikompilasi ke dalam PHP secara built-in. Pastikan `opcache.enable=1` di `php.ini`.

### 5.2 Fitur PHP 8.5 yang Digunakan

```php
// 1. Pipe Operator — generate unique_code jadwal
$uniqueCode = $subject->code
    |> strtoupper(...)
    |> fn(string $s) => $s . '-' . str_replace([' ', '-'], '', $class->name)
    |> fn(string $s) => $s . '-' . substr($academicYear->name, -2);

// 2. array_first() / array_last()
$nextSchedule = array_first($todaySchedules);
$latestAttendance = array_last($attendanceList);

// 3. #[\NoDiscard]
#[\NoDiscard('Gunakan return value untuk cek keberhasilan simpan')]
public function submitAttendance(array $records): bool { ... }

// 4. clone with (opsional, untuk DTO)
$updatedAttendance = clone($attendance, ['status' => 'hadir']);

// 5. Deprecasi yang WAJIB dihindari:
// ❌ (boolean) $val   → ✅ (bool) $val
// ❌ (integer) $val   → ✅ (int) $val
// ❌ (double)  $val   → ✅ (float) $val
// ❌ case 'x';        → ✅ case 'x':
```

### 5.3 Aturan Anti-Undefined Error

> ⛔ **CRITICAL RULE #1** — Pelanggaran ini menyebabkan `Null Pointer Exception`.

```php
// ❌ DILARANG KERAS
$userId = Auth::user()->id;
$userId = auth()->user()->id;

// ✅ WAJIB
$userId = Auth::id();
$userId = auth()->id();
```

> ⛔ **CRITICAL RULE #2** — Setiap Facade bawaan Laravel WAJIB di-import eksplisit.

```php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
```

### 5.4 Aturan JavaScript (⚠️ DIPERBARUI di v5.0)

```js
// resources/js/app.js — HANYA INI
import "./bootstrap";
```

- **DILARANG** `import Alpine from 'alpinejs'` — Livewire 3 sudah mengelola Alpine otomatis.
- **jQuery** sudah tersedia secara global dari template (`public/vendor/jquery/jquery.min.js`). Tidak perlu install via npm.
- **Jangan** load jQuery via npm/Vite — akan konflik dengan jQuery dari template.
- Plugin template (DataTables, Select2, SweetAlert2, Toastr, MetisMenu, dll.) WAJIB di-load dari `public/vendor/`, bukan dari node_modules.
- Untuk interaktivitas Livewire + jQuery, gunakan `Livewire.on()` atau `@this.call()` — jangan mix `wire:` dengan jQuery event handler pada elemen yang sama.

### 5.5 Aturan Performa Database

```php
// ❌ DILARANG — N+1 Query
$schedules = Schedule::all();
foreach ($schedules as $schedule) {
    echo $schedule->teacher->name;
}

// ✅ WAJIB — Eager Loading
$schedules = Schedule::with(['teacher', 'subject', 'class'])->get();

// ❌ DILARANG — Loop insert
foreach ($students as $student) {
    StudentAttendance::create([...]);
}

// ✅ WAJIB — Batch Insert / Upsert
StudentAttendance::upsert($records, ['student_id', 'schedule_id', 'date'], ['status']);
```

> ⚠️ **PENTING:** `upsert()` dan `insert()` massal **TIDAK** memicu Eloquent events. Dispatch Job manual setelah operasi bulk.

### 5.6 Aturan SoftDeletes

```php
// Tabel yang WAJIB menggunakan SoftDeletes
// users, students, classes, schedules, subjects

// Tabel yang TIDAK menggunakan SoftDeletes (data permanen / audit)
// student_attendances, teacher_attendances, student_leaves
// notification_queue, settings_logs, attendance_logs
```

### 5.7 Aturan Validasi Input

```php
// ❌ DILARANG — validasi inline
public function store(Request $request) { $request->validate([...]); }

// ✅ WAJIB — Form Request terpisah
public function store(StoreStudentRequest $request) {
    // $request->validated() sudah bersih
}
```

### 5.8 Aturan Timezone

```php
// config/app.php
'timezone' => 'Asia/Jakarta',

->whereDate('date', today())       // ✅ timezone-aware
->where('date', Carbon::today())   // ✅ explicit
->where('date', date('Y-m-d'))     // ❌ menggunakan server timezone
```

---

## 6. Arsitektur Database Lengkap

> Sistem menggunakan **murni NIP/Username** untuk login. Tidak ada OAuth, tidak ada kolom `email`.

### 6.1 Entity Relationship Overview

```
units (1) ──< users (N)
units (1) ──< students (N)
units (1) ──< subjects (N)
units (1) ──< classes (N)
units (1) ──< school_calendars (N) [nullable]

academic_years (1) ──< classes (N)
academic_years (1) ──< schedules (N)
academic_years (1) ──< class_student (N)
academic_years (1) ──< student_attendances (N)
academic_years (1) ──< teacher_attendances (N)

classes (1) ──< class_student (N) >── students
classes (1) ──< schedules (N)

schedules (1) ──< schedule_substitutions (N)
schedules (1) ──< student_attendances (N)
schedules (1) ──< attendance_logs (N)

students (1) ──< student_attendances (N)
students (1) ──< student_leaves (N)
students (1) ──< notification_queue (N)

users (1) ──< schedules (N)              [teacher_id]
users (1) ──< teacher_attendances (N)
users (1) ──< schedule_substitutions (N) [substitute_user_id]
users (1) ──< attendance_logs (N)        [changed_by]
```

### 6.2 Tabel Master

#### `units`

```sql
CREATE TABLE units (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(50) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
-- Seed: id=1 'MTs', id=2 'MA'
```

#### `academic_years`

```sql
CREATE TABLE academic_years (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(20) NOT NULL,   -- '2024/2025'
    start_date DATE NOT NULL,
    end_date   DATE NOT NULL,
    is_active  BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
-- Hanya boleh ada 1 record dengan is_active = true pada satu waktu
```

#### `users`

```sql
CREATE TABLE users (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    username   VARCHAR(50) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    role       ENUM('superadmin','admin','walikelas','guru') NOT NULL,
    unit_id    BIGINT UNSIGNED NULL,   -- NULL untuk superadmin
    phone      VARCHAR(20) NULL,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (unit_id) REFERENCES units(id)
);
-- CATATAN: User model TIDAK menggunakan UnitScope (mencegah infinite loop)
```

#### `students`

```sql
CREATE TABLE students (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nis        VARCHAR(20) NOT NULL UNIQUE,
    nisn       VARCHAR(10) NULL,
    name       VARCHAR(100) NOT NULL,
    unit_id    BIGINT UNSIGNED NOT NULL,
    status     ENUM('aktif','lulus','pindah','dikeluarkan') NOT NULL DEFAULT 'aktif',
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (unit_id) REFERENCES units(id),
    INDEX idx_unit_status (unit_id, status)
);
```

### 6.3 Tabel Kelas & Jadwal

#### `classes`

```sql
CREATE TABLE classes (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name                VARCHAR(50) NOT NULL,
    unit_id             BIGINT UNSIGNED NOT NULL,
    homeroom_teacher_id BIGINT UNSIGNED NULL,
    academic_year_id    BIGINT UNSIGNED NOT NULL,
    deleted_at          TIMESTAMP NULL,
    created_at          TIMESTAMP NULL,
    updated_at          TIMESTAMP NULL,
    FOREIGN KEY (unit_id) REFERENCES units(id),
    FOREIGN KEY (homeroom_teacher_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id)
);
```

#### `class_student` (pivot)

```sql
CREATE TABLE class_student (
    id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    class_id         BIGINT UNSIGNED NOT NULL,
    student_id       BIGINT UNSIGNED NOT NULL,
    academic_year_id BIGINT UNSIGNED NOT NULL,
    enrolled_at      DATE NOT NULL,
    created_at       TIMESTAMP NULL,
    updated_at       TIMESTAMP NULL,
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id),
    UNIQUE KEY uq_student_year (student_id, academic_year_id)
);
```

#### `subjects`

```sql
CREATE TABLE subjects (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code       VARCHAR(10) NOT NULL,    -- 'BIND', 'MTK', 'FIS', 'ALQH', dst
    name       VARCHAR(100) NOT NULL,
    unit_id    BIGINT UNSIGNED NOT NULL,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (unit_id) REFERENCES units(id),
    UNIQUE KEY uq_code_unit (code, unit_id)
);
```

#### `schedules`

```sql
CREATE TABLE schedules (
    id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    unit_id          BIGINT UNSIGNED NOT NULL,    -- ← DENORMALISASI untuk UnitScope
    class_id         BIGINT UNSIGNED NOT NULL,
    subject_id       BIGINT UNSIGNED NOT NULL,
    teacher_id       BIGINT UNSIGNED NOT NULL,
    academic_year_id BIGINT UNSIGNED NOT NULL,
    day_of_week      TINYINT UNSIGNED NOT NULL,   -- 0=Minggu, 1=Senin..6=Sabtu
    start_time       TIME NOT NULL,
    end_time         TIME NOT NULL,
    unique_code      VARCHAR(30) NULL UNIQUE,      -- 'BIND-10A-25'
    deleted_at       TIMESTAMP NULL,
    created_at       TIMESTAMP NULL,
    updated_at       TIMESTAMP NULL,
    FOREIGN KEY (unit_id) REFERENCES units(id),
    FOREIGN KEY (class_id) REFERENCES classes(id),
    FOREIGN KEY (subject_id) REFERENCES subjects(id),
    FOREIGN KEY (teacher_id) REFERENCES users(id),
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id),
    INDEX idx_teacher_day (teacher_id, day_of_week),
    INDEX idx_class_day (class_id, day_of_week),
    INDEX idx_unit (unit_id)
);
```

**Logika generate `unique_code`:**

```php
private function generateUniqueCode(Subject $subject, Classes $class, AcademicYear $year): string
{
    return $subject->code
        |> strtoupper(...)
        |> fn(string $s) => $s . '-' . str_replace([' ', '-', '/'], '', strtoupper($class->name))
        |> fn(string $s) => $s . '-' . substr(str_replace('/', '', $year->name), -2);
    // Contoh: 'BIND' → 'BIND-10A' → 'BIND-10A-25'
}
```

#### `schedule_substitutions`

```sql
CREATE TABLE schedule_substitutions (
    id                 BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    schedule_id        BIGINT UNSIGNED NOT NULL,
    substitute_user_id BIGINT UNSIGNED NOT NULL,
    date               DATE NOT NULL,
    notes              TEXT NULL,
    created_at         TIMESTAMP NULL,
    updated_at         TIMESTAMP NULL,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id),
    FOREIGN KEY (substitute_user_id) REFERENCES users(id),
    UNIQUE KEY uq_schedule_date (schedule_id, date),
    INDEX idx_substitute_date (substitute_user_id, date)
);
```

### 6.4 Tabel Absensi (Inti Sistem)

#### `student_attendances`

```sql
CREATE TABLE student_attendances (
    id                   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    unit_id              BIGINT UNSIGNED NOT NULL,    -- ← DENORMALISASI
    student_id           BIGINT UNSIGNED NOT NULL,
    schedule_id          BIGINT UNSIGNED NOT NULL,
    academic_year_id     BIGINT UNSIGNED NOT NULL,
    date                 DATE NOT NULL,
    status               ENUM('hadir','alpha','sakit','izin') NOT NULL,
    is_merged            BOOLEAN NOT NULL DEFAULT FALSE,
    merged_from_class_id BIGINT UNSIGNED NULL,
    created_by           BIGINT UNSIGNED NULL,
    notes                TEXT NULL,
    created_at           TIMESTAMP NULL,
    updated_at           TIMESTAMP NULL,
    FOREIGN KEY (unit_id) REFERENCES units(id),
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (schedule_id) REFERENCES schedules(id),
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id),
    FOREIGN KEY (merged_from_class_id) REFERENCES classes(id),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY uq_attendance (student_id, schedule_id, date),
    INDEX idx_student_date (student_id, date),
    INDEX idx_schedule_date (schedule_id, date),
    INDEX idx_unit (unit_id)
);
```

#### `teacher_attendances`

```sql
CREATE TABLE teacher_attendances (
    id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    unit_id          BIGINT UNSIGNED NOT NULL,    -- ← DENORMALISASI
    user_id          BIGINT UNSIGNED NOT NULL,
    academic_year_id BIGINT UNSIGNED NOT NULL,
    date             DATE NOT NULL,
    status           ENUM('hadir','alpha','sakit','izin') NOT NULL,
    is_auto          BOOLEAN NOT NULL DEFAULT FALSE,
    created_at       TIMESTAMP NULL,
    updated_at       TIMESTAMP NULL,
    FOREIGN KEY (unit_id) REFERENCES units(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id),
    UNIQUE KEY uq_teacher_date (user_id, date),
    INDEX idx_user_date (user_id, date),
    INDEX idx_unit (unit_id)
);
```

#### `attendance_logs`

```sql
CREATE TABLE attendance_logs (
    id             BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    attendance_id  BIGINT UNSIGNED NOT NULL,
    changed_by     BIGINT UNSIGNED NOT NULL,
    old_status     ENUM('hadir','alpha','sakit','izin') NOT NULL,
    new_status     ENUM('hadir','alpha','sakit','izin') NOT NULL,
    reason         TEXT NULL,
    changed_at     TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attendance_id) REFERENCES student_attendances(id),
    FOREIGN KEY (changed_by) REFERENCES users(id),
    INDEX idx_attendance (attendance_id),
    INDEX idx_changed_by (changed_by),
    INDEX idx_changed_at (changed_at)
);
-- Tidak ada SoftDelete — log tidak boleh dihapus
```

### 6.5 Tabel Pendukung

#### `student_leaves`

```sql
CREATE TABLE student_leaves (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    unit_id    BIGINT UNSIGNED NOT NULL,    -- ← DENORMALISASI
    student_id BIGINT UNSIGNED NOT NULL,
    date       DATE NOT NULL,
    status     ENUM('sakit','izin') NOT NULL,
    notes      TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (unit_id) REFERENCES units(id),
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    UNIQUE KEY uq_student_leave_date (student_id, date),
    INDEX idx_date (date),
    INDEX idx_unit (unit_id)
);
```

#### `school_calendars`

```sql
CREATE TABLE school_calendars (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    date        DATE NOT NULL UNIQUE,
    type        ENUM('holiday','special_event') NOT NULL,
    description VARCHAR(150) NULL,
    unit_id     BIGINT UNSIGNED NULL,
    created_at  TIMESTAMP NULL,
    updated_at  TIMESTAMP NULL,
    FOREIGN KEY (unit_id) REFERENCES units(id),
    INDEX idx_date_type (date, type)
);
```

#### `settings`

```sql
CREATE TABLE settings (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    key         VARCHAR(100) NOT NULL UNIQUE,
    value       TEXT NOT NULL,
    type        ENUM('integer','boolean','string','json') NOT NULL DEFAULT 'string',
    description VARCHAR(200) NULL,
    updated_at  TIMESTAMP NULL
);
```

**Seed wajib tabel `settings`:**

| `key`                     | `value`               | `type`  | `description`                                 |
| ------------------------- | --------------------- | ------- | --------------------------------------------- |
| `alpha_threshold_mode`    | `cumulative`          | string  | Mode hitung alpha: `cumulative` atau `weekly` |
| `alpha_threshold_count`   | `5`                   | integer | Jumlah alpha maksimal sebelum notifikasi      |
| `attendance_cutoff_time`  | `14:00`               | string  | Jam batas input absensi KBM (format HH:MM)    |
| `wa_notification_enabled` | `true`                | boolean | Toggle seluruh sistem notifikasi WA           |
| `wa_message_template`     | _(lihat Bagian 10.3)_ | string  | Template pesan WA dengan variabel             |
| `default_weekend_days`    | `[0,6]`               | json    | Hari weekend default: 0=Minggu, 6=Sabtu       |

#### `notification_queue`

```sql
CREATE TABLE notification_queue (
    id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    unit_id      BIGINT UNSIGNED NOT NULL,    -- ← DENORMALISASI
    student_id   BIGINT UNSIGNED NOT NULL,
    alpha_count  TINYINT UNSIGNED NOT NULL,
    message      TEXT NOT NULL,
    status       ENUM('pending','sent','dismissed') NOT NULL DEFAULT 'pending',
    triggered_at TIMESTAMP NOT NULL,
    sent_at      TIMESTAMP NULL,
    sent_by      BIGINT UNSIGNED NULL,
    created_at   TIMESTAMP NULL,
    updated_at   TIMESTAMP NULL,
    FOREIGN KEY (unit_id) REFERENCES units(id),
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (sent_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_student (student_id),
    INDEX idx_unit (unit_id)
);
```

#### `settings_logs`

```sql
CREATE TABLE settings_logs (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    key        VARCHAR(100) NOT NULL,
    old_value  TEXT NULL,
    new_value  TEXT NOT NULL,
    changed_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (changed_by) REFERENCES users(id)
);
```

---

## 7. Struktur Direktori & Konvensi Kode

### 7.1 Struktur Direktori Laravel (⚠️ DIPERBARUI di v5.0)

```
app/
├── Http/
│   ├── Controllers/
│   └── Requests/
│       ├── StoreStudentRequest.php
│       ├── UpdateStudentRequest.php
│       └── ...
├── Livewire/
│   ├── Auth/
│   │   └── LoginForm.php
│   ├── Dashboard/
│   │   ├── TodayScheduleWidget.php
│   │   └── AlphaNotificationWidget.php
│   ├── Master/
│   │   ├── StudentIndex.php
│   │   ├── StudentForm.php
│   │   ├── ClassIndex.php
│   │   └── ...
│   ├── Academic/
│   │   ├── ScheduleIndex.php
│   │   ├── SubjectIndex.php
│   │   ├── StudentTake.php
│   │   ├── AttendanceRecap.php
│   │   └── SubstitutionForm.php
│   ├── Uks/
│   │   └── LeaveForm.php
│   ├── Notification/
│   │   └── AlphaNotificationIndex.php
│   └── System/
│       ├── CalendarManager.php
│       └── SettingsAlpha.php
├── Models/          (sama seperti v4.0)
├── Models/Scopes/
│   └── UnitScope.php
├── Observers/
│   └── StudentAttendanceObserver.php
├── Jobs/
│   └── CreateAlphaNotification.php
├── Console/Commands/
│   ├── AutoHadirDanTeguran.php
│   ├── AutoAlphaGuru.php
│   └── BackupDatabase.php
└── Policies/
    ├── StudentPolicy.php
    ├── SchedulePolicy.php
    └── AttendancePolicy.php

resources/
├── css/
│   └── app.css           ← HANYA untuk override custom PPNI, BUKAN Bootstrap
├── js/
│   ├── app.js            ← import './bootstrap'; SAJA
│   └── bootstrap.js
└── views/
    ├── layouts/
    │   ├── app.blade.php     ← Layout utama menggunakan struktur Akademi template
    │   ├── auth.blade.php    ← Layout halaman login/register (fullwidth)
    │   └── fullwidth.blade.php
    ├── elements/
    │   ├── sidebar.blade.php ← Sidebar PPNI (dari template, disesuaikan)
    │   ├── header.blade.php  ← Header Akademi (topbar)
    │   └── footer.blade.php
    ├── livewire/
    │   ├── auth/
    │   ├── dashboard/
    │   ├── master/
    │   ├── academic/
    │   ├── uks/
    │   ├── notification/
    │   └── system/
    └── errors/

public/
├── css/
│   ├── style.css         ← CSS utama template Akademi (JANGAN diedit)
│   └── ppni-custom.css   ← Override khusus PPNI (warna brand, custom badge)
├── js/
│   └── custom.js         ← JS custom PPNI (jika perlu)
├── vendor/               ← Semua vendor dari template (Bootstrap, jQuery, dll.)
│   ├── bootstrap/
│   ├── jquery/
│   ├── datatables/
│   ├── sweetalert2/
│   ├── toastr/
│   ├── metismenu/
│   ├── perfect-scrollbar/
│   ├── select2/
│   ├── fullcalendar-5.11.0/
│   └── ...
└── icons/
    ├── bootstrap-icons/
    ├── fontawesome/
    └── material-design-iconic-font/
```

### 7.2 Konvensi Penamaan

| Entitas            | Konvensi                            | Contoh                                       |
| ------------------ | ----------------------------------- | -------------------------------------------- |
| Model              | PascalCase, singular                | `StudentAttendance`                          |
| Tabel DB           | snake_case, plural                  | `student_attendances`                        |
| Livewire Component | PascalCase                          | `StudentTake`                                |
| View Livewire      | kebab-case                          | `student-take.blade.php`                     |
| Form Request       | `{Action}{Model}Request`            | `StoreStudentRequest`                        |
| Job                | PascalCase, verb phrase             | `CreateAlphaNotification`                    |
| Observer           | `{Model}Observer`                   | `StudentAttendanceObserver`                  |
| Route name         | kebab-case, titik sebagai separator | `students.index`, `academic.attendance.take` |

### 7.3 Model Boilerplate (Student) — tidak berubah dari v4.0

```php
<?php

namespace App\Models;

use App\Models\Scopes\UnitScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = ['nis', 'nisn', 'name', 'unit_id', 'status'];
    protected $casts = ['deleted_at' => 'datetime'];

    protected static function booted(): void
    {
        static::addGlobalScope(new UnitScope());
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(Classes::class, 'class_student')
            ->withPivot(['academic_year_id', 'enrolled_at'])
            ->withTimestamps();
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(StudentLeave::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }

    public function isOnLeaveToday(): bool
    {
        return $this->leaves()->whereDate('date', today())->exists();
    }
}
```

---

## 8. Protokol Anti-Looping (Unit Scope) — tidak berubah dari v4.0

### 8.1 Implementasi UnitScope

```php
<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UnitScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (app()->runningInConsole()) return;
        if (! auth()->hasUser()) return;

        $user = auth()->user();

        if ($user->role === 'superadmin') return;
        if (! $user->unit_id) return;

        $builder->where($model->getTable() . '.unit_id', $user->unit_id);
    }
}
```

### 8.2 Matrix Penerapan UnitScope

| Model               | UnitScope       | Catatan                |
| ------------------- | --------------- | ---------------------- |
| `User`              | ❌ **DILARANG** | Penyebab infinite loop |
| `Student`           | ✅ Wajib        | unit_id langsung       |
| `Classes`           | ✅ Wajib        | unit_id langsung       |
| `Subject`           | ✅ Wajib        | unit_id langsung       |
| `Schedule`          | ✅ Wajib        | unit_id denormalisasi  |
| `StudentAttendance` | ✅ Wajib        | unit_id denormalisasi  |
| `TeacherAttendance` | ✅ Wajib        | unit_id denormalisasi  |
| `StudentLeave`      | ✅ Wajib        | unit_id denormalisasi  |
| `NotificationQueue` | ✅ Wajib        | unit_id denormalisasi  |
| `AcademicYear`      | ❌ Tidak        | Shared lintas unit     |
| `SchoolCalendar`    | ❌ Tidak        | Difilter manual        |
| `AttendanceLog`     | ❌ Tidak        | Audit trail global     |
| `Setting`           | ❌ Tidak        | Global config          |

---

## 9. Logika Bisnis Detail per Modul — tidak berubah dari v4.0

_(Seluruh logika bisnis identik dengan v4.0: autentikasi, dashboard, manajemen santri, jadwal, StudentTake, rekap, guru badal, koreksi absensi, UKS)_

Lihat kode lengkap di Section 9 v4.0 — semua logika backend tidak berubah, hanya tampilan frontendnya yang berubah ke Bootstrap.

---

## 10. Sistem Notifikasi Alpha WhatsApp — tidak berubah dari v4.0

_(Alur, template pesan, implementasi Job, Observer — identik dengan v4.0)_

---

## 11. Otomatisasi & Pekerja Latar Belakang — tidak berubah dari v4.0

_(Scheduler, `ppni:auto-hadir`, `ppni:auto-alpha-guru`, konfigurasi Redis — identik dengan v4.0)_

---

## 12. Hierarki Akses & Gates — tidak berubah dari v4.0

### 12.1 Matrix Akses Lengkap

| Fitur                      | Superadmin |  Admin  |      Walikelas       |       Guru        |
| -------------------------- | :--------: | :-----: | :------------------: | :---------------: |
| **Dashboard**              |  ✅ Full   | ✅ Full |     ✅ Terbatas      |    ✅ Terbatas    |
| **Santri: lihat**          |     ✅     |   ✅    |   ✅ (kelas wali)    |        ❌         |
| **Santri: tambah/edit**    |     ✅     |   ✅    |          ❌          |        ❌         |
| **Santri: hapus (soft)**   |     ✅     |   ❌    |          ❌          |        ❌         |
| **Mapel: lihat semua**     |     ✅     |   ✅    |          ✅          |        ✅         |
| **Mapel: kelola**          |     ✅     |   ✅    |          ❌          |        ❌         |
| **Jadwal: lihat**          |     ✅     |   ✅    |     ✅ (diampu)      |    ✅ (diampu)    |
| **Jadwal: kelola**         |     ✅     |   ✅    |          ❌          |        ❌         |
| **Absensi: input**         |     ✅     |   ✅    | ✅ (kelas wali+ampu) | ✅ (diampu+badal) |
| **Absensi: rekap lihat**   |     ✅     |   ✅    |   ✅ (kelas wali)    |  ✅ (kelas ampu)  |
| **Absensi: koreksi**       |     ✅     |   ✅    |          ❌          |        ❌         |
| **Absensi: export**        |     ✅     |   ✅    |   ✅ (kelas wali)    |        ❌         |
| **UKS: izin santri**       |     ✅     |   ✅    |          ❌          |        ❌         |
| **Notifikasi alpha**       |     ✅     |   ✅    |          ❌          |        ❌         |
| **Kalender libur**         |     ✅     |   ❌    |          ❌          |        ❌         |
| **Pengaturan alpha**       |     ✅     |   ❌    |          ❌          |        ❌         |
| **Backup status**          |     ✅     |   ❌    |          ❌          |        ❌         |
| **Ganti password sendiri** |     ✅     |   ✅    |          ✅          |        ✅         |

### 12.2 Definisi Gate (Laravel Native) — identik dengan v4.0

```php
// app/Providers/AppServiceProvider.php → boot()
Gate::define('manage-master-data', fn(User $user) =>
    in_array($user->role, ['superadmin', 'admin'])
);
Gate::define('manage-system', fn(User $user) =>
    $user->role === 'superadmin'
);
Gate::define('delete-student', fn(User $user) =>
    $user->role === 'superadmin'
);
Gate::define('view-class-recap', function (User $user, Classes $class) {
    if (in_array($user->role, ['superadmin', 'admin'])) return true;
    if ($user->role === 'walikelas') return $class->homeroom_teacher_id === $user->id;
    return false;
});
Gate::define('input-attendance', function (User $user, Schedule $schedule) {
    if (in_array($user->role, ['superadmin', 'admin'])) return true;
    if ($schedule->teacher_id === $user->id) return true;
    return $schedule->substitutions()
        ->where('substitute_user_id', $user->id)
        ->whereDate('date', today())
        ->exists();
});
Gate::define('correct-attendance', fn(User $user) =>
    in_array($user->role, ['superadmin', 'admin'])
);
Gate::define('manage-leaves', fn(User $user) =>
    in_array($user->role, ['superadmin', 'admin'])
);
Gate::define('manage-notifications', fn(User $user) =>
    in_array($user->role, ['superadmin', 'admin'])
);
Gate::define('view-subjects', fn(User $user) => true);
```

---

## 13. Estetika Frontend (UI/UX) ⚠️ REWRITE TOTAL di v5.0

> Seluruh bagian ini MENGGANTIKAN Section 13 v4.0. Tidak ada lagi referensi ke Tailwind CSS.

### 13.1 Framework & Sumber CSS

**CSS Framework: Bootstrap 5** yang sudah di-bundle dalam template **Akademi by DexignLab**.

```
Sumber CSS (urutan load di layout):
1. public/vendor/bootstrap/css/bootstrap.min.css
2. public/css/style.css          ← CSS utama Akademi (sudah include Bootstrap overrides)
3. public/css/ppni-custom.css    ← Override spesifik PPNI (dibuat baru)
```

**Jangan** install Bootstrap via npm. Gunakan file yang sudah ada di `public/vendor/`.

### 13.2 Tipografi

Template Akademi menggunakan **Poppins** sebagai font utama (sudah di-import di `style.css`).

| Kategori      | Class Bootstrap/Custom                   | Keterangan           |
| ------------- | ---------------------------------------- | -------------------- |
| Judul halaman | `h4 fw-bold` dengan warna `var(--title)` | `--title: #303972`   |
| Sub-judul     | `h5 fw-semibold`                         | Warna `var(--title)` |
| Label / UI    | `fs-14` atau `small`                     | Default Poppins      |
| Data tabel    | `fs-14`                                  | Default              |
| Metadata      | `fs-12 text-muted`                       | Kecil, abu           |

> `--font-family-base: Poppins, sans-serif` sudah ditetapkan di `:root` dalam `style.css`. Tidak perlu re-deklarasi.

### 13.3 Palet Warna (CSS Variables dari Akademi)

```css
/* Sudah didefinisikan di public/css/style.css — jangan override di sini */
:root {
    --primary: #4d44b5; /* Ungu utama — tombol, nav aktif, aksen */
    --primary-hover: #3d3690;
    --primary-dark: #1e1a46;
    --secondary: #fb7d5b; /* Coral/orange — aksen sekunder */
    --title: #303972; /* Judul, heading */

    /* RGBA variants sudah tersedia: --rgba-primary-1 s/d --rgba-primary-9 */
}
```

**Override warna untuk PPNI** di `public/css/ppni-custom.css`:

```css
/* public/css/ppni-custom.css */
/* Badge status absensi */
.badge-hadir {
    background-color: #10b981;
    color: #fff;
}
.badge-alpha {
    background-color: #ef4444;
    color: #fff;
}
.badge-sakit {
    background-color: #f59e0b;
    color: #fff;
}
.badge-izin {
    background-color: #3b82f6;
    color: #fff;
}

/* Row terkunci UKS */
.row-locked td {
    background-color: #f8fafc !important;
    color: #94a3b8;
}

/* Override primary untuk warna brand PPNI jika perlu */
/* Jika ingin mengubah warna utama dari ungu ke hijau pesantren: */
/* --primary: #047857; */
/* --primary-hover: #065f46; */
```

### 13.4 Mapping Token Warna Status

| Status         | Bootstrap Class              | Custom Class   | Penggunaan           |
| -------------- | ---------------------------- | -------------- | -------------------- |
| Hadir          | `badge bg-success`           | `.badge-hadir` | Badge & row warna    |
| Alpha          | `badge bg-danger`            | `.badge-alpha` | Badge & row warna    |
| Sakit          | `badge bg-warning text-dark` | `.badge-sakit` | Badge & row warna    |
| Izin           | `badge bg-info text-dark`    | `.badge-izin`  | Badge & row warna    |
| Terkunci (UKS) | `table-secondary`            | `.row-locked`  | Row santri yang izin |

### 13.5 Layout Struktur Akademi

```
┌─────────────────────────────────────────────────────┐
│ .nav-header (logo PPNI + hamburger)                 │
├─────────────────────────────────────────────────────┤
│ .header (topbar: page title + notif + user dropdown)│
├────────────────┬────────────────────────────────────┤
│                │                                    │
│  .dlabnav      │  .content-body                     │
│  (sidebar)     │  (konten utama)                    │
│                │                                    │
│  MetisMenu     │  @yield('content')                 │
│  accordion     │                                    │
│                │                                    │
└────────────────┴────────────────────────────────────┘
```

### 13.6 Blade Layout Utama (`resources/views/layouts/app.blade.php`)

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PPNI System | @yield('title')</title>

    {{-- CSS Template Akademi --}}
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('icons/bootstrap-icons/font/bootstrap-icons.css') }}" rel="stylesheet">

    {{-- CSS Custom PPNI --}}
    <link href="{{ asset('css/ppni-custom.css') }}" rel="stylesheet">

    @stack('styles')

    {{-- Livewire styles --}}
    @livewireStyles
</head>
<body>
    <div id="preloader">
        <div class="loader"><div class="dots">
            <div class="dot mainDot"></div>
            <div class="dot"></div><div class="dot"></div>
        </div></div>
    </div>

    <div id="main-wrapper">
        {{-- Nav Header --}}
        <div class="nav-header">
            <a href="{{ route('dashboard') }}" class="brand-logo">
                <span class="brand-title fw-bold fs-4" style="color: var(--primary);">
                    PPNI System
                </span>
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span>
                    <span class="line"></span>
                    <span class="line"></span>
                </div>
            </div>
        </div>

        {{-- Header (topbar) --}}
        @include('elements.header')

        {{-- Sidebar --}}
        @include('elements.sidebar')

        {{-- Content Body --}}
        <div class="content-body default-height">
            @yield('content')
        </div>

        {{-- Footer --}}
        @include('elements.footer')
    </div>

    {{-- JS Template Akademi (urutan WAJIB diikuti) --}}
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/perfect-scrollbar/js/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('vendor/metismenu/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('vendor/deznav/js/deznav-init.js') }}"></script>

    {{-- Livewire scripts (WAJIB setelah jQuery) --}}
    @livewireScripts

    @stack('scripts')
</body>
</html>
```

### 13.7 Sidebar PPNI (`resources/views/elements/sidebar.blade.php`)

```blade
<div class="dlabnav">
    <div class="dlabnav-scroll">
        <ul class="metismenu" id="menu">

            {{-- Dashboard --}}
            <li class="{{ request()->routeIs('dashboard') ? 'mm-active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <i class="bi bi-speedometer2"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            {{-- Absensi --}}
            <li class="{{ request()->routeIs('attendance.*') ? 'mm-active' : '' }}">
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="bi bi-calendar-check"></i>
                    <span class="nav-text">Absensi</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('schedules.index') }}">Jadwal & Absen</a></li>
                    <li><a href="{{ route('attendance.recap', ['class' => 'today']) }}">Rekap Kelas</a></li>
                </ul>
            </li>

            {{-- Data Master (Admin+) --}}
            @can('manage-master-data')
            <li class="{{ request()->routeIs('students.*', 'classes.*') ? 'mm-active' : '' }}">
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="bi bi-people"></i>
                    <span class="nav-text">Data Master</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('students.index') }}">Santri</a></li>
                    <li><a href="{{ route('classes.index') }}">Kelas</a></li>
                    <li><a href="{{ route('substitutions.index') }}">Guru Badal</a></li>
                </ul>
            </li>
            @endcan

            {{-- Mata Pelajaran (semua role) --}}
            <li class="{{ request()->routeIs('subjects.*') ? 'mm-active' : '' }}">
                <a href="{{ route('subjects.index') }}">
                    <i class="bi bi-book"></i>
                    <span class="nav-text">Mata Pelajaran</span>
                </a>
            </li>

            {{-- UKS & Notifikasi (Admin+) --}}
            @can('manage-leaves')
            <li class="{{ request()->routeIs('leaves.*') ? 'mm-active' : '' }}">
                <a href="{{ route('leaves.index') }}">
                    <i class="bi bi-heart-pulse"></i>
                    <span class="nav-text">UKS & Izin</span>
                </a>
            </li>
            @endcan

            @can('manage-notifications')
            <li class="{{ request()->routeIs('notifications.*') ? 'mm-active' : '' }}">
                <a href="{{ route('notifications.index') }}">
                    <i class="bi bi-bell"></i>
                    <span class="nav-text">Notifikasi Alpha</span>
                    {{-- Badge count jika ada pending --}}
                </a>
            </li>
            @endcan

            {{-- Sistem (Superadmin) --}}
            @can('manage-system')
            <li class="{{ request()->routeIs('system.*') ? 'mm-active' : '' }}">
                <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
                    <i class="bi bi-gear"></i>
                    <span class="nav-text">Sistem</span>
                </a>
                <ul aria-expanded="false">
                    <li><a href="{{ route('system.calendar') }}">Kalender Libur</a></li>
                    <li><a href="{{ route('system.settings') }}">Pengaturan Alpha</a></li>
                </ul>
            </li>
            @endcan

        </ul>

        <div class="copyright">
            <p><strong>PPNI Smart Attendance</strong></p>
            <p class="fs-12">Unit Banin — MTs & MA</p>
        </div>
    </div>
</div>
```

### 13.8 Interaktivitas Livewire dengan Bootstrap

```blade
{{-- Tombol submit dengan loading state --}}
<button
    wire:click="submit"
    wire:loading.attr="disabled"
    wire:loading.class="disabled"
    class="btn btn-primary"
    type="button"
>
    <span wire:loading.remove>
        <i class="bi bi-save me-1"></i> Simpan Absensi
    </span>
    <span wire:loading class="d-flex align-items-center gap-2">
        <span class="spinner-border spinner-border-sm" role="status"></span>
        Menyimpan...
    </span>
</button>

{{-- Flash message menggunakan Toastr via Livewire event --}}
{{-- Di Livewire component PHP: --}}
{{-- $this->dispatch('notify', type: 'success', message: 'Absensi berhasil disimpan.'); --}}

{{-- Di app.blade.php @stack('scripts') atau file JS custom: --}}
<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('notify', ({ type, message }) => {
        toastr[type](message);
    });
});
</script>
```

### 13.9 Komponen HTML Standar PPNI

#### Card Header Halaman

```blade
<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-sm-6">
            <h4 class="breadcrumb-title">{{ $pageTitle }}</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">{{ $pageTitle }}</li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ $cardTitle }}</h4>
                </div>
                <div class="card-body">
                    @yield('card-content')
                </div>
            </div>
        </div>
    </div>
</div>
```

#### Tabel Data (DataTables)

```blade
<table class="table table-responsive-md" id="ppni-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Nama</th>
            <th>NIS</th>
            <th>Kelas</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($students as $student)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $student->name }}</td>
            <td><span class="text-muted fs-12">{{ $student->nis }}</span></td>
            <td>{{ $student->currentClass?->name ?? '-' }}</td>
            <td>
                <span class="badge badge-{{ $student->status === 'aktif' ? 'success' : 'secondary' }}">
                    {{ ucfirst($student->status) }}
                </span>
            </td>
            <td>
                <a href="{{ route('students.edit', $student) }}" class="btn btn-primary btn-xs sharp me-1">
                    <i class="bi bi-pencil"></i>
                </a>
                <button wire:click="confirmDelete({{ $student->id }})" class="btn btn-danger btn-xs sharp">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
```

#### Badge Status Absensi

```blade
{{-- Gunakan helper atau Blade directive --}}
@php
    $badgeMap = [
        'hadir' => 'badge-hadir',
        'alpha' => 'badge-alpha',
        'sakit' => 'badge-sakit',
        'izin'  => 'badge-izin',
    ];
@endphp
<span class="badge {{ $badgeMap[$attendance->status] ?? 'bg-secondary' }}">
    {{ ucfirst($attendance->status) }}
</span>
```

#### Modal Konfirmasi (SweetAlert2)

```blade
{{-- Trigger dari Livewire --}}
<script>
document.addEventListener('livewire:initialized', () => {
    Livewire.on('confirm-delete', ({ id, name }) => {
        Swal.fire({
            title: 'Hapus data ini?',
            text: `${name} akan dihapus dari sistem.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'var(--primary)',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                @this.delete(id);
            }
        });
    });
});
</script>
```

#### Form Input Standar

```blade
<div class="mb-3">
    <label class="form-label fw-semibold">Nama Santri <span class="text-danger">*</span></label>
    <input
        type="text"
        wire:model.live.debounce.300ms="name"
        class="form-control @error('name') is-invalid @enderror"
        placeholder="Masukkan nama lengkap"
    >
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
```

#### Dashboard Stat Card

```blade
<div class="col-xl-3 col-sm-6">
    <div class="card">
        <div class="card-body d-flex align-items-center">
            <div class="me-auto">
                <h2 class="fs-32 fw-bold mb-0 text-primary">{{ $totalStudents }}</h2>
                <span class="fs-14 text-muted">Total Santri Aktif</span>
            </div>
            <div class="icon-box bg-primary-light rounded-circle p-3">
                <i class="bi bi-people fs-24 text-primary"></i>
            </div>
        </div>
    </div>
</div>
```

### 13.10 Inisialisasi DataTables di View Livewire

```blade
@push('scripts')
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
<script>
$(document).ready(function() {
    $('#ppni-table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/id.json'
        },
        responsive: true,
    });
});

// Re-init DataTables setelah Livewire update DOM
document.addEventListener('livewire:updated', function() {
    if ($.fn.DataTable.isDataTable('#ppni-table')) {
        $('#ppni-table').DataTable().destroy();
    }
    $('#ppni-table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.1/i18n/id.json'
        },
        responsive: true,
    });
});
</script>
@endpush
```

> ⚠️ **Jika tabel sering diupdate via Livewire**, pertimbangkan menggunakan Livewire's built-in `wire:poll` atau `$refresh` daripada DataTables untuk menghindari konflik DOM. DataTables hanya cocok untuk tabel yang jarang berubah.

---

## 14. Komponen Livewire — Katalog & Kontrak

| Komponen                  | Route                      | Gate                   | Deskripsi                                |
| ------------------------- | -------------------------- | ---------------------- | ---------------------------------------- |
| `LoginForm`               | `/login`                   | —                      | Form login username + password           |
| `TodayScheduleWidget`     | `/dashboard`               | authenticated          | Jadwal hari ini per guru, card Bootstrap |
| `AlphaNotificationWidget` | `/dashboard`               | `manage-notifications` | Widget notif alpha pending               |
| `StudentIndex`            | `/students`                | `manage-master-data`   | Tabel Bootstrap + DataTables + search    |
| `StudentForm`             | `/students/create`         | `manage-master-data`   | Form Bootstrap + SweetAlert2             |
| `ScheduleIndex`           | `/schedules`               | authenticated          | Jadwal per unit/hari                     |
| `SubjectIndex`            | `/subjects`                | `view-subjects`        | Daftar semua mapel (read-only)           |
| `StudentTake`             | `/attendance/{scheduleId}` | `input-attendance`     | Form absensi + Bootstrap badge status    |
| `AttendanceRecap`         | `/recap/{classId}`         | `view-class-recap`     | Rekap + Bootstrap table + export         |
| `LeaveForm`               | `/leaves`                  | `manage-leaves`        | Input izin/sakit + Select2 santri        |
| `AlphaNotificationIndex`  | `/notifications`           | `manage-notifications` | Daftar notif + Toastr copy WA            |
| `SubstitutionForm`        | `/substitutions`           | `manage-master-data`   | Assign guru badal + Select2              |
| `CalendarManager`         | `/system/calendar`         | `manage-system`        | FullCalendar 5.11                        |
| `SettingsAlpha`           | `/system/settings`         | `manage-system`        | Form pengaturan threshold + template WA  |

### 14.1 Contoh Livewire View: StudentTake

```blade
{{-- resources/views/livewire/academic/student-take.blade.php --}}
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="row page-titles">
        <div class="col">
            <h4 class="breadcrumb-title">Input Absensi</h4>
            <p class="text-muted mb-0">
                {{ $schedule->subject->name }} — {{ $schedule->class->name }}
                <span class="badge bg-secondary ms-1">{{ $schedule->unique_code }}</span>
            </p>
        </div>
    </div>

    {{-- Cutoff Warning --}}
    @if($isPastCutoff)
    <div class="alert alert-warning d-flex align-items-center" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <div>Waktu input absensi telah berakhir (pukul {{ $cutoffTime }} WIB). Data ditampilkan sebagai referensi.</div>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Daftar Hadir</h4>
                    <span class="badge bg-primary">{{ count($students) }} Santri</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">#</th>
                                    <th>Nama Santri</th>
                                    <th width="80">NIS</th>
                                    <th width="280">Status Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $i => $student)
                                <tr class="{{ $student['is_locked'] ? 'row-locked' : '' }}" wire:key="student-{{ $student['id'] }}">
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        {{ $student['name'] }}
                                        @if($student['is_merged'])
                                            <span class="badge bg-info text-dark ms-1" title="Kelas gabungan">Gabung</span>
                                        @endif
                                    </td>
                                    <td><code class="fs-12">{{ $student['nis'] }}</code></td>
                                    <td>
                                        @if($student['is_locked'])
                                            <span class="badge badge-{{ $student['lock_reason'] === 'sakit' ? 'sakit' : 'izin' }}">
                                                <i class="bi bi-lock-fill me-1"></i>
                                                {{ ucfirst($student['lock_reason']) }} (UKS)
                                            </span>
                                        @else
                                            <div class="btn-group btn-group-sm" role="group">
                                                @foreach (['hadir', 'alpha', 'sakit', 'izin'] as $status)
                                                <input
                                                    type="radio"
                                                    class="btn-check"
                                                    id="status-{{ $student['id'] }}-{{ $status }}"
                                                    wire:model="students.{{ $i }}.status"
                                                    value="{{ $status }}"
                                                    @disabled($isPastCutoff)
                                                >
                                                <label
                                                    class="btn btn-outline-{{ ['hadir'=>'success','alpha'=>'danger','sakit'=>'warning','izin'=>'info'][$status] }}"
                                                    for="status-{{ $student['id'] }}-{{ $status }}"
                                                >{{ ucfirst($status) }}</label>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(!$isPastCutoff)
                <div class="card-footer text-end">
                    <button
                        wire:click="submit"
                        wire:loading.attr="disabled"
                        class="btn btn-primary px-4"
                        type="button"
                    >
                        <span wire:loading.remove>
                            <i class="bi bi-save me-1"></i> Simpan Absensi
                        </span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...
                        </span>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
```

---

## 15. Alur Navigasi & Routing — tidak berubah dari v4.0

```php
// routes/web.php (identik v4.0)

Route::middleware('guest')->group(function () {
    Route::get('/login', LoginForm::class)->name('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/logout', LogoutController::class)->name('logout');
    Route::get('/profile/password', ChangePassword::class)->name('profile.password');

    Route::get('/schedules', ScheduleIndex::class)->name('schedules.index');
    Route::get('/subjects', SubjectIndex::class)->name('subjects.index');
    Route::get('/attendance/{schedule}', StudentTake::class)->name('attendance.take');
    Route::get('/recap/{class}', AttendanceRecap::class)->name('attendance.recap');

    Route::middleware('can:manage-master-data')->group(function () {
        Route::get('/students', StudentIndex::class)->name('students.index');
        Route::get('/students/create', StudentForm::class)->name('students.create');
        Route::get('/students/{student}/edit', StudentForm::class)->name('students.edit');
        Route::get('/classes', ClassIndex::class)->name('classes.index');
        Route::get('/substitutions', SubstitutionForm::class)->name('substitutions.index');
        Route::get('/notifications', AlphaNotificationIndex::class)->name('notifications.index');
    });

    Route::get('/leaves', LeaveForm::class)->name('leaves.index')
        ->middleware('can:manage-leaves');

    Route::middleware('can:manage-system')->prefix('system')->group(function () {
        Route::get('/calendar', CalendarManager::class)->name('system.calendar');
        Route::get('/settings', SettingsAlpha::class)->name('system.settings');
    });
});
```

---

## 16. Migrasi, Seeding & Factory — tidak berubah dari v4.0

_(Urutan migrasi, DatabaseSeeder, SubjectSeeder — identik dengan v4.0)_

---

## 17. Kebijakan Data & Retensi — tidak berubah dari v4.0

---

## 18. Panduan Testing & Quality Gate — tidak berubah dari v4.0

---

## 19. Panduan Deployment & Environment

### 19.1 Environment Variables — tidak berubah dari v4.0

```env
APP_NAME="PPNI System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ppni.yourpondok.ac.id
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id

DB_CONNECTION=mysql
DB_DATABASE=ppni_db

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

FILESYSTEM_DISK=local
BACKUP_DISK=google
```

### 19.2 Checklist Deployment (Tambahan v5.0)

```
□ APP_DEBUG=false di production
□ APP_KEY sudah di-generate
□ PHP 8.5 terinstall
□ Migrasi sudah dijalankan
□ Seeder sudah dijalankan
□ Storage link sudah dibuat (php artisan storage:link)
□ Queue worker berjalan via Supervisor
□ Cron Laravel berjalan
□ Redis berjalan
□ Backup Google Drive sudah dikonfigurasi

⚠️ KHUSUS v5.0 (Template Akademi):
□ Seluruh folder public/vendor/ dari template sudah ada
□ Seluruh folder public/icons/ dari template sudah ada
□ File public/css/style.css sudah ada
□ File public/css/ppni-custom.css sudah dibuat
□ jquery-ppni-init.js atau script inisialisasi MetisMenu/DataTables sudah berjalan
□ Tidak ada duplikasi load jQuery (cek Network tab di browser DevTools)
□ Toastr notifications sudah berjalan untuk Livewire events
□ SweetAlert2 sudah berjalan untuk konfirmasi hapus
□ Password superadmin default sudah diganti
```

---

## 20. Glosarium Istilah Domain — tidak berubah dari v4.0

| Istilah                | Makna dalam Sistem                                                |
| ---------------------- | ----------------------------------------------------------------- |
| **Santri**             | Siswa di pondok pesantren.                                        |
| **KBM**                | Kegiatan Belajar Mengajar.                                        |
| **Guru Badal**         | Guru pengganti.                                                   |
| **Walikelas**          | Guru yang bertanggung jawab atas satu kelas.                      |
| **UKS**                | Unit Kesehatan Sekolah — fitur izin/sakit.                        |
| **Alpha**              | Status tidak hadir tanpa keterangan.                              |
| **Pusat Izin**         | Halaman Admin untuk mencatat santri yang izin/sakit hari ini.     |
| **Tahun Ajaran Aktif** | `academic_years` dengan `is_active = true`. Hanya boleh ada satu. |
| **Threshold Alpha**    | Batas jumlah alpha yang memicu notifikasi.                        |
| **Cutoff Time**        | Pukul 14:00 WIB — batas input absensi manual.                     |
| **Unit**               | MTs atau MA. Data terisolasi per unit.                            |
| **Denormalisasi**      | Kolom `unit_id` yang ditambahkan untuk mendukung UnitScope.       |
| **Unique Code**        | Format `{KODE_MAPEL}-{KELAS}-{TAHUN}`, misal `BIND-10A-25`.       |

---

## 21. Panduan Integrasi Template Akademi ⚠️ BARU di v5.0

### 21.1 Tentang Template

Template yang digunakan adalah **"Akademi" by DexignLab** — sebuah Laravel admin dashboard template untuk manajemen sekolah. Template ini menggunakan:

- Bootstrap 5 sebagai CSS framework utama
- Poppins sebagai font utama
- MetisMenu untuk sidebar accordion navigation
- Perfect Scrollbar untuk scroll sidebar
- DezNav untuk inisialisasi sidebar
- jQuery sebagai dasar semua plugin

**Warna primary template:** `#4D44B5` (ungu/indigo)
**Warna secondary template:** `#FB7D5B` (coral)

### 21.2 File-File Template yang Digunakan PPNI

Dari template Akademi, yang dipertahankan untuk PPNI:

```
public/
├── css/
│   └── style.css          ← CSS utama (wajib, jangan hapus/edit)
├── vendor/
│   ├── bootstrap/         ← Bootstrap 5
│   ├── jquery/            ← jQuery 3.x
│   ├── metismenu/         ← Sidebar accordion
│   ├── perfect-scrollbar/ ← Scroll sidebar
│   ├── deznav/            ← Sidebar init script
│   ├── datatables/        ← Tabel data + paginasi
│   ├── sweetalert2/       ← Modal konfirmasi
│   ├── toastr/            ← Notifikasi toast
│   ├── select2/           ← Dropdown searchable
│   ├── fullcalendar-5.11.0/ ← Kalender libur (M-07-A)
│   ├── bootstrap-datepicker/ ← Date picker form
│   └── chartist/          ← Chart statistik (opsional)
└── icons/
    ├── bootstrap-icons/   ← Icon utama PPNI
    ├── fontawesome/       ← Icon tambahan
    └── material-design-iconic-font/
```

File-file template yang **TIDAK digunakan** PPNI dan boleh diabaikan:

- Semua views di `resources/views/akademi/` (ganti dengan Livewire views)
- `resources/views/elements/` yang asli (buat ulang untuk PPNI)
- `app/Http/Controllers/AkademiAdminController.php`
- `config/dz.php` (sistem asset loading template lama — ganti dengan Blade stack biasa)

### 21.3 Penyesuaian Warna Brand

Template Akademi menggunakan ungu (`#4D44B5`) sebagai primary color. Jika ingin mengubah ke warna yang lebih bernuansa pesantren (hijau), tambahkan di `public/css/ppni-custom.css`:

```css
/* Ganti primary ke hijau pesantren */
:root {
    --primary: #047857; /* Emerald 700 */
    --primary-hover: #065f46; /* Emerald 800 */
    --primary-dark: #022c22; /* Emerald 950 */
    --rgba-primary-1: rgba(4, 120, 87, 0.1);
    --rgba-primary-2: rgba(4, 120, 87, 0.2);
    --rgba-primary-3: rgba(4, 120, 87, 0.3);
    --rgba-primary-4: rgba(4, 120, 87, 0.4);
    --rgba-primary-5: rgba(4, 120, 87, 0.5);
    --title: #1e3a2f;
}
```

> Ini opsional. Default ungu juga tetap profesional dan sesuai untuk konteks pendidikan.

### 21.4 Konfigurasi Asset di `config/dz.php` vs Stack Blade

Template asli menggunakan `config/dz.php` untuk manajemen asset per-halaman. Untuk PPNI + Livewire, **lebih sederhana** menggunakan `@stack('styles')` dan `@stack('scripts')` di layout:

```blade
{{-- Di layout app.blade.php --}}
@stack('styles')   {{-- sebelum </head> --}}
@stack('scripts')  {{-- sebelum </body> --}}

{{-- Di Livewire view yang butuh DataTables --}}
@push('styles')
<link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
<script> $(document).ready(function() { $('#ppni-table').DataTable(); }); </script>
@endpush
```

### 21.5 Konflik Livewire + jQuery

Masalah umum yang muncul saat menggabungkan Livewire 3 dengan jQuery:

| Masalah                                                            | Penyebab                                          | Solusi                                                                |
| ------------------------------------------------------------------ | ------------------------------------------------- | --------------------------------------------------------------------- |
| MetisMenu sidebar collapse tidak berfungsi setelah Livewire update | Livewire diff DOM menghapus event handler jQuery  | Gunakan `Livewire.hook('morph.updated', () => { ... })` untuk re-init |
| DataTables reset setelah `wire:model` update                       | Livewire re-render tabel DOM                      | Destroy & re-init DataTable di `livewire:updated` event               |
| SweetAlert tidak muncul                                            | jQuery plugin belum terpanggil saat Livewire boot | Gunakan `document.addEventListener('livewire:initialized', ...)`      |
| Select2 tidak muncul                                               | Sama dengan SweetAlert                            | Re-init Select2 di `livewire:updated`                                 |

**Pattern re-init jQuery plugins setelah Livewire:**

```js
// Letakkan di public/js/custom.js atau di @push('scripts')
document.addEventListener("livewire:updated", function () {
    // Re-init MetisMenu
    if (typeof $.fn.metisMenu !== "undefined") {
        $("#menu").metisMenu();
    }
});
```

---

## Changelog Blueprint

| Versi    | Perubahan Utama                                                                                                                                                                                                                                                                       |
| -------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **v1.0** | Blueprint awal — arsitektur dasar, core logic, UI/UX, cron, gates                                                                                                                                                                                                                     |
| **v2.0** | + Tabel absensi lengkap; + `academic_year_id` di pivot; + settings; + notification_queue                                                                                                                                                                                              |
| **v3.0** | + Latar belakang & tujuan; + peta aktor; + SQL DDL; + Job, Observer, Command; + testing guide                                                                                                                                                                                         |
| **v4.0** | + Fix 5 bug kritis; + `subjects.code`; + `attendance_logs`; + `schedules.unique_code`; + Gates native; + PHP 8.5                                                                                                                                                                      |
| **v5.0** | + **Migrasi CSS Framework: Tailwind → Bootstrap 5 + Akademi Template (DexignLab)**; + Section 21 Panduan Integrasi Template; + Update Section 5.1, 5.4, 7.1, 13, 14 mengikuti struktur Bootstrap/Akademi; + Custom CSS PPNI di `ppni-custom.css`; + Panduan konflik Livewire + jQuery |

---

_— Akhir Dokumen Blueprint PPNI System v5.0 —_

> **Untuk AI Agent (Copilot):** Baca dokumen ini dari awal hingga akhir sebelum menulis satu baris kode pun. Perhatikan khusus: (1) CSS Framework adalah **Bootstrap 5**, bukan Tailwind — jangan pernah tulis class Tailwind; (2) jQuery sudah tersedia global dari template — jangan install via npm; (3) UnitScope hanya bekerja pada model dengan `unit_id` langsung; (4) `upsert()` tidak trigger Eloquent events — dispatch Job manual; (5) `ppni:auto-alpha-guru` hanya tandai guru yang punya jadwal hari ini; (6) Re-init jQuery plugins setelah setiap Livewire DOM update.
