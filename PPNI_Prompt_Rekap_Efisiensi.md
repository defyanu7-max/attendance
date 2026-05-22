# PPNI SYSTEM — PROMPT INSTRUKSI AGENT
## Modul: Efisiensi Struktur & Tampilan Rekap Absensi (M-04-C `AttendanceRecap`)

> **Scope:** Instruksi ini berlaku HANYA untuk semua hal yang berkaitan dengan tampilan rekap, tabel ringkasan, dan output laporan dalam sistem PPNI. Tempelkan instruksi ini di atas context Blueprint v5.0 setiap kali agent diminta mengerjakan fitur rekap.

---

## ⚡ PERINTAH UTAMA

Ketika kamu mengerjakan fitur rekap absensi (`AttendanceRecap`, `/recap/{classId}`), kamu **WAJIB** mengikuti semua aturan di bawah ini tanpa pengecualian. Tujuannya adalah agar data rekap:
1. **Mudah dipindai secara visual** — pengguna menemukan informasi kritis dalam < 3 detik
2. **Pengelompokan yang tegas** — setiap kelompok data memiliki batas visual yang jelas
3. **Hierarki informasi yang benar** — data penting tampil besar/menonjol, data pendukung kecil/redup
4. **Zero ambiguitas status** — status absensi (Hadir/Alpha/Sakit/Izin) selalu jelas secara warna dan teks

---

## 📐 ATURAN STRUKTUR DATA REKAP

### R-01 — Urutan Hierarki Tampilan (WAJIB diikuti)

Setiap halaman rekap harus disusun dari atas ke bawah mengikuti urutan ini:

```
[LEVEL 1] Header Konteks
    └─ Identitas kelas, tahun ajaran, walikelas, rentang tanggal filter

[LEVEL 2] Panel Ringkasan (Summary Cards)
    └─ Total pertemuan | Rata-rata kehadiran | Santri paling alpha | Hari libur

[LEVEL 3] Tabel Rekap Utama
    └─ Dikelompokkan per santri, kolom = mata pelajaran atau tanggal

[LEVEL 4] Baris Statistik Footer Tabel
    └─ Total per kolom (jumlah Hadir/Alpha/Sakit/Izin per mapel/tanggal)

[LEVEL 5] Panel Peringatan Alpha
    └─ Daftar santri yang melampaui threshold — tampil di bagian bawah, warna merah
```

**DILARANG:** Menempatkan panel peringatan alpha di atas tabel utama — ini mengganggu fokus.
**DILARANG:** Menampilkan tabel tanpa summary cards di atasnya.

---

### R-02 — Dua Mode Tampilan Rekap (WAJIB ada toggle)

Rekap harus mendukung dua mode yang bisa di-toggle oleh pengguna:

#### MODE A — Rekap per Mata Pelajaran (default)
```
Kolom  : No | Nama Santri | [MAPEL-1] | [MAPEL-2] | ... | Total Alpha | %Hadir
Baris  : Satu baris = satu santri
Cell   : Badge status (H/A/S/I) atau angka kumulatif
Group  : Tidak ada sub-group — santri diurutkan abjad
```

#### MODE B — Rekap per Tanggal (timeline)
```
Kolom  : No | Nama Santri | [Tgl-1] | [Tgl-2] | ... | Total Alpha | %Hadir
Baris  : Satu baris = satu santri
Cell   : Badge kecil (H/A/S/I) satu per sel
Group  : Header tanggal dikelompokkan per minggu (colspan)
```

**Implementasi toggle di Livewire:**
```php
// Di Livewire component
public string $viewMode = 'by_subject'; // 'by_subject' | 'by_date'

public function toggleMode(string $mode): void
{
    $this->viewMode = $mode;
}
```

