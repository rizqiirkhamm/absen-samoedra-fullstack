@extends('admin.layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Notification Messages -->
    <div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2 w-80">
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg drop-shadow-lg flex items-start notification transition-all duration-300 transform translate-x-0">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
            </div>
            <div>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        @endif
        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg drop-shadow-lg flex items-start notification transition-all duration-300 transform translate-x-0">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500 mr-2 mt-1"></i>
            </div>
            <div>
                <span>{{ session('error') }}</span>
            </div>
        </div>
        @endif
        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg drop-shadow-lg flex items-start notification transition-all duration-300 transform translate-x-0">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500 mr-2 mt-1"></i>
            </div>
            <div>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>

    <!-- Backdrop for mobile sidebar -->
    <div id="sidebar-backdrop" class="hidden"></div>

    <!-- Sidebar -->
    <x-admin-sidebar></x-admin-sidebar>

    <!-- Main Content -->
    <div class="ml-64 transition-all duration-300 ease-in-out" id="main-content">
        <!-- Top Navigation -->
        <header class="bg-white drop-shadow-sm">
            <div class="flex items-center justify-between h-16 px-6">
                <div class="flex items-center">
                    <button id="toggleSidebar" class="text-gray-600 hover:text-purple-600 mr-4 flex md:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-user text-purple-500 mr-3"></i> Pengaturan Akun
                    </h1>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="p-8">
            <div class="max-w-4xl mx-auto space-y-6">
                <!-- Update Name Card -->
                <div class="bg-white rounded-xl drop-shadow-md overflow-hidden">
                    <div class="px-8 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Ubah Nama</h2>
                    </div>
                    <div class="p-8">
                        <form id="updateNameForm" method="POST" action="{{ route('account.update-name') }}">
                            @csrf
                            <div class="mb-6">
                                <label for="current_name" class="block text-sm font-medium text-gray-700 mb-2">Nama
                                    Saat Ini</label>
                                <input type="text" id="current_name" name="current_name"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                    value="{{ Auth::user()->name }}" disabled>
                            </div>
                            <div class="mb-6">
                                <label for="new_name" class="block text-sm font-medium text-gray-700 mb-2">Nama
                                    Baru</label>
                                <input type="text" id="new_name" name="new_name" required minlength="3" maxlength="255"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                @error('new_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit"
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                                Simpan Perubahan Nama
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Update Email Card -->
                <div class="bg-white rounded-xl drop-shadow-md overflow-hidden">
                    <div class="px-8 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Ubah Email</h2>
                    </div>
                    <div class="p-8">
                        <form id="updateEmailForm" method="POST" action="{{ route('account.update-email') }}">
                            @csrf
                            <div class="mb-6">
                                <label for="current_email" class="block text-sm font-medium text-gray-700 mb-2">Email
                                    Saat Ini</label>
                                <input type="email" id="current_email" name="current_email"
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                    value="{{ Auth::user()->email }}" disabled>
                            </div>
                            <div class="mb-6">
                                <label for="new_email" class="block text-sm font-medium text-gray-700 mb-2">Email
                                    Baru</label>
                                <input type="email" id="new_email" name="new_email" required
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                @error('new_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-6">
                                <label for="new_email_confirmation"
                                    class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Email Baru</label>
                                <input type="email" id="new_email_confirmation" name="new_email_confirmation" required
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                @error('new_email_confirmation')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit"
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                                Simpan Perubahan Email
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Update Password Card -->
                <div class="bg-white rounded-xl drop-shadow-md overflow-hidden">
                    <div class="px-8 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Ubah Password</h2>
                    </div>
                    <div class="p-8">
                        <form id="updatePasswordForm" method="POST" action="{{ route('account.update-password') }}">
                            @csrf
                            <div class="mb-6">
                                <label for="current_password"
                                    class="block text-sm font-medium text-gray-700 mb-2">Password Saat Ini</label>
                                <input type="password" id="current_password" name="current_password" required
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-6">
                                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">Password
                                    Baru</label>
                                <input type="password" id="new_password" name="new_password" required
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <p class="mt-1 text-xs text-gray-500">Password minimal 8 karakter dan mengandung huruf
                                    besar, kecil, angka, dan simbol.</p>
                                @error('new_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-6">
                                <label for="new_password_confirmation"
                                    class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password
                                    Baru</label>
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                    required
                                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                @error('new_password_confirmation')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit"
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                                Simpan Perubahan Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@push('scripts')
<script src="{{ asset('account-settings.js') }}"></script>
@endpush
@endsection