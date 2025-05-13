@extends('admin.layouts.app')

@section('content')

<!-- Main Dashboard Layout -->
<div class="min-h-screen bg-gray-50">
    <!-- Notification Messages -->
    <div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2 w-80">
        @if(session('success'))
        <div
            class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg drop-shadow-lg flex items-start notification transition-all duration-300 transform translate-x-0">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-500 mr-2 mt-1"></i>
            </div>
            <div>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        @endif
        @if(session('error'))
        <div
            class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg drop-shadow-lg flex items-start notification transition-all duration-300 transform translate-x-0">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500 mr-2 mt-1"></i>
            </div>
            <div>
                <span>{{ session('error') }}</span>
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
                        <i class="fas fa-chart-line text-purple-500 mr-3"></i> Dashboard Presensi
                    </h1>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="p-8">
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl drop-shadow-md p-6 mb-6 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-semibold mb-1">Selamat Datang, <span>{{ Auth::user()->name }}</span>
                        </h2>
                        <p class="opacity-90">Ringkasan aktivitas presensi karyawan Samodra</p>
                    </div>
                    <div
                        class="bg-white bg-opacity-20 p-3 w-14 h-14 flex justify-center items-center aspect-square rounded-full">
                        <i class="fas fa-calendar-check text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                <!-- Total Karyawan -->
                <div
                    class="bg-white rounded-xl drop-shadow-md overflow-hidden hover:drop-shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div
                                class="w-14 h-14 md:w-16 md:h-16 aspect-square flex justify-center items-center rounded-full bg-blue-100 text-blue-600 mr-4">
                                <i class="fas fa-user-group text-xl md:text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500">Total Karyawan</p>
                                <p class="text-2xl font-semibold text-gray-800">{{ $totalKaryawan }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kehadiran Hari Ini -->
                <div
                    class="bg-white rounded-xl drop-shadow-md overflow-hidden hover:drop-shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div
                                class="w-14 h-14 md:w-16 md:h-16 aspect-square flex justify-center items-center rounded-full bg-green-100 text-green-600 mr-4">
                                <i class="fas fa-user-check text-xl md:text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500">Kehadiran Hari Ini</p>
                                <div class="flex items-baseline">
                                    <p class="text-2xl font-semibold text-gray-800">{{ number_format(abs($dailyPercentage), 1) }}%</p>
                                    <p class="text-sm text-gray-500 ml-2">({{ $absensiHariIni }} dari {{ $totalKaryawan }})</p>
                            </div>
                        </div>
                    </div>
                </div>
                </div>

                <!-- Kehadiran Minggu Ini -->
                <div
                    class="bg-white rounded-xl drop-shadow-md overflow-hidden hover:drop-shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div
                                class="w-14 h-14 md:w-16 md:h-16 aspect-square flex justify-center items-center rounded-full bg-amber-100 text-amber-600 mr-4">
                                <i class="fas fa-calendar-week text-xl md:text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500">Rata-rata Kehadiran Minggu Ini</p>
                                <p class="text-2xl font-semibold text-gray-800">{{ number_format(abs($weeklyPercentage), 1) }}%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kehadiran Bulan Ini -->
                <div
                    class="bg-white rounded-xl drop-shadow-md overflow-hidden hover:drop-shadow-lg transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div
                                class="w-14 h-14 md:w-16 md:h-16 aspect-square flex justify-center items-center rounded-full bg-purple-100 text-purple-600 mr-4">
                                <i class="fas fa-calendar-alt text-xl md:text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-gray-500">Rata-rata Kehadiran Bulan Ini</p>
                                <p class="text-2xl font-semibold text-gray-800">{{ number_format(abs($monthlyPercentage), 1) }}%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Weekly Attendance Chart -->
                <div class="bg-white rounded-xl drop-shadow-md overflow-hidden hover:drop-shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800">Statistik Kehadiran Mingguan</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ now()->startOfWeek()->format('d M') }} - {{ now()->endOfWeek()->format('d M Y') }}</p>
                            </div>
                            <div class="bg-amber-50 text-amber-600 px-3 py-1 rounded-lg text-sm font-medium">
                                {{ number_format(abs($weeklyPercentage), 1) }}% rata-rata
                            </div>
                        </div>
                        <div class="relative h-[300px]">
                            <canvas id="weeklyChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Monthly Attendance Chart -->
                <div class="bg-white rounded-xl drop-shadow-md overflow-hidden hover:drop-shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800">Statistik Kehadiran Bulanan</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ now()->format('F Y') }}</p>
                            </div>
                            <div class="bg-purple-50 text-purple-600 px-3 py-1 rounded-lg text-sm font-medium">
                                {{ number_format(abs($monthlyPercentage), 1) }}% rata-rata
                            </div>
                        </div>
                        <div class="relative h-[300px]">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employee Attendance Statistics -->
            <div class="bg-white rounded-xl drop-shadow-md overflow-hidden hover:drop-shadow-xl transition-shadow duration-300 mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">Statistik Kehadiran Per Karyawan</h3>
                            <p class="text-sm text-gray-500 mt-1">Detail kehadiran masing-masing karyawan</p>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Karyawan
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Hadir Untuk
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Minggu Ini
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-center text-sm font-medium text-gray-500 uppercase tracking-wider">
                                        Bulan Ini
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($employeeStats as $stat)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-medium">
                                                {{ strtoupper(substr($stat['nama'], 0, 1)) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-base font-medium text-gray-900">{{ $stat['nama'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-base text-gray-500">{{ $stat['hadir_untuk'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col items-center">
                                            <div class="relative w-full h-2 bg-gray-200 rounded-full mb-2">
                                                <div class="absolute top-0 left-0 h-full bg-amber-500 rounded-full" style="width: {{ $stat['weekly']['percentage'] }}%"></div>
                                            </div>
                                            <div class="text-sm">
                                                <span class="font-medium text-gray-900">{{ number_format(abs($stat['weekly']['percentage']), 1) }}%</span>
                                                <span class="text-gray-500">({{ $stat['weekly']['attendance'] }}/7)</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col items-center">
                                            <div class="relative w-full h-2 bg-gray-200 rounded-full mb-2">
                                                <div class="absolute top-0 left-0 h-full bg-purple-500 rounded-full" style="width: {{ $stat['monthly']['percentage'] }}%"></div>
                                            </div>
                                            <div class="text-sm">
                                                <span class="font-medium text-gray-900">{{ number_format(abs($stat['monthly']['percentage']), 1) }}%</span>
                                                <span class="text-gray-500">({{ $stat['monthly']['attendance'] }}/{{ now()->daysInMonth }})</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Employee Management Section -->
            <div class="bg-white rounded-xl drop-shadow-md overflow-hidden mb-6">
                <div
                    class="px-8 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <h2 class="text-xl font-semibold text-gray-800 mb-2 sm:mb-0">Kelola Karyawan</h2>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 w-full sm:w-auto">
                        <div class="relative w-full sm:w-56">
                            <input type="text" id="employeeSearch" placeholder="Cari karyawan..."
                                class="pl-10 pr-4 py-2 w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-base">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                        <button id="addEmployeeBtn"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 text-base font-medium flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i> Tambah Karyawan
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto table-container">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-8 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Nama</th>
                                <th scope="col"
                                    class="px-8 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Hadir Untuk</th>
                                <th scope="col"
                                    class="px-8 py-3 text-right text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="employeeTableBody" class="bg-white divide-y divide-gray-200">
                            @if(isset($employees))
                            @forelse($employees as $employee)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-8 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-medium">
                                            {{ strtoupper(substr($employee->nama, 0, 1)) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-base font-medium text-gray-900">{{ $employee->nama }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-4 whitespace-nowrap">
                                    <div class="text-base text-gray-500">{{ $employee->hadir_untuk }}</div>
                                </td>
                                <td class="px-8 py-4 whitespace-nowrap text-right text-base font-medium">
                                    <button data-id="{{ $employee->id }}" data-nama="{{ $employee->nama }}"
                                        data-hadir-untuk="{{ $employee->hadir_untuk }}"
                                        class="edit-employee-btn text-purple-600 hover:text-purple-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button data-id="{{ $employee->id }}" data-nama="{{ $employee->nama }}"
                                        class="delete-employee-btn text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-8 py-4 text-center text-base text-gray-500">Belum ada data
                                    karyawan</td>
                            </tr>
                            @endforelse
                            @else
                            <tr>
                                <td colspan="3" class="px-8 py-4 text-center text-base text-gray-500">Data karyawan
                                    tidak tersedia</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Attendance Section -->
            <div class="bg-white rounded-xl drop-shadow-md overflow-hidden">
                <div
                    class="px-8 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <h2 class="text-xl font-semibold text-gray-800 mb-2 sm:mb-0">Riwayat Presensi Terkini</h2>
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 w-full sm:w-auto">
                        <!-- Search Input -->
                    <div class="relative w-full sm:w-56">
                        <input type="text" id="attendanceSearch" placeholder="Cari presensi..."
                            class="pl-10 pr-4 py-2 w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-base">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                        <!-- Sort Dropdown -->
                        <div class="relative w-full sm:w-48">
                            <select id="attendanceSort"
                                class="pl-4 pr-8 py-2 w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-base">
                                <option value="latest">Terbaru</option>
                                <option value="oldest">Terlama</option>
                            </select>
                </div>
                        <!-- Date Filter -->
                        <div class="relative w-full sm:w-48">
                            <input type="date" id="attendanceDateFilter"
                                class="pl-4 pr-4 py-2 w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-base">
                        </div>
                        <!-- Export Button -->
                        <a href="{{ route('admin.export') }}" id="exportBtn"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium flex items-center justify-center">
                            <i class="fas fa-file-excel mr-2"></i> Export Excel
                        </a>
                    </div>
                </div>
                <div class="overflow-x-auto table-container">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-8 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Nama</th>
                                <th scope="col"
                                    class="px-8 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Lokasi</th>
                                <th scope="col"
                                    class="px-8 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal</th>
                                <th scope="col"
                                    class="px-8 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Jam Masuk</th>
                                <th scope="col"
                                    class="px-8 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Jam Keluar</th>
                                <th scope="col"
                                    class="px-8 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Durasi Kerja</th>
                                <th scope="col"
                                    class="px-8 py-3 text-right text-sm font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTableBody" class="bg-white divide-y divide-gray-200">
                            @forelse($attendances as $attendance)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-8 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($attendance->foto)
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full"
                                                 src="{{ asset('storage/' . $attendance->foto) }}" alt="Foto">
                                        </div>
                                    @else
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-400">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                        <div class="ml-4">
                                            <div class="text-base font-medium text-gray-900">{{ $attendance->nama }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $attendance->hadir_untuk }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-4 whitespace-nowrap">
                                    <div class="text-base text-gray-500">{{ $attendance->lokasi }}</div>
                                </td>
                                <td class="px-8 py-4 whitespace-nowrap">
                                    <div class="text-base text-gray-500">
                                        {{ date('Y-m-d', strtotime($attendance->tanggal)) }}</div>
                                </td>
                                <td class="px-8 py-4 whitespace-nowrap">
                                    <div class="text-base text-gray-500">
                                        {{ $attendance->check_in ? date('H:i:s', strtotime($attendance->check_in)) : '-' }}
                                    </div>
                                </td>
                                <td class="px-8 py-4 whitespace-nowrap">
                                    <div class="text-base text-gray-500">
                                        {{ $attendance->check_out ? date('H:i:s', strtotime($attendance->check_out)) : '-' }}
                                    </div>
                                </td>
                                <td class="px-8 py-4 whitespace-nowrap">
                                    <div class="text-base text-gray-500">
                                        @if($attendance->check_out)
                                            @php
                                                $totalMinutes = abs($attendance->work_hours); // Ensure positive value
                                                if ($totalMinutes < 60) {
                                                    echo $totalMinutes . ' menit';
                                                } elseif ($totalMinutes == 60) {
                                                    echo '1 jam';
                                                } else {
                                                    $hours = floor($totalMinutes / 60);
                                                    $minutes = $totalMinutes % 60;
                                                    echo $minutes > 0 ? "$hours jam $minutes menit" : "$hours jam";
                                                }
                                            @endphp
                                        @else
                                            Masih bekerja
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-4 whitespace-nowrap text-right text-base font-medium">
                                    <button data-id="{{ $attendance->id }}"
                                        class="detail-btn text-purple-600 hover:text-purple-900 mr-3">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button data-id="{{ $attendance->id }}" data-nama="{{ $attendance->nama }}"
                                        class="delete-attendance-btn text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-8 py-4 text-center text-base text-gray-500">Belum ada data
                                    presensi</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Controls -->
                <div
                    class="px-8 py-4 border-t border-gray-200 flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                    <!-- Per Page Dropdown -->
                    <div class="flex items-center space-x-2">
                        <label for="perPage" class="text-sm text-gray-600">Tampilkan:</label>
                        <select id="perPage"
                            class="rounded-lg border-gray-300 text-sm focus:border-purple-500 focus:ring-purple-500">
                            <option value="5" {{ $attendances->perPage() == 5 ? 'selected' : '' }}>5</option>
                            <option value="10" {{ $attendances->perPage() == 10 ? 'selected' : '' }}>10</option>
                            <option value="15" {{ $attendances->perPage() == 15 ? 'selected' : '' }}>15</option>
                            <option value="20" {{ $attendances->perPage() == 20 ? 'selected' : '' }}>20</option>
                        </select>
                    </div>

                    <!-- Pagination Links -->
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600 mr-2">
                            Menampilkan {{ $attendances->firstItem() ?? 0 }} - {{ $attendances->lastItem() ?? 0 }} dari
                            {{ $attendances->total() }} data
                        </span>
                        <div class="flex space-x-2">
                            @if ($attendances->onFirstPage())
                            <button disabled class="px-3 py-1 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            @else
                            <a href="{{ $attendances->previousPageUrl() }}&per_page={{ request('per_page', 5) }}"
                                class="px-3 py-1 rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                            @endif

                            @foreach ($attendances->getUrlRange(1, $attendances->lastPage()) as $page => $url)
                            @if ($page == $attendances->currentPage())
                            <span class="px-3 py-1 rounded-lg bg-purple-600 text-white">{{ $page }}</span>
                            @else
                            <a href="{{ $url }}&per_page={{ request('per_page', 5) }}"
                                class="px-3 py-1 rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">{{ $page }}</a>
                            @endif
                            @endforeach

                            @if ($attendances->hasMorePages())
                            <a href="{{ $attendances->nextPageUrl() }}&per_page={{ request('per_page', 5) }}"
                                class="px-3 py-1 rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                            @else
                            <button disabled class="px-3 py-1 rounded-lg bg-gray-100 text-gray-400 cursor-not-allowed">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal Detail -->
<x-detail-modal></x-detail-modal>

<!-- Modal Tambah Karyawan -->
<x-modal-tambah-karyawan></x-modal-tambah-karyawan>

<!-- Modal Edit Karyawan -->
<x-modal-edit-karyawan></x-modal-edit-karyawan>

<!-- Modal Konfirmasi Delete Presensi -->
<x-delete-attendance-modal></x-delete-attendance-modal>

<!-- Modal Konfirmasi Delete Karyawan -->
<x-delete-employee-modal></x-delete-employee-modal>

@push('scripts')
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

        // =============================================
        // NOTIFICATION HANDLING
        // =============================================
        const notifications = document.querySelectorAll('.notification');
        notifications.forEach(notification => {
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        });

        // Helper function to show notifications
        function showNotification(type, message) {
            const container = document.getElementById('notification-container');
            const notification = document.createElement('div');
            notification.className =
                `bg-${type === 'success' ? 'green' : 'red'}-100 border border-${type === 'success' ? 'green' : 'red'}-400 text-${type === 'success' ? 'green' : 'red'}-700 px-4 py-3 rounded-lg drop-shadow-lg flex items-start notification transition-all duration-300 transform translate-x-0`;
            notification.innerHTML = `
            <div class="flex-shrink-0">
                <i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle text-${type === 'success' ? 'green' : 'red'}-500 mr-2 mt-1"></i>
            </div>
            <div>
                <span>${message}</span>
            </div>
        `;
            container.appendChild(notification);
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => notification.remove(), 300);
            }, 5000);
        }

        // =============================================
        // MODAL HANDLING FUNCTIONS
        // =============================================
        const showModal = (modal, content) => {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.style.opacity = '1';
                content.style.opacity = '1';
                content.style.transform = 'scale(1)';
            }, 10);
        };

        const hideModal = (modal, content, form = null) => {
            modal.style.opacity = '0';
            content.style.opacity = '0';
            content.style.transform = 'scale(0.95)';
            setTimeout(() => {
                modal.classList.add('hidden');
                if (form) form.reset();
            }, 300);
        };

        // Helper function to format duration
        function formatDuration(totalMinutes) {
            totalMinutes = Math.abs(totalMinutes); // Ensure positive value
            if (totalMinutes < 60) {
                return `${totalMinutes} menit`;
            } else if (totalMinutes === 60) {
                return '1 jam';
            } else {
                const hours = Math.floor(totalMinutes / 60);
                const minutes = totalMinutes % 60;
                return minutes > 0 ? `${hours} jam ${minutes} menit` : `${hours} jam`;
            }
        }

        // =============================================
        // DETAIL ATTENDANCE MODAL
        // =============================================
        const detailModal = document.getElementById('detailModal');
        const detailModalContent = document.getElementById('detailModalContent');
        const detailButtons = document.querySelectorAll('.detail-btn');
        const closeModalBtn = document.getElementById('closeModalBtn');

        if (detailButtons) {
            detailButtons.forEach(button => {
                button.addEventListener('click', async () => {
                    try {
                        const response = await fetch(
                            `/admin/attendance/${button.dataset.id}`, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            });
                        if (!response.ok) throw new Error('Gagal memuat data');
                        const data = await response.json();

                        // Set check-in photo
                        setImageSafe('detailFotoCheckIn', data.foto);

                        // Handle check-out section
                        if (data.check_out) {
                            document.getElementById('checkoutInfo').classList.remove(
                                'hidden');
                            setCheckoutImage('detailFotoCheckOut', data.foto_checkout, data
                                .early_leave_reason);
                        } else {
                            document.getElementById('checkoutInfo').classList.add('hidden');
                        }

                        // Set text content
                        setTextSafe('detailNama', data.nama);
                        setTextSafe('detailHadirUntuk', data.hadir_untuk);
                        setTextSafe('detailLokasi', data.lokasi);
                        setTextSafe('detailTanggal', data.tanggal);
                        setTextSafe('detailCheckInTime', data.check_in);
                        setTextSafe('detailCheckOutTime', data.check_out);

                        // Update status
                        const statusElement = document.getElementById(
                        'detailCheckInStatus');
                        if (statusElement) {
                            statusElement.textContent = data.check_out ? 'Selesai Bekerja' :
                                'Masih Bekerja';
                            statusElement.className = data.check_out ?
                                'text-base font-medium text-green-600' :
                                'text-base font-medium text-blue-600';
                        }

                        // Handle work hours
                        if (data.check_out) {
                            const totalMinutes = Math.abs(data
                            .work_hours); // Ensure positive
                            setTextSafe('detailWorkHours', formatDuration(totalMinutes));
                        }

                        // Handle early leave reason
                        const earlyLeaveInfo = document.getElementById('earlyLeaveInfo');
                        if (data.early_leave_reason) {
                            earlyLeaveInfo.classList.remove('hidden');
                            setTextSafe('detailEarlyLeaveReason', data.early_leave_reason);
                        } else {
                            earlyLeaveInfo.classList.add('hidden');
                        }

                        // Update attendance percentage
                        const percentage = Math.abs(data.attendance_percentage ||
                        0); // Ensure positive
                        setTextSafe('detailAttendancePercentage',
                            percentage > 0 ? `${percentage}%` : '0%'
                        );

                        showModal(detailModal, detailModalContent);
                    } catch (error) {
                        console.error('Error:', error);
                        showNotification('error', error.message);
                    }
                });
            });
        }

        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', () => hideModal(detailModal, detailModalContent));
        }

        // =============================================
        // EMPLOYEE MODALS
        // =============================================
        // Add Employee Modal
        const addEmployeeModal = document.getElementById('addEmployeeModal');
        const addEmployeeModalContent = document.getElementById('addEmployeeModalContent');
        const addEmployeeBtn = document.getElementById('addEmployeeBtn');
        const closeAddEmployeeModalBtn = document.getElementById('closeAddEmployeeModalBtnSecondary');
        const addEmployeeForm = document.getElementById('addEmployeeForm');

        if (addEmployeeBtn) {
            addEmployeeBtn.addEventListener('click', () => showModal(addEmployeeModal,
            addEmployeeModalContent));
        }

        if (closeAddEmployeeModalBtn) {
            closeAddEmployeeModalBtn.addEventListener('click', () => hideModal(addEmployeeModal,
                addEmployeeModalContent, addEmployeeForm));
        }

        if (addEmployeeForm) {
            addEmployeeForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(addEmployeeForm);
                const submitBtn = addEmployeeForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';

                try {
                    const response = await fetch('/admin/employees', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Gagal menyimpan');

                    showNotification('success', data.message || 'Karyawan berhasil ditambahkan');
                    hideModal(addEmployeeModal, addEmployeeModalContent, addEmployeeForm);
                    setTimeout(() => location.reload(), 1500);
                } catch (error) {
                    showNotification('error', error.message);
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        }

        // Edit Employee Modal
        const editEmployeeModal = document.getElementById('editEmployeeModal');
        const editEmployeeModalContent = document.getElementById('editEmployeeModalContent');
        const editEmployeeButtons = document.querySelectorAll('.edit-employee-btn');
        const closeEditEmployeeModalBtn = document.getElementById('closeEditEmployeeModalBtnSecondary');
        const editEmployeeForm = document.getElementById('editEmployeeForm');

        if (editEmployeeButtons) {
            editEmployeeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    document.getElementById('editEmployeeId').value = button.dataset.id;
                    document.getElementById('editEmployeeNama').value = button.dataset.nama;
                    document.getElementById('editEmployeeMengajar').value = button.dataset
                        .hadir_untuk;
                    showModal(editEmployeeModal, editEmployeeModalContent);
                });
            });
        }

        if (closeEditEmployeeModalBtn) {
            closeEditEmployeeModalBtn.addEventListener('click', () => hideModal(editEmployeeModal,
                editEmployeeModalContent, editEmployeeForm));
        }

        if (editEmployeeForm) {
            editEmployeeForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(editEmployeeForm);
                formData.append('_method', 'PUT');
                const submitBtn = editEmployeeForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';

                try {
                    const response = await fetch(`/admin/employees/${formData.get('id')}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Gagal menyimpan');

                    showNotification('success', data.message || 'Perubahan berhasil disimpan');
                    hideModal(editEmployeeModal, editEmployeeModalContent, editEmployeeForm);
                    setTimeout(() => location.reload(), 1500);
                } catch (error) {
                    showNotification('error', error.message);
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        }

        // =============================================
        // DELETE MODALS
        // =============================================
        // Delete Attendance Modal
        const deleteAttendanceModal = document.getElementById('deleteAttendanceModal');
        const deleteAttendanceModalContent = document.getElementById('deleteAttendanceModalContent');
        const deleteAttendanceButtons = document.querySelectorAll('.delete-attendance-btn');
        const cancelDeleteAttendanceBtn = document.getElementById('cancelDeleteAttendanceBtn');
        const deleteAttendanceForm = document.getElementById('deleteAttendanceForm');

        if (deleteAttendanceButtons) {
            deleteAttendanceButtons.forEach(button => {
                button.addEventListener('click', () => {
                    document.getElementById('deleteAttendanceId').value = button.dataset.id;
                    document.getElementById('deleteAttendanceName').textContent = button.dataset
                        .nama;
                    document.getElementById('deleteAttendanceForm').action =
                        `/admin/attendances/${button.dataset.id}`;
                    showModal(deleteAttendanceModal, deleteAttendanceModalContent);
                });
            });
        }

        if (cancelDeleteAttendanceBtn) {
            cancelDeleteAttendanceBtn.addEventListener('click', () => hideModal(deleteAttendanceModal,
                deleteAttendanceModalContent));
        }

        if (deleteAttendanceForm) {
            deleteAttendanceForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(deleteAttendanceForm);
                const submitBtn = deleteAttendanceForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menghapus...';

                try {
                    const response = await fetch(deleteAttendanceForm.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            _method: 'DELETE'
                        })
                    });

                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Gagal menghapus');

                    showNotification('success', data.message || 'Presensi berhasil dihapus');
                    hideModal(deleteAttendanceModal, deleteAttendanceModalContent);
                    setTimeout(() => location.reload(), 1500);
                } catch (error) {
                    showNotification('error', error.message);
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        }

        // Delete Employee Modal
        const deleteEmployeeModal = document.getElementById('deleteEmployeeModal');
        const deleteEmployeeModalContent = document.getElementById('deleteEmployeeModalContent');
        const deleteEmployeeButtons = document.querySelectorAll('.delete-employee-btn');
        const cancelDeleteEmployeeBtn = document.getElementById('cancelDeleteEmployeeBtn');
        const deleteEmployeeForm = document.getElementById('deleteEmployeeForm');

        if (deleteEmployeeButtons) {
            deleteEmployeeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    document.getElementById('deleteEmployeeId').value = button.dataset.id;
                    document.getElementById('deleteEmployeeName').textContent = button.dataset
                        .nama;
                    document.getElementById('deleteEmployeeForm').action =
                        `/admin/employees/${button.dataset.id}`;
                    showModal(deleteEmployeeModal, deleteEmployeeModalContent);
                });
            });
        }

        if (cancelDeleteEmployeeBtn) {
            cancelDeleteEmployeeBtn.addEventListener('click', () => hideModal(deleteEmployeeModal,
                deleteEmployeeModalContent));
        }

        if (deleteEmployeeForm) {
            deleteEmployeeForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const formData = new FormData(deleteEmployeeForm);
                const submitBtn = deleteEmployeeForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;

                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menghapus...';

                try {
                    const response = await fetch(deleteEmployeeForm.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            _method: 'DELETE'
                        })
                    });

                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Gagal menghapus');

                    showNotification('success', data.message || 'Karyawan berhasil dihapus');
                    hideModal(deleteEmployeeModal, deleteEmployeeModalContent);
                    setTimeout(() => location.reload(), 1500);
                } catch (error) {
                    showNotification('error', error.message);
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        }

        // =============================================
        // SEARCH FUNCTIONALITY
        // =============================================
        // Debounce function
        function debounce(func, timeout = 500) {
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => {
                    func.apply(this, args);
                }, timeout);
            };
        }

        // Employee Search
        const employeeSearch = document.getElementById('employeeSearch');
        if (employeeSearch) {
            const searchEmployees = debounce(async (query) => {
                if (query.length < 2) {
                    // Jika query terlalu pendek, reload data asli
                    window.location.reload();
                    return;
                }

                try {
                    const loadingIcon = employeeSearch.nextElementSibling;
                    loadingIcon.classList.remove('fa-search');
                    loadingIcon.classList.add('fa-spinner', 'fa-spin');

                    console.log('Sending employee search request with query:', query);

                    const response = await fetch(
                        `/admin/employees/search?search=${encodeURIComponent(query)}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content
                            }
                        });

                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }

                    const employees = await response.json();
                    console.log('Employee search response received:', employees);

                    updateEmployeeTable(employees);

                } catch (error) {
                    console.error('Employee search error:', error);
                    showNotification('error', 'Gagal melakukan pencarian karyawan');
                } finally {
                    const loadingIcon = employeeSearch.nextElementSibling;
                    loadingIcon.classList.add('fa-search');
                    loadingIcon.classList.remove('fa-spinner', 'fa-spin');
                }
            });

            employeeSearch.addEventListener('input', (e) => {
                searchEmployees(e.target.value.trim());
            });
        }

        // Update Employee Table
        function updateEmployeeTable(employees) {
            const tbody = document.querySelector('#employeeTableBody');
            if (!tbody) {
                console.error('Employee table body not found! Check your HTML.');
                return;
            }

            tbody.innerHTML = '';

            if (employees && employees.length > 0) {
                employees.forEach(employee => {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50 transition-colors duration-150';
                    row.innerHTML = `
                    <td class="px-8 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-medium">
                                ${employee.nama.charAt(0).toUpperCase()}
                            </div>
                            <div class="ml-4">
                                <div class="text-base font-medium text-gray-900">${employee.nama}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-4 whitespace-nowrap">
                        <div class="text-base text-gray-500">${employee.hadir_untuk}</div>
                    </td>
                    <td class="px-8 py-4 whitespace-nowrap text-right text-base font-medium">
                        <button data-id="${employee.id}" data-nama="${employee.nama}" data-hadir-untuk="${employee.hadir_untuk}" class="edit-employee-btn text-purple-600 hover:text-purple-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button data-id="${employee.id}" data-nama="${employee.nama}" class="delete-employee-btn text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                    tbody.appendChild(row);
                });
                attachEmployeeEventListeners();
            } else {
                tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="px-8 py-4 text-center text-base text-gray-500">Tidak ditemukan data karyawan</td>
                </tr>
            `;
            }
        }

        // Attendance Sort and Filter
        const attendanceSort = document.getElementById('attendanceSort');
        const attendanceDateFilter = document.getElementById('attendanceDateFilter');

        if (attendanceSort || attendanceDateFilter) {
            const fetchAttendances = debounce(async () => {
                const sort = attendanceSort ? attendanceSort.value : 'latest';
                const date = attendanceDateFilter ? attendanceDateFilter.value : '';
                const search = attendanceSearch.value.trim();

                try {
                    const loadingIcon = attendanceSearch.nextElementSibling;
                    loadingIcon.classList.remove('fa-search');
                    loadingIcon.classList.add('fa-spinner', 'fa-spin');

                    const params = new URLSearchParams();
                    if (search) params.append('search', search);
                    if (sort) params.append('sort', sort);
                    if (date) params.append('date', date);

                    const response = await fetch(`/admin/attendances/search?${params.toString()}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]').content
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }

                    const attendances = await response.json();
                    updateAttendanceTable(attendances);

                } catch (error) {
                    console.error('Attendance fetch error:', error);
                    showNotification('error', 'Gagal memuat data presensi');
                } finally {
                    const loadingIcon = attendanceSearch.nextElementSibling;
                    loadingIcon.classList.add('fa-search');
                    loadingIcon.classList.remove('fa-spinner', 'fa-spin');
                }
            }, 500);

            if (attendanceSort) {
                attendanceSort.addEventListener('change', () => {
                    fetchAttendances();
                });
            }

            if (attendanceDateFilter) {
                attendanceDateFilter.addEventListener('change', () => {
                    fetchAttendances();
                });
            }
        }

        // Attendance Search Functionality
        const attendanceSearch = document.getElementById('attendanceSearch');
        if (attendanceSearch) {
            const searchAttendances = debounce(async (query) => {
                try {
                    const loadingIcon = attendanceSearch.nextElementSibling;
                    loadingIcon.classList.remove('fa-search');
                    loadingIcon.classList.add('fa-spinner', 'fa-spin');

                    const params = new URLSearchParams();
                    if (query) params.append('search', query);
                    if (attendanceSort.value) params.append('sort', attendanceSort.value);
                    if (attendanceDateFilter.value) params.append('date', attendanceDateFilter.value);
                    params.append('per_page', perPageSelect.value);

                    const response = await fetch(`/admin/attendances/search?${params.toString()}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }

                    const attendances = await response.json();
                    updateAttendanceTable(attendances);

                } catch (error) {
                    console.error('Attendance search error:', error);
                    showNotification('error', 'Gagal melakukan pencarian presensi');
                } finally {
                    const loadingIcon = attendanceSearch.nextElementSibling;
                    loadingIcon.classList.add('fa-search');
                    loadingIcon.classList.remove('fa-spinner', 'fa-spin');
                }
            });

            attendanceSearch.addEventListener('input', (e) => {
                const query = e.target.value.trim();
                searchAttendances(query);
            });
        }

        // Update Attendance Table
        function updateAttendanceTable(attendances) {
            const tbody = document.querySelector('#attendanceTableBody');
            if (!tbody) {
                console.error('Attendance table body not found! Check your HTML.');
                return;
            }

            tbody.innerHTML = '';

            if (attendances && attendances.success && attendances.data && attendances.data.length > 0) {
                attendances.data.forEach(attendance => {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50 transition-colors duration-150';
                    row.innerHTML = `
                    <td class="px-8 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            ${attendance.foto ? 
                                `<div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="${attendance.foto}" alt="Foto">
                                </div>` : 
                                `<div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-400">
                                    <i class="fas fa-user"></i>
                                </div>`
                            }
                            <div class="ml-4">
                                <div class="text-base font-medium text-gray-900">${attendance.nama}</div>
                            <div class="text-sm text-gray-500">${attendance.hadir_untuk}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-8 py-4 whitespace-nowrap">
                    <div class="text-base text-gray-500">${attendance.lokasi}</div>
                    </td>
                    <td class="px-8 py-4 whitespace-nowrap">
                    <div class="text-base text-gray-500">${attendance.tanggal}</div>
                    </td>
                    <td class="px-8 py-4 whitespace-nowrap">
                    <div class="text-base text-gray-500">${attendance.check_in}</div>
                </td>
                <td class="px-8 py-4 whitespace-nowrap">
                    <div class="text-base text-gray-500">${attendance.check_out}</div>
                </td>
                <td class="px-8 py-4 whitespace-nowrap">
                    <div class="text-base text-gray-500">${
                        attendance.check_out && !isNaN(attendance.duration) ? formatDuration(Math.abs(attendance.duration)) : 'Masih bekerja'
                    }</div>
                    </td>
                    <td class="px-8 py-4 whitespace-nowrap text-right text-base font-medium">
                        <button data-id="${attendance.id}" class="detail-btn text-purple-600 hover:text-purple-900 mr-3">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button data-id="${attendance.id}" data-nama="${attendance.nama}" class="delete-attendance-btn text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                `;
                    tbody.appendChild(row);
                });

                // Re-attach event listeners
                attachAttendanceEventListeners();
            } else {
                tbody.innerHTML = `
                <tr>
                <td colspan="7" class="px-8 py-4 text-center text-base text-gray-500">Tidak ditemukan data presensi</td>
                </tr>
            `;
            }
        }

        // Function to re-attach employee event listeners
        function attachEmployeeEventListeners() {
            document.querySelectorAll('.edit-employee-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const id = btn.getAttribute('data-id');
                    const nama = btn.getAttribute('data-nama');
                    const hadir_untuk = btn.getAttribute('data-hadir-untuk');
                    document.getElementById('editEmployeeId').value = id;
                    document.getElementById('editEmployeeNama').value = nama;
                    document.getElementById('editEmployeeMengajar').value = hadir_untuk;
                    showModal(editEmployeeModal, editEmployeeModalContent);
                });
            });

            document.querySelectorAll('.delete-employee-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const id = btn.getAttribute('data-id');
                    const nama = btn.getAttribute('data-nama');
                    document.getElementById('deleteEmployeeId').value = id;
                    document.getElementById('deleteEmployeeName').textContent = nama;
                    document.getElementById('deleteEmployeeForm').action =
                        `/admin/employees/${id}`;
                    showModal(deleteEmployeeModal, deleteEmployeeModalContent);
                });
            });
        }

        // Function to re-attach attendance event listeners
        function attachAttendanceEventListeners() {
            document.querySelectorAll('.detail-btn').forEach(button => {
                button.addEventListener('click', async () => {
                    try {
                        const response = await fetch(
                            `/admin/attendance/${button.dataset.id}`);
                        if (!response.ok) throw new Error('Failed to load data');
                        const data = await response.json();

                        // Set check-in photo
                        setImageSafe('detailFotoCheckIn', data.foto);

                        // Handle check-out section
                        if (data.check_out) {
                            document.getElementById('checkoutInfo').classList.remove(
                                'hidden');
                            setCheckoutImage('detailFotoCheckOut', data.foto_checkout, data
                                .early_leave_reason);
                        } else {
                            document.getElementById('checkoutInfo').classList.add('hidden');
                        }

                        // Set text content
                        setTextSafe('detailNama', data.nama);
                        setTextSafe('detailHadirUntuk', data.hadir_untuk);
                        setTextSafe('detailLokasi', data.lokasi);
                        setTextSafe('detailTanggal', data.tanggal);
                        setTextSafe('detailCheckInTime', data.check_in);
                        setTextSafe('detailCheckOutTime', data.check_out);

                        // Update status
                        const statusElement = document.getElementById(
                        'detailCheckInStatus');
                        if (statusElement) {
                            statusElement.textContent = data.check_out ? 'Selesai Bekerja' :
                                'Masih Bekerja';
                            statusElement.className = data.check_out ?
                                'text-base font-medium text-green-600' :
                                'text-base font-medium text-blue-600';
                        }

                        // Handle work hours
                        if (data.check_out) {
                            const totalMinutes = Math.abs(data
                            .work_hours); // Ensure positive
                            setTextSafe('detailWorkHours', formatDuration(totalMinutes));
                        }

                        // Handle early leave reason
                        const earlyLeaveInfo = document.getElementById('earlyLeaveInfo');
                        if (data.early_leave_reason) {
                            earlyLeaveInfo.classList.remove('hidden');
                            setTextSafe('detailEarlyLeaveReason', data.early_leave_reason);
                        } else {
                            earlyLeaveInfo.classList.add('hidden');
                        }

                        // Update attendance percentage
                        const percentage = Math.abs(data.attendance_percentage ||
                        0); // Ensure positive
                        setTextSafe('detailAttendancePercentage',
                            percentage > 0 ? `${percentage}%` : '0%'
                        );

                        showModal(detailModal, detailModalContent);
                    } catch (error) {
                        console.error('Error:', error);
                        showNotification('error', error.message);
                    }
                });
            });

            // Delete attendance buttons
            document.querySelectorAll('.delete-attendance-btn').forEach(button => {
                button.addEventListener('click', () => {
                    document.getElementById('deleteAttendanceId').value = button.dataset.id;
                    document.getElementById('deleteAttendanceName').textContent = button.dataset
                        .nama;
                    document.getElementById('deleteAttendanceForm').action =
                        `/admin/attendances/${button.dataset.id}`;
                    showModal(deleteAttendanceModal, deleteAttendanceModalContent);
                });
            });
        }

        // Click outside modal to close
        window.addEventListener('click', (e) => {
            if (e.target === detailModal) hideModal(detailModal, detailModalContent);
            if (e.target === addEmployeeModal) hideModal(addEmployeeModal, addEmployeeModalContent,
                addEmployeeForm);
            if (e.target === editEmployeeModal) hideModal(editEmployeeModal, editEmployeeModalContent,
                editEmployeeForm);
            if (e.target === deleteAttendanceModal) hideModal(deleteAttendanceModal,
                deleteAttendanceModalContent);
            if (e.target === deleteEmployeeModal) hideModal(deleteEmployeeModal,
                deleteEmployeeModalContent);
        });

        // Initialize event listeners for initial table content
        attachEmployeeEventListeners();
        attachAttendanceEventListeners();

        // Per Page Change Handler
        const perPageSelect = document.getElementById('perPage');
        if (perPageSelect) {
            perPageSelect.addEventListener('change', function () {
                const url = new URL(window.location.href);
                url.searchParams.set('per_page', this.value);
                window.location.href = url.toString();
            });
        }

        // Update unread notifications count every 30 seconds
        function updateUnreadNotifications() {
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

        // Initial update
        updateUnreadNotifications();

        // Update every 30 seconds
        setInterval(updateUnreadNotifications, 30000);

        // Safely set image sources with error handling
        function setImageSafe(elementId, imageUrl, defaultUrl = '/images/default-user.jpg') {
            const element = document.getElementById(elementId);
            if (element) {
                if (imageUrl) {
                    element.src = imageUrl;
                    element.onerror = () => {
                        element.src = defaultUrl;
                    };
                } else {
                    element.src = defaultUrl;
                }
            }
        }

        // Handle checkout image (auto-checkout vs manual)
        function setCheckoutImage(elementId, imageUrl, earlyLeaveReason) {
            const container = document.getElementById('checkoutPhotoContainer');
            if (!container) {
                console.error('Checkout photo container not found!');
                return;
            }

            if (!imageUrl && earlyLeaveReason === 'Auto checkout by system') {
                // Display placeholder for auto-checkout
                container.innerHTML = `
                    <div class="w-full h-56 bg-white flex flex-col items-center justify-center text-gray-500 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-base font-medium">Auto Checkout by System</p>
                    </div>
                `;
            } else {
                // Display image for manual checkout
                container.innerHTML = `
                    <img id="${elementId}" src="" alt="Foto Check Out" class="w-full h-56 object-cover bg-gray-200">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                `;
                const imgElement = document.getElementById(elementId);
                if (imgElement) {
                    if (imageUrl) {
                        imgElement.src = imageUrl;
                        imgElement.onerror = () => {
                            imgElement.src = '/images/default-user.jpg';
                        };
                    } else {
                        imgElement.src = '/images/default-user.jpg';
                    }
                }
            }
        }

        // Safely set text content
        function setTextSafe(elementId, text, defaultText = '-') {
            const element = document.getElementById(elementId);
            if (element) {
                element.textContent = text || defaultText;
            }
        }

        // Gradient for charts
        function createGradient(ctx, color1, color2) {
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, color1);
            gradient.addColorStop(1, color2);
            return gradient;
        }

        // Custom tooltip styles
        const customTooltipStyle = {
            backgroundColor: 'rgba(255, 255, 255, 0.95)',
            borderColor: 'rgba(0, 0, 0, 0.1)',
            borderWidth: 1,
            titleColor: '#1F2937',
            bodyColor: '#4B5563',
            bodyFont: {
                family: "'Fredoka', sans-serif",
                size: 13
            },
            padding: 12,
            boxPadding: 6,
            cornerRadius: 8,
            boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)'
        };

        // Weekly Chart
        const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
        const weeklyGradient = createGradient(weeklyCtx, 'rgba(245, 158, 11, 0.3)', 'rgba(245, 158, 11, 0.02)');
        
        new Chart(weeklyCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($weeklyAttendances->keys()) !!},
                datasets: [{
                    label: 'Persentase Kehadiran',
                    data: {!! json_encode($weeklyAttendances->pluck('percentage')) !!},
                    backgroundColor: weeklyGradient,
                    borderColor: 'rgb(245, 158, 11)',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    maxBarThickness: 40,
                    hoverBackgroundColor: 'rgba(245, 158, 11, 0.4)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            },
                            font: {
                                family: "'Fredoka', sans-serif",
                                size: 11
                            },
                            color: '#6B7280'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: "'Fredoka', sans-serif",
                                size: 11
                            },
                            color: '#6B7280'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        ...customTooltipStyle,
                        callbacks: {
                            label: function(context) {
                                const attendance = {!! json_encode($weeklyAttendances) !!}[context.label];
                                return [
                                    `Kehadiran: ${context.parsed.y.toFixed(1)}%`,
                                    `Total: ${attendance.count} karyawan`
                                ];
                            }
                        }
                    }
                }
            }
        });

        // Monthly Chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyGradient = createGradient(monthlyCtx, 'rgba(147, 51, 234, 0.2)', 'rgba(147, 51, 234, 0.02)');
        
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($monthlyAttendances->keys()) !!},
                datasets: [{
                    label: 'Persentase Kehadiran',
                    data: {!! json_encode($monthlyAttendances->pluck('percentage')) !!},
                    backgroundColor: monthlyGradient,
                    borderColor: 'rgb(147, 51, 234)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'white',
                    pointBorderColor: 'rgb(147, 51, 234)',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: 'white',
                    pointHoverBorderColor: 'rgb(147, 51, 234)',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1000,
                    easing: 'easeInOutQuart'
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            },
                            font: {
                                family: "'Fredoka', sans-serif",
                                size: 11
                            },
                            color: '#6B7280'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxTicksLimit: 10,
                            font: {
                                family: "'Fredoka', sans-serif",
                                size: 11
                            },
                            color: '#6B7280'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        ...customTooltipStyle,
                        callbacks: {
                            label: function(context) {
                                const attendance = {!! json_encode($monthlyAttendances) !!}[context.label];
                                return [
                                    `Kehadiran: ${context.parsed.y.toFixed(1)}%`,
                                    `Total: ${attendance.count} karyawan`
                                ];
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