```blade
{{-- Toggle button di card-header --}}
<div class="btn-group btn-group-sm" role="group">
    <button
        wire:click="toggleMode('by_subject')"
        type="button"
        class="btn {{ $viewMode === 'by_subject' ? 'btn-primary' : 'btn-outline-primary' }}"
    >
        <i class="bi bi-book me-1"></i>Per Mapel
    </button>
    <button
        wire:click="toggleMode('by_date')"
        type="button"
        class="btn {{ $viewMode === 'by_date' ? 'btn-primary' : 'btn-outline-primary' }}"
    >
        <i class="bi bi-calendar3 me-1"></i>Per Tanggal
    </button>
</div>
```

---

### R-03 — Struktur Summary Cards (WAJIB 4 kartu)

```blade
<div class="row mb-4 g-3">

    {{-- Card 1: Total Pertemuan --}}
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="fs-28 fw-bold text-primary">{{ $summary['total_sessions'] }}</div>
                <div class="fs-12 text-muted mt-1">Total Pertemuan</div>
            </div>
        </div>
    </div>

    {{-- Card 2: Rata-rata Kehadiran --}}
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="fs-28 fw-bold text-success">{{ $summary['avg_attendance'] }}%</div>
                <div class="fs-12 text-muted mt-1">Rata-rata Kehadiran</div>
            </div>
        </div>
    </div>

    {{-- Card 3: Total Santri Alpha Kritis --}}
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100 {{ $summary['critical_alpha_count'] > 0 ? 'border-danger border' : '' }}">
            <div class="card-body text-center py-3">
                <div class="fs-28 fw-bold text-danger">{{ $summary['critical_alpha_count'] }}</div>
                <div class="fs-12 text-muted mt-1">Santri Alpha Kritis</div>
            </div>
        </div>
    </div>

    {{-- Card 4: Total Santri di Kelas --}}
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-3">
                <div class="fs-28 fw-bold" style="color: var(--title)">{{ $summary['total_students'] }}</div>
                <div class="fs-12 text-muted mt-1">Total Santri</div>
            </div>
        </div>
    </div>

</div>
```

---

### R-04 — Aturan Sel Tabel Rekap (WAJIB)

#### Tampilan sel status individual (Mode A & B):
```blade
{{-- Sel tabel: satu status per sel --}}
@php
    $status = $attendanceMap[$studentId][$columnId] ?? null;
    $config = [
        'hadir' => ['class' => 'badge-hadir',   'label' => 'H'],
        'alpha' => ['class' => 'badge-alpha',   'label' => 'A'],
        'sakit' => ['class' => 'badge-sakit',   'label' => 'S'],
        'izin'  => ['class' => 'badge-izin',    'label' => 'I'],
        null    => ['class' => 'bg-light text-muted border', 'label' => '—'],
    ];
    $cell = $config[$status] ?? $config[null];
@endphp
<td class="text-center px-1">
    <span class="badge {{ $cell['class'] }} rounded-1" style="min-width:28px; font-size:11px;">
        {{ $cell['label'] }}
    </span>
</td>
```

#### Sel kolom Total Alpha — harus menonjol jika kritis:
```blade
<td class="text-center fw-bold {{ $totalAlpha >= $alphaThreshold ? 'text-danger' : 'text-body' }}">
    {{ $totalAlpha }}
    @if($totalAlpha >= $alphaThreshold)
        <i class="bi bi-exclamation-circle-fill text-danger ms-1" title="Melampaui threshold"></i>
    @endif
</td>
```

#### Sel kolom %Hadir — gunakan progress bar mini:
```blade
<td class="text-center" style="min-width: 80px;">
    <div class="d-flex flex-column align-items-center gap-1">
        <span class="fs-12 fw-semibold {{ $pct >= 75 ? 'text-success' : ($pct >= 50 ? 'text-warning' : 'text-danger') }}">
            {{ $pct }}%
        </span>
        <div class="progress w-100" style="height: 4px;">
            <div
                class="progress-bar {{ $pct >= 75 ? 'bg-success' : ($pct >= 50 ? 'bg-warning' : 'bg-danger') }}"
                style="width: {{ $pct }}%"
            ></div>
        </div>
    </div>
</td>
```

---

### R-05 — Pengelompokan Header Tabel (WAJIB untuk Mode B / Per Tanggal)

