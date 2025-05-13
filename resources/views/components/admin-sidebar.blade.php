<div class="fixed inset-y-0 left-0 z-30 w-72 bg-gradient-to-br from-white via-gray-50 to-gray-100 text-gray-800 drop-shadow-xl transition-all duration-300 ease-in-out"
    id="sidebar">
    <!-- Logo/Brand Section -->
    <div class="px-6 py-5 border-b border-gray-200/50 flex items-center justify-center bg-white/50">
        <img src="{{ asset('assets/samoedralogo.png') }}" alt="Samoedra Logo" class="w-48">
    </div>

    <!-- Navigation Menu -->
    <nav class="mt-6 px-3 overflow-y-auto h-[calc(100vh-180px)]">
        <!-- Dashboard Section -->
        <div class="px-4 text-xs font-semibold text-gray-500 uppercase mb-3 tracking-wider flex items-center">
            <i class="fas fa-compass mr-2"></i>
            <span>Navigation</span>
        </div>

        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 rounded-xl mx-2 mb-2 group
                  @if($currentRoute == 'dashboard') bg-purple-100 text-purple-600 @else text-gray-600 hover:bg-purple-50 hover:text-purple-600 @endif
                  transition-all duration-200 ease-in-out transform hover:scale-[1.02] border-b border-gray-200">
            <i class="fas fa-users mr-3 text-lg"></i>
            <span>Dashboard Karyawan</span>
        </a>

        <!-- Settings Section -->
        <div class="px-4 text-xs font-semibold text-gray-500 uppercase mt-6 mb-3 tracking-wider flex items-center">
            <i class="fas fa-cog mr-2"></i>
            <span>Settings</span>
        </div>

        <a href="{{ route('account.settings') }}" class="flex items-center px-4 py-3 rounded-xl mx-2 mb-2 group
                  @if($currentRoute == 'account.settings') bg-purple-100 text-purple-600 @else text-gray-600 hover:bg-purple-50 hover:text-purple-600 @endif
                  transition-all duration-200 ease-in-out transform hover:scale-[1.02] border-b border-gray-200">
            <i class="fas fa-user mr-3 text-lg"></i>
            <span>Pengaturan Akun</span>
        </a>

        <a href="{{ route('notifications.index') }}" class="flex items-center px-4 py-3 rounded-xl mx-2 mb-2 group
                  @if($currentRoute == 'notifications.index') bg-purple-100 text-purple-600 @else text-gray-600 hover:bg-purple-50 hover:text-purple-600 @endif
                  transition-all duration-200 ease-in-out transform hover:scale-[1.02] border-b border-gray-200">
            <i class="fas fa-bell mr-3 text-lg"></i>
            <span>Notifications</span>
            <span class="notification-count ml-auto aspect-square bg-red-100 text-red-600 text-xs min-w-6 h-6 flex justify-center items-center rounded-full hidden">0</span>
        </a>
    </nav>

    <!-- User Profile Section -->
    <div class="absolute bottom-0 w-full px-6 py-4 border-t border-gray-300/50 bg-white/80 backdrop-blur-sm">
        <div class="flex items-center">
            <div
                class="h-10 w-10 aspect-square rounded-full bg-gradient-to-r from-purple-400 to-indigo-400 flex items-center justify-center text-white font-semibold shadow-md ring-2 ring-purple-300/50 transition-all duration-300 hover:ring-purple-400 hover:shadow-lg">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div class="ml-3">
                <p class="text-gray-800 font-medium">{{ Auth::user()->name }}</p>
                <p class="text-sm text-gray-500">{{ Auth::user()->email }}</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="text-gray-500 hover:text-purple-600 text-xs flex items-center bg-transparent border-none p-0 cursor-pointer transition-colors duration-200 mt-1">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
