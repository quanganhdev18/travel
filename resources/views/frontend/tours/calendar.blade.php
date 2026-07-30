@extends('layouts.master')

@section('title', 'Lịch Khởi Hành Tour - Travel Wonder')

@section('content')
<div style="padding-top: 90px; min-height: 100vh; background: #f8fafc;">

    {{-- Page Hero --}}
    <section style="background: linear-gradient(135deg, #007CE8 0%, #005bb5 100%); padding: 48px 0 60px;">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3" style="font-size: 0.9rem; opacity: 0.8;">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-white text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('frontend.tours.index') }}" class="text-white text-decoration-none">Tour trọn gói</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Lịch khởi hành</li>
                </ol>
            </nav>
            <h1 class="text-white mb-2" style="font-size: 2.2rem; font-weight: 800;">
                <i class="bi bi-calendar2-week me-3"></i>Lịch Khởi Hành Tour
            </h1>
            <p class="text-white mb-0" style="opacity: 0.85; font-size: 1.05rem;">
                Tra cứu lịch trình khởi hành của tất cả các tour một cách nhanh chóng và tiện lợi.
            </p>
        </div>
    </section>

    {{-- Main Content --}}
    <div class="container" style="margin-top: -30px; padding-bottom: 60px;">

        {{-- Calendar Card --}}
        <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
            <div class="card-body p-4">

                {{-- Row 1: Filters --}}
                <div class="d-flex flex-wrap gap-2 mb-3">

                    {{-- Destination Filter (từ CSDL) --}}
                    <div class="position-relative" style="min-width: 190px; flex: 1;">
                        <i class="bi bi-geo-alt-fill position-absolute" style="top:50%;transform:translateY(-50%);left:14px;color:#64748b;pointer-events:none;z-index:1;"></i>
                        <select id="filter-dest" class="form-select ps-5 border-0 bg-light" style="border-radius:12px;font-weight:500;color:#1e3a5f;cursor:pointer;" onchange="fetchCalendarData()">
                            <option value="">Tất cả điểm đến</option>
                            @foreach($destinations as $dest)
                                <option value="{{ $dest->id }}">{{ $dest->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Category Filter (danh mục tour từ CSDL) --}}
                    <div class="position-relative" style="min-width: 190px; flex: 1;">
                        <i class="bi bi-compass-fill position-absolute" style="top:50%;transform:translateY(-50%);left:14px;color:#64748b;pointer-events:none;z-index:1;"></i>
                        <select id="filter-category" class="form-select ps-5 border-0 bg-light" style="border-radius:12px;font-weight:500;color:#1e3a5f;cursor:pointer;" onchange="fetchCalendarData()">
                            <option value="">Tất cả danh mục</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Budget Filter (đồng bộ với trang Tour index) --}}
                    <div class="position-relative" style="min-width: 180px; flex: 1;">
                        <i class="bi bi-tags-fill position-absolute" style="top:50%;transform:translateY(-50%);left:14px;color:#64748b;pointer-events:none;z-index:1;"></i>
                        <select id="filter-budget" class="form-select ps-5 border-0 bg-light" style="border-radius:12px;font-weight:500;color:#1e3a5f;cursor:pointer;" onchange="fetchCalendarData()">
                            <option value="">Tất cả mức giá</option>
                            <option value="under_1m">Dưới 1 triệu</option>
                            <option value="1m_2m">1 - 2 triệu</option>
                            <option value="2m_4m">2 - 4 triệu</option>
                            <option value="over_4m">Trên 4 triệu</option>
                        </select>
                    </div>

                    {{-- Duration Filter (đồng bộ với trang Tour index) --}}
                    <div class="position-relative" style="min-width: 170px; flex: 1;">
                        <i class="bi bi-clock-fill position-absolute" style="top:50%;transform:translateY(-50%);left:14px;color:#64748b;pointer-events:none;z-index:1;"></i>
                        <select id="filter-duration" class="form-select ps-5 border-0 bg-light" style="border-radius:12px;font-weight:500;color:#1e3a5f;cursor:pointer;" onchange="fetchCalendarData()">
                            <option value="">Tất cả thời gian</option>
                            <option value="2d1n">2N1Đ</option>
                            <option value="3d2n">3N2Đ</option>
                            <option value="4d3n">4N3Đ</option>
                            <option value="5d4n">5N4Đ</option>
                            <option value="6d5n">6N5Đ</option>
                            <option value="7d6n">7N6Đ</option>
                        </select>
                    </div>

                    {{-- Reset Filters Button --}}
                    <div class="position-relative" style="flex: 0 0 auto;">
                        <button onclick="resetFilters()" class="btn" style="background:#e2e8f0;color:#475569;border-radius:12px;padding:9px 16px;font-weight:600;font-size:0.9rem;border:none;display:flex;align-items:center;gap:6px;transition:all 0.2s;height:100%;" onmouseover="this.style.background='#cbd5e1';this.style.color='#1e293b';" onmouseout="this.style.background='#e2e8f0';this.style.color='#475569';" title="Làm mới bộ lọc">
                            <i class="bi bi-arrow-clockwise"></i> Làm mới
                        </button>
                    </div>
                </div>

                {{-- Row 2: Legend + Month Nav + Today button --}}
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">

                    {{-- Legend --}}
                    <div class="d-flex gap-3 flex-wrap align-items-center" style="font-size: 0.82rem; color: #475569;">
                        <span class="d-flex align-items-center gap-1">
                            <span style="width:8px;height:8px;border-radius:50%;background:#10b981;display:inline-block;"></span> Còn chỗ
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <span style="width:8px;height:8px;border-radius:50%;background:#f59e0b;display:inline-block;"></span> Sắp hết
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <span style="width:8px;height:8px;border-radius:50%;background:#ef4444;display:inline-block;"></span> Hết chỗ
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <span style="width:10px;height:10px;border-radius:50%;border:2px solid #ef4444;display:inline-block;"></span> Ngày lễ
                        </span>
                    </div>

                    {{-- Month Nav + Today --}}
                    <div class="d-flex align-items-center gap-2" style="flex-shrink: 0;">
                        {{-- Nút Hôm nay --}}
                        <button onclick="goToToday()" class="btn btn-sm" style="background: #007CE8; color: white; border-radius: 8px; padding: 7px 16px; font-weight: 600; font-size: 0.85rem; border: none; display: flex; align-items: center; gap: 6px; transition: all 0.2s;" onmouseover="this.style.background='#005bb5';" onmouseout="this.style.background='#007CE8';">
                            <i class="bi bi-calendar-check"></i> Hôm nay
                        </button>

                        {{-- Điều hướng tháng --}}
                        <div class="d-flex align-items-center gap-2" style="background:#f8fafc;padding:6px;border-radius:12px;border:1px solid #e2e8f0;">
                            <button onclick="prevMonth()" style="border-radius:8px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;background:white;color:#1e3a5f;border:1px solid #e2e8f0;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='#007CE8';this.style.color='white';this.style.borderColor='#007CE8';" onmouseout="this.style.background='white';this.style.color='#1e3a5f';this.style.borderColor='#e2e8f0';">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <div id="month-display" style="font-weight:700;font-size:1.05rem;color:#1e3a5f;min-width:150px;text-align:center;text-transform:uppercase;letter-spacing:0.5px;"></div>
                            <button onclick="nextMonth()" style="border-radius:8px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;background:white;color:#1e3a5f;border:1px solid #e2e8f0;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='#007CE8';this.style.color='white';this.style.borderColor='#007CE8';" onmouseout="this.style.background='white';this.style.color='#1e3a5f';this.style.borderColor='#e2e8f0';">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Loading --}}
                <div id="calendar-loading" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary" role="status" style="width:40px;height:40px;"></div>
                    <p class="mt-3 text-muted">Đang tải lịch...</p>
                </div>

                {{-- Calendar Grid (Chứa cả tiêu đề và nội dung) --}}
                <div id="calendar-grid" style="
                    display: grid;
                    grid-template-columns: repeat(7, minmax(130px, 1fr));
                    grid-auto-rows: minmax(130px, auto);
                    gap: 1px;
                    background: #e2e8f0;
                    border: 1px solid #e2e8f0;
                    border-radius: 16px;
                    overflow: auto;
                    max-height: 65vh;
                    scroll-behavior: smooth;
                    scroll-snap-type: y mandatory;
                    transition: opacity 0.2s;
                    position: relative;
                ">
                </div>

                {{-- Pagination / Scroll down indicator --}}
                <div id="calendar-pagination" class="text-center mt-3 d-none">
                    <button onclick="scrollCalendar()" id="scroll-btn" class="btn btn-outline-primary rounded-pill px-4 fw-bold shadow-sm" style="background:white; border-color:#cbd5e1; color:#007CE8; transition:all 0.2s;" onmouseover="this.style.background='#f8fafc';this.style.borderColor='#007CE8';" onmouseout="this.style.background='white';this.style.borderColor='#cbd5e1';">
                        <i class="bi bi-chevron-down"></i> Xem các tuần tiếp theo
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal --}}
<div id="tour-modal" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(15,23,42,0.75);display:none;justify-content:center;align-items:center;z-index:9999;backdrop-filter:blur(8px);opacity:0;transition:opacity 0.3s;" onclick="closeModal(event)">
    <div id="modal-inner" onclick="event.stopPropagation()" style="background:#f8fafc;width:90%;max-width:760px;border-radius:24px;max-height:85vh;display:flex;flex-direction:column;box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);transform:translateY(30px);transition:transform 0.3s cubic-bezier(0.16,1,0.3,1);overflow:hidden;">
        <div style="padding:22px 28px;background:white;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;">
            <h3 id="modal-title" style="margin:0;font-size:1.25rem;font-weight:800;color:#1e3a5f;display:flex;align-items:center;gap:10px;"></h3>
            <button onclick="closeModal()" style="background:#f1f5f9;border:none;width:38px;height:38px;border-radius:50%;cursor:pointer;font-size:18px;display:flex;align-items:center;justify-content:center;color:#64748b;transition:all 0.2s;" onmouseover="this.style.background='#e2e8f0';this.style.color='#ef4444';" onmouseout="this.style.background='#f1f5f9';this.style.color='#64748b';">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div id="modal-body" style="padding:20px 28px;overflow-y:auto;display:flex;flex-direction:column;gap:14px;">
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // --- State ---
    const TODAY      = new Date();
    let currentYear  = TODAY.getFullYear();
    let currentMonth = TODAY.getMonth() + 1;
    const todayStr   = `${TODAY.getFullYear()}-${String(TODAY.getMonth()+1).padStart(2,'0')}-${String(TODAY.getDate()).padStart(2,'0')}`;

    const API_URL = "{{ route('frontend.tours.calendar.data') }}";

    let holidayMap = {}; // { 'YYYY-MM-DD': ['Tên lễ', ...] }

    const formatPrice = (p) =>
        new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(p);

    const getStatus = (seats, status) => {
        if (status === 'full' || seats === 0)
            return { color: '#ef4444', text: 'Đã hết chỗ' };
        if (seats <= 5)
            return { color: '#f59e0b', text: `Còn ${seats} chỗ` };
        return { color: '#10b981', text: `Còn ${seats} chỗ` };
    };

    const getDotColor = (seats, status) => getStatus(seats, status).color;

    const updateHeader = () => {
        document.getElementById('month-display').innerText =
            `Tháng ${currentMonth} / ${currentYear}`;
    };

    const prevMonth = () => {
        if (--currentMonth < 1) { currentMonth = 12; currentYear--; }
        fetchCalendarData();
    };

    const nextMonth = () => {
        if (++currentMonth > 12) { currentMonth = 1; currentYear++; }
        fetchCalendarData();
    };

    const goToToday = () => {
        currentYear  = TODAY.getFullYear();
        currentMonth = TODAY.getMonth() + 1;
        fetchCalendarData();
    };

    const resetFilters = () => {
        document.getElementById('filter-dest').value = '';
        document.getElementById('filter-category').value = '';
        document.getElementById('filter-budget').value = '';
        document.getElementById('filter-duration').value = '';
        fetchCalendarData();
    };

    const fetchCalendarData = () => {
        updateHeader();
        const grid   = document.getElementById('calendar-grid');
        const loader = document.getElementById('calendar-loading');

        loader.classList.remove('d-none');
        grid.style.opacity = '0.4';

        const params = new URLSearchParams({
            year:  currentYear,
            month: currentMonth,
        });

        const dest     = document.getElementById('filter-dest').value;
        const cat      = document.getElementById('filter-category').value;
        const budget   = document.getElementById('filter-budget').value;
        const duration = document.getElementById('filter-duration').value;

        if (dest)     params.append('destination_id', dest);
        if (cat)      params.append('category_id', cat);
        if (budget)   params.append('budget', budget);
        if (duration) params.append('duration', duration);

        fetch(`${API_URL}?${params}`)
            .then(r => r.json())
            .then(data => {
                holidayMap = data.holidays || {};
                renderCalendar(data.tours || {});
            })
            .catch(() => { holidayMap = {}; renderCalendar({}); })
            .finally(() => {
                loader.classList.add('d-none');
                grid.style.opacity = '1';
            });
    };

    const renderCalendar = (data) => {
        const grid = document.getElementById('calendar-grid');
        grid.innerHTML = '';

        // Render sticky weekday headers
        const weekdays = ['CN', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
        weekdays.forEach(wd => {
            const hd = document.createElement('div');
            hd.style.cssText = 'text-align:center;font-weight:700;color:#64748b;padding:12px 4px;text-transform:uppercase;font-size:0.78rem;letter-spacing:1px;background:#f8fafc;position:sticky;top:0;z-index:10;box-shadow:0 1px 2px rgba(0,0,0,0.05);';
            hd.innerText = wd;
            grid.appendChild(hd);
        });

        const firstDay    = new Date(currentYear, currentMonth - 1, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth, 0).getDate();
        const prevDays    = new Date(currentYear, currentMonth - 1, 0).getDate();

        const cells = [];
        for (let i = 0; i < firstDay; i++)
            cells.push({ day: prevDays - firstDay + i + 1, type: 'muted' });
        for (let d = 1; d <= daysInMonth; d++) {
            const ds = `${currentYear}-${String(currentMonth).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            cells.push({ day: d, type: 'current', dateStr: ds });
        }
        let nx = 1;
        while (cells.length < 42) cells.push({ day: nx++, type: 'muted' });

        cells.forEach(cell => {
            const el = document.createElement('div');

            if (cell.type === 'muted') {
                el.style.cssText = 'background:#fafbfc;padding:10px;display:flex;flex-direction:column;';
                el.innerHTML = `<div style="font-size:0.95rem;font-weight:700;color:#cbd5e1;">${cell.day}</div>`;
                grid.appendChild(el);
                return;
            }

            const tours    = data[cell.dateStr] || [];
            const isToday  = cell.dateStr === todayStr;
            const hasTour  = tours.length > 0;
            const holidays = holidayMap[cell.dateStr] || [];
            const isHoliday = holidays.length > 0;

            el.style.cssText = `
                background: white;
                padding: 10px;
                display: flex;
                flex-direction: column;
                transition: background 0.2s;
                position: relative;
                ${hasTour ? 'cursor: pointer;' : ''}
                ${isHoliday ? 'background: #fff8f8;' : ''}
            `;

            if (hasTour) {
                el.addEventListener('mouseenter', () => { el.style.background = isHoliday ? '#fff0f0' : '#f0f7ff'; });
                el.addEventListener('mouseleave', () => { el.style.background = isHoliday ? '#fff8f8' : 'white'; });
                el.onclick = () => openModal(cell.dateStr, tours, holidays);
            }

            // --- Day number (kết hợp hôm nay + ngày lễ) ---
            let dayNumHtml;
            if (isToday && isHoliday) {
                // Vừa hôm nay vừa là lễ: xanh + viền đỏ
                dayNumHtml = `<span style="background:#007CE8;color:white;border-radius:8px;padding:2px 8px;font-size:1.05rem;font-weight:800;display:inline-flex;align-items:center;outline:3px solid #ef4444;outline-offset:2px;flex-shrink:0;">${cell.day}</span>`;
            } else if (isToday) {
                dayNumHtml = `<span style="background:#007CE8;color:white;border-radius:8px;padding:2px 8px;font-size:1.05rem;font-weight:800;display:inline-flex;align-items:center;flex-shrink:0;">${cell.day}</span>`;
            } else if (isHoliday) {
                dayNumHtml = `<span style="border:2.5px solid #ef4444;color:#ef4444;border-radius:50%;width:28px;height:28px;display:inline-flex;align-items:center;justify-content:center;font-size:0.9rem;font-weight:800;flex-shrink:0;">${cell.day}</span>`;
            } else {
                dayNumHtml = `<span style="font-size:1rem;font-weight:700;color:#1e3a5f;flex-shrink:0;">${cell.day}</span>`;
            }

            // --- Tên ngày lễ hiển thị cùng hàng với số ngày (truncated, tooltip đầy đủ) ---
            let holidayLineHtml = '';
            if (isHoliday) {
                const nameText = holidays[0]; // Chỉ hiện tên lễ đầu tiên
                const moreCount = holidays.length > 1 ? ` +${holidays.length - 1}` : '';
                holidayLineHtml = `
                    <div title="${holidays.join(', ')}" style="font-size:0.65rem;font-weight:700;color:#ef4444;background:#fee2e2;padding:2px 6px;border-radius:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;line-height:1.3;cursor:help;margin-left:6px;flex:1;min-width:0;">${nameText}${moreCount}</div>`;
            }

            let html = `
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px;">
                    <div style="display:flex;align-items:center;min-width:0;flex:1;">
                        ${dayNumHtml}
                        ${holidayLineHtml}
                    </div>
                    ${hasTour ? `<span style="font-size:0.62rem;background:#e6f2fd;color:#005bb5;padding:2px 6px;border-radius:20px;font-weight:700;flex-shrink:0;margin-left:4px;">${tours.length}</span>` : ''}
                </div>
            `;

            if (hasTour) {
                html += `<div style="display:flex;flex-direction:column;gap:4px;flex-grow:1;overflow:hidden;">`;
                tours.slice(0, 3).forEach(t => {
                    const dotColor = getDotColor(t.available_seats, t.status);
                    html += `
                        <div style="font-size:0.72rem;color:#1e3a5f;font-weight:600;display:flex;align-items:center;gap:5px;padding:3px 4px;border-radius:5px;overflow:hidden;">
                            <span style="width:6px;height:6px;border-radius:50%;background:${dotColor};flex-shrink:0;"></span>
                            <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${t.tour_name}</span>
                        </div>`;
                });
                if (tours.length > 3) {
                    html += `<div style="font-size:0.68rem;color:#007CE8;font-weight:700;margin-top:auto;padding:2px 4px;">+${tours.length - 3} tour khác</div>`;
                }
                html += `</div>`;
            } else if (isHoliday) {
                // Không hiển thị gì thêm nếu là ngày lễ nhưng không có tour (giữ cho gọn)
            } else {
                html += `<div style="font-size:0.75rem;color:#cbd5e1;text-align:center;margin-top:auto;padding-bottom:4px;">—</div>`;
            }

            el.innerHTML = html;
            grid.appendChild(el);
        });
        
        // Kiểm tra chiều cao sau khi render để quyết định hiện nút cuộn trang không
        setTimeout(checkCalendarScroll, 50); // Đợi DOM cập nhật layout
    };


    // --- Modal ---
    const openModal = (dateStr, tours, holidays) => {
        const [y, m, d] = dateStr.split('-');

        let holidayBadge = '';
        if (holidays && holidays.length) {
            holidayBadge = holidays.map(h =>
                `<span style="font-size:0.72rem;background:#fee2e2;color:#ef4444;padding:3px 8px;border-radius:6px;font-weight:700;">${h}</span>`
            ).join('');
        }

        document.getElementById('modal-title').innerHTML = `
            <i class="bi bi-geo-alt-fill" style="color:#007CE8;"></i>
            Khởi hành ngày ${d}/${m}/${y}
            ${holidayBadge}
        `;

        const body = document.getElementById('modal-body');
        body.innerHTML = '';

        tours.forEach(t => {
            const st     = getStatus(t.available_seats, t.status);
            const isFull = t.status === 'full' || t.available_seats === 0;
            const fallback = 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?q=80&w=800&auto=format&fit=crop';
            const imgSrc   = t.image_url || fallback;

            const card = document.createElement('div');
            card.style.cssText = `
                background: white;
                border-radius: 14px;
                display: flex;
                height: 170px;
                overflow: hidden;
                border: 1px solid #e2e8f0;
                box-shadow: 0 2px 12px rgba(0,0,0,0.04);
                transition: transform 0.2s, box-shadow 0.2s;
                flex-shrink: 0;
            `;
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-3px)';
                card.style.boxShadow = '0 12px 28px rgba(0,0,0,0.08)';
                card.style.borderColor = '#cbd5e1';
            });
            card.addEventListener('mouseleave', () => {
                card.style.transform = '';
                card.style.boxShadow = '0 2px 12px rgba(0,0,0,0.04)';
                card.style.borderColor = '#e2e8f0';
            });

            card.innerHTML = `
                <div style="position:relative;width:220px;height:170px;flex-shrink:0;overflow:hidden;">
                    <span style="position:absolute;top:10px;left:10px;background:rgba(255,255,255,0.95);padding:4px 9px;border-radius:7px;font-size:0.72rem;font-weight:800;color:#1e3a5f;z-index:2;box-shadow:0 2px 8px rgba(0,0,0,0.12);">${t.duration}</span>
                    <img src="${imgSrc}" alt="${t.tour_name}" style="width:100%;height:100%;object-fit:cover;display:block;" onerror="this.src='${fallback}'">
                </div>
                <div style="padding:16px 20px;flex-grow:1;display:flex;flex-direction:column;overflow:hidden;">
                    <h4 style="font-weight:800;font-size:1rem;color:#1e3a5f;margin:0 0 6px;line-height:1.35;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">${t.tour_name}</h4>
                    <div style="color:#64748b;font-size:0.82rem;margin-bottom:10px;display:flex;align-items:center;gap:5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        <i class="bi bi-map" style="flex-shrink:0;"></i> ${t.destination || 'Việt Nam'}
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:auto;gap:8px;flex-wrap:nowrap;">
                        <div style="flex-shrink:0;">
                            <div style="font-size:0.72rem;color:#94a3b8;margin-bottom:2px;">Giá từ</div>
                            <div style="color:#007CE8;font-weight:800;font-size:1.05rem;white-space:nowrap;">${formatPrice(t.price)}</div>
                            <div style="font-size:0.75rem;font-weight:700;margin-top:2px;color:${st.color};">${st.text}</div>
                        </div>
                        <a href="${t.tour_url}" style="background:${isFull ? '#e2e8f0' : '#007CE8'};color:${isFull ? '#94a3b8' : 'white'};border:none;padding:9px 16px;border-radius:10px;font-weight:700;font-size:0.82rem;text-decoration:none;pointer-events:${isFull ? 'none' : 'auto'};display:inline-flex;align-items:center;gap:5px;white-space:nowrap;flex-shrink:0;">
                            ${isFull ? 'Hết vé' : '<i class="bi bi-arrow-right-circle"></i> Xem tour'}
                        </a>
                    </div>
                </div>
            `;

            body.appendChild(card);
        });

        const overlay = document.getElementById('tour-modal');
        overlay.style.display = 'flex';
        requestAnimationFrame(() => {
            overlay.style.opacity = '1';
            document.getElementById('modal-inner').style.transform = 'translateY(0)';
        });
        document.body.style.overflow = 'hidden';
    };

    const closeModal = (e) => {
        if (e && e.target !== document.getElementById('tour-modal')) return;
        const overlay = document.getElementById('tour-modal');
        overlay.style.opacity = '0';
        document.getElementById('modal-inner').style.transform = 'translateY(30px)';
        setTimeout(() => { overlay.style.display = 'none'; }, 300);
        document.body.style.overflow = 'auto';
    };

    // --- Pagination / Scroll Logic ---
    const checkCalendarScroll = () => {
        const grid = document.getElementById('calendar-grid');
        const btn = document.getElementById('calendar-pagination');
        if (!btn) return;
        if (grid.scrollHeight > grid.clientHeight + 10) {
            btn.classList.remove('d-none');
            updateScrollButton();
        } else {
            btn.classList.add('d-none');
        }
    };

    const updateScrollButton = () => {
        const grid = document.getElementById('calendar-grid');
        const btnNode = document.getElementById('scroll-btn');
        if (!btnNode) return;
        if (grid.scrollTop + grid.clientHeight >= grid.scrollHeight - 20) {
            btnNode.innerHTML = `<i class="bi bi-chevron-up"></i> Quay lại đầu tháng`;
        } else {
            btnNode.innerHTML = `<i class="bi bi-chevron-down"></i> Xem các tuần tiếp theo`;
        }
    };

    const scrollCalendar = () => {
        const grid = document.getElementById('calendar-grid');
        if (grid.scrollTop + grid.clientHeight >= grid.scrollHeight - 20) {
            grid.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            grid.scrollBy({ top: grid.clientHeight - 80, behavior: 'smooth' });
        }
    };

    document.getElementById('calendar-grid').addEventListener('scroll', updateScrollButton);

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    document.addEventListener('DOMContentLoaded', fetchCalendarData);
</script>
@endpush