Saat mode per tanggal, kolom tanggal harus dikelompokkan per minggu dengan `colspan`:

```blade
{{-- thead row 1: Minggu --}}
<tr class="table-light">
    <th rowspan="2" class="align-middle">#</th>
    <th rowspan="2" class="align-middle" style="min-width:160px">Nama Santri</th>
    @foreach($weekGroups as $week)
    <th
        colspan="{{ $week['count'] }}"
        class="text-center border-start fs-12 text-muted fw-semibold py-2"
    >
        Minggu {{ $week['number'] }}
        <span class="d-block fw-normal" style="font-size:10px">
            {{ $week['start'] }} – {{ $week['end'] }}
        </span>
    </th>
    @endforeach
    <th rowspan="2" class="text-center align-middle border-start" style="min-width:70px">Alpha</th>
    <th rowspan="2" class="text-center align-middle" style="min-width:80px">% Hadir</th>
</tr>

{{-- thead row 2: Tanggal --}}
<tr class="table-light">
    @foreach($dates as $date)
    <th class="text-center px-1 {{ $date->isWeekend() ? 'bg-light' : '' }}" style="min-width:36px; font-size:11px;">
        <div class="fw-semibold">{{ $date->format('d') }}</div>
        <div class="text-muted" style="font-size:9px">{{ $date->format('D') }}</div>
    </th>
    @endforeach
</tr>
```

---

### R-06 — Baris Footer Statistik Tabel (WAJIB)

Baris terakhir tabel harus berisi statistik per kolom:

```blade
<tfoot class="table-light">
    <tr class="fw-semibold fs-12">
        <td colspan="2" class="text-end pe-3 text-muted">Jumlah per kolom ↓</td>
        @foreach($columns as $col)
        <td class="text-center">
            @php $stat = $columnStats[$col['id']]; @endphp
            <div class="d-flex flex-column gap-0" style="font-size:10px; line-height:1.4">
                <span class="text-success" title="Hadir">{{ $stat['hadir'] }}</span>
                <span class="text-danger"  title="Alpha">{{ $stat['alpha'] }}</span>
                <span class="text-warning" title="Sakit">{{ $stat['sakit'] }}</span>
                <span class="text-info"    title="Izin"> {{ $stat['izin']  }}</span>
            </div>
        </td>
        @endforeach
        <td class="text-center text-danger fw-bold">{{ $totalAllAlpha }}</td>
        <td></td>
    </tr>
</tfoot>
```

---

### R-07 — Panel Peringatan Alpha (WAJIB di bawah tabel)

```blade
@if($criticalStudents->isNotEmpty())
<div class="card border-danger mt-4">
    <div class="card-header bg-danger text-white py-2">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Santri Melampaui Threshold Alpha (≥ {{ $alphaThreshold }}x)</strong>
    </div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama Santri</th>
                    <th class="text-center">Total Alpha</th>
                    <th class="text-center">% Kehadiran</th>
                    <th>Status Notifikasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($criticalStudents as $s)
                <tr>
                    <td class="fw-semibold">{{ $s->name }}</td>
                    <td class="text-center">
                        <span class="badge badge-alpha fs-13">{{ $s->total_alpha }}</span>
                    </td>
                    <td class="text-center">
                        <span class="text-danger fw-bold">{{ $s->attendance_pct }}%</span>
                    </td>
                    <td>
                        @if($s->has_notification)
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-whatsapp me-1"></i>Notifikasi Terkirim
                            </span>
                        @else
                            <span class="badge bg-secondary">Belum Ada Notifikasi</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
```

---

### R-08 — Filter & Kontrol Rekap (WAJIB ada di card-header)

