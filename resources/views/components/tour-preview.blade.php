@props(['tour'])

<!-- Tour Preview Overlay -->
<div x-show="showPreview"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="tour-preview-overlay"
     style="display: none;"
     @click.stop>
    <div class="tour-preview-content">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h5 class="fw-bold mb-0">{{ \Illuminate\Support\Str::limit($tour->title, 45) }}</h5>
            @if($tour->categories->isNotEmpty())
                <span class="badge bg-primary-subtle ms-2 flex-shrink-0">{{ $tour->categories->first()->name }}</span>
            @endif
        </div>

        <div class="tour-preview-details">
            <!-- Destination -->
            @if($tour->destination)
            <div class="preview-item">
                <i class="bi bi-geo-alt-fill"></i>
                <div>
                    <small class="d-block">Điểm đến</small>
                    <strong>{{ $tour->destination->name }}</strong>
                </div>
            </div>
            @endif

            <!-- Duration -->
            @if($tour->duration_days && $tour->duration_nights)
            <div class="preview-item">
                <i class="bi bi-clock-fill"></i>
                <div>
                    <small class="d-block">Thời gian</small>
                    <strong>{{ $tour->duration_days }}N{{ $tour->duration_nights }}Đ</strong>
                </div>
            </div>
            @endif

            <!-- Departure Location -->
            @if($tour->departure_location)
            <div class="preview-item">
                <i class="bi bi-pin-map-fill"></i>
                <div>
                    <small class="d-block">Khởi hành</small>
                    <strong>{{ $tour->departure_location->name }}</strong>
                </div>
            </div>
            @endif

            <!-- Next Schedule -->
            @php
                $nextSchedule = $tour->activeSchedules->first();
            @endphp
            @if($nextSchedule)
            <div class="preview-item">
                <i class="bi bi-calendar-check-fill"></i>
                <div>
                    <small class="d-block">Gần nhất</small>
                    <strong>{{ \Carbon\Carbon::parse($nextSchedule->departure_date)->format('d/m/Y') }}</strong>
                    @if($nextSchedule->available_seats)
                        <small class="text-success d-block">{{ $nextSchedule->available_seats }} chỗ</small>
                    @endif
                </div>
            </div>
            @endif

            <!-- Price -->
            <div class="preview-item border-top">
                <i class="bi bi-cash-coin"></i>
                <div class="w-100">
                    <small class="d-block">Giá từ</small>
                    <div class="d-flex align-items-baseline justify-content-between">
                        <strong class="fs-5">{{ format_currency($tour->base_price ?? 0) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-auto pt-2">
            <a href="{{ route('frontend.tours.show', $tour->slug) }}" 
               class="btn btn-sm btn-primary w-100"
               @click.stop>
                <i class="bi bi-arrow-right-circle me-1"></i> Xem chi tiết ngay
            </a>
        </div>
    </div>
</div>
