@extends('layouts.master')

@section('content')
<div class="container py-5">
    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-body p-4 p-md-5">
            <div class="mb-4 text-primary" style="font-size: 20px; font-weight: 500;">
                <i class="bi bi-airplane-engines me-2"></i>Tìm kiếm chuyến bay
            </div>

            <form action="{{ route('frontend.flights.search') }}" method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="tour_booking_id" value="{{ request('tour_booking_id') }}">
                <div class="col-md-3">
                    <label class="form-label text-muted small">Điểm khởi hành</label>
                    <select name="origin" class="form-select border-secondary-subtle" required>
                        <option value="HAN" {{ request('origin') == 'HAN' ? 'selected' : '' }}>Hà Nội (HAN)</option>
                        <option value="SGN" {{ request('origin') == 'SGN' ? 'selected' : '' }}>TP. Hồ Chí Minh (SGN)
                        </option>
                        <option value="DAD" {{ request('origin') == 'DAD' ? 'selected' : '' }}>Đà Nẵng (DAD)</option>
                        <option value="PQC" {{ request('origin') == 'PQC' ? 'selected' : '' }}>Phú Quốc (PQC)</option>
                        <option value="CXR" {{ request('origin') == 'CXR' ? 'selected' : '' }}>Nha Trang (CXR)</option>
                        <option value="DLI" {{ request('origin') == 'DLI' ? 'selected' : '' }}>Đà Lạt (DLI)</option>
                        <option value="HPH" {{ request('origin') == 'HPH' ? 'selected' : '' }}>Hải Phòng (HPH)</option>
                        <option value="VII" {{ request('origin') == 'VII' ? 'selected' : '' }}>Vinh (VII)</option>
                        <option value="HUI" {{ request('origin') == 'HUI' ? 'selected' : '' }}>Huế (HUI)</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label text-muted small">Điểm đến</label>
                    <select name="destination" class="form-select border-secondary-subtle" required>
                        <option value="SGN" {{ request('destination') == 'SGN' ? 'selected' : '' }}>TP. Hồ Chí Minh
                            (SGN)</option>
                        <option value="HAN" {{ request('destination') == 'HAN' ? 'selected' : '' }}>Hà Nội (HAN)
                        </option>
                        <option value="DAD" {{ request('destination') == 'DAD' ? 'selected' : '' }}>Đà Nẵng (DAD)
                        </option>
                        <option value="PQC" {{ request('destination') == 'PQC' ? 'selected' : '' }}>Phú Quốc (PQC)
                        </option>
                        <option value="CXR" {{ request('destination') == 'CXR' ? 'selected' : '' }}>Nha Trang (CXR)
                        </option>
                        <option value="DLI" {{ request('destination') == 'DLI' ? 'selected' : '' }}>Đà Lạt (DLI)
                        </option>
                        <option value="HPH" {{ request('destination') == 'HPH' ? 'selected' : '' }}>Hải Phòng (HPH)
                        </option>
                        <option value="VII" {{ request('destination') == 'VII' ? 'selected' : '' }}>Vinh (VII)</option>
                        <option value="HUI" {{ request('destination') == 'HUI' ? 'selected' : '' }}>Huế (HUI)</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted small">Ngày đi</label>
                    <input type="date" name="departure_date" class="form-control border-secondary-subtle"
                        value="{{ request('departure_date', date('Y-m-d', strtotime('+7 days'))) }}" required>
                </div>

                <div class="col-md-2">
                    <label class="form-label text-muted small">Hành khách & Hạng ghế</label>
                    <div class="input-group">
                        <input type="number" name="passengers" class="form-control border-secondary-subtle"
                            value="{{ request('passengers', 1) }}" min="1" max="9" title="Số hành khách">
                        <select name="cabin_class" class="form-select border-secondary-subtle" style="max-width: 90px;"
                            title="Hạng ghế">
                            <option value="economy" {{ request('cabin_class') == 'economy' ? 'selected' : '' }}>Phổ
                                thông</option>
                            <option value="business" {{ request('cabin_class') == 'business' ? 'selected' : '' }}>Thương
                                gia</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 py-2">Tìm chuyến bay</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        @forelse($offers as $offer)
        <div class="col-12">
            <div class="card border rounded shadow-sm hover-shadow transition">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <div class="text-muted small mb-1">Hãng khai thác</div>
                            <div style="font-weight: 500; font-size: 16px;">{{ $offer['owner']['name'] }}</div>
                        </div>

                        <div
                            class="col-md-6 my-3 my-md-0 d-flex justify-content-between align-items-center text-center px-md-5">
                            <div>
                                <div style="font-size: 24px; font-weight: 500;">
                                    {{ \Carbon\Carbon::parse($offer['slices'][0]['segments'][0]['departing_at'])->format('H:i') }}
                                </div>
                                <div class="text-muted small">{{ $offer['slices'][0]['origin']['iata_code'] }}</div>
                            </div>
                            <div class="flex-grow-1 px-4">
                                <div class="text-muted" style="font-size: 12px;">Bay thẳng</div>
                                <hr class="my-1 border-secondary">
                                @php
                                $depart = \Carbon\Carbon::parse($offer['slices'][0]['segments'][0]['departing_at']);
                                $arrive = \Carbon\Carbon::parse($offer['slices'][0]['segments'][0]['arriving_at']);
                                $diffMins = $depart->diffInMinutes($arrive);
                                @endphp
                                <div class="text-muted" style="font-size: 12px;">
                                    {{ floor($diffMins / 60) }} giờ {{ $diffMins % 60 }} phút
                                </div>
                            </div>
                            <div>
                                <div style="font-size: 24px; font-weight: 500;">
                                    {{ \Carbon\Carbon::parse($offer['slices'][0]['segments'][0]['arriving_at'])->format('H:i') }}
                                </div>
                                <div class="text-muted small">{{ $offer['slices'][0]['destination']['iata_code'] }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 text-md-end border-md-start ps-md-4">
                            <div class="text-muted small mb-1">Tổng giá vé</div>
                            <div class="text-danger mb-2" style="font-size: 22px; font-weight: 500;">
                                {{ number_format($offer['total_amount'], 0, ',', '.') }} {{ $offer['total_currency'] }}
                            </div>
                            <a href="{{ route('frontend.flights.checkout', [
    'offer_id' => $offer['id'], 
    'passenger_id' => $offer['passengers'][0]['id'] ?? '',
    'tour_booking_id' => request('tour_booking_id'),
    'total_amount' => $offer['total_amount'],
    'total_currency' => $offer['total_currency']
]) }}" class="btn btn-primary px-4 py-2 w-100 w-md-auto">Chọn vé</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted" style="font-size: 16px;">Vui lòng chọn tuyến bay và ngày đi để xem kết quả.</div>
        </div>
        @endforelse
    </div>
</div>
@endsection