```blade
<div class="card-header flex-wrap gap-2 d-flex align-items-center justify-content-between">

    {{-- Kiri: Judul + Info Kelas --}}
    <div>
        <h5 class="card-title mb-0">Rekap Absensi</h5>
        <p class="fs-12 text-muted mb-0">
            {{ $class->name }} · {{ $activeYear->name }}
            <span class="ms-2 badge bg-secondary">{{ $class->unit->name }}</span>
        </p>
    </div>

    {{-- Kanan: Filter + Toggle + Export --}}
    <div class="d-flex align-items-center gap-2 flex-wrap">

        {{-- Filter Rentang Bulan --}}
        <select wire:model.live="filterMonth" class="form-select form-select-sm" style="width:auto">
            <option value="">Semua Bulan</option>
            @foreach($availableMonths as $m)
                <option value="{{ $m['value'] }}">{{ $m['label'] }}</option>
            @endforeach
        </select>

        {{-- Toggle Mode --}}
        <div class="btn-group btn-group-sm">
            <button wire:click="toggleMode('by_subject')"
                class="btn {{ $viewMode === 'by_subject' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="bi bi-book"></i> Per Mapel
            </button>
            <button wire:click="toggleMode('by_date')"
                class="btn {{ $viewMode === 'by_date' ? 'btn-primary' : 'btn-outline-primary' }}">
                <i class="bi bi-calendar3"></i> Per Tanggal
            </button>
        </div>

        {{-- Export (hanya untuk walikelas/admin/superadmin) --}}
        @can('view-class-recap', $class)
        <a href="{{ route('recap.export', ['classId' => $class->id, 'month' => $filterMonth]) }}"
           class="btn btn-sm btn-outline-success">
            <i class="bi bi-download me-1"></i>Export
        </a>
        @endcan
    </div>

</div>
```

---

### R-09 — Aturan Lebar Kolom & Scroll Tabel

```blade
{{-- WAJIB: Bungkus tabel rekap dengan div scroll horizontal --}}
<div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
    <table class="table table-bordered table-hover table-sm align-middle"
           style="border-collapse: separate; border-spacing: 0;">
        {{-- Kolom nama santri harus sticky --}}
        <colgroup>
            <col style="width: 40px">       {{-- No --}}
            <col style="min-width: 160px; max-width: 200px"> {{-- Nama --}}
            {{-- Kolom status: lebar seragam 36px tiap kolom --}}
        </colgroup>

        <thead class="sticky-top" style="top: 0; z-index: 2;">
            {{-- ... header ... --}}
        </thead>

        <tbody>
            @foreach($students as $student)
            <tr class="{{ $student->total_alpha >= $alphaThreshold ? 'table-danger' : '' }}">
                {{-- Kolom nama: sticky kiri agar bisa scroll horizontal --}}
                <td class="fw-medium sticky-col" style="position: sticky; left: 0; background: white; z-index: 1;">
                    <div>{{ $student->name }}</div>
                    <div class="fs-11 text-muted">{{ $student->nis }}</div>
                </td>
                {{-- ... kolom status ... --}}
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
```

**CSS tambahan di `ppni-custom.css`:**
```css
/* Sticky column kiri pada tabel rekap */
.table-responsive .sticky-col {
    position: sticky !important;
    left: 0;
    background: #fff;
    z-index: 1;
    box-shadow: 2px 0 4px rgba(0,0,0,0.08);
}

/* Row alpha kritis — highlight lembut */
.table-danger .sticky-col {
    background: #fff5f5 !important;
}

/* Header minggu pada mode per-tanggal */
thead tr:first-child th.border-start {
    border-left: 2px solid #dee2e6 !important;
}
```

---

### R-10 — Aturan Backend: Struktur Data yang Dikembalikan ke View

Livewire component `AttendanceRecap` WAJIB mengembalikan data dalam format yang sudah diproses ini — **jangan proses logika pengelompokan di dalam Blade**:

