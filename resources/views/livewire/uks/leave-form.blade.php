@section('title', 'UKS & Izin Santri')
@section('page-title', 'UKS & Izin')

<div class="container-fluid">
    <div class="row page-titles">
        <div class="col-sm-6">
            <h4 class="breadcrumb-title">Pusat Izin Santri</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">UKS & Izin</li>
            </ol>
        </div>
    </div>

    <div class="row">
        {{-- Left: Input Form --}}
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-heart-pulse me-2"></i> Catat Izin / Sakit
                    </h4>
                </div>
                <div class="card-body">
                    {{-- Student Search --}}
                    <div class="mb-3 position-relative">
                        <label class="form-label fw-semibold">Cari Santri <span class="text-danger">*</span></label>
                        @if($selectedStudent)
                            <div class="alert alert-info py-2 d-flex justify-content-between align-items-center mb-0">
                                <div>
                                    <i class="bi bi-person-fill me-1"></i>
                                    <strong>{{ $selectedStudent->name }}</strong>
                                    <span class="text-muted ms-1">({{ $selectedStudent->nis }})</span>
                                </div>
                                <button wire:click="$set('selectedStudentId', null)" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        @else
                            <input type="text" wire:model.live.debounce.300ms="search"
                                   class="form-control"
                                   placeholder="Ketik nama / NIS santri...">
                            @if($suggestions->isNotEmpty())
                                <div class="list-group position-absolute w-100 shadow-lg" style="z-index: 999; max-height: 200px; overflow-y: auto;">
                                    @foreach($suggestions as $s)
                                        <button type="button"
                                                wire:click="selectStudent({{ $s->id }})"
                                                class="list-group-item list-group-item-action">
                                            <strong>{{ $s->name }}</strong>
                                            <span class="text-muted float-end">{{ $s->nis }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Leave Type --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" wire:model="leaveStatus" value="sakit" id="leave-sakit">
                                <label class="form-check-label" for="leave-sakit">
                                    <span class="badge badge-sakit">Sakit</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" wire:model="leaveStatus" value="izin" id="leave-izin">
                                <label class="form-check-label" for="leave-izin">
                                    <span class="badge badge-izin">Izin</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Date Range --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rentang Tanggal <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label text-muted fs-12 mb-1">Dari Tanggal</label>
                                <input type="date" wire:model.live="startDate" class="form-control @error('startDate') is-invalid @enderror">
                                @error('startDate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label text-muted fs-12 mb-1">Sampai Tanggal</label>
                                <input type="date" wire:model.live="endDate" class="form-control @error('endDate') is-invalid @enderror"
                                       min="{{ $startDate }}">
                                @error('endDate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        @if($startDate && $endDate && $startDate <= $endDate)
                            @php
                                $diff = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate));
                            @endphp
                            <small class="text-muted mt-1 d-block">
                                <i class="bi bi-info-circle me-1"></i>
                                {{ $diff === 0 ? '1 hari' : ($diff + 1) . ' hari (weekend & libur akan dilewati otomatis)' }}
                            </small>
                        @endif
                    </div>

                    {{-- Keterangan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <textarea wire:model="leaveNotes" class="form-control" rows="2"
                                  placeholder="Opsional: sakit demam, izin ke rumah sakit, dll."></textarea>
                    </div>

                    <button wire:click="submitLeave"
                            wire:loading.attr="disabled"
                            class="btn btn-primary w-100"
                            @disabled(!$selectedStudentId)>
                        <span wire:loading.remove>
                            <i class="bi bi-plus-circle me-1"></i> Catat Izin
                        </span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Right: Today's Leaves List --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-list-check me-2"></i> Daftar Izin
                    </h4>
                    <div class="d-flex align-items-center gap-2">
                        <input type="date" wire:model.live="dateFilter" class="form-control form-control-sm"
                               style="width: 150px;">
                        <span class="badge bg-primary">{{ $todayLeaves->count() }} santri</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($todayLeaves->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-emoji-smile fs-1 text-success"></i>
                            <p class="text-muted mt-2">Tidak ada santri yang izin/sakit pada tanggal ini.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="40">#</th>
                                        <th>Nama Santri</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                        <th width="70">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayLeaves as $leave)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $leave->student->name }}</strong>
                                            <br><code class="fs-12">{{ $leave->student->nis }}</code>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $leave->status }}">
                                                {{ ucfirst($leave->status) }}
                                            </span>
                                        </td>
                                        <td class="text-muted fs-12">{{ $leave->notes ?? '-' }}</td>
                                        <td>
                                            @if($leave->date->isToday())
                                            <button wire:click="removeLeave({{ $leave->id }})"
                                                    class="btn btn-danger btn-xs sharp"
                                                    title="Batalkan">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
