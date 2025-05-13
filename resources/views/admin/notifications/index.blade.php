@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="transition-all duration-300 ease-in-out" id="main-content">
        <!-- Top Navigation -->
        <header class="bg-white shadow-sm backdrop-blur-md bg-opacity-80 sticky top-0 z-10">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-4 space-y-4 sm:space-y-0">
                <div class="flex items-center">
                    <button id="toggleSidebar" class="text-gray-600 hover:text-purple-600 mr-4 flex md:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div>
                        <h1 class="text-xl font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-bell text-purple-500 mr-3"></i>
                            <span>Notifikasi</span>
                        </h1>
                    </div>
                </div>
                <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto space-x-4">
                    <div class="flex space-x-2 bg-gray-100 p-1 rounded-xl">
                        <button
                            class="filter-btn active px-4 py-2 rounded-lg bg-white shadow-sm text-sm font-medium text-purple-600 transition-all duration-200"
                            data-filter="all">
                            Semua
                        </button>
                        <button
                            class="filter-btn px-4 py-2 rounded-lg hover:bg-white hover:shadow-sm text-sm font-medium text-gray-600 hover:text-purple-600 transition-all duration-200"
                            data-filter="unread">
                            Belum Dibaca
                        </button>
                    </div>
                    <button id="markAllAsRead"
                        class="group flex-shrink-0 flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-400 text-white rounded-xl transition-all duration-200">
                        <i class="fas fa-check-double group-hover:scale-110 transition-transform"></i>
                        <span class="hidden sm:inline-block ml-2">Tandai Sudah Dibaca</span>
                    </button>
                </div>
            </div>
        </header>

        <div id="sidebar-backdrop" class="hidden"></div>

        <!-- Sidebar -->
        <x-admin-sidebar></x-admin-sidebar>

        <!-- Notifications Content -->
        <main class="p-4 sm:p-6">
            <div class="max-w-4xl mx-auto">
                @if($notifications->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm p-8 sm:p-12 text-center">
                    <div class="inline-block p-6 rounded-full bg-gradient-to-br from-purple-50 to-purple-100 mb-6">
                        <i class="fas fa-bell-slash text-4xl sm:text-5xl text-purple-400"></i>
                    </div>
                    <h3 class="text-xl font-medium text-gray-700 mb-3">Tidak Ada Notifikasi</h3>
                    <p class="text-gray-500 max-w-md mx-auto leading-relaxed">
                        Anda belum memiliki notifikasi saat ini. Kami akan memberi tahu Anda ketika ada aktivitas baru.
                    </p>
                </div>
                @else
                <div class="space-y-3 sm:space-y-4" id="notification-container">
                    @foreach($notifications as $notification)
                    <div class="notification-item group bg-white rounded-xl sm:rounded-2xl border border-gray-200 hover:border-purple-200 transition-all duration-200 hover:shadow-md {{ $notification->is_read ? '' : 'border-l-4 border-l-purple-500' }}"
                        data-id="{{ $notification->id }}" data-read="{{ $notification->is_read ? 'true' : 'false' }}">
                        <div class="p-4 sm:p-5">
                            <div class="flex items-start gap-3 sm:gap-4">
                                <!-- Notification Icon -->
                                <div class="flex-shrink-0">
                                    @if(str_contains(strtolower($notification->title), 'check in') ||
                                    str_contains(strtolower($notification->message), 'check in'))
                                    <div
                                        class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-blue-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <i class="fas fa-sign-in-alt text-lg sm:text-xl text-blue-500"></i>
                                    </div>
                                    @elseif(str_contains(strtolower($notification->title), 'check out') ||
                                    str_contains(strtolower($notification->message), 'check out'))
                                    <div
                                        class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-red-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <i class="fas fa-sign-out-alt text-lg sm:text-xl text-red-500"></i>
                                    </div>
                                    @elseif(str_contains(strtolower($notification->title), 'karyawan dihapus'))
                                    <div
                                        class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-yellow-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <i class="fas fa-user-minus text-lg sm:text-xl text-yellow-500"></i>
                                    </div>
                                    @else
                                    @switch($notification->type)
                                    @case('success')
                                    <div
                                        class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-green-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <i class="fas fa-check-circle text-lg sm:text-xl text-green-500"></i>
                                    </div>
                                    @break
                                    @case('warning')
                                    <div
                                        class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-yellow-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <i class="fas fa-exclamation-triangle text-lg sm:text-xl text-yellow-500"></i>
                                    </div>
                                    @break
                                    @case('error')
                                    <div
                                        class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-red-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <i class="fas fa-times-circle text-lg sm:text-xl text-red-500"></i>
                                    </div>
                                    @break
                                    @default
                                    <div
                                        class="h-10 w-10 sm:h-12 sm:w-12 rounded-xl bg-blue-50 flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <i class="fas fa-info-circle text-lg sm:text-xl text-blue-500"></i>
                                    </div>
                                    @endswitch
                                    @endif
                                </div>
                                <!-- Notification Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <h3 class="text-base sm:text-lg font-semibold text-gray-900">
                                            {{ $notification->title }}</h3>
                                        <div class="flex items-center space-x-2 sm:space-x-3">
                                            @if(!$notification->is_read)
                                            <span
                                                class="inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                                                Baru
                                            </span>
                                            @endif
                                            <span class="text-sm text-gray-500 whitespace-nowrap">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-sm sm:text-base text-gray-600 leading-relaxed">
                                        {{ $notification->message }}</p>

                                    <!-- Action Buttons -->
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @if(!$notification->is_read)
                                        <button
                                            class="mark-as-read-btn inline-flex items-center px-3 sm:px-4 py-1.5 sm:py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                                            <i class="fas fa-check mr-2"></i> Tandai Sudah Dibaca
                                        </button>
                                        @endif
                                        @if($notification->action_url)
                                        <a href="{{ $notification->action_url }}"
                                            class="inline-flex items-center px-3 sm:px-4 py-1.5 sm:py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200">
                                            <i class="fas fa-arrow-right mr-2"></i> Lihat Detail
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </main>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // =============================================
        // SIDEBAR TOGGLE FUNCTIONALITY
        // =============================================
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const toggleSidebar = document.getElementById('toggleSidebar');
        const sidebarBackdrop = document.getElementById('sidebar-backdrop');

        // Toggle sidebar function
        const toggleSidebarFn = () => {
            sidebar.classList.toggle('open');
            sidebarBackdrop.classList.toggle('active');

            // Untuk desktop, tetap geser konten
            if (window.innerWidth >= 768) {
                mainContent.classList.toggle('ml-64');
            }
        };

        // Sidebar toggle button
        if (toggleSidebar) {
            toggleSidebar.addEventListener('click', toggleSidebarFn);
        }

        // Close sidebar when clicking on backdrop
        if (sidebarBackdrop) {
            sidebarBackdrop.addEventListener('click', toggleSidebarFn);
        }

        // Responsive behavior
        function handleResize() {
            if (window.innerWidth >= 768) {
                // Desktop - sidebar selalu terbuka
                sidebar.classList.add('open');
                sidebarBackdrop.classList.remove('active');
                mainContent.classList.add('ml-64');
            } else {
                // Mobile - sidebar default tertutup
                sidebar.classList.remove('open');
                sidebarBackdrop.classList.remove('active');
                mainContent.classList.remove('ml-64');
            }
        }

        window.addEventListener('resize', handleResize);
        handleResize(); // Initialize

        // Filter notifications with animation
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.filter-btn').forEach(b => {
                    b.classList.remove('active', 'bg-white', 'shadow-sm',
                        'text-purple-600');
                    b.classList.add('hover:bg-white', 'hover:shadow-sm',
                        'text-gray-600');
                });
                this.classList.add('active', 'bg-white', 'shadow-sm', 'text-purple-600');
                this.classList.remove('hover:bg-white', 'hover:shadow-sm', 'text-gray-600');

                const filter = this.dataset.filter;
                const notifications = document.querySelectorAll('.notification-item');

                notifications.forEach(notification => {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateY(10px)';

                    setTimeout(() => {
                        if (filter === 'all' ||
                            (filter === 'unread' && notification.dataset
                                .read === 'false')) {
                            notification.style.display = 'block';
                            setTimeout(() => {
                                notification.style.opacity = '1';
                                notification.style.transform =
                                    'translateY(0)';
                            }, 50);
                        } else {
                            notification.style.display = 'none';
                        }
                    }, 200);
                });
            });
        });

        // Add transition styles to notification items
        document.querySelectorAll('.notification-item').forEach(item => {
            item.style.transition = 'all 0.3s ease-in-out';
        });

        // Mark single notification as read
        document.querySelectorAll('.mark-as-read-btn').forEach(button => {
            button.addEventListener('click', function () {
                const notificationItem = this.closest('.notification-item');
                const notificationId = notificationItem.dataset.id;

                fetch(`/notifications/${notificationId}/mark-as-read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            notificationItem.classList.remove('border-l-4',
                                'border-l-purple-500');
                            notificationItem.dataset.read = 'true';

                            // Update status badge
                            const statusBadge = notificationItem.querySelector(
                                '.bg-purple-100');
                            if (statusBadge) {
                                statusBadge.remove();
                            }

                            // Remove the mark as read button
                            this.remove();
                            updateUnreadCount();
                        }
                    });
            });
        });

        // Mark all notifications as read
        document.getElementById('markAllAsRead').addEventListener('click', function () {
            fetch('/notifications/mark-all-as-read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelectorAll('.notification-item').forEach(item => {
                            item.classList.remove('border-l-4', 'border-l-purple-500');
                            item.dataset.read = 'true';

                            // Remove status badges
                            const statusBadges = item.querySelectorAll('.bg-purple-100');
                            statusBadges.forEach(badge => badge.remove());
                        });

                        // Remove all mark as read buttons
                        document.querySelectorAll('.mark-as-read-btn').forEach(btn => btn.remove());
                        updateUnreadCount();
                    }
                });
        });

        // Function to update unread count in sidebar
        function updateUnreadCount() {
            fetch('/notifications/unread-count')
                .then(response => response.json())
                .then(data => {
                    const countElement = document.querySelector('.notification-count');
                    if (countElement) {
                        if (data.count > 0) {
                            countElement.textContent = data.count;
                            countElement.classList.remove('hidden');
                        } else {
                            countElement.classList.add('hidden');
                        }
                    }
                });
        }
    });

</script>

<style>
    .notification-item {
        opacity: 1;
        transform: translateY(0);
        transition: all 0.3s ease-in-out;
    }

</style>
@endsection
