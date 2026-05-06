@extends('layouts.master')

@section('content')
@php
$familyName = '';
$givenName = '';
if(isset($passenger) && $passenger->full_name) {
$nameParts = explode(' ', trim($passenger->full_name));
$familyName = array_shift($nameParts);
$givenName = implode(' ', $nameParts);
}
@endphp

<div class="container py-5">
    <div class="card border-0 shadow-sm rounded-4 mx-auto" style="max-width: 800px;">
        <div class="card-body p-4 p-md-5">
            <div class="mb-4 text-primary" style="font-size: 20px; font-weight: 500;">
                xác nhận thông tin hành khách chuyến bay
            </div>

            <form action="{{ route('frontend.flights.book') }}" method="POST">
                @csrf
                <input type="hidden" name="offer_id" value="{{ $offerId }}">
                <input type="hidden" name="passenger_id" value="{{ $passengerId }}">
                <input type="hidden" name="tour_booking_id" value="{{ $tourBookingId }}">
                <input type="hidden" name="total_amount" value="{{ $totalAmount }}">
                <input type="hidden" name="total_currency" value="{{ $totalCurrency }}">

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label text-muted small">danh xưng</label>
                        <select name="title" class="form-select" required>
                            <option value="mr" {{ ($passenger->gender ?? '') == 'male' ? 'selected' : '' }}>ông</option>
                            <option value="ms" {{ ($passenger->gender ?? '') == 'female' ? 'selected' : '' }}>bà/cô
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small">họ (không dấu)</label>
                        <input type="text" name="family_name" class="form-control"
                            value="{{ \Illuminate\Support\Str::ascii($familyName) }}" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-muted small">tên đệm và tên (không dấu)</label>
                        <input type="text" name="given_name" class="form-control"
                            value="{{ \Illuminate\Support\Str::ascii($givenName) }}" required>
                    </div>

                    <div class="col-md-4 mt-4">
                        <label class="form-label text-muted small">Ngày sinh</label>
                        <!-- Thêm \Carbon\Carbon::parse() để đảm bảo xuất ra đúng chuẩn YYYY-MM-DD cho thẻ input date -->
                        <input type="date" name="born_on" class="form-control"
                            value="{{ isset($passenger->date_of_birth) ? \Carbon\Carbon::parse($passenger->date_of_birth)->format('Y-m-d') : '' }}"
                            required>
                    </div>
                    <div class="col-md-4 mt-4">
                        <label class="form-label text-muted small">giới tính</label>
                        <select name="gender" class="form-select" required>
                            <option value="m" {{ ($passenger->gender ?? '') == 'male' ? 'selected' : '' }}>nam</option>
                            <option value="f" {{ ($passenger->gender ?? '') == 'female' ? 'selected' : '' }}>nữ</option>
                        </select>
                    </div>
                    <div class="col-md-4 mt-4">
                        <label class="form-label text-muted small">Số điện thoại</label>
                        <!-- Xử lý nếu số điện thoại có số 0 ở đầu thì bỏ đi, sau đó ghép với +84 -->
                        <input type="text" name="phone_number" class="form-control"
                            value="+84{{ ltrim($user->phone ?? '', '0') }}" required placeholder="+84...">
                    </div>

                    <div class="col-md-12 mt-4">
                        <label class="form-label text-muted small">email nhận vé</label>
                        <input type="email" name="email" class="form-control" value="{{ $user->email ?? '' }}" required>
                    </div>
                </div>

                <div class="alert alert-info mt-4 small border-0 bg-light">
                    dữ liệu hành khách đã được tự động đồng bộ và loại bỏ dấu tiếng việt từ hồ sơ đặt tour trước đó.
                </div>

                <button type="submit" class="btn btn-primary w-100 py-3 mt-3">xuất vé máy bay</button>
            </form>
        </div>
    </div>
</div>
@endsection