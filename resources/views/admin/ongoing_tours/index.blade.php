@extends('layouts.admin')

@section('page-title', 'Quản lý Điều Hành Tour')

@section('content')
<div class="admin-card border-0 mb-4">
    <div class="admin-card-body">
        <form action="{{ route('admin.ongoing_tours.index') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>-- Tất cả lịch trình --</option>
                    <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>Sắp khởi hành</option>
                    <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Đang diễn ra</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="admin-card border-0 mb-4">
    <div class="admin-card-header bg-white py-3">
        <h5 class="admin-card-title"><i class="bi bi-geo-alt me-2 text-primary"></i>Danh sách Lịch trình Tour</h5>
    </div>
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Tên Tour</th>
                        <th>Ngày Khởi Hành</th>
                        <th>Số Khách / Sức Chứa</th>
                        <th>Hướng Dẫn Viên</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $schedule)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark text-truncate" style="max-width: 250px;" title="{{ $schedule->tour->title ?? '' }}">
                                {{ $schedule->tour->title ?? 'N/A' }}
                            </div>
                            <small class="text-muted">Mã: #{{ str_pad($schedule->id, 5, '0', STR_PAD_LEFT) }}</small>
                        </td>
                        <td>
                            <div class="fw-500 text-primary">
                                <i class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($schedule->departure_date)->format('d/m/Y') }}
                                @if($schedule->tour && $schedule->tour->departure_time)
                                    <span class="ms-1 text-warning" title="Giờ khởi hành"><i class="bi bi-clock-fill me-1"></i>{{ \Carbon\Carbon::parse($schedule->tour->departure_time)->format('H\hi') }}</span>
                                @endif
                            </div>
                            <small class="text-muted">Đến {{ \Carbon\Carbon::parse($schedule->return_date)->format('d/m/Y') }}</small>
                        </td>
                        <td>
                            @php
                                $guests = $schedule->total_guests ?? 0;
                                $percent = $schedule->capacity > 0 ? round(($guests / $schedule->capacity) * 100) : 0;
                            @endphp
                            <div class="fw-bold">{{ $guests }} / {{ $schedule->capacity }}</div>
                            <div class="progress mt-1" style="height: 5px;">
                                <div class="progress-bar bg-{{ $percent >= 100 ? 'danger' : 'success' }}" style="width: {{ $percent }}%"></div>
                            </div>
                        </td>
                        <td>
                            @if($schedule->schedule_guides->count() > 0)
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($schedule->schedule_guides as $sg)
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-2 py-1">
                                            <i class="bi bi-person-fill me-1"></i>{{ $sg->tour_guide->name ?? 'N/A' }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="badge-soft badge-soft-warning px-2 py-1">Chưa phân công</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-action text-primary bg-primary bg-opacity-10" 
                                data-bs-toggle="modal" data-bs-target="#assignGuideModal{{ $schedule->id }}" title="Phân công HDV">
                                <i class="bi bi-person-plus"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 text-light mb-2 d-block"></i>
                            Chưa có lịch trình nào.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="admin-card-body border-top py-3">
        {{ $schedules->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>

@foreach($schedules as $schedule)
<div class="modal fade" id="assignGuideModal{{ $schedule->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('admin.ongoing_tours.assign_guides', $schedule->id) }}" method="POST">
                @csrf
                <div class="modal-header border-bottom px-4 py-3 bg-light">
                    <h5 class="modal-title fw-600">Phân công Hướng Dẫn Viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold small text-uppercase">Tour: {{ $schedule->tour->title ?? 'N/A' }}</label>
                        <p class="mb-0">Khởi hành: <strong>{{ \Carbon\Carbon::parse($schedule->departure_date)->format('d/m/Y') }}@if($schedule->tour && $schedule->tour->departure_time) ({{ \Carbon\Carbon::parse($schedule->tour->departure_time)->format('H\hi') }})@endif</strong></p>
                    </div>

                    <hr>

                    <div x-data="guideSelector({{ json_encode($schedule->schedule_guides->pluck('guide_id')->toArray()) }}, {{ json_encode($schedule->busy_guide_ids) }})">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <label class="form-label fw-500 mb-0">Chọn Hướng dẫn viên:</label>
                            <span class="badge rounded-pill"
                                :class="selectedCount === 2 ? 'bg-danger' : 'bg-primary'">
                                <span x-text="selectedCount"></span>/2
                            </span>
                        </div>
                        <p class="text-muted small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            Mỗi lịch trình chỉ được phân công tối đa <strong>2 hướng dẫn viên</strong>.
                        </p>

                        <div class="d-flex flex-column gap-2" style="max-height: 250px; overflow-y: auto;">
                            @forelse($tourGuides as $guide)
                            <div class="form-check p-0">
                                <label class="d-flex align-items-center gap-3 p-2 rounded-2 border guide-option-label"
                                    :class="{
                                        'border-primary bg-primary bg-opacity-10': selected.includes('{{ $guide->id }}'),
                                        'opacity-50 pe-none': busyGuides.includes('{{ $guide->id }}') || (!selected.includes('{{ $guide->id }}') && selectedCount >= 2),
                                        'guide-hover': selected.includes('{{ $guide->id }}') || (!busyGuides.includes('{{ $guide->id }}') && selectedCount < 2)
                                    }"
                                    for="guide_{{ $schedule->id }}_{{ $guide->id }}"
                                    style="cursor: pointer;">
                                    <input class="form-check-input m-0 flex-shrink-0"
                                        type="checkbox"
                                        name="guide_ids[]"
                                        value="{{ $guide->id }}"
                                        id="guide_{{ $schedule->id }}_{{ $guide->id }}"
                                        x-model="selected"
                                        :disabled="busyGuides.includes('{{ $guide->id }}') || (!selected.includes('{{ $guide->id }}') && selectedCount >= 2)"
                                        @change="onCheck">
                                    <div class="flex-grow-1">
                                        <div class="fw-500 d-flex align-items-center gap-2">
                                            <span>{{ $guide->name }}</span>
                                            <template x-if="busyGuides.includes('{{ $guide->id }}')">
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle px-2 py-0.5" style="font-size: 10px; font-weight: 500;">Bận trùng lịch</span>
                                            </template>
                                        </div>
                                        <small class="text-muted">{{ $guide->phone }}</small>
                                    </div>
                                    <i class="bi bi-check-circle-fill text-primary ms-auto"
                                        x-show="selected.includes('{{ $guide->id }}')" style="display:none!important"
                                        x-transition></i>
                                </label>
                            </div>
                            @empty
                            <div class="text-muted small">Chưa có hướng dẫn viên nào trong hệ thống. Vui lòng thêm mới.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-admin-primary">Lưu Phân Công</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@push('scripts')
<script>
function guideSelector(initialSelected, busyGuides) {
    return {
        selected: Array.isArray(initialSelected) ? initialSelected.map(id => id.toString()) : [],
        busyGuides: Array.isArray(busyGuides) ? busyGuides.map(id => id.toString()) : [],
        get selectedCount() {
            return this.selected.length;
        },
        onCheck() {
            // Alpine x-model handles add/remove automatically
        }
    };
}
</script>
<style>
.guide-option-label {
    transition: all 0.18s ease;
    user-select: none;
}
.guide-option-label:not(.opacity-50):hover {
    background-color: rgba(var(--bs-primary-rgb), 0.05);
    border-color: var(--bs-primary) !important;
}
</style>
@endpush


@endsection