```php
// Di Livewire class AttendanceRecap
public function getRekapDataProperty(): array
{
    // Kembalikan array terstruktur, BUKAN raw Collection mentah

    return [
        'students'     => $this->buildStudentRows(),   // array per santri
        'columns'      => $this->buildColumns(),        // array kolom (mapel/tanggal)
        'column_stats' => $this->buildColumnStats(),    // statistik per kolom
        'week_groups'  => $this->buildWeekGroups(),     // hanya untuk mode by_date
        'summary'      => $this->buildSummary(),        // 4 kartu ringkasan
        'critical'     => $this->buildCriticalList(),   // santri alpha kritis
    ];
}

private function buildStudentRows(): array
{
    // Format: [
    //   'id'             => int,
    //   'name'           => string,
    //   'nis'            => string,
    //   'status_map'     => ['column_id' => 'hadir'|'alpha'|'sakit'|'izin'|null],
    //   'total_alpha'    => int,
    //   'total_hadir'    => int,
    //   'attendance_pct' => float (0-100),
    //   'is_critical'    => bool,
    // ]
}

private function buildSummary(): array
{
    // Format:
    // [
    //   'total_sessions'      => int,
    //   'avg_attendance'      => float,
    //   'critical_alpha_count'=> int,
    //   'total_students'      => int,
    // ]
}
```

**DILARANG:** Melakukan `foreach` dengan query database di dalam loop Blade.
**WAJIB:** Semua data disiapkan di `getRekapDataProperty()` dengan eager loading penuh sebelum dikirim ke view.

---

### R-11 — Legenda Status (WAJIB tampil di bawah tabel)

```blade
<div class="d-flex align-items-center gap-3 mt-2 flex-wrap fs-12 text-muted">
    <span class="fw-semibold">Keterangan:</span>
    <span><span class="badge badge-hadir me-1">H</span>Hadir</span>
    <span><span class="badge badge-alpha me-1">A</span>Alpha</span>
    <span><span class="badge badge-sakit me-1">S</span>Sakit</span>
    <span><span class="badge badge-izin me-1">I</span>Izin</span>
    <span><span class="badge bg-light text-muted border me-1">—</span>Tidak Ada Jadwal / Libur</span>
    <span class="ms-2">
        <i class="bi bi-square-fill text-danger opacity-25 me-1"></i>
        Baris merah = melampaui threshold alpha
    </span>
</div>
```

---

## 🚫 LARANGAN KERAS UNTUK REKAP

```
❌ DILARANG menulis logika hitung statistik di dalam file Blade (.blade.php)
❌ DILARANG menampilkan status absensi sebagai teks mentah ('hadir', 'alpha') — WAJIB badge
❌ DILARANG menggunakan tabel tanpa scroll container jika kolom > 8
❌ DILARANG menghilangkan sticky column untuk nama santri
❌ DILARANG menampilkan rekap tanpa summary cards
❌ DILARANG menggunakan warna selain dari badge-map yang sudah ditetapkan di ppni-custom.css
❌ DILARANG menggunakan Alpine.js (x-data, x-show) untuk toggle mode — WAJIB Livewire wire:click
❌ DILARANG query N+1 di dalam loop render tabel
```

---

## ✅ CHECKLIST SEBELUM SUBMIT KODE REKAP

Sebelum menyerahkan kode komponen `AttendanceRecap`, pastikan semua item ini terpenuhi:

```
□ Summary cards 4 buah sudah ada di atas tabel
□ Toggle "Per Mapel" / "Per Tanggal" berfungsi via wire:click
□ Filter bulan tersedia di card-header
□ Kolom nama santri sticky (tidak ikut scroll horizontal)
□ Sel status menggunakan badge satu huruf (H/A/S/I)
□ Baris santri alpha kritis berwarna merah muda (table-danger)
□ Kolom Total Alpha menampilkan ikon warning jika melampaui threshold
□ Kolom %Hadir menggunakan progress bar mini 4px
□ Footer tabel berisi statistik per kolom (H/A/S/I)
□ Panel peringatan alpha muncul di BAWAH tabel (bukan di atas)
□ Legenda status ada di bawah tabel
□ Tidak ada query database di dalam Blade loop
□ Semua data disiapkan di getRekapDataProperty() dengan eager loading
□ Export button tersedia (jika role bisa export)
□ Tabel dibungkus table-responsive dengan max-height: 70vh
```

---

*Instruksi ini adalah tambahan di atas PPNI Blueprint v5.0 — semua aturan teknis (Bootstrap 5, jQuery, UnitScope, Livewire 3) tetap berlaku.*
