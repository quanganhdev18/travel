<li class="nav-item dropdown" x-data="notificationComponent()" x-init="init()">
    <a class="nav-link dropdown-toggle d-flex align-items-center py-1 position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-weight: 600;">
        <i class="bi bi-bell fs-5"></i>
        <span x-show="unreadCount > 0" x-text="unreadCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;" x-cloak></span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="notificationDropdown" style="border-radius: 12px; margin-top: 5px; width: 350px; max-height: 400px; overflow-y: auto;">
        <li class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center bg-light" style="position: sticky; top: 0; z-index: 10;">
            <strong class="mb-0">Thông báo</strong>
            <button @click="markAllAsRead()" class="btn btn-sm btn-link text-decoration-none p-0" style="font-size: 0.8rem;" x-show="unreadCount > 0">Đánh dấu đã đọc</button>
        </li>
        <template x-if="notifications.length === 0">
            <li class="px-3 py-4 text-center text-muted">
                <i class="bi bi-bell-slash fs-4 mb-2 d-block"></i>
                Chưa có thông báo nào
            </li>
        </template>
        <template x-for="notification in notifications" :key="notification.id">
            <li>
                <a class="dropdown-item py-3 border-bottom text-wrap" :href="notification.data ? (notification.data.link || '#') : (notification.link || '#')" @click="markAsRead(notification.id)" :class="{ 'bg-light': !notification.read_at }">
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <div class="me-3">
                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" :class="getIconBgClass(notification.data ? notification.data.type : notification.type)">
                                <i :class="getIconClass(notification.data ? notification.data.type : notification.type)"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <strong class="d-block mb-1" style="font-size: 0.95rem;" x-text="notification.data ? (notification.data.title || 'Thông báo mới') : (notification.title || 'Thông báo mới')"></strong>
                            <p class="mb-1 text-secondary" style="font-size: 0.85rem;" x-text="notification.data ? notification.data.message : notification.message"></p>
                            <small class="text-muted" x-text="new Date(notification.created_at).toLocaleString('vi-VN')"></small>
                        </div>
                        <div x-show="!notification.read_at" class="ms-2 mt-1">
                            <span class="p-1 bg-primary rounded-circle d-inline-block"></span>
                        </div>
                    </div>
                </a>
            </li>
        </template>
        <li class="text-center py-2 bg-light" style="position: sticky; bottom: 0;">
            <a href="{{ url('/user/profile?tab=notifications') }}" class="text-decoration-none" style="font-size: 0.85rem;">Xem tất cả</a>
        </li>
    </ul>

    <!-- Toast Container for Notifications (Tailwind + Alpine) -->
    <div class="fixed bottom-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none" style="position: fixed; z-index: 9999; bottom: 1rem; right: 1rem; display: flex; flex-direction: column; gap: 0.5rem; pointer-events: none;">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 sm:scale-100"
                 x-transition:leave-end="opacity-0 sm:scale-95"
                 class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden" style="max-width: 24rem; width: 100%; background-color: white; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); border-radius: 0.5rem; pointer-events: auto; border: 1px solid rgba(0,0,0,0.05); overflow: hidden;">
                <div class="p-4" style="padding: 1rem;">
                    <div class="flex items-start" style="display: flex; align-items: flex-start;">
                        <div class="flex-shrink-0" style="flex-shrink: 0;">
                            <div class="rounded-full flex items-center justify-content-center text-white" style="width: 32px; height: 32px; display:flex; align-items:center; justify-content:center; border-radius: 9999px;" :class="getIconBgClass(toast.type).replace('bg-', 'bg-').replace('primary', 'blue-500').replace('success', 'green-500').replace('warning', 'orange-500')">
                                <i :class="getIconClass(toast.type)"></i>
                            </div>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5" style="margin-left: 0.75rem; width: 0; flex: 1 1 0%; padding-top: 0.125rem;">
                            <strong class="text-sm font-bold text-gray-900" style="font-size: 0.875rem; font-weight: 700; color: #111827; margin: 0; display: block;" x-text="toast.title || 'Thông báo mới'"></strong>
                            <p class="text-sm font-medium text-gray-600 mt-1" style="font-size: 0.875rem; font-weight: 500; color: #4b5563; margin: 0; margin-top: 0.25rem;" x-text="toast.message"></p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex" style="margin-left: 1rem; flex-shrink: 0; display: flex;">
                            <button @click="removeToast(toast.id)" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none" style="background-color: white; border-radius: 0.375rem; display: inline-flex; color: #9ca3af; border: none; cursor: pointer; padding: 0;">
                                <span class="sr-only">Close</span>
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</li>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('notificationComponent', () => ({
        notifications: [],
        unreadCount: 0,
        toasts: [],
        userId: {{ auth()->id() ?? 'null' }},

        init() {
            if (!this.userId) return;

            this.fetchNotifications();
            this.listenForNotifications();
        },

        fetchNotifications() {
            fetch('/notifications')
                .then(res => res.json())
                .then(data => {
                    if (data.notifications) {
                        this.notifications = data.notifications.data;
                        this.unreadCount = data.unread_count;
                    }
                })
                .catch(err => console.error(err));
        },

        listenForNotifications() {
            if (window.Echo) {
                window.Echo.private('App.Models.User.' + this.userId)
                    .notification((notification) => {
                        this.notifications.unshift(notification);
                        this.unreadCount++;
                        this.showToast(notification);
                    });
            }
        },

        markAsRead(id) {
            fetch(`/notifications/${id}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '{{ csrf_token() }}'
                }
            }).then(() => {
                let notif = this.notifications.find(n => n.id === id);
                if (notif && !notif.read_at) {
                    notif.read_at = new Date().toISOString();
                    this.unreadCount = Math.max(0, this.unreadCount - 1);
                }
            });
        },

        markAllAsRead() {
            fetch('/notifications/mark-all-as-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '{{ csrf_token() }}'
                }
            }).then(() => {
                this.notifications.forEach(n => n.read_at = n.read_at || new Date().toISOString());
                this.unreadCount = 0;
            });
        },

        showToast(notification) {
            const id = Date.now();
            const message = notification.message || (notification.data ? notification.data.message : '');
            const title = notification.title || (notification.data ? notification.data.title : 'Thông báo mới');
            const type = notification.type || (notification.data ? notification.data.type : 'default');
            
            this.toasts.push({ id, message, title, type });
            setTimeout(() => {
                this.removeToast(id);
            }, 5000);
        },

        removeToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        },

        getIconClass(type) {
            if (type === 'payment_success') return 'bi bi-cash-coin';
            if (type === 'booking_success') return 'bi bi-check-circle-fill';
            if (type === 'departure_reminder') return 'bi bi-alarm-fill';
            if (type === 'booking_status') return 'bi bi-arrow-repeat';
            return 'bi bi-bell-fill';
        },

        getIconBgClass(type) {
            if (type === 'payment_success') return 'bg-success';
            if (type === 'booking_success') return 'bg-success';
            if (type === 'departure_reminder') return 'bg-warning text-dark';
            if (type === 'booking_status') return 'bg-info';
            return 'bg-primary';
        }
    }));
});
</script>